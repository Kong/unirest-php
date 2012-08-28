<?php
require_once(dirname(__FILE__) . "/HeaderAuthentication.php");

class CustomHeaderAuthentication extends HeaderAuthentication {

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


