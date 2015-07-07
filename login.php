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

//セッションのスタート
session_start();

//FacebookSessionの呼び出し
FacebookSession::setDefaultApplication(APP_ID, APP_SECRET);

/*******************************************************************************************/

    /****************************************
    *Facebookからセッション情報を入手する
    ****************************************/

//リダイレクト先のURLを指定し、インスタンスを生成
$helper = new FacebookRedirectLoginHelper(REDIRECT_URL);

//セッション情報を取得
try {
    $session = $helper->getSessionFromRedirect();

 // Facebookがエラーを返した場合に、例外をcatchする
} catch (Exception $ex) {
    die($ex->getMessage);
}

//セッションを保持している場合は、main.phpに飛ぶ。
//それ以外は、facebookへリクエストを送る
if (isset($session)) {

    //ログインしたユーザーのFBユーザーIDとアクセストークンをDBに保管する

    //ユーザーIDとトークンを入手
    $access_token = $session->getToken();

    $session_info = $session->getSessionInfo()->asArray();
    $user_id = $session_info['user_id'];

    //新規ユーザーか既存ユーザーか確認。新規の場合は、DBへの保存と、
    //FBからフィードをプッシュでとってくる


    if (checkNewOrNot($user_id) == 'new') {

        //IDとトークンの保存
        storageToken($user_id, $access_token);
        //Feedの取得と、DBへ保存
        $feed = getFeedFromFacebook($session, $user_id);
        storageFeedToDb($user_id, $feed);

    } else {

    }

    //セッション情報と、ログアウトURLをmain.phpに渡す
    $_SESSION['session'] = $session;
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
