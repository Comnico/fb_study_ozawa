<?php
require_once("vendor/autoload.php");
// Make sure to load the Facebook SDK for PHP via composer or manually
use Facebook\FacebookSession;
use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookRequest;
use Facebook\GraphUser;
use Facebook\FacebookRequestException;

session_start();


FacebookSession::setDefaultApplication('452878368210796', 'f0e25cc2d8ce6f0a8e2e80bf35c64081');

$helper = new FacebookRedirectLoginHelper('http://localhost/fb_study_ozawa/login.php');

try {
 $session = $helper->getSessionFromRedirect();
} catch( FacebookRequestException $ex ) {
 // When Facebook returns an error
} catch( Exception $ex ) {
 // When validation fails or other local issues
}

//セッションを保持している場合は、main.phpに飛ぶ。それ以外(初回)は、facebookへリクエストを送る
if ( isset( $session ) ) {

$_SESSION['session'] = $session;
header('location: main.php');
exit();

} else {
 // show login url
 echo '<a href="' . $helper->getLoginUrl() . '">Login</a>';
 //header("location: $helper->getLoginUrl()");
 //exit();
}
