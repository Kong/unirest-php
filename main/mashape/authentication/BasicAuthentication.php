<?php
require_once(dirname(__FILE__) . "/HeaderAuthentication.php");

class BasicAuthentication extends HeaderAuthentication {

	private $headers;

	function __construct($username, $password) {
		$headerValue = $username . ":" . $password;
		$this->headers = array("Authorization: Basic " . base64_encode($headerValue));
	}

	public function handleHeaders() {
		return $this->headers;
	}

	public function handleParams() {
		return null;
	}
}
?>



