<?php

require_once(dirname(__FILE__) . "/../exceptions/MashapeClientException.php");
require_once(dirname(__FILE__) . "/HttpMethod.php");
require_once(dirname(__FILE__) . "/HttpClient.php");

define("TOKEN_URL", "https://api.mashape.com/requestToken?devkey={devkey}");

class TokenUtil {

	public static function requestToken($developerKey) {
		$parameters = array("devkey"=>$developerKey);

		$response = HttpClient::doRequest(HttpMethod::POST, TOKEN_URL, $parameters, null);

		if (empty($response->errors)) {
			$token = $response->token;
			return $token;
		} else {
			throw new MashapeClientException($response->errors[0]->message, $response->errors[0]->code);
		}
	}

}

?>