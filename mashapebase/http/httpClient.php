<?php

/*
 * Mashape PHP Client library.
 *
 * Copyright (C) 2011 Mashape, Inc.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 *
 * The author of this software is Mashape, Inc.
 * For any question or feedback please contact us at: support@mashape.com
 *
 */

require_once(dirname(__FILE__) . "/../init/init.php");
require_once(dirname(__FILE__) . "/../exceptions/mashapeClientException.php");

define("METHOD", "_method");
define("TOKEN", "_token");
define("LANGUAGE", "_language");
define("VERSION", "_version");

class HttpMethod
{
	const DELETE = 0;
	const GET = 1;
	const POST = 2;
	const PUT = 3;
}

class HttpClient {

	public static function call($baseUrl, $httpMethod, $method, $token, $parameters) {
		if (empty($parameters)) {
			$parameters = array();
		} else {
			// Remove null parameters
			$keys = array_keys($parameters);
			for ($i = 0;$i<count($keys);$i++) {
				$key = $keys[$i];
				if ($parameters[$key] === null) {
					unset($parameters[$key]);
				} else {
					// Convert every value to a string value
				    $parameters[$key] = (string)$parameters[$key];
				}
			}
		}

		$parameters[METHOD] = $method;
		$parameters[TOKEN] = $token;
		$parameters[LANGUAGE] = CLIENT_LIBRARY_LANGUAGE;
		$parameters[VERSION] = CLIENT_LIBRARY_VERSION;

		$response;
		switch($httpMethod) {
			case HttpMethod::DELETE:
				$response = self::doDelete($baseUrl, $parameters);
				break;
			case HttpMethod::GET:
				$response = self::doGet($baseUrl, $parameters);
				break;
			case HttpMethod::POST:
				$response = self::doPost($baseUrl, $parameters);
				break;
			case HttpMethod::PUT:
				$response = self::doPut($baseUrl, $parameters);
				break;
			default:
				throw new MashapeClientException(EXCEPTION_NOTSUPPORTED_HTTPMETHOD, EXCEPTION_NOTSUPPORTED_HTTPMETHOD_CODE);
		}
		if (empty($response)) {
			throw new MashapeClientException(EXCEPTION_EMPTY_REQUEST, EXCEPTION_SYSTEM_ERROR_CODE);
		}
		$responseObject = json_decode($response);
		if (empty($responseObject)) {
			throw new MashapeClientException(EXCEPTION_JSONDECODE_REQUEST, EXCEPTION_SYSTEM_ERROR_CODE);
		}
		return $responseObject;
	}

	public static function doGet($url, $parameters) {
		$queryString = "";
		if (!empty($parameters)) {
			$keys = array_keys($parameters);
			for ($i = 0;$i<count($keys);$i++) {
				$key = $keys[$i];
				if ($i != 0) {
					$queryString .= "&";
				}
				$queryString .= $key . "=" . urlencode($parameters[$key]);
			}
		}

		$response = self::makeRequest($url . "?" . $queryString, "GET", null);
		return $response;
	}

	private static function doPost($url, $parameters) {
		$response = self::makeRequest($url, "POST", $parameters);
		return $response;
	}

	private static function doPut($url, $parameters) {
		$response = self::makeRequest($url, "PUT", $parameters);
		return $response;
	}

	private static function doDelete($url, $parameters) {
		$response = self::makeRequest($url, "DELETE", $parameters);
		return $response;
	}

	private static function makeRequest($url, $httpMethod, $parameters) {
		$data = null;
		if (!(empty($parameters))) {
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

}

?>
