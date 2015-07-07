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
use Facebook\FacebookRequest;
use Facebook\FacebookRequestException;
use Facebook\GraphUser;
use Facebook\GraphLocation;
use Facebook\GraphSessionInfo;

/*******************************************************************************************/

        /****************************************
        *DB関連のfunction
        ****************************************/


    //新規登録か既存登録か確認するfunction
    //新規の場合は文字列'new'を、既存の場合は文字列'exist'を返す
    function checkNewOrNot($user_id)
    {
        $db = new PDO(DATABASE_NAME, DATABASE_USERNAME, DATABASE_PASSWORD);

        try {
            //SQL文　指定のユーザーIDが登録されているか確認する　無ければ0を、あれば1を返す
            $sqlQuery = "SELECT COUNT(user_id) FROM fb_token WHERE user_id = :user_id";
            $sqlStatement = $db->prepare($sqlQuery);

            //プレースホルダ
            $sqlStatement->bindValue(':user_id', $user_id);

            //実行
            $sqlStatement->execute();

            //DBから取得した値を取り出し、0と同じであればnewを、それ以外は1を返す
            $count = $sqlStatement->fetch(PDO::FETCH_ASSOC);
            $count_int =  (int)$count['COUNT(user_id)'];
            $result = ($count_int == 0 ? 'new' : 'exist');

            return $result;

            } catch (PDOException $e) {
                die('storageTokenに不具合があります。' .$e->getMessage());
            }

    }

    //初回接続時にプッシュで情報を取得するfunction
    function pushGet() {
        //作りかけ
    }

    //初回接続してきたユーザーのuser_idとaccesstokenをDBに記録するfunction
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


    //全ての登録ユーザーのIDとアクセストークンを配列にして出力するfunction
    function userDumpFromDB() {
    $db = new PDO(DATABASE_NAME, DATABASE_USERNAME, DATABASE_PASSWORD);

    try {
        //SQL文　ユーザーIDとアクセストークンを全て出力する
        $sqlQuery = "SELECT user_id, access_token FROM fb_token";
        $sqlStatement = $db->prepare($sqlQuery);

        //実行
        $sqlStatement->execute();

        //DBから取得した値を取り出す
        $result = $sqlStatement->fetchall(PDO::FETCH_ASSOC);

        //結果を出力
        return $result;

        } catch (PDOException $e) {
            die('userDumpFromDBに不具合があります。' .$e->getMessage());
        }

    }


   //配列の内容をデータベースに書き込むfunction.
   //getFeedFromFacebookで取得した配列をMySQLへ書き込むために使う
    function storageFeedToDb($page_id, $array)
    {
        //try,catchでPDOの例外を検知する
        try {
            //DBに接続
            $db = new PDO(DATABASE_NAME, DATABASE_USERNAME, DATABASE_PASSWORD);

            //foreachで、連続してdbへ書き込む
            foreach ($array as $f) {
                //SQLクエリをセット
                $sqlQuery = "INSERT IGNORE INTO fb_feed (page_id, editor_id, editor_name, post_id, post_date, post_message, image_url)
               VALUES(:page_id, :editor_id, :editor_name, :post_id, :post_date, :post_message, :image_url)";
                $sqlStatement = $db->prepare($sqlQuery);

                    //以下、SQLクエリのプレースホルダに値を代入。
                    //post_messageとimage_urlは、配列が無い場合はNULLとする
                  $sqlStatement->bindValue(':page_id', $page_id);
                  $sqlStatement->bindValue(':editor_id', $f->from->id);
                  $sqlStatement->bindValue(':editor_name', $f->from->name);
                  $sqlStatement->bindValue(':post_id', $f->id);
                  $sqlStatement->bindValue(':post_date', $f->updated_time);
                if (isset($f->message)) {
                    $sqlStatement->bindValue(':post_message', $f->message);
                } else {
                    $sqlStatement->bindValue(':post_message', null, PDO::PARAM_NULL);
                }
                if (isset($f->picture)) {
                    $sqlStatement->bindValue(':image_url', $f->picture);
                } else {
                    $sqlStatement->bindValue(':image_url', null, PDO::PARAM_NULL);
                }

                //実行
                $sqlStatement->execute();


            }
          //DB切断
            $db = null;

        }
        //例外処理
        catch (PDOException $e) {
            die('storageFeedToDbに不具合があります。' .$e->getMessage());
        }
    }


       //dbからのDataを取得し、連想配列にして出力
       //取得件数は新規日付20件
        function getDataFromDb()
        {

          //try,catchでPDOの例外を検知する
            try {
                //DBに接続
                $db = new PDO(DATABASE_NAME, DATABASE_USERNAME, DATABASE_PASSWORD);

                //SQLクエリをセット
                $sqlQuery = "SELECT id, page_id, editor_id, editor_name, post_id, post_date, post_message, image_url FROM fb_feed ORDER BY post_date DESC LIMIT 20";
                $sqlStatement = $db->prepare($sqlQuery);

                //実行
                $sqlStatement->execute();

                //SQLから取得したデータを配列に変換
                $data = $sqlStatement->fetchall(PDO::FETCH_ASSOC);

                //出力
                return $data;

                //DB切断
                $db = null;

                //PDOでエラーが発生した場合の例外処理
            } catch (PDOException $e) {
                die('エラーが発生しました。' .$e->getMessage());
            }
        }



/*******************************************************************************************/

        /****************************************
        *Facebook関連のfunction
        ****************************************/


   //facebookの情報を配列にして出力するfunction
    function getFeedFromFacebook($session, $page_id)
    {

       //Graph APIへ送るセッション情報と、feed取得のための構文を指定。
        $feed_request = new FacebookRequest($session, 'GET', "/${page_id}/feed");
         //Graph APIへ送信
         $response = $feed_request->execute();
         //Facebookから返ったきたデータを、配列に変換
         $feed = $response->getGraphObject()->getProperty('data')->asArray();
         //配列を出力
         return $feed;
    }



   //iconを取得するfunction
    function getIcon($session, $editor_id)
    {

         //Graph APIへ送るセッション情報と、feed取得のための構文を指定。
         $icon_request = new FacebookRequest($session, 'GET', "/${editor_id}/picture?redirect=false");
         //Graph APIへ送信
         $icon_obj = $icon_request->execute();
         //Facebookから返ったきたデータを、配列に変換
         $icon_url = $icon_obj->getGraphObject()->asArray();
         //配列を出力
         return $icon_url['url'];
    }


?>
