<?php

//定数を外部ファイルから読み込み
require_once('constant.php');
//composerのrequire
require_once("vendor/autoload.php");
//functionのrequire
require_once('function.php');
//facebookクラスのrequire
require_once('facebook_core.php');

//FacebookSDKの中から、使用するものを選択
use Facebook\FacebookSession;
use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookRequest;
use Facebook\FacebookRequestException;
use Facebook\GraphUser;
use Facebook\GraphLocation;
use Facebook\GraphSessionInfo;

FacebookSession::setDefaultApplication(APP_ID, APP_SECRET);
/*******************************************************************************************/
const AT = 'CAAGb4ZCZADf2wBANK4F8ovBmqgIteAnbwK4BvIzmH6aQLorL9zUIJ4rywDbnCvQlbiagzt8JRVRe1LiaJeh3hG7VRaI6201BZCUrZAqPrhpPxNigiDU0Ahxu87rxXKZCzhyaBay4RYE8OuAXS5SVC7Iw9x0wWZBeUbno6vx9FRdIcdOwyGxdnXJJCCkOe0cZBRkGZCiSfQ2ZCiPEZCwyfOIUDkVywfQKyGsZCgZD';
const ID = '853981858011604';
/*******************************************************************************************/

$session = FacebookCore::getSession(AT);
$fb = new FacebookCore($session, ID);
$res = $fb->getFeed();
$yes = $fb->getIcon(ID);
$result = $res[0];



print('<pre>');
var_dump($yes);
var_dump($result->id);
print('</pre>');
