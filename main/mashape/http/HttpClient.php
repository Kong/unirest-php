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
require_once(dirname(__FILE__) . "/UrlUtils.php");
require_once(dirname(__FILE__) . "/MashapeResponse.php");
require_once(dirname(__FILE__) . "/../auth/HeaderAuth.php");
require_once(dirname(__FILE__) . "/../auth/BasicAuth.php");
require_once(dirname(__FILE__) . "/../auth/CustomHeaderAuth.php");
require_once(dirname(__FILE__) . "/../auth/MashapeAuth.php");
require_once(dirname(__FILE__) . "/../auth/QueryAuth.php");

class HttpClient {

	public static function doRequest($httpMethod, $url, $parameters, $authHandlers, $encodeJson = true) {
		
		if (!($httpMethod == HttpMethod::DELETE || $httpMethod == HttpMethod::GET ||
		$httpMethod == HttpMethod::POST || $httpMethod == HttpMethod::PUT)) {
			throw new MashapeClientException(EXCEPTION_NOTSUPPORTED_HTTPMETHOD, EXCEPTION_NOTSUPPORTED_HTTPMETHOD_CODE);
		}
		
		$response = self::execRequest($httpMethod, $url, $parameters, $authHandlers);

		if ($encodeJson) {
			$response->parseBodyAsJson();
		}
		
		return $response;
	}

	private static function execRequest($httpMethod, $url, $parameters, $authHandlers) {
		$data = null;
		if ($parameters == null) {
			$parameters = array();
		}
		
		$headers = array();
        $headers[] = UrlUtils::generateClientHeaders();
		// Authentication
		foreach($authHandlers as $handler) {
			if ($handler instanceof QueryAuth) {
				$parameters = array_merge($parameters, $handler->handleParams());
			} else if ($handler instanceof HeaderAuth) {
				$headers[] = $handler->handleHeader();
			}
		}
		
		UrlUtils::prepareRequest($url, $parameters, ($httpMethod != HttpMethod::GET) ? true : false);

		if ($httpMethod != HttpMethod::GET) {
			$data = http_build_query($parameters);
		}
        $ch = curl_init ();

        // prepare the request
        curl_setopt ($ch, CURLOPT_URL , $url); 
		if ($httpMethod != HttpMethod::GET) {
			curl_setopt ($ch, CURLOPT_CUSTOMREQUEST, $httpMethod);
            //curl_setopt ($ch, CURLOPT_POST, 1);
            curl_setopt ($ch, CURLOPT_POSTFIELDS, $data);
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
