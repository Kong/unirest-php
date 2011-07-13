<?php

require_once(dirname(__FILE__) . "/../json/Json.php");
require_once(dirname(__FILE__) . "/../exceptions/MashapeClientException.php");
require_once(dirname(__FILE__) . "/HttpMethod.php");
require_once(dirname(__FILE__) . "/UrlUtils.php");

class HttpClient {

	public static function doRequest($httpMethod, $url, $parameters, $token) {
		if (!($httpMethod == HttpMethod::DELETE || $httpMethod == HttpMethod::GET ||
		$httpMethod == HttpMethod::POST || $httpMethod == HttpMethod::PUT)) {
			throw new MashapeClientException(EXCEPTION_NOTSUPPORTED_HTTPMETHOD, EXCEPTION_NOTSUPPORTED_HTTPMETHOD_CODE);
		}

		UrlUtils::addClientParameters($url, $parameters, $token);
		UrlUtils::prepareRequest($url, $parameters, ($httpMethod != HttpMethod::GET) ? true : false);
		
		$response = self::execRequest($httpMethod, $url, $parameters);
		if (empty($response)) {
		    throw new MashapeClientException(EXCEPTION_EMPTY_RESPONSE, EXCEPTION_SYSTEM_ERROR_CODE);
		}

		$jsonResponse = json_decode($response);
		if (empty($jsonResponse)) {
			throw new MashapeClientException(sprintf(EXCEPTION_JSONDECODE_REQUEST, $response), EXCEPTION_SYSTEM_ERROR_CODE);
		}

		return $jsonResponse;

	}

	private static function execRequest($httpMethod, $url, $parameters) {
		$data = null;
		if ($httpMethod != HttpMethod::GET) {
			$url = self::removeQueryString($url);
			$data = http_build_query($parameters);
		}

		$opts = array('http' =>
		array(
				'ignore_errors' => true,
		        'method'  => $httpMethod,
		        'content' => $data
		)
		);

		$context  = stream_context_create($opts);
		$response = @file_get_contents($url, false, $context);
		return $response;
	}

	private static function removeQueryString($url) {
		$pos = strpos($url, "?");
		if ($pos !== false) {
			return substr($url, 0, $pos);
		}
		return $url;
	}

}



?>
