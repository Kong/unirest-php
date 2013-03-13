<?php

require_once(dirname(__FILE__) . "/Chunked.php");
require_once(dirname(__FILE__) . "/HttpResponse.php");
require_once(dirname(__FILE__) . "/HttpMethod.php");

class Unicorn {

	// Use it like: Unicorn::getCallbackFunction(func_get_args());
	/*
	private static function getCallbackFunction($arg_list) {
		$argCount = count($arg_list);
		if ($argCount > 1) {
			$lastArgument = $arg_list[$argCount - 1];
			if (is_callable($lastArgument)) {
				return $lastArgument;
			}
		}
		return NULL;
	}
	
	private static function doAsync($operation, $callback) {
		require_once(dirname(__FILE__) . "/Thread.php");
		$id = ThreadStore::add(Unicorn::random(), $operation, $callback);
		$thread = new UnicornThread($id);
		$thread->start();
		return $thread;
	}
	*/
	
	public static function get($url, $headers = array()) {
		return Unicorn::request(HttpMethod::GET, $url, NULL, $headers);
	}
	
	public static function post($url, $headers = array(), $body = NULL) {
		return Unicorn::request(HttpMethod::POST, $url, $body, $headers);
	}
	
	public static function delete($url, $headers = array()) {
		return Unicorn::request(HttpMethod::DELETE, $url, NULL, $headers);
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
			if ($key == "user-agent" || key == "expect") continue;
			$lowercaseHeaders[] = $key . ": " . $val;
		}
		$lowercaseHeaders[] = "user-agent: unicorn-php/1.0";
		$lowercaseHeaders[] = "expect:";
				
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
