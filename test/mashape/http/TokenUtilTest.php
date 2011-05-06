<?php

require_once(MASHAPE_CLIENT_LIBRAY_PATH . "/http/TokenUtil.php");

class TokenUtilTest extends PHPUnit_Framework_TestCase {

	function testRequestToken() {
		try {
			TokenUtil::requestToken(null);
			$this->assertFalse(true);
		} catch (MashapeClientException $e) {
			$this->assertEquals(2001, $e->getCode());
		}

		try {
			TokenUtil::requestToken("");
			$this->assertFalse(true);
		} catch (MashapeClientException $e) {
			$this->assertEquals(2001, $e->getCode());
		}

		try {
			TokenUtil::requestToken("bla");
			$this->assertFalse(true);
		} catch (MashapeClientException $e) {
			$this->assertEquals(2001, $e->getCode());
		}
	}

}

?>