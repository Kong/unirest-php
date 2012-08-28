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
require_once(dirname(__FILE__) . "/MashapeResponse.php");
require_once(dirname(__FILE__) . "/../authentication/HeaderAuthentication.php");
require_once(dirname(__FILE__) . "/../authentication/BasicAuthentication.php");
require_once(dirname(__FILE__) . "/../authentication/CustomHeaderAuthentication.php");
require_once(dirname(__FILE__) . "/../authentication/MashapeAuthentication.php");
require_once(dirname(__FILE__) . "/../authentication/QueryAuthentication.php");

class HttpClient {

	public static function doRequest($httpMethod, $url, $parameters, $authHandlers, $contentType = ContentType::FORM, $encodeJson = true) {

		if (!($httpMethod == HttpMethod::DELETE || $httpMethod == HttpMethod::GET ||
		$httpMethod == HttpMethod::POST || $httpMethod == HttpMethod::PUT)) {
			throw new MashapeClientException(EXCEPTION_NOTSUPPORTED_HTTPMETHOD, EXCEPTION_NOTSUPPORTED_HTTPMETHOD_CODE);
		}

		$response = self::execRequest($httpMethod, $url, $parameters, $authHandlers, $contentType);

		if ($encodeJson) {
			$response->parseBodyAsJson();
		}

		return $response;
	}

	private static function execRequest($httpMethod, $url, $parameters, $authHandlers, $contentType) {
		$data = null;
		if ($parameters == null) {
			$parameters = array();
		}
		if ($authHandlers == null) {
			$authHandlers = array();
		}

		$headers = array();
        $headers[] = UrlUtils::generateClientHeaders();
		// Authentication
		foreach($authHandlers as $handler) {
			if ($handler instanceof QueryAuthentication) {
				$parameters = array_merge($parameters, $handler->handleParams());
			} else if ($handler instanceof HeaderAuthentication) {
				$headers[] = $handler->handleHeader();
			}
		}

		UrlUtils::prepareRequest($url, $parameters, ($httpMethod != HttpMethod::GET) ? true : false);

		if ($httpMethod != HttpMethod::GET) {
			switch ($contentType) {
			case ContentType::FORM:
				$data = http_build_query($parameters);
				break;
			case ContentType::MULTIPART:
				$data = $parameters;
				break;
			case ContentType::JSON:
				// TODO support json
			default:
				throw new MashapeClientException(
					EXCEPTION_NOTSUPPORTED_CONTENTTYPE, 
					EXCEPTION_NOTSUPPORTED_CONTENTTYPE_CODE);
			}
		} else if ($contentType != ContentType::FORM) {
			// if we have a GET request that is anything other than urlencoded 
			// form data, we shouldn't allow it.
			throw new MashapeClientException(
				EXCEPTION_GET_INVALID_CONTENTTYPE, 
				EXCEPTION_GET_INVALID_CONTENTTYPE_CODE);
		}
        $ch = curl_init ();

        // prepare the request
        curl_setopt ($ch, CURLOPT_URL , $url);
		if ($httpMethod != HttpMethod::GET) {
			curl_setopt ($ch, CURLOPT_CUSTOMREQUEST, $httpMethod);
            //curl_setopt ($ch, CURLOPT_POST, 1);
            curl_setopt ($ch, CURLOPT_POSTFIELDS, $parameters);
        }
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt ($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt ($ch, CURLINFO_HEADER_OUT, true);
        $response = curl_exec($ch);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$responseHeaders = curl_getinfo($ch, CURLINFO_HEADER_OUT);
        curl_close($ch);

		return new MashapeResponse($response, $httpCode, $responseHeaders);
	}
}

?>
