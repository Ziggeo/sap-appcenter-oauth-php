<?php
/**
 * Created by PhpStorm.
 * User: pablo-i
 * Date: 08/08/18
 * Time: 16:57
 */

namespace Sap\OAuth;

use League\OAuth1\Client\Credentials\ClientCredentials;
use League\OAuth1\Client\Signature\HmacSha1Signature;


abstract class SapOAuth {
	private $identifier;
	private $secret;
	private $credentials;


	function __construct($identifier, $secret) {
		$this->identifier = $identifier;
		$this->secret = $secret;
		$credentials = new ClientCredentials();
		$credentials->setIdentifier($this->identifier);
		$credentials->setSecret($this->secret);
		$this->credentials = $credentials;
	}

	public function getSignature() {
		return new HmacSha1Signature($this->credentials);
	}
}