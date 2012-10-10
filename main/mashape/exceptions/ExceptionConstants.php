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

define("EXCEPTION_NOTSUPPORTED_HTTPMETHOD_CODE", 1003);
define("EXCEPTION_NOTSUPPORTED_HTTPMETHOD", 
	"HTTP method not supported. Only DELETE, GET, POST, PUT are supported");

define("EXCEPTION_CONTENT_TYPE_JSON_ARRAY_CODE", 1004);
define("EXCEPTION_CONTENT_TYPE_JSON_ARRAY", 
	"Content Type JSON does not accept array parameters. Parameters should be"
	." a JSON string");

define("EXCEPTION_CONTENT_TYPE_NON_ARRAY_CODE", 1005);
define("EXCEPTION_CONTENT_TYPE_NON_ARRAY", 
	"Parameters must be an array unless content type is set to JSON");

define("EXCEPTION_CONTENT_TYPE_JSON_QUERYAUTH_CODE", 1006);
define("EXCEPTION_CONTENT_TYPE_JSON_QUERYAUTH", 
	"Query Authentication cannot be used in conjunction with content type JSON");

define("EXCEPTION_OAUTH1_AUTHORIZE_CODE", 1007);
define("EXCEPTION_OAUTH1_AUTHORIZE", 
	"Before consuming an OAuth endpoint, you must invoke the authorize("
		."'access_token', 'access_secret') function with non-null values");

define("EXCEPTION_OAUTH2_AUTHORIZE_CODE", 1007);
define("EXCEPTION_OAUTH2_AUTHORIZE", 
	"Before consuming an OAuth endpoint, you must invoke the authorize("
		."'access_token') function with a non-null value");

define("EXCEPTION_NOTSUPPORTED_CONTENTTYPE_CODE", 415);
define("EXCEPTION_NOTSUPPORTED_CONTENTTYPE", 
	"Content Type not supported. Currently only application/x-www-form-urlencoded, "
	."application/json, and multipart/form-data are supported");

define("EXCEPTION_GET_INVALID_CONTENTTYPE_CODE", 415);
define("EXCEPTION_GET_INVALID_CONTENTTYPE", "A GET request must have a content"
	." type of application/x-www-form-urlencoded or application/json");

define("EXCEPTION_CURL_CODE", 520);
define("EXCEPTION_CURL", 
	"Encountered an exception making the request");

define("EXCEPTION_SYSTEM_ERROR_CODE", 2000);
define("EXCEPTION_JSONDECODE_REQUEST", "Can't deserialize the response JSON: %s");

?>