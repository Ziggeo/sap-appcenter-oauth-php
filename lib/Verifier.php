<?php
/**
 * Created by PhpStorm.
 * User: pablo-i
 * Date: 08/08/18
 * Time: 11:15
 */

namespace Sap\OAuth;

class Verifier extends SapOAuth {

	public function verifyUrl($oauth_signed, $request, $uri, $method = "GET") {
		//Remove signed value to verify base request
		$key = array_search($oauth_signed, $request);
		if ($key !== false)
			unset($request[$key]);
		$signature = $this->getSignature();
		$signed = $signature->sign($uri, $request, $method);
		return hash_equals($oauth_signed, $signed);
	}
}
