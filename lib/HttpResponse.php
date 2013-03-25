<?php
/*
The MIT License

Copyright (c) 2013 Mashape (http://mashape.com)

Permission is hereby granted, free of charge, to any person obtaining
a copy of this software and associated documentation files (the
"Software"), to deal in the Software without restriction, including
without limitation the rights to use, copy, modify, merge, publish,
distribute, sublicense, and/or sell copies of the Software, and to
permit persons to whom the Software is furnished to do so, subject to
the following conditions:

The above copyright notice and this permission notice shall be
included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/

class HttpResponse {
	
	private $code;
	private $raw_body;
	private $body;
	private $headers;

	function __construct($code, $raw_body, $headers) {
		$this->code = $code;
		$this->headers = $this->get_headers_from_curl_response($headers);
		$this->raw_body = $raw_body;
		$this->body = $raw_body;
		$json = json_decode($raw_body);
		if (json_last_error() == JSON_ERROR_NONE) {
			$this->body = $json;
		}
	}

	public function __get($property) {
		if (property_exists($this, $property)) {
			return $this->$property;
		}
	}

	public function __set($property, $value) {
		if (property_exists($this, $property)) {
			$this->$property = $value;
		}
		return $this;
	}
	
	private function get_headers_from_curl_response($headers)
	{
			foreach (explode("\r\n", $headers) as $i => $line) {
			if ($i !== 0) {
				if (!empty($key) && substr($key, 0, 4) != "HTTP") {
					$result[$key] = $value;
				} else {
					list ($key, $value) = explode(': ', $line);
				}
			}	
		}
			return $result;
	}
}

?>

