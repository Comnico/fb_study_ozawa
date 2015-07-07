<?php

/*******************************************************************************************/

    /****************************************
    *必要なファイルのrequire等を行う
    ****************************************/

//定数を外部ファイルから読み込み
require_once('constant.php');
//composerのrequire
require_once("vendor/autoload.php");
//functionのrequire
require_once('function.php');

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
    $user_data = userDumpFromDB();

    //foreachで、出力した全てのユーザーごとに、
    //FeedをFacebookから取得し、DBへ書き込みを行う
    foreach($user_data as $u){

        //アクセストークンを利用してセッションを開始する
        $session = new FacebookSession($u['access_token']);
        $user_id = $u['user_id'];

        // print('<pre>');
        // var_dump(checkUpdate($session, $user_id));
        // print('</pre>');

        // //指定したユーザーIDのページを取得する
        $feed = getFeedFromFacebook($session, $user_id);
        //DBへ保存
        storageFeedToDb($user_id, $feed);
        }
    print('done.');
?>
