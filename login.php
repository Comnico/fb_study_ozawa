<?php
/**
*login.php
*Facebookへのログインと、セッション情報、アクセストークンの取得と、
*トークンのDB保存とリダイレクトを行うファイル
*/
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

//セッションのスタート
session_start();

//FacebookSessionの呼び出し
FacebookSession::setDefaultApplication(APP_ID, APP_SECRET);

/*******************************************************************************************/

//リダイレクト先のURLを指定し、インスタンスを生成
$helper = new FacebookRedirectLoginHelper(REDIRECT_URL);

//セッション情報を取得
try {
        $session = $helper->getSessionFromRedirect();
} catch (Exception $ex) {
    die($ex->getMessage);
}

//セッションを保持しているかどうか確認する。
if (isset($session)) {
    //セッション情報から、トークンとユーザーIDを入手
    $access_token = $session->getToken();
    $session_info = $session->getSessionInfo()->asArray();
    $user_id = $session_info['user_id'];

    //新規ユーザーか既存ユーザーか確認。
    //新規の場合は、トークンをDBへ保存し、
    //FBからフィードを取得する。
    //既存ユーザーの場合、トークンをアップデートする。

    //新規ユーザーの場合
    if (DbCore::isNew($user_id) == true) {
        //トークンの保存
        DbCore::storageToken($user_id, $access_token);

        //Feedの取得
        $fb = new FacebookCore($session);
        $feed = $fb->outputFeed($user_id);

        //DBへの保存
        DbCore::storageFeed($user_id, $feed);

    //既存ユーザーの場合、
    //トークンをアップデートする
    } elseif (DbCore::isNew($user_id) == false) {
        DbCore::updateToken($user_id, $access_token);
    }

    //セッション情報と、ログインしたユーザーID、ログアウトURLを
    //$_SESSIONに格納して、main.phpに渡す
    $_SESSION['session'] = $session;
    $_SESSION['user_id'] = $user_id;
    $_SESSION['logout_url'] = $helper->getLogoutUrl($session, AFTER_LOGOUT_URL);

    //main.phpへリダイレクト
    header('location: main.php');
    exit();

    //以下、セッション情報を持っていない場合
} else {
    //permissionを追加したい場合は、$scopeに追記する。
    $scope = array('user_posts');
    $login_url = $helper->getLoginUrl($scope);

    //リダイレクト
    header("location: ${login_url}");
    exit();
}
