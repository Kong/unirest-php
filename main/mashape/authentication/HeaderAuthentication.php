<?php
require_once(dirname(__FILE__) . "/Authentication.php");

abstract class HeaderAuthentication implements Authentication {
	public function handleParams() {
		return null;
	}
}
?>