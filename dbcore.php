<?php
/**
*dbcore.php
*DBへの読み書き等のファンクションをまとめたクラス"DbCore"を記載したファイル
*/
/*******************************************************************************************/

    /****************************************
    *必要なファイルのrequire等を行う
    ****************************************/

//定数を外部ファイルから読み込み
require_once('constant.php');
//composerのrequire
require_once("vendor/autoload.php");

/*******************************************************************************************/


class DbCore
{

    private $db;
    private $data;

    public function __construct()
    {
        $this->db = new PDO(DATABASE_NAME, DATABASE_USERNAME, DATABASE_PASSWORD);
    }

    //静的メソッド用に、DBの情報をセットするファンクション。
    private static function dbSet()
    {
        $result = new PDO(DATABASE_NAME, DATABASE_USERNAME, DATABASE_PASSWORD);
        return $result;
    }

    //新規登録か既存登録か確認するfunction
    //新規の場合は文字列'new'を、既存の場合は文字列'exist'を返す
    public static function isNew($user_id)
    {
        $db = self::dbSet();
        try {
            //SQL文　指定のユーザーIDが登録されているか確認する
            //無ければ0を、あれば1を返す
            $sqlQuery = "SELECT COUNT(user_id) FROM fb_token WHERE user_id = :user_id";
            $sqlStatement = $db->prepare($sqlQuery);
            $sqlStatement->bindValue(':user_id', $user_id);
            $sqlStatement->execute();

            //DBから取得した値を取り出し、0と同じであればnewを、それ以外は1を返す
            $count = $sqlStatement->fetch(PDO::FETCH_ASSOC);
            $count_int =  (int)$count['COUNT(user_id)'];
            $result = ($count_int == 0 ? true : false);
            return $result;

        } catch (PDOException $e) {
                die('isNewに不具合があります。' .$e->getMessage());
        }

    }



    //初回接続してきたユーザーのuser_idとaccesstokenをDBに記録するfunction
    public static function storageToken($user_id, $access_token)
    {
        $db = self::dbSet();
        try {
            //SQL文　db内の最新記事の日付の取得を指定
            $sqlQuery = "INSERT INTO fb_token (user_id, access_token) VALUES(:user_id, :access_token)";
            $sqlStatement = $db->prepare($sqlQuery);
            $sqlStatement->bindValue(':user_id', $user_id);
            $sqlStatement->bindValue(':access_token', $access_token);
            $sqlStatement->execute();
        } catch (PDOException $e) {
                die('storageTokenに不具合があります。' .$e->getMessage());
        }
    }

    public static function updateToken($user_id, $access_token)
    {
        $db = new PDO(DATABASE_NAME, DATABASE_USERNAME, DATABASE_PASSWORD);
        try {
            //SQL文　db内の最新記事の日付の取得を指定
            $sqlQuery = "UPDATE fb_token SET access_token = :access_token WHERE user_id = :user_id";
            $sqlStatement = $db->prepare($sqlQuery);
            $sqlStatement->bindValue(':user_id', $user_id);
            $sqlStatement->bindValue(':access_token', $access_token);
            $sqlStatement->execute();

        } catch (PDOException $e) {
                die('updateTokenに不具合があります。' .$e->getMessage());
        }

    }



    //全ての登録ユーザーのIDとアクセストークンを配列にして出力するfunction
    public static function userDump()
    {
                $db = self::dbSet();
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
            die('userDumpに不具合があります。' .$e->getMessage());
        }

    }


   //配列の内容をデータベースに書き込むfunction.
    public static function storageFeed($page_id, $array)
    {
        $db = self::dbSet();

        //try,catchでPDOの例外を検知する
        try {
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
        } catch (PDOException $e) {
            die('storageFeedに不具合があります。' .$e->getMessage());
        }
    }


       //dbからのDataを取得して、プロパティ$dataに格納する
       //取得件数は新規日付20件
        public function requestData($page_id)
       {
          //try,catchでPDOの例外を検知する
        try {
                //SQLクエリをセット
                $sqlQuery = "SELECT id, page_id, editor_id, editor_name, post_id, post_date, post_message, image_url
                            FROM fb_feed WHERE page_id = :page_id ORDER BY post_date DESC LIMIT 20";
                $sqlStatement = $this->db->prepare($sqlQuery);
                $sqlStatement->bindValue(':page_id', $page_id);
                $sqlStatement->execute();

                //出力
                $this->data = $sqlStatement;


                //PDOでエラーが発生した場合の例外処理
        } catch (PDOException $e) {
                die('エラーが発生しました。' .$e->getMessage());
        }
        }

        public function outputData()
        {
                //SQLから取得したデータを配列に変換
                return $this->data->fetchall(PDO::FETCH_ASSOC);
        }
}
