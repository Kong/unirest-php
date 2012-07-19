<?php
require_once(dirname(__FILE__) . "/../http/AuthUtil.php");
require_once(dirname(__FILE__) . "/HeaderAuth.php");

class MashapeAuth extends HeaderAuth {

	private $header;

	function __construct($publicKey, $privateKey) {
		$this->header = AuthUtil::generateAuthenticationHeader($publicKey, $privateKey);
	}

	public function handleHeader() {
		return $this->header;
	}
}
?>

