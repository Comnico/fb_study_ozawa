<?php
require_once("vendor/autoload.php");
// Make sure to load the Facebook SDK for PHP via composer or manually
use Facebook\FacebookSession;
use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookRequest;
use Facebook\GraphUser;
use Facebook\FacebookRequestException;

session_start();
$session = $_SESSION['session'];
FacebookSession::setDefaultApplication('452878368210796', 'f0e25cc2d8ce6f0a8e2e80bf35c64081');


 // セッションデータの保持を確認。
 //　持っていない場合は、トップページにリダイレクト
 if ( isset( $session ) ) {

  // feedの全取得
  $my_feed = new FacebookRequest( $session, 'GET', '/me/feed');
  $response = $my_feed->execute();
  $feed = $response->getGraphObject()->getProperty('data')->asArray();


  //iconを取得するfunction
   function getIcon($id,$session){
     $icon_request = new FacebookRequest( $session, 'GET', "/{$id}/picture?redirect=false");
     $icon_obj = $icon_request->execute();
     $icon_url = $icon_obj->getGraphObject()->asArray();
     return $icon_url['url'];
   }

   //ログアウトするfunction

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
    <text_logout>ログアウトする</text_logout>
    </div>

<div class="main">
<div class="contents">

  <?php
 //foreachで展開。空欄のものは'@'でエラーメッセージを回避
 $count = 0;
 foreach($feed as $f){
   print('<div class="post">');
   ?>

   <pre>
  <img src="<?php print(getIcon($feed[$count]->from->id,$session)); ?>">
  <a href="http://www.facebook.com/<?php print($feed[$count]->from->id); ?> "><?php print($feed[$count]->from->name);?></a>
  <a href="http://www.facebook.com/<?php print($feed[$count]->id); ?> "><?php print($feed[$count]->updated_time);?></a>
  <?php @print($feed[$count]->message);?>

  <?php @print('<img src="' . $feed[$count]->picture . '">'); ?>
   <?php print('</div>'); ?>

   <?php
   $count ++;
 }
 $count = NULL;
 var_dump($feed);
?>

</pre>

</div>
</div>

<div class="footer">
<text>&copy; 2015 comnico inc.</text>
</div>
 </body>
 </html>



<?php
 }else{
   header('location: login.php');
   exit();
 }