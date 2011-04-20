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
require_once(dirname(__FILE__) . "/urlUtils.php");

class HttpMethod
{
	const DELETE = 0;
	const GET = 1;
	const POST = 2;
	const PUT = 3;
}

class HttpClient {

	public static function call($url, $httpMethod, $token, $parameters) {
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

		$parameters[TOKEN] = $token;
		$parameters[LANGUAGE] = CLIENT_LIBRARY_LANGUAGE;
		$parameters[VERSION] = CLIENT_LIBRARY_VERSION;

		$url = UrlUtils::addClientParameters($url);

		$response;
		switch($httpMethod) {
			case HttpMethod::DELETE:
				$response = self::doDelete($url, $parameters);
				break;
			case HttpMethod::GET:
				$response = self::doGet($url, $parameters);
				break;
			case HttpMethod::POST:
				$response = self::doPost($url, $parameters);
				break;
			case HttpMethod::PUT:
				$response = self::doPut($url, $parameters);
				break;
			default:
				throw new MashapeClientException(EXCEPTION_NOTSUPPORTED_HTTPMETHOD, EXCEPTION_NOTSUPPORTED_HTTPMETHOD_CODE);
		}
		if (empty($response)) {
			throw new MashapeClientException(EXCEPTION_EMPTY_REQUEST, EXCEPTION_SYSTEM_ERROR_CODE);
		}
		$responseObject = json_decode($response);
		if (empty($responseObject)) {
			throw new MashapeClientException(sprintf(EXCEPTION_JSONDECODE_REQUEST, $response), EXCEPTION_SYSTEM_ERROR_CODE);
		}
		return $responseObject;
	}

	private static function replaceParameters($url, $parameters) {
		$finalUrl = UrlUtils::getCleanUrl($url, $parameters);
		if (!empty($parameters)) {
			$keys = array_keys($parameters);
			for ($i = 0;$i<count($keys);$i++) {
				$key = $keys[$i];
				$finalUrl = str_replace("{" . $key . "}", urlencode($parameters[$key]), $finalUrl);
			}
		}
		return $finalUrl;
	}

	private static function doGet($url, $parameters) {
		$finalUrl = self::replaceParameters($url, $parameters);
		$response = self::makeRequest($finalUrl, "GET", null);
		return $response;
	}

	public static function doPost($url, $parameters) {
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
		$finalUrl = $url;
		if (!(empty($parameters))) {
			// It's a POST/PUT/DELETE request
			$data = http_build_query(array_merge($parameters, UrlUtils::getQueryStringParameters($url)));
			$finalUrl = self::replaceParameters($url, $parameters);
			$finalUrl = UrlUtils::removeQueryString($finalUrl);
		}

		$opts = array('http' =>
		array(
				'ignore_errors' => true,
		        'method'  => $httpMethod,
		        'content' => $data
		)
		);

		$context  = stream_context_create($opts);
		$response = @file_get_contents($finalUrl, false, $context);
		return $response;
	}

}

?>
