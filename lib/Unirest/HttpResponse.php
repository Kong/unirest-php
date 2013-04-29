<?php
class HttpResponse
{	
	private $code;
	private $raw_body;
	private $body;
	private $headers;

	/**
	 * HttpResponse constructor
	 * @param int $code     	Response code of the cURL request
	 * @param string $raw_body  The raw body of the cURL response
	 * @param string $headers   Raw header string from cURL response
	 */
	function __construct($code, $raw_body, $headers)
	{
		$this->code = $code;
		$this->headers = $this->get_headers_from_curl_response($headers);
		$this->raw_body = $raw_body;
		$this->body = $raw_body;
		$json = json_decode($raw_body);
		if (json_last_error() == JSON_ERROR_NONE) {
			$this->body = $json;
		}
	}

	/**
	 * Return a property of the response if it exists
	 * Possibilities include:
	 * - code
	 * - raw_body
	 * - body (if the response is json-decodable)
	 * - headers
	 * @param  [type] $property [description]
	 * @return [type]           [description]
	 */
	public function __get($property)
	{
		if (property_exists($this, $property)) {
			return $this->$property;
		}
	}

	/**
	 * Set the properties of this object
	 * @param string $property The property name
	 * @param mixed $value    The property value
	 */
	public function __set($property, $value)
	{
		if (property_exists($this, $property)) {
			$this->$property = $value;
		}
		return $this;
	}
	
	/**
	 * Retrieve the cURL response headers from the
	 * header string and convert it into an array
	 * @param  string $headers header string from cURL response
	 * @return array           headers in array form
	 */
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
