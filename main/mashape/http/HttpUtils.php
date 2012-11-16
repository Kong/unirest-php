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

class HttpUtils {

	const JSON_PARAM_BODY = "88416847677069008618";

	public static function cleanParameters(&$parameters) {
        if ($parameters == null) {
            $parameters = array();
        } else if (is_array($parameters)) {
            // Remove null parameters
            $keys = array_keys($parameters);
            for ($i = 0;$i<count($keys);$i++) {
                $key = $keys[$i];
                if ($parameters[$key] === null) {
                    unset($parameters[$key]);
                } else {
                    $parameters[$key] = (string)$parameters[$key];
                }
            }
        }
	}

	public static function buildDataForContentType($contentType, $parameters, &$headers) {
		$data = null;
		switch ($contentType) {
		case ContentType::FORM:
			$data = http_build_query($parameters);
			break;
		case ContentType::MULTIPART:
			$data = $parameters;
			break;
		case ContentType::JSON:
			$headers[] = "Content-Type: application/json";
			$data = json_encode($parameters[JSON_PARAM_BODY]);
			break;
		default:
			throw new MashapeClientException(
				EXCEPTION_NOTSUPPORTED_CONTENTTYPE,
				EXCEPTION_NOTSUPPORTED_CONTENTTYPE_CODE);
		}
		return $data;
	}

	public static function handleAuthentication($authHandlers) {
		$headers = array();
		$parameters = array();
		$headers[] = self::generateClientHeaders();
		// Authentication
		foreach($authHandlers as $handler) {
			if ($handler instanceof QueryAuthentication) {
				$parameters = array_merge($parameters, $handler->handleParams());
			} else if ($handler instanceof HeaderAuthentication) {
				$headers = array_merge($headers, $handler->handleHeaders());
			} else if ($handler instanceof Oauth10aAuthentication) {
				$headers = array_merge($headers, $handler->handleHeaders());
			} else if ($handler instanceof Oauth2Authentication) {
				$parameters = array_merge($parameters, $handler->handleParams());
			}
		}
		return array($headers, $parameters);
	}

	public static function generateClientHeaders() {
		$headers = "User-Agent: mashape-php/1.0: ";
		return $headers;
	}
}

?>