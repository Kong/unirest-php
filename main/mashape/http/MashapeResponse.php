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

require_once(dirname(__FILE__) . "/../json/Json.php");
require_once(dirname(__FILE__) . "/Chunked.php");
require_once(dirname(__FILE__) . "/../exceptions/MashapeClientException.php");

class MashapeResponse {
    public $statusCode;
    public $body;
    public $rawBody;
    public $headers;

    function __construct($response, $statusCode, $headers) {
        $this->rawBody = $response;
        $this->headers = $headers;
        $this->statusCode = $statusCode;
    }

    function parseBodyAsJson() {
        $this->body = json_decode($this->rawBody);
		if (empty($this->body) && ($this->statusCode == 200)) {
			// It may be a chunked response
			//$this->body = json_decode(http_chunked_decode($this->rawBody));
			if (empty($this->body)) {
                throw new MashapeClientException(
                    sprintf(EXCEPTION_JSONDECODE_REQUEST, $this->rawBody),
                    EXCEPTION_SYSTEM_ERROR_CODE);
			}
		}
    }
}
?>
