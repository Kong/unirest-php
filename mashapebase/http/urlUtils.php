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

class UrlUtils {

	private static function addRouteParameter($url, $parameterName) {
		$result = $url;
		$pos = strpos($url, "?");
		if ($pos === false) {
			$result .= "?";
		}
		if (substr($result, strlen($result) - 1, 1) != "?") {
			$result .= "&";
		}
		$result .= $parameterName . "={" . $parameterName . "}";
		return $result;
	}

	public static function addClientParameters($url) {
		$result = self::addRouteParameter($url, TOKEN);
		$result = self::addRouteParameter($result, LANGUAGE);
		$result = self::addRouteParameter($result, VERSION);
		return $result;
	}

	public static function getCleanUrl($url, $parameters) {
		if ($parameters == null) {
			$parameters = array();
		}

		$finalUrl = "";

		for($i=0;$i < strlen($url);$i++) {
			$curchar = substr($url, $i, 1);

			if ($curchar == "{") {
				// It may be a placeholder

				$pos = strpos($url, "}", $i);
				if ($pos !== false) {
					// It's a placeholder

					$placeHolder = substr($url, $i + 1, $pos - 1 - $i); // Get the placeholder name without {..}
					if (array_key_exists($placeHolder, $parameters) === false) {
						// If it doesn't exist in the array, remove it
							
						if (substr($url, $i - 1, 1) == "=") {
							// It's a query string placeholder, remove also its name

							for ($t = strlen($finalUrl) - 1;$t>=0;$t--) {
								$backChar = substr($finalUrl, $t, 1);
								if ($backChar == "?" || $backChar == "&") {
									$finalUrl = substr($finalUrl, 0, ($backChar == "?") ? $t + 1 : $t);
									break;
								}
							}

						}
							
						$i = $pos;
						continue;
							
					}
				}
			}
			$finalUrl .= $curchar;

		}

		return str_replace("?&", "?", $finalUrl);

	}

	public static function removeQueryString($url) {
		$urlParts = explode("?", $url);
		return $urlParts[0];
	}

	public static function getQueryStringParameters($url) {
		$result = array();
		$urlParts = explode("?", $url);
		if (count($urlParts) > 1) {
			$queryString = $urlParts[1];
			$parameters = explode("&", $queryString);
			foreach($parameters as $parameter) {
				$p = explode("=", $parameter);
				if (count($p) > 1) {
					if (self::isPlaceHolder($p[1]) == false) {
						$result[$p[0]] = $p[1];
					}
				}
			}
		}
		return $result;
	}

	private static function isPlaceholder($val) {
		if (!empty($val)) {
			if (strlen($val) >= 2) {
				if (substr($val, 0, 1) == "{" && substr($val, strlen($val) - 1, 1) == "}") {
					return true;
				}
			}
		}
		return false;
	}

}
?>