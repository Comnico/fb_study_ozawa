<?php

/*******************************************************************************************
本ファイルは、作りかけのfunction等を下書きとして保存しておくファイルである
*******************************************************************************************/

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

/*******************************************************************************************/

    //時間表記を"1時間前""2日前""3ヶ月前"4年前"　などに変更するfunction
    //2015-05-02T14:44:35+0000
    //作りかけ
    function dateDiff($date){
    $array = preg_split('/[\s:\/'T'\-\+]/', $date);
      print_r($array);
    }
    dateDiff('2015-05-02T14:44:35+0000');


    //facebookとdbとの、フィードの差分を確認し、
    //差分がある場合は、差分のフィードを連想配列で取得するfunction
    //作りかけ　現在、実装の予定無し
    function checkUpdate($session, $page_id)
    {

        //DBに接続
        $db = new PDO(DATABASE_NAME, DATABASE_USERNAME, DATABASE_PASSWORD);

        //SQL文　db内の最新記事の日付の取得を指定
        $sqlQuery = "SELECT post_date FROM fb_feed ORDER BY post_date DESC LIMIT 1";
        $sqlStatement = $db->prepare($sqlQuery);

        //実行
        $sqlStatement->execute();

        //取得した連想配列を取り出し、DateTime型へ変換し、整形し出力する
        $post_date_array = $sqlStatement->fetchall(PDO::FETCH_ASSOC);
        $post_date = $post_date_array[0]['post_date'];
        $date_time = DateTime::createFromFormat('Y-m-d G:i:s', $post_date);
        $time = $date_time->format('Y-m-d');

        //db内の最新記事の日付以降のfeedは無いか、リクエストを送信
        $feed_request = new FacebookRequest($session, 'GET', "/${page_id}/feed?since=${time}");
        //Graph APIへ送信
        $response = $feed_request->execute();
        //Facebookから返ったきたデータを、配列に変換
        $feed = $response->getGraphObject()->getProperty('data')->asArray();

        return $feed;
}
