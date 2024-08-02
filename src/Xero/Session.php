<?php
namespace NDISmate\Xero;

use \NDISmate\CORE\KeyValue;


class Session {

	var $provider;
    var $xero_session;
	var $config;
    var $options;
	
	
	function __construct() {
		
		$config = [
			'clientId'                => XERO_CLIENT_ID,   
			'clientSecret'            => XERO_CLIENT_SECRET,
			'redirectUri'             => XERO_REDIRECT_URL,
			'urlAuthorize'            => 'https://login.xero.com/identity/connect/authorize',
			'urlAccessToken'          => 'https://identity.xero.com/connect/token',
			'urlResourceOwnerDetails' => 'https://api.xero.com/api.xro/2.0/Organisation'
		];

		// 'scope' => ['openid email profile offline_access accounting.settings accounting.transactions accounting.contacts accounting.journals.read accounting.reports.read accounting.attachments']
		$scope =['offline_access accounting.transactions accounting.contacts accounting.settings payroll.employees payroll.payruns payroll.payslip payroll.timesheets payroll.settings'];
		
		$this->provider = new \League\OAuth2\Client\Provider\GenericProvider($config);
	
		$this->options = [
			'scope' => $scope
		];

		// If we are connecting this account, we can exit here because we don't have an existing connection to reestablish.
		// if(isset($data['connect'])&&$data['connect']==true) return;

		$this->getSession();

		if ($this->hasExpired()) {

			if(empty($this->xero_session['refresh_token'])){
				return ["http_code"=>400, "error_message"=>"not connected"];
			}
		
			$this->provider = new \League\OAuth2\Client\Provider\GenericProvider($config);
	
			// Scope defines the data your app has permission to access.
			// Learn more about scopes at https://developer.xero.com/documentation/oauth2/scopes
			$this->options = [
				'scope' => $scope
			];		

			try {	
				$newAccessToken = $this->provider->getAccessToken('refresh_token', [
					'refresh_token' => $this->getRefreshToken()
				]);
			
				// Save my token, expiration and refresh token
				$this->setToken(
					$newAccessToken->getToken(),
					$newAccessToken->getRefreshToken(),
					$newAccessToken->getExpires()
				);
			} catch(\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
				// Failed to get the access token or user details.
				return ["http_code"=>400, "error_message"=>$e->getMessage()];
			}

		}
		
   	}


	function checkConnected(){
		if(!empty($this->xero_session['refresh_token'])) return true;
		return false;
	}


	function testConnection(){		
		try {
			$config = \XeroAPI\XeroPHP\Configuration::getDefaultConfiguration()->setAccessToken( (string)$this->getSession()['token'] );	
			$identityApi = new \XeroAPI\XeroPHP\Api\IdentityApi(
				new \GuzzleHttp\Client(),
				$config
			);  
			$connections = $identityApi->getConnections();
			// Connection is successful
			return ["http_code"=>200];
		} catch (\Exception $e) {
			// An error occurred
			return ["http_code"=>403, "error_message"=>$e->getMessage()];
		}
	}

/**
 * Gets an authorization url that is used for a client to connect to the app using oAuth2
 */
	function connect(){

		$this->getSession();
      
        // This returns the authorizeUrl with necessary parameters applied (e.g. state).
        $authorizationUrl = $this->provider->getAuthorizationUrl($this->options);
      
        // Save the state generated for you and store it to the session.
        // For security, on callback we compare the saved state with the one returned to ensure they match.
		$this->setState($this->provider->getState());
      
		return ["http_code"=>200, "authorizationUrl"=>$authorizationUrl];
      
    }



/**
 * Disconnects a client from the app
 */
	function disconnect(){
		try {
			// delete the session locally - even if the rest of this fails, we aren't able to connect so this is fine.
			$this->deleteSession(); 

			$config = \XeroAPI\XeroPHP\Configuration::getDefaultConfiguration()->setAccessToken( (string)$this->getSession()['token'] );	
			
			$identityApi = new \XeroAPI\XeroPHP\Api\IdentityApi(
				new \GuzzleHttp\Client(),
				$config
			);  
			$connections = $identityApi->getConnections();
			
			// TODO:  There is an issue where if xero resets the Demo Company (which they do every so often), the connection will be lost and the user will not be able to reconnect.  This is because the connection is still in the database, but the company is no longer there.  The solution is to delete the connection from the database if the company is not found.    
			
			// print_r($connections);
			// return ["http_code"=>200, "result"=>$connections];
			
			if(!empty($connections)){
				$id = $connections[0]->getId();
				$result = $identityApi->deleteConnection($id);
			}
			
			return ["http_code"=>200, "result"=>"Connection Deleted"];
		} catch (\Exception $e) {
			// An error occurred
			return ["http_code"=>200, "result"=>"Connection Deleted"];
		}

	}





