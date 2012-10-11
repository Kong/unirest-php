<?php
require_once(dirname(__FILE__) . "/OAuthAuthentication.php");

class OAuth10aAuthentication extends OAuthAuthentication {

	function __construct($consumerKey, $consumerSecret, $callbackUrl) {
		parent::__construct($consumerKey, $consumerSecret, $callbackUrl);
	}

	public function handleHeaders() {
		if (!isset($this->accessToken) || !isset($this->accessSecret)) {
			throw new MashapeClientException(
				EXCEPTION_OAUTH1_AUTHORIZE, 
				EXCEPTION_OAUTH1_AUTHORIZE_CODE);
		}
		$headers = array();
		$headers[] = "x-mashape-oauth-consumerkey: " . $this->consumerKey;
		$headers[] = "x-mashape-oauth-consumersecret: " . $this->consumerSecret;
		$headers[] = "x-mashape-oauth-accesstoken: " . $this->accessToken;
		$headers[] = "x-mashape-oauth-accesssecret: " . $this->accessSecret;
		return $headers;
	}
}
?>