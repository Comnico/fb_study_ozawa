<?php

/*******************************************************************************************/

    /****************************************
    *必要なファイルのrequire等を行う
    ****************************************/

//定数を外部ファイルから読み込み
require_once ('constant.php');
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

//セッションのスタート
//$_SESSIONの変数を入れ替える
session_start();
$session = $_SESSION['session'];
$logout_url = $_SESSION['logout_url'];

//取得するページを指定
$page_id = NIKU;

//FacebookSessionの呼び出し
FacebookSession::setDefaultApplication(APP_ID, APP_SECRET);


/*******************************************************************************************/


  /****************************************
  *セッションデータの保持を確認。
  *持っていない場合は、トップページにリダイレクト
  *(elseは、ファイル末尾に記載)
  ****************************************/

 if ( isset( $session ) ) {


/*******************************************************************************************/


   /****************************************
   *以下、function
   ****************************************/

   //facebookとdbの差分を確認するfunction
   function checkUpdate () {

   }

   //facebookの情報を配列にして出力するfunction
   function getFeedFromFacebook ($session, $page_id) {

     //Graph APIへ送るセッション情報と、feed取得のための構文を指定。
     $feed_request = new FacebookRequest( $session, 'GET', "/${page_id}/feed");
     //Graph APIへ送信
     $response = $feed_request->execute();
     //Facebookから返ったきたデータを、配列に変換
     $feed = $response->getGraphObject()->getProperty('data')->asArray();
     //配列を出力
     return $feed;
   }


   //配列の内容をデータベースに書き込むfunction.
   //getFeedFromFacebookで取得した配列をMySQLへ書き込むために使う
   function storageFeedToDb ($page_id, $array) {

     //try,catchでPDOの例外を検知する
    try {
          //DBに接続
          $db = new PDO (DATABASE_NAME,DATABASE_USERNAME,DATABASE_PASSWORD);

          //foreachで連続して書き込む
          foreach($array as $f){

          //SQLクエリをセット
          $sqlQuery = "INSERT INTO fb_feed (page_id, editor_id, editor_name, post_id, post_date, post_message, image_url) VALUES(:page_id, :editor_id, :editor_name, :post_id, :post_date, :post_message, :image_url)";
          $sqlStatement = $db->prepare ($sqlQuery);

          //以下、SQLクエリのプレースホルダに値を代入。
          //post_messageとimage_urlは、配列が無い場合はNULLとする
          $sqlStatement->bindValue (':page_id',$page_id);
          $sqlStatement->bindValue (':editor_id',$f->from->id);
          $sqlStatement->bindValue (':editor_name',$f->from->name);
          $sqlStatement->bindValue (':post_id',$f->id);
          $sqlStatement->bindValue (':post_date',$f->updated_time);
           if (isset ($f->message)) {
          $sqlStatement->bindValue (':post_message',$f->message);
          } else {$sqlStatement->bindValue (':post_message', NULL, PDO::PARAM_NULL);
          }
          if (isset ($f->picture)) {
          $sqlStatement->bindValue (':image_url',$f->picture);
          } else { $sqlStatement->bindValue (':image_url', NULL, PDO::PARAM_NULL);
          }

          //実行
          $sqlStatement->execute();


        }
          //DB切断
          $db = NULL;
      }
      //例外処理
      catch(PDOException $e){
      die('storageFeedToDbに不具合があります。' .$e->getMessage());
      }
    }



   //dbからのDataを取得し、連想配列にして出力
   //取得件数は新規日付20件
   function getDataFromDb(){

  //try,catchでPDOの例外を検知する
   try {

   //DBに接続
   $db = new PDO (DATABASE_NAME,DATABASE_USERNAME,DATABASE_PASSWORD);

   //SQLクエリをセット
   $sqlQuery = "SELECT id, page_id, editor_id, editor_name, post_id, post_date, post_message, image_url FROM fb_feed ORDER BY post_date DESC LIMIT 20";
   $sqlStatement = $db->prepare ($sqlQuery);

   //実行
   $sqlStatement->execute();

   //SQLから取得したデータを配列に変換
   $data = $sqlStatement->fetchall(PDO::FETCH_ASSOC);

   //出力
   return $data;

   //DB切断
   $db = NULL;

   //PDOでエラーが発生した場合の例外処理
   }catch(PDOException $e){
   die('エラーが発生しました。' .$e->getMessage());
   }
   }

   //iconを取得するfunction
   function getIcon($session, $editor_id){

     //Graph APIへ送るセッション情報と、feed取得のための構文を指定。
     $icon_request = new FacebookRequest( $session, 'GET', "/${editor_id}/picture?redirect=false");
     //Graph APIへ送信
     $icon_obj = $icon_request->execute();
     //Facebookから返ったきたデータを、配列に変換
     $icon_url = $icon_obj->getGraphObject()->asArray();
     //配列を出力
     return $icon_url['url'];
   }

/*******************************************************************************************/

    /****************************************
    *fbからフィードの情報を取得し、dbに書き込む
    ****************************************/

   $feed = getFeedFromFacebook($session, $page_id);
   storageFeedToDb($page_id, $feed);


/*******************************************************************************************/

    /****************************************
    *dbから、ページに表示用のデータを取得
    ****************************************/
    $data = getDataFromDb();


//以下、html

?>

<!DOCTYPE html>
<html>
<head>
  <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>

  <div class="h1">
    <text>FB Study</text>
    <text_logout>
      <a href=" <?php print($logout_url);?> ">
      ログアウトする
      </a>
      </text_logout>
    </div>

<div class="main">
<div class="contents">

  <?php

 //foreachで展開。post_messageとimage_urlがnullの場合は、出力しないようにする

 foreach($data as $f){
   print('<div class="post">');
   ?>

  <img src="<?php print(getIcon($session, $f['editor_id'])); ?>">
  <br/><a href="http://www.facebook.com/<?php print($f['editor_id']); ?> "><?php print($f['editor_name']);?></a>
  <br/><a href="http://www.facebook.com/<?php print($f['post_id']); ?> "><?php print($f['post_date']);?></a>
  <br/><?php if(isset ($f['post_message'] )) { print($f['post_message']); } ?>
  <br/><?php if(isset ($f['image_url'] )) { print('<img src="' . $f['image_url'] . '">'); } ?>
  <br/><?php print('</div>'); ?>


   <?php

 }
 $count = NULL;
?>


</div>
</div>

<div class="footer">
<text>&copy; 2015 comnico inc.</text>
</div>
 </body>
 </html>



<?php

// セッションデータを持っていない場合は、トップページにリダイレクト


 }else{
   header('location: index.php');
   exit();
 }
