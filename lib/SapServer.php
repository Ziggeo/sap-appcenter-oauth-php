<?php

namespace Sap\OAuth;

use GuzzleHttp\Exception\BadResponseException;
use League\OAuth1\Client\Credentials\ClientCredentialsInterface;
use League\OAuth1\Client\Credentials\ClientCredentials;
use League\OAuth1\Client\Credentials\CredentialsInterface;
use League\OAuth1\Client\Credentials\TokenCredentials;
use League\OAuth1\Client\Server\Server;
use League\OAuth1\Client\Server\User;
use League\OAuth1\Client\Signature\HmacSha1Signature;


class SapServer extends Server {

	const PATTERN = '/OAuth oauth_consumer_key=".*?", oauth_nonce=".*?", oauth_signature=".*?", oauth_signature_method="HMAC-SHA1", oauth_timestamp="\d{10}", oauth_version="1.0"/';
	const METERED_USAGE_URL_DEV = 'https://nextgen.sapappcenter.com/api/integration/v1/billing/usage';
	const METERED_USAGE_URL = 'https://www.sapappcenter.com/api/integration/v1/billing/usage';

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

	public function getEventData($url, $type = "json") {
		$client = $this->createHttpClient();

		$headers = $this->getHeaders($this->getClientCredentials(), 'GET', $url);
		try {
			$response = $client->get($url, [
				'headers' => $headers,
			]);
		} catch (BadResponseException $e) {
			$response = $e->getResponse();
			$body = $response->getBody();
			$statusCode = $response->getStatusCode();

			throw new \Exception(
				"Received error [$body] with status code [$statusCode] when retrieving token credentials."
			);
		}

		switch ($type) {
			case 'json':
				$resp = json_decode((string) $response->getBody(), true);
				break;

			case 'xml':
				$resp = simplexml_load_string((string) $response->getBody());
				break;
			default:
				throw new \InvalidArgumentException("Invalid response type [{$this->responseType}].");
		}

		return $resp;
	}

	public function sendMeteredUsage($data, $type = "json", $dev = FALSE) {
        $client = $this->createHttpClient();

        $url = (!@$dev) ? self::METERED_USAGE_URL : self::METERED_USAGE_URL_DEV;

        $headers = $this->getHeaders($this->getClientCredentials(), 'POST', $url);

        try {
            $response = $client->post($url, [
                'headers' => $headers,
                'json' => $data
            ]);
        } catch (BadResponseException $e) {
            $response = $e->getResponse();
            $body = $response->getBody();
            $statusCode = $response->getStatusCode();

            throw new \Exception(
                "Received error [$body] with status code [$statusCode] when retrieving token credentials."
            );
        }

        switch ($type) {
            case 'json':
                $resp = json_decode((string) $response->getBody(), true);
                break;

            case 'xml':
                $resp = simplexml_load_string((string) $response->getBody());
                break;
            default:
                throw new \InvalidArgumentException("Invalid response type [{$this->responseType}].");
        }

        return $resp;
    }
	/**
	 * Generate the OAuth protocol header for requests other than temporary
	 * credentials, based on the URI, method, given credentials & body query
	 * string.
	 *
	 * @param string               $method
	 * @param string               $uri
	 * @param CredentialsInterface $credentials
	 * @param array                $bodyParameters
	 *
	 * @return string
	 */
	protected function protocolHeader($method, $uri, CredentialsInterface $credentials, array $bodyParameters = array())
	{
		$parameters = array_merge(
			$this->baseProtocolParameters(),
			$this->additionalProtocolParameters()
		);

		//$this->signature->setCredentials($credentials);

		$parameters['oauth_signature'] = $this->signature->sign(
			$uri,
			array_merge($parameters, $bodyParameters),
			$method
		);

		return $this->normalizeProtocolParameters($parameters);
	}

	/**
	 * Takes an array of protocol parameters and normalizes them
	 * to be used as a HTTP header.
	 *
	 * @param array $parameters
	 *
	 * @return string
	 */
	protected function normalizeProtocolParameters(array $parameters)
	{
		array_walk($parameters, function (&$value, $key) {
			$value = rawurlencode($key).'="'.rawurlencode($value).'"';
		});

		return 'OAuth '.implode(',', $parameters);
	}
}