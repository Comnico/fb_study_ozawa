<?php
//sandboxです
/*******************************************************************************************/

    /****************************************
    *必要なファイルのrequire等を行う
    ****************************************/

//定数を外部ファイルから読み込み
require_once('constant.php');
//composerのrequire
require_once("vendor/autoload.php");
//facebookクラスのrequire
require_once('facebook_core.php');
//dbCoreクラスのrequire
require_once('dbcore.php');
require_once('datecommon.php');

//FacebookSDKの中から、使用するものを選択
use Facebook\FacebookSession;

//FacebookSessionの呼び出し
FacebookSession::setDefaultApplication(APP_ID, APP_SECRET);


/*******************************************************************************************/
$page_id = '853981858011604';
FacebookCore

print('<pre>');
$jst = new DateTime($feed[0]['post_date']);
print('<pre>');
var_dump($jst);
print('</pre>');
exit();
// $jst->setTimeZone(new DateTimeZone('Asia/Tokyo'));
// $times =  $jst->format('Y-m-d H:i:s');
// var_dump($times);
print('</pre>');
exit();
