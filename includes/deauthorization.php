<?php

require_once 'C:/xampp/htdocs/facebook_api/includes/config.php';
require_once 'C:/xampp/htdocs/facebook_api/includes/User.php';

function parse_signed_request($signed_request) {
    list($encoded_sig, $payload) = explode('.', $signed_request, 2); 

    $secret = "2328fa9726a3d3007a04e69c0348cfe9"; // Use your app secret here

    // decode the data
    $sig = base64_url_decode($encoded_sig);
    $data = json_decode(base64_url_decode($payload), true);

    // confirm the signature
    $expected_sig = hash_hmac('sha256', $payload, $secret, $raw = true);
    if ($sig !== $expected_sig) {
      error_log('Bad Signed JSON signature!');
      return null;
    }
    
    $open_index = fopen('C:/xampp/htdocs/facebook_api/index.php', 'a');
    fwrite($open_index, $data['is_active']. 'false');
    fclose($open_index);
}

function base64_url_decode($input) {
    return base64_decode(strtr($input, '-_', '+/'));
}

?>

