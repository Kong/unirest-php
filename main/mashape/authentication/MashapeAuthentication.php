<?php
require_once(dirname(__FILE__) . "/AuthenticationUtil.php");
require_once(dirname(__FILE__) . "/HeaderAuthentication.php");

class MashapeAuthentication extends HeaderAuthentication {

	private $header;

	function __construct($publicKey, $privateKey) {
		$this->header = AuthenticationUtil::generateAuthenticationHeader($publicKey, $privateKey);
	}

	public function handleHeader() {
		return $this->header;
	}
}
?>

