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

class AuthenticationUtil {

	public static function generateAuthenticationHeader($publicKey, $privateKey) {
		$header = "";
		if (!($publicKey == null || $privateKey == null)) {
			$hash = hash_hmac("sha1", $publicKey, $privateKey);
			$header = "X-Mashape-Authorization: " . base64_encode($publicKey . ":" . $hash);
		}
		return $header;
	}

}

?>