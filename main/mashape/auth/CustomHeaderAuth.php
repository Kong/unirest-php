<?php
require_once(dirname(__FILE__) . "/HeaderAuth.php");

class CustomHeaderAuth extends HeaderAuth {

	private $header;

	function __construct($headerName, $headerValue) {
		$this->header = $headerName . ": " . $headerValue;
	}

	public function handleHeader() {
		return $this->header;
	}

	public function handleParams() {
		return null;
	}
}
?>


