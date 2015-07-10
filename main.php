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

//セッションのスタート
//$_SESSIONの変数を入れ替える
session_start();
$session = $_SESSION['session'];
$user_id = $_SESSION['user_id'];
$logout_url = $_SESSION['logout_url'];

//FacebookSessionの呼び出し
FacebookSession::setDefaultApplication(APP_ID, APP_SECRET);


/*******************************************************************************************/


  /****************************************
  *セッションデータの保持を確認。
  *持っていない場合は、トップページにリダイレクト
  *(elseは、ファイル末尾に記載)
  ****************************************/

if (isset($session)) {

    //dbから、ページに表示用のデータを取得
    $data = getDataFromDb($user_id);

//以下、html

?>

<!DOCTYPE html>
<html>
<head>
  <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>

  <div class="header">
    <div id="title">FB Study</div>
    <div id="text_logout">
      <a href=" <?php print($logout_url);?> ">ログアウトする</a>
      </div>
    </div>

<div class="main">
<div class="contents">

    <?php

 //foreachで展開。post_messageとimage_urlがnullの場合は、出力しないようにする

 foreach($data as $d){
   print('<div class="post">');
   ?>

<div id="icon">
    <img src="<?php print(getIcon($session, $d['editor_id'])); ?>">
</div>

  <a href="http://www.facebook.com/<?php print($d['editor_id']); ?> "><?php print($d['editor_name']);?></a>
  <a href="http://www.facebook.com/<?php print($d['post_id']); ?> "><?php print($d['post_date']);?></a>
                        <?php if (isset($d['post_message'])) {
                            print($d['post_message']);
                        }
                        ?>
                <?php if (isset($d['image_url'])) {
                    print('<img src="' . $d['image_url'] . '">');
                    }
                    ?>
                <?php print('</div>'); ?>


<?php

    }
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


} else {
        header('location: index.php');
        exit();
}
