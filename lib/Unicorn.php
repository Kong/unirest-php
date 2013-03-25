<?php

require_once(dirname(__FILE__) . "/Chunked.php");
require_once(dirname(__FILE__) . "/HttpResponse.php");
require_once(dirname(__FILE__) . "/HttpMethod.php");

class Unicorn {

	public static function get($url, $headers = array()) {
		return Unicorn::request(HttpMethod::GET, $url, NULL, $headers);
	}
	
	public static function post($url, $headers = array(), $body = NULL) {
		return Unicorn::request(HttpMethod::POST, $url, $body, $headers);
	}
	
	public static function delete($url, $headers = array(), $body = NULL) {
		return Unicorn::request(HttpMethod::DELETE, $url, $body, $headers);
	}

	public static function put($url, $headers = array(), $body = NULL) {
		return Unicorn::request(HttpMethod::PUT, $url, $body, $headers);
	}
	
	public static function patch($url, $headers = array(), $body = NULL) {
		return Unicorn::request(HttpMethod::PATCH, $url, $body, $headers);
	}

	private static function request($httpMethod, $url, $body = NULL, $headers = array()) {
		$lowercaseHeaders = array();
		foreach ($headers as $key => $val) {
			$key = trim(strtolower($key));
			if ($key == "user-agent") continue;
			$lowercaseHeaders[] = $key . ": " . $val;
		}
		$lowercaseHeaders[] = "user-agent: unicorn-php/1.0";
		
		$ch = curl_init();
		if ($httpMethod != HttpMethod::GET) {
			curl_setopt ($ch, CURLOPT_CUSTOMREQUEST, $httpMethod);
			curl_setopt ($ch, CURLOPT_POSTFIELDS, $body);
		}
				
		curl_setopt ($ch, CURLOPT_URL , Unicorn::encodeUrl($url));
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt ($ch, CURLOPT_MAXREDIRS, 10);
		curl_setopt ($ch, CURLOPT_HTTPHEADER, $lowercaseHeaders);
		curl_setopt ($ch, CURLOPT_HEADER, true);
		curl_setopt ($ch, CURLOPT_HTTPHEADER, array("Expect:"));
		curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, false);
		
		$response = curl_exec($ch);
		$error = curl_error($ch);
		if ($error) {
			throw new Exception($error);
		}
		
		// Split the full response in its headers and body
		$curl_info = curl_getinfo($ch);
		$header_size = $curl_info["header_size"];
		$header = substr($response, 0, $header_size);
		$body = substr($response, $header_size);
		$httpCode = $curl_info["http_code"];
		
		return new HttpResponse($httpCode, $body, $header);
	}
	
	private static function encodeUrl($url) {
		// Parse URL into pieces
		$url_parsed = parse_url($url);

		// Build the basics bypassing notices
		$scheme = $url_parsed['scheme'] . '://';
		$host = $url_parsed['host'];
		$port = ( isset($url_parsed['port']) ? $url_parsed['port'] : null );
		$path = ( isset($url_parsed['path']) ? $url_parsed['path'] : null );
		$query = ( isset($url_parsed['query']) ? $url_parsed['query'] : null );

		// Do we need to encode anything?
		if ($query != null) {
			// Break up the query into an array
			parse_str($url_parsed['query'], $query_parsed);
			// Encode and build query based on RFC 1738
			$query = '?'.http_build_query($query_parsed);
		}

		// Return the completed URL
		$result = $scheme . $host . $port . $path . $query;
		return $result;
	}
	
}	
	
?>