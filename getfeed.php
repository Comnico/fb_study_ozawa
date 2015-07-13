<?php

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

//FacebookSDKの中から、使用するものを選択
use Facebook\FacebookSession;
use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookRequest;
use Facebook\FacebookRequestException;
use Facebook\GraphUser;
use Facebook\GraphLocation;
use Facebook\GraphSessionInfo;

//FacebookSessionの呼び出し
FacebookSession::setDefaultApplication(APP_ID, APP_SECRET);

/*******************************************************************************************/


    //全てのユーザーIDを出力
    $user_data = DbCore::userDump();

    //foreachで、出力した全てのユーザーごとに、
    //FeedをFacebookから取得し、DBへ書き込みを行う
    foreach($user_data as $u){

        //アクセストークンを利用してセッションを開始する
        $session = FacebookCore::getSession($u['access_token']);
        $user_id = $u['user_id'];

        //指定したユーザーIDのページを取得する
        $fb = new FacebookCore($session);
        $feed = $fb->getFeed($user_id);

        //フィードをDBへ保存
        DbCore::storageFeed($user_id, $feed);
        }

    print('done.');
?>
