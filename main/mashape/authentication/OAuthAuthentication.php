<?php
require_once(dirname(__FILE__) . "/Authentication.php");

class OAuthAuthentication implements Authentication {

	protected $consumerKey;
	protected $consumerSecret;
	protected $redirectUrl;
	protected $accessToken;
	protected $accessSecret;

	function __construct($consumerKey, $consumerSecret, $redirectUrl) {
		$this->consumerKey = $consumerKey;
		$this->consumerSecret = $consumerSecret;
		$this->redirectUrl = $redirectUrl;
	}

	public function addAccessToken($accessToken, $accessSecret = null) {
		$this->accessToken = $accessToken;
		$this->accessSecret = $accessSecret;
	}

    public function getOAuthBaseParams() {
		$params = array(
			"consumerKey" => $this->consumerKey,
			"consumerSecret" => $this->consumerSecret,
			"redirectUrl" => $this->redirectUrl,
		);
        return $params;
    }

	public function handleParams() {
		return null;
	}

	public function handleHeaders() {
		return null;
	}
}
?>