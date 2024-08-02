<?php


$config =  [
    'authorizationRequestUrl' => 'https://appcenter.intuit.com/connect/oauth2',
    'tokenEndPointUrl' => 'https://oauth.platform.intuit.com/oauth2/v1/tokens/bearer',
    'client_id' => QUICKBOOKS_CLIENT_ID,
    'client_secret' => QUICKBOOKS_CLIENT_SECRET,
    'oauth_scope' => 'com.intuit.quickbooks.accounting openid profile email phone address',
    'oauth_redirect_uri' => QUICKBOOKS_REDIRECT_URI,
];



$dataService = DataService::Configure(array(
    'auth_mode' => 'oauth2',
    'ClientID' => $config['client_id'],
    'ClientSecret' =>  $config['client_secret'],
    'RedirectURI' => $config['oauth_redirect_uri'],
    'scope' => $config['oauth_scope'],
    'baseUrl' => "development"
));

$OAuth2LoginHelper = $dataService->getOAuth2LoginHelper();

// Get the Authorization URL from the SDK
$authUrl = $OAuth2LoginHelper->getAuthorizationCodeURL();



function processCode()
{

    // Create SDK instance
    $config = include('config.php');

    $dataService = DataService::Configure(array(
        'auth_mode' => 'oauth2',
        'ClientID' => $config['client_id'],
        'ClientSecret' =>  $config['client_secret'],
        'RedirectURI' => $config['oauth_redirect_uri'],
        'scope' => $config['oauth_scope'],
        'baseUrl' => "development"
    ));

    $OAuth2LoginHelper = $dataService->getOAuth2LoginHelper();
    $parseUrl = parseAuthRedirectUrl($_SERVER['QUERY_STRING']);

    /*
        * Update the OAuth2Token
        */
    $accessToken =    $OAuth2LoginHelper->exchangeAuthorizationCodeForToken($parseUrl['code'], $parseUrl['realmId']);
    $dataService->updateOAuth2Token($accessToken);

    /*
        * Setting the accessToken for session variable
        */
    $_SESSION['sessionAccessToken'] = $accessToken;
}
