<?php
require_once(dirname(__FILE__) . "/HeaderAuthentication.php");

class CustomHeaderAuthentication extends HeaderAuthentication {

	private $headers;

	function __construct($headerName, $headerValue) {
		$this->headers = array($headerName . ": " . $headerValue);
	}

	public function handleHeaders() {
		return $this->headers;
	}

	public function handleParams() {
		return null;
	}
}
?>


