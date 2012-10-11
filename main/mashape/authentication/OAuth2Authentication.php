<?php
require_once(dirname(__FILE__) . "/OAuthAuthentication.php");

class OAuth2Authentication extends OAuthAuthentication {

	function __construct($consumerKey, $consumerSecret, $callbackUrl) {
		parent::__construct($consumerKey, $consumerSecret, $callbackUrl);
	}

	public function handleParams() {
		if ($this->accessToken == null) {
			throw new MashapeClientException(
				EXCEPTION_OAUTH2_AUTHORIZE, 
				EXCEPTION_OAUTH2_AUTHORIZE_CODE);
		}
		$params = array("accesstoken" => $this->accessToken);
		return $params;
	}
}
?>