<?php

require_once(dirname(__FILE__) . "/Chunked.php");
require_once(dirname(__FILE__) . "/HttpResponse.php");
require_once(dirname(__FILE__) . "/HttpMethod.php");

class HttpClient {

	const USER_AGENT = "mashape-php/3.0";

	public static function get($url, $headers = array()) {
		return HttpClient::request(HttpMethod::GET, $url, NULL, $headers);
	}
	
	public static function post($url, $body = NULL, $headers = array()) {
		return HttpClient::request(HttpMethod::POST, $url, $body, $headers);
	}

	private static function request($httpMethod, $url, $body = NULL, $headers = array()) {
		
		$lowercaseHeaders = array();
		foreach ($headers as $key => $val) {
			$lowercaseHeaders[strtolower($key)] = $val;
		}
		
		$lowercaseHeaders["user-agent"] = USER_AGENT;
		
		$ch = curl_init();
		if ($httpMethod != HttpMethod::GET) {
			curl_setopt ($ch, CURLOPT_CUSTOMREQUEST, $httpMethod);
			//TODO: Remove
			/*
			if (is_array($body)) {
				$parameters = "";
				foreach($body as $key => $value) {
					if (is_array($value)) {
						throw new Exception("Nested arrays are not supported");
					}
					
					$parameters .= $key . "=";
					if (substr($value, 0, 1) == "@") {
						// It's a path
						$parameters .= $value;
					} else {
						$parameters .= rawurlencode($value);
					}
					
					$parameters .= "&";
				}
				
				if (strlen($parameters) > 1) {
					$parameters = substr($parameters, 0, strlen($parameters) - 1);
				}
				
				$body = $parameters;
				var_dump($body);
			}
			*/
			curl_setopt ($ch, CURLOPT_POSTFIELDS, $body);
		}
		
		curl_setopt ($ch, CURLOPT_URL , HttpClient::encodeUrl($url));
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt ($ch, CURLOPT_MAXREDIRS, 10);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
		curl_setopt ($ch, CURLOPT_HTTPHEADER, $lowercaseHeaders);
		curl_setopt ($ch, CURLOPT_HEADER, true);
		curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, false);
		
		$response = curl_exec($ch);
		if (curl_error($ch)) {
			throw new Exception(curl_error($ch));
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
		$parsedUrl = parse_url($url);
		parse_str( $parsedUrl['query'], $query ); // generating an array by reference (yes, kinda weird)

		$result = $parsedUrl["scheme"] . "://" . $parsedUrl["host"] . (($parsedUrl["port"] != NULL ? ":" . $parsedUrl["port"] : "")) . $parsedUrl["path"] . "?";	
	
		if ($query != null) {
			foreach($query as $key => $val) {
				$result .= $key . "=" . rawurlencode($val) . "&";
			}
			$result = substr($result, 0, strlen($result) - 1);
		}
		return $result;
	}
	
}	
	
?>