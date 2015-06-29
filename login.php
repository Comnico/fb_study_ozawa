<?php
require_once("vendor/autoload.php");
// Make sure to load the Facebook SDK for PHP via composer or manually
use Facebook\FacebookSession;
use Facebook\FacebookRedirectLoginHelper;

session_start();


FacebookSession::setDefaultApplication('452878368210796', 'f0e25cc2d8ce6f0a8e2e80bf35c64081');

$helper = new FacebookRedirectLoginHelper('http://fb-study-ozawa.herokuapp.com/login.php');

try {
 $session = $helper->getSessionFromRedirect();
 // When Facebook returns an error
} catch( Exception $ex ) {
 // When validation fails or other local issues
}

//セッションを保持している場合は、main.phpに飛ぶ。それ以外(初回)は、facebookへリクエストを送る
if ( isset( $session ) ) {
print_r($session);
//セッション情報と、ログアウトURLをmain.phpに渡す
//$_SESSION['session'] = $session;
//$_SESSION['logout_url'] = $helper->getLogoutUrl($session, 'http://fb-study-ozawa.herokuapp.com/index.php');

//リダイレクト
header('location: main.php');
exit();

} else {

//permissionを追加したい場合は、$scopeに追記する。
$scope = array('user_posts');
$login_url = $helper->getLoginUrl($scope);

//リダイレクト
header("location: ${login_url}");
exit();
}
