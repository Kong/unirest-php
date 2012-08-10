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

define("PLACEHOLDER_REGEX", "/\{([\w\.]+)\}/");
class UrlUtils {

	public static function prepareRequest(&$url, &$parameters, $addRegularQueryStringParameters = false) {
		if ($parameters == null) {
			$parameters = array();
		}
		// Remove null parameters
        $parameters = array_filter($parameters, function($value) { return !is_null($value); });

		$finalUrl = $url;
		$matches = null;
		$match = preg_match_all(PLACEHOLDER_REGEX, $url, $matches);

		if (!empty($matches) && count($matches) > 1) {
            $bracketedMatches = $matches[0];
			$plainMatches = $matches[1];
			foreach ($plainMatches as $index => $key) {
				if (array_key_exists($key, $parameters)) {
                    $finalUrl = str_replace($bracketedMatches[$index], rawurlencode($parameters[$key]), $finalUrl);
					unset($parameters[$key]);
				} else {
					$finalUrl = preg_replace("/&?[\w]*=?\{" . $key . "\}/", "", $finalUrl);
				}
			}
		}

		$finalUrl = str_replace("?&", "?", $finalUrl);
		$finalUrl = preg_replace("/\?$/", "", $finalUrl);

		if ($addRegularQueryStringParameters) {
			// Get regular query string parameters
			self::addRegularQueryStringParameters($finalUrl, $parameters);
		} else {
			foreach ($parameters as $paramKey => $paramValue) {
				$delimiter = (strpos($finalUrl, "?") === false) ? "?" : "&";
				$finalUrl .= $delimiter . $paramKey . "=" . urlencode($paramValue);
			}
		}

		$url = $finalUrl;
	}

	private static function addRegularQueryStringParameters($url, &$parameters) {
		$urlParts = explode("?", $url);
		if (count($urlParts) > 1) {
			$queryString = $urlParts[1];
			$queryStringParameters = explode("&", $queryString);

			foreach ($queryStringParameters as $queryStringParameter) {
				$queryStringParameterParts = explode("=", $queryStringParameter);
				if (count($queryStringParameterParts) > 1) {
                    list($paramKey, $paramValue) = $queryStringParameterParts;
					if (!self::isPlaceHolder($paramValue)) {
						$parameters[$paramKey] = $paramValue;
					}
				}
			}
		}
	}

	private static function isPlaceHolder($value) {
		return preg_match(PLACEHOLDER_REGEX, $value);
	}

	public static function generateClientHeaders() {
		$headers = "User-Agent: mashape-php/1.0: ";
		return $headers;
	}

}

?>
