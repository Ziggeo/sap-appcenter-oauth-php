<?php

namespace Sap\OAuth;

class Verifier extends SapServer {

	public function verifyUrl($oauth_sign, $request, $uri, $method = "GET") {
		//Remove signed value to verify base request
		$key = array_search($oauth_sign, $request);
		if ($key !== false)
			unset($request[$key]);
		$signature = $this->getSignature();
		$signed = $signature->sign($uri, $request, $method);
		return hash_equals($oauth_sign, $signed);
	}

	public function verifyCall($headers, $uri, $method = "GET") {
		$matches = $this->verifyAndGetHeaders($headers["Authorization"]);
		if (!count($matches))
			return FALSE;
		return $this->verifyUrl($matches["oauth_signature"], $matches, $uri, $method);
	}
	
	private function verifyAndGetHeaders($headers) {
		$matches = preg_match(parent::PATTERN, urldecode($headers));
		$headers_parsed = array();
		if ($matches) {
			$striped = preg_replace("/OAuth /", "", $headers);
			$parsed = preg_split("/, /", $striped);
			foreach ($parsed as $header) {
				$split = preg_split("/=/", $header);
				$headers_parsed[$split[0]] = preg_replace("/\"/", "", urldecode($split[1]));
			}
		}
		return $headers_parsed;
	}
}
