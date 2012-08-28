<?php
require_once(dirname(__FILE__) . "/HeaderAuthentication.php");

class BasicAuthentication extends HeaderAuthentication {

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



