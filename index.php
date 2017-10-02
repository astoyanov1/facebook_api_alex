<?php

require_once 'C:/xampp/htdocs/facebook_api/includes/config.php';
require_once 'C:/xampp/htdocs/facebook_api/includes/user.php';
require_once 'C:/xampp/htdocs/facebook_api/includes/deauthorization.php';

if(isset($accessToken)){
    if(isset($_SESSION['facebook_access_token'])){
        $fb->setDefaultAccessToken($_SESSION['facebook_access_token']);
    } else{
        $_SESSION['facebook_access_token'] = (string) $accessToken;
        
        $oAuth2Client = $fb->getOAuth2Client();
        
        $longLivedAccessToken = $oAuth2Client->getLongLivedAccessToken($_SESSION['facebook_access_token']);
        $_SESSION['facebook_access_token'] = (string) $longLivedAccessToken;
        
        $fb->setDefaultAccessToken($_SESSION['facebook_access_token']);
    }
    
    if(isset($_GET['code'])){
        header('Location: ./');
    }
    
    try {
        $profileRequest = $fb->get('/me?fields=name,first_name,last_name,email,link,gender,locale,picture');
        $fbUserProfile = $profileRequest->getGraphNode()->asArray();
    } catch(FacebookResponseException $e) {
            echo 'Graph returned an error: ' . $e->getMessage();
            session_destroy();
            header("Location: ./");
        exit;
    } catch(FacebookSDKException $e) {
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
        exit;
    }
    
    $user = new User();
    
    $fbUserData = array(
        'oauth_provider'=> 'facebook',
        'oauth_uid'     => $fbUserProfile['id'],
        'token'         => $_SESSION['facebook_access_token'],
        'is_active'     => 'true',
        'first_name'    => $fbUserProfile['first_name'],
        'last_name'     => $fbUserProfile['last_name'],
        'email'         => $fbUserProfile['email'],
        'gender'        => $fbUserProfile['gender'],
        'locale'        => $fbUserProfile['locale'],
        'picture'       => $fbUserProfile['picture']['url'],
        'link'          => $fbUserProfile['link']
    );
    $userData = $user->checkUser($fbUserData);
    
    $_SESSION['userData'] = $userData;
    
    $logoutURL = $helper->getLogoutUrl($accessToken, $redirectURL.'logout.php');
    
    // facebook profile data
    if(!empty($userData)){
        $output  = '<h1>Facebook Profile </h1>';
        $output .= '<img src="'.$userData['picture'].'">';
        $output .= '<br/><br/>Facebook ID : ' . $userData['oauth_uid'];
        $output .= '<br/>Name : ' . $userData['first_name'].' '.$userData['last_name'];
        $output .= '<br/>Email : ' . $userData['email'];
        $output .= '<br/>Gender : ' . $userData['gender'];
        $output .= '<br/>Locale : ' . $userData['locale'];
        $output .= '<br/>Status : logged in facebook';
        $output .= '<br/><a href="'.$userData['link'].'" target="_blank">Visit your facebook page</a>';
        $output .= '<br/>You can logout from <a href="'.$logoutURL.'">HERE</a>'; 
    } else{
        $output = '<h3 style="color:red">Some problem occurred, please try again.</h3>';
    }
    
} else{
    $loginURL = $helper->getLoginUrl($redirectURL, $fbPermissions);
    $output = '<a href="'.htmlspecialchars($loginURL).'"><img src="img/fblogin-btn.png"></a>';
}
?>
<html>
    <head>
        <title>Facebook login page</title>
        <style type="text/css"> h1{font-family:Arial, Helvetica, sans-serif;color:#999999;} </style>
    </head>
    <body>
        <div style="">
            <?php echo $output; ?>
        </div>
    </body>
</html>
