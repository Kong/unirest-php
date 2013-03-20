<?php

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

