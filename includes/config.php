<?php

if(!session_id()){
    session_start();
}

// Include facebook sdk
require_once 'C:/xampp/htdocs/facebook_api/vendor/autoload.php';

// Include libraries
use Facebook\Facebook;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;

/*
 * Setting up the facebook sdk
 */

$appId         = '1917859411816130';
$appSecret     = '2328fa9726a3d3007a04e69c0348cfe9'; 
$redirectURL   = 'http://localhost/facebook_api/';
$fbPermissions = array('email'); 

$fb = new Facebook(array(
    'app_id' => $appId,
    'app_secret' => $appSecret,
    'default_graph_version' => 'v2.10',
));

// Get redirect login helper
$helper = $fb->getRedirectLoginHelper();

// Try to get access token
try {
    if(isset($_SESSION['facebook_access_token'])){
            $accessToken = $_SESSION['facebook_access_token'];
    } else{
            $accessToken = $helper->getAccessToken();
    }
} catch(FacebookResponseException $e) {
        echo 'Graph returned an error: ' . $e->getMessage();
      exit;
} catch(FacebookSDKException $e) {
        echo 'Facebook SDK returned an error: ' . $e->getMessage();
      exit;
}

?>
