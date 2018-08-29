<?php

namespace Sap\OAuth;

class Signer extends SapServer {

	public function signUrl($uri, $params, $method) {
		$signature = $this->getSignature();
		$signed = $signature->sign($uri, $params, $method);
		return $signed;
	}

	public function signRequest($uri, $method = "GET") {
		$params = $this->baseProtocolParameters();
		$params["oauth_signature"] = $this->signUrl($uri, $params, $method);
		return $params;
	}

}