    function authenticate($data){
		$this->getSession();

        // If we don't have an authorization code then get one
        if (!isset($data['code'])) {
			return ["http_code"=>400, "error_message"=>"Something went wrong, no authorization code found"];
            
        // Check given state against previously stored one to mitigate CSRF attack
        } elseif (empty($data['state']) || ($data['state'] !== $this->getState())) {
			$this->setState(null);
			return ["http_code"=>400, "error_message"=>"Invalid state"];
            
        } else {
        
            try {
                // Try to get an access token using the authorization code grant.
                $accessToken = $this->provider->getAccessToken('authorization_code', [
                    'code' => $data['code']
                ]);
                    
                $config = \XeroAPI\XeroPHP\Configuration::getDefaultConfiguration()->setAccessToken( (string)$accessToken->getToken() );
                $identityApi = new \XeroAPI\XeroPHP\Api\IdentityApi(
                    new \GuzzleHttp\Client(),
                    $config
                );
                
                $result = $identityApi->getConnections();

                // Save my tokens, expiration
                $this->setToken(
                    $accessToken->getToken(),
                    $accessToken->getRefreshToken(),
					$accessToken->getExpires()
                );

				return ["http_code"=>200, "result"=>$result];
                            
            } catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
				return ["http_code"=>400, "error_message"=>"Callback failed"];
            }
        }

    }


    public function getSession() {
		$xero_session = KeyValue::get('xero_session');
		if(!empty($xero_session)) $this->xero_session = json_decode($xero_session, true);
    	return $this->xero_session;
    }


	public function setToken($token, $refreshToken, $expires = null){    
		$this->xero_session['token'] = $token;
		$this->xero_session['refresh_token'] = $refreshToken;
		$this->xero_session['expires'] = $expires;
		$this->saveSession();		
	}


	function saveSession(){
		KeyValue::set('xero_session', json_encode($this->xero_session));		
	}


	function deleteSession(){
		KeyValue::delete('xero_session');
		$this->xero_session = null;
	}


	function setState($oauth2state){
		$this->xero_session['oauth2state'] = $oauth2state;
		$this->saveSession();
	}


	public function getState() {
        return $this->xero_session['oauth2state'];
	}


	public function getToken() {
        //If it doesn't exist or is expired, return null
        if (
            empty($this->getSession())
            || (
                $this->xero_session['expires'] !== null
                && $this->xero_session['expires'] <= time()
            )
        ) {
            return null;
        }
        return $this->getSession();
	}

	
	public function getAccessToken() {
        return $this->xero_session['token'];
	}


	public function getRefreshToken() {
		if(isset($this->xero_session['refresh_token']))
        return $this->xero_session['refresh_token'];
	}


	public function getExpires() {
        return $this->xero_session['expires'];
    }


	public function hasExpired()	{
		if (!empty($this->xero_session['expires'])) {
			if(time() > $this->xero_session['expires']){
				return true;
			} else {
				return false;
			}
		} else {
			return true;
		}
	}


}