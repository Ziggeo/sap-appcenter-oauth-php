<?php

namespace Sap\OAuth;

use League\OAuth1\Client\Credentials\ClientCredentials;
use League\OAuth1\Client\Credentials\TokenCredentials;
use League\OAuth1\Client\Server\Server;
use League\OAuth1\Client\Server\User;
use League\OAuth1\Client\Signature\HmacSha1Signature;


class SapServer extends Server {

	const PATTERN = '/OAuth oauth_consumer_key=".*?", oauth_nonce=".*?", oauth_signature=".*?", oauth_signature_method="HMAC-SHA1", oauth_timestamp="\d{10}", oauth_version="1.0"/';

	public function urlTemporaryCredentials() {
		// TODO: Implement urlTemporaryCredentials() method.
	}

	public function urlAuthorization() {
		// TODO: Implement urlAuthorization() method.
	}

	public function urlTokenCredentials() {
		// TODO: Implement urlTokenCredentials() method.
	}

	public function urlUserDetails() {
		// TODO: Implement urlUserDetails() method.
	}

	public function userDetails( $data, TokenCredentials $tokenCredentials ) {
		// TODO: Implement userDetails() method.
	}

	public function userUid( $data, TokenCredentials $tokenCredentials ) {
		// TODO: Implement userUid() method.
	}

	public function userEmail( $data, TokenCredentials $tokenCredentials ) {
		// TODO: Implement userEmail() method.
	}

	public function userScreenName( $data, TokenCredentials $tokenCredentials ) {
		// TODO: Implement userScreenName() method.
	}

}