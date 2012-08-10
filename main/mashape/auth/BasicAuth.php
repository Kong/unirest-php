<?php
require_once(dirname(__FILE__) . "/HeaderAuth.php");

class BasicAuth extends HeaderAuth {

	private $header;

	function __construct($username, $password) {
		$headerValue = $username . ":" . $password;
		$this->header = "Authorization: Basic " . base64_encode($headerValue);
	}

	public function handleHeader() {
		return $this->header;
	}

	public function handleParams() {
		return null;
	}
}
?>



