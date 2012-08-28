<?php
require_once(dirname(__FILE__) . "/Authentication.php");

class QueryAuthentication implements Authentication {

	private $params;

	function __construct($queryKey, $queryValue) {
		$this->params = array($queryKey => $queryValue);
	}

	public function handleHeader() {
		return null;
	}

	public function handleParams() {
		return $this->params;
	}
}
?>

