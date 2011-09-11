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

require_once(MASHAPE_CLIENT_LIBRAY_PATH . "/http/HttpClient.php");

class HttpClientTest extends PHPUnit_Framework_TestCase {

	function testDoRequest() {

		try {
			HttpClient::doRequest("ciao", "http://www.ciao.com", null, null);
			$this->assertFalse(true);
		} catch (MashapeClientException $e) {
			$this->assertEquals(1003, $e->getCode());
		}

		try {
			HttpClient::doRequest(HttpMethod::GET, "http://www.google.com", null, null);
			$this->assertFalse(true);
		} catch (MashapeClientException $e) {
			$this->assertEquals(2000, $e->getCode());
		}

		$response = HttpClient::doRequest(HttpMethod::POST, "https://api.mashape.com/requestToken", null, null);
		$this->assertEquals(2001, $response->errors[0]->code);
	}

}

?>
