<?php
session_start();
require_once '../../google-api-php-client-2.4.0/vendor/autoload.php';

$clientId = '650088888366-62bjkkspfv141i8rk0auhk2ocigm5kul.apps.googleusercontent.com'; // Replace with your client ID
$clientSecret = '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'; // Replace with your client secret
$redirectUri = 'http://localhost/projects/CampusCatalyst/templates/login/callback.php';

$client = new Google_Client();
$client->setClientId($clientId);
$client->setClientSecret($clientSecret);
$client->setRedirectUri($redirectUri);
$client->addScope("email");

// Redirect to Google's OAuth 2.0 server
$auth_url = $client->createAuthUrl();
header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
?>