<?php

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
