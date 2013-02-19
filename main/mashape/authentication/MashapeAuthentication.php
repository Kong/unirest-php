<?php
require_once(dirname(__FILE__) . "/AuthenticationUtil.php");
require_once(dirname(__FILE__) . "/HeaderAuthentication.php");

class MashapeAuthentication extends HeaderAuthentication {

	private $headers;

	function __construct($mashapeKey) {
		$this->headers = array(AuthenticationUtil::generateAuthenticationHeader($mashapeKey));
	}

	public function handleHeaders() {
		return $this->headers;
	}
}
?>