<?php
require_once(dirname(__FILE__) . "/Auth.php");

abstract class HeaderAuth implements Auth {
	public function handleParams() {
		return null;
	}
}
?>
