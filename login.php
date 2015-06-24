<?php
session_start();
require_once("vendor/autoload.php");
// Make sure to load the Facebook SDK for PHP via composer or manually

use Facebook\FacebookSession;
use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookRequest;
use Facebook\GraphUser;
use Facebook\GraphLocation;
use Facebook\GraphSessionInfo;
use Facebook\FacebookRequestException;


FacebookSession::setDefaultApplication('452878368210796', 'f0e25cc2d8ce6f0a8e2e80bf35c64081');

$helper = new FacebookRedirectLoginHelper('http://localhost/fb_study_ozawa/login.php');

try {
 $session = $helper->getSessionFromRedirect();
} catch( FacebookRequestException $ex ) {
 // When Facebook returns an error
} catch( Exception $ex ) {
 // When validation fails or other local issues
}

// see if we have a session
if ( isset( $session ) ) {
 // // graph api request for user data
 // $request = new FacebookRequest( $session, 'GET', '/me' );
 // $response = $request->execute();
 // // get response
 // $graphObject = $response->getGraphObject();
 //
 // // print data
 // echo  print_r( $graphObject, 1 );


 print('<br/><br/>---------------------------------------------------------------------------------<br/><br/>');

 $me = (new FacebookRequest($session, 'GET', '/me'));
 $responseMe = $me->execute();
 $graphObjectMe = $responseMe->getGraphObject();

print('<pre>');
var_dump($graphObjectMe);
print('</pre>');


} else {
 // show login url
 echo '<a href="' . $helper->getLoginUrl() . '">Login</a>';
}
