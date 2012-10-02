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

require_once(dirname(__FILE__) . "/../exceptions/MashapeClientException.php");
require_once(dirname(__FILE__) . "/HttpMethod.php");
require_once(dirname(__FILE__) . "/ContentType.php");
require_once(dirname(__FILE__) . "/UrlUtils.php");
require_once(dirname(__FILE__) . "/HttpUtils.php");
require_once(dirname(__FILE__) . "/MashapeResponse.php");
require_once(dirname(__FILE__) . "/../authentication/HeaderAuthentication.php");
require_once(dirname(__FILE__) . "/../authentication/BasicAuthentication.php");
require_once(dirname(__FILE__) . "/../authentication/CustomHeaderAuthentication.php");
require_once(dirname(__FILE__) . "/../authentication/MashapeAuthentication.php");
require_once(dirname(__FILE__) . "/../authentication/QueryAuthentication.php");
require_once(dirname(__FILE__) . "/../authentication/Oauth10aAuthentication.php");
require_once(dirname(__FILE__) . "/../authentication/Oauth2Authentication.php");

class HttpClient {

	public static function doRequest($httpMethod, $url, $parameters, 
			$authHandlers, $contentType = ContentType::FORM, $encodeJson = true) {
		HttpUtils::cleanParameters($parameters);

		if ($authHandlers == null) {
			$authHandlers = array();
		}

		self::validateRequest($httpMethod, $url, $parameters, $authHandlers, $contentType);
		$response = self::execRequest($httpMethod, $url, $parameters, $authHandlers, $contentType, $encodeJson);

		return $response;
	}

	private static function validateRequest($httpMethod, $url, $parameters, $authHandlers, $contentType) {
		if ( !($httpMethod == HttpMethod::DELETE 
				|| $httpMethod == HttpMethod::GET 
				|| $httpMethod == HttpMethod::POST 
				|| $httpMethod == HttpMethod::PUT 
				|| $httpMethod == HttpMethod::PATCH)) {
			// we only support these HTTP methods.
			throw new MashapeClientException(EXCEPTION_NOTSUPPORTED_HTTPMETHOD, 
				EXCEPTION_NOTSUPPORTED_HTTPMETHOD_CODE);
		}
		if ($contentType == ContentType::JSON && is_array($parameters)) {
			// Content type JSON does not allow array parameters.
			throw new MashapeClientException(
				EXCEPTION_CONTENT_TYPE_JSON_ARRAY, 
				EXCEPTION_CONTENT_TYPE_JSON_ARRAY_CODE);
		}
		if (!is_array($parameters) && $contentType != ContentType::JSON) {
			// Raw parameters are only allows for ContentType::JSON 
			throw new MashapeClientException(
				EXCEPTION_CONTENT_TYPE_NON_ARRAY, 
				EXCEPTION_CONTENT_TYPE_NON_ARRAY_CODE);
		}
		if ($httpMethod == HttpMethod::GET && $contentType != ContentType::FORM) {
			// if we have a GET request that is anything other than urlencoded 
			// form data, we shouldn't allow it.
			throw new MashapeClientException(
				EXCEPTION_GET_INVALID_CONTENTTYPE, 
				EXCEPTION_GET_INVALID_CONTENTTYPE_CODE);
		}
		if ($contentType == ContentType::JSON) {
			foreach ($authHandlers as $handler) {
				if ($handler instanceof QueryAuthentication) {
					// bad. No room for query auth parameters if the whole body is json
					throw new MashapeClientException(
						EXCEPTION_CONTENT_TYPE_JSON_QUERYAUTH, 
						EXCEPTION_CONTENT_TYPE_JSON_QUERYAUTH_CODE);
				}
			}
		}
	}

	private static function execRequest($httpMethod, $url, $parameters, $authHandlers, $contentType, $encodeJson) {
		// first, collect the headers and parameters we'll need from the authentication handlers
		list($headers, $authParameters) = HttpUtils::handleAuthentication($authHandlers);
		if (is_array($parameters)) {
			$parameters = array_merge($parameters, $authParameters);
		}

		// prepare the request
		$ch = curl_init ();
		
		if ($httpMethod == HttpMethod::GET) {
			$url = UrlUtils::buildUrlWithQueryString($url, $parameters);
		} else {
			$data = HttpUtils::buildDataForContentType($contentType, $parameters, $headers);
			curl_setopt ($ch, CURLOPT_CUSTOMREQUEST, $httpMethod);
			curl_setopt ($ch, CURLOPT_POSTFIELDS, $data);
		}
		curl_setopt ($ch, CURLOPT_URL , $url);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt ($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt ($ch, CURLINFO_HEADER_OUT, true);
		curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, false);
		$response = curl_exec($ch);
		if (curl_error($ch)) {
			throw new MashapeClientException(
				EXCEPTION_CURL . ":" . curl_error($ch), 
				EXCEPTION_CURL_CODE);
		}
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$responseHeaders = curl_getinfo($ch, CURLINFO_HEADER_OUT);
		curl_close($ch);

		return new MashapeResponse($response, $httpCode, $responseHeaders, $encodeJson);
	}
}

?>
