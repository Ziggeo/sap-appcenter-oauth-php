<?php

namespace Sap\OAuth;

use Auth_OpenID_DumbStore;
use Auth_OpenID_Consumer;
use Auth_OpenID_AX_AttrInfo;
use Auth_OpenID_AX_FetchRequest;
use Auth_OpenID_AX_FetchResponse;
use Auth_OpenID_SRegRequest;
use Auth_OpenID_SRegResponse;
use Exception;
use Auth_OpenID;


class SapSSO {

    private $store;
    private $return_to;
    private $trust_root;

    function __construct($store_secret, $return_to, $trust_root = "") {
        $this->store = new Auth_OpenID_DumbStore($store_secret);
        $this->return_to = $return_to;
        $this->trust_root = $trust_root;
    }

    public function initialize_sso($openid, $account_id = NULL) {
        $consumer = new Auth_OpenID_Consumer($this->store);
        // Begin the OpenID authentication process.
        $auth_request = $consumer->begin($openid);

        // No auth request means we can't begin OpenID.
        if (!$auth_request) {
            throw new Exception("Authentication error; not a valid OpenID.");
        }

        $sreg_request = Auth_OpenID_SRegRequest::build(
        // Required
            array(),
            // Optional
            array('fullname', 'email'));

        if ($sreg_request) {
            $auth_request->addExtension($sreg_request);
        }

        // Create attribute request object

        $attribute[] = Auth_OpenID_AX_AttrInfo::make('http://axschema.org/namePerson/first',1,0);
        $attribute[] = Auth_OpenID_AX_AttrInfo::make('http://axschema.org/namePerson/last',1,0);
        $attribute[] = Auth_OpenID_AX_AttrInfo::make('http://nextgen.sapappcenter.com/schema/company/uuid',1,0);

// Create AX fetch request
        $ax = new Auth_OpenID_AX_FetchRequest;

// Add attributes to AX fetch request
        foreach($attribute as $attr){
            $ax->add($attr);
        }

// Add AX fetch request to authentication request
        $auth_request->addExtension($ax);

        // Redirect the user to the OpenID server for authentication.
        // Store the token for this authentication so we can verify the
        // response.

        // For OpenID 2, use a Javascrip form to send a POST request to the server.

        // Generate form markup and render it.
        $form_id = 'openid_message';
        $form_html = $auth_request->htmlMarkup(self::getTrustRoot(), $this->return_to,
            false, array('id' => $form_id));

        // Display an error if the form markup couldn't be generated;
        // otherwise, render the HTML.
        if (Auth_OpenID::isFailure($form_html)) {
            displayError("Could not redirect to server: " . $form_html->message);
        } else {
            print $form_html;
        }
    }

    public function complete_sso($account_id = "") {
        $consumer = new Auth_OpenID_Consumer($this->store);
        $return_to = $this->return_to;
        $url_parts = parse_url($_SERVER['REQUEST_URI']);
        $response = $consumer->complete($return_to, Auth_OpenID::params_from_string($url_parts["query"]));

        // Check the response status.
        if ($response->status == Auth_OpenID_CANCEL) {
            // This means the authentication was cancelled.
            $msg = 'Verification cancelled.';
        } else if ($response->status == Auth_OpenID_FAILURE) {
            // Authentication failed; display the error message.
            $msg = "OpenID authentication failed: " . $response->message;
        } else if ($response->status == Auth_OpenID_SUCCESS) {
            // This means the authentication succeeded; extract the
            // identity URL and Simple Registration data (if it was
            // returned).
            $openid = $response->getDisplayIdentifier();
            $esc_identity = htmlentities($openid);

            $success = sprintf('You have successfully verified ' .
                '<a href="%s">%s</a> as your identity.',
                $esc_identity, $esc_identity);

            $sreg_resp = Auth_OpenID_SRegResponse::fromSuccessResponse($response);

            $sreg = $sreg_resp->contents();

            if (@$sreg['email']) {
                $success .= "  You also returned '".htmlentities($sreg['email']).
                    "' as your email.";
            }

            if (@$sreg['nickname']) {
                $success .= "  Your nickname is '".htmlentities($sreg['nickname']).
                    "'.";
            }

            if (@$sreg['fullname']) {
                $success .= "  Your fullname is '".htmlentities($sreg['fullname']).
                    "'.";
            }
            //TODO END THIS
            $ax = new Auth_OpenID_AX_FetchResponse();
            $obj = $ax->fromSuccessResponse($response);
        }
    }
}
