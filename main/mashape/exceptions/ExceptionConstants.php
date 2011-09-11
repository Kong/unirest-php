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
define("EXCEPTION_NOTSUPPORTED_HTTPMETHOD", "HTTP method not supported. Only DELETE, GET, POST, PUT are supported");

define("EXCEPTION_SYSTEM_ERROR_CODE", 2000);
define("EXCEPTION_JSONDECODE_REQUEST", "Can't deserialize the response JSON from the component. Maybe you tried to make an HTTPS request but PHP is not compiled with OpenSSL support, or the request returned an invalid JSON value: %s");

?>
