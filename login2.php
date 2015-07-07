<?php

/*******************************************************************************************/

    /****************************************
    *必要なファイルのrequire等を行う
    ****************************************/

//定数を外部ファイルから読み込み
require_once('constant.php');
//composerのrequire
require_once("vendor/autoload.php");

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
} catch (FacebookRequestException $ex) {
    die($ex->getMessage);
} catch (\Exception $ex) {
    die($ex->getMessage);
}

//セッションを保持している場合は、main.phpに飛ぶ。それ以外は、facebookへリクエストを送る
if (isset($session)) {
//セッション情報と、ログアウトURLをmain.phpに渡す
    $_SESSION['session'] = $session;
    $_SESSION['logout_url'] = $helper->getLogoutUrl($session, AFTER_LOGOUT_URL);


    //ユーザーIDとトークンを保持する。データをぶっこぬく
    $access_token = $session->getToken();
    $sessioninfo = $session->getSessionInfo()->asArray();
    $user_id = $sessioninfo['user_id'];


    //新規登録か既存登録か確認するfunction
    function checkNewOrNot($user_id)
    {
        $db = new PDO(DATABASE_NAME, DATABASE_USERNAME, DATABASE_PASSWORD);

        try {
            //SQL文　db内の最新記事の日付の取得を指定
            $sqlQuery = "SELECT COUNT(user_id) FROM fb_token WHERE user_id = :user_id";
            $sqlStatement = $db->prepare($sqlQuery);

            $sqlStatement->bindValue(':user_id', $user_id);

            //実行
            $sqlStatement->execute();

            //実行結果の値を返す
            $count = $sqlStatement->fetch(PDO::FETCH_ASSOC);
            return $count['COUNT(user_id)'];



        } catch (PDOException $e) {
                die('storageTokenに不具合があります。' .$e->getMessage());
        }

    }

    //初回接続時にプッシュで情報を取得するfunction
    function pushGet() {
        //作りかけ
    }

    //function.初回接続してきたユーザーのuser_idとaccesstokenをDBに記録する
    function storageToken($user_id, $access_token)
    {
        $db = new PDO(DATABASE_NAME, DATABASE_USERNAME, DATABASE_PASSWORD);

        try {
            //SQL文　db内の最新記事の日付の取得を指定
            $sqlQuery = "INSERT INTO fb_token (user_id, access_token) VALUES(:user_id, :access_token)";
            $sqlStatement = $db->prepare($sqlQuery);

            $sqlStatement->bindValue(':user_id', $user_id);
            $sqlStatement->bindValue(':access_token', $access_token);

            //実行
            $sqlStatement->execute();



        } catch (PDOException $e) {
                die('storageTokenに不具合があります。' .$e->getMessage());
        }
    }



    if (checkNewOrNot($user_id) == 0) {
        print('NEW.');
        storageToken($user_id, $access_token);
    } else {
            print('DATA EXIST.');
    }

    print('check your db');

    // //main.phpへリダイレクト
    // header('location: main.php');
    // exit();


    //以 下、セッション情報を持っていない場合
} else {
    //permissionを追加したい場合は、$scopeに追記する。
    $scope = array('user_posts');
    $login_url = $helper->getLoginUrl($scope);

    //リダイレクト
    header("location: ${login_url}");
    exit();
}
