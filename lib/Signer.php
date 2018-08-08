<?php
/**
 * Created by PhpStorm.
 * User: pablo-i
 * Date: 08/08/18
 * Time: 11:15
 */

namespace Sap\OAuth;

class Signer extends SapOAuth {

	public function signUrl($data, $uri, $method = "GET") {
		$signature = $this->getSignature();
		$signed = $signature->sign($uri, $data, $method);
		return $signed;
	}
}
