<?php

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
    $db = new DbCore();
    $db->requestData($user_id);
    $data = $db->outputData();

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

foreach ($data as $d) {
        //投稿者のアイコンを取得
        $icon = new FacebookCore($session);
        $url = $icon->getIcon($d['editor_id']);

        print('<div class="post">');
    ?>

    <div id="icon"><img src="<?php print($url); ?>"></div>
    <a href="http://www.facebook.com/<?php print($d['editor_id']); ?> "><?php print($d['editor_name']);?></a>
    <a href="http://www.facebook.com/<?php print($d['post_id']); ?> "><?php print($d['post_date']);?></a>

    <?php
    //投稿本文がある場合は出力する
    if (isset($d['post_message'])) {
        print($d['post_message']);
    }
    //画像がある場合は、画像のURLを出力する。
    if (isset($d['image_url'])) {
        print('<img src="' . $d['image_url'] . '">');
    }

    print('</div>');
}

?>

</div>
</div>

<div class="footer">

<script>
    window.intercomSettings = {
        app_id: "juh7du4f",
        name: "Ozawas", // Full name
        email: "afuv846vtew3@sute.jp", // Email address
        created_at: 1438300800 // Signup date as a Unix timestamp
    };
</script>
<script>
    (function() {
        var w = window;
        var ic = w.Intercom;
        if (typeof ic === "function") {
            ic('reattach_activator');
            ic('update', intercomSettings);
        } else {
            var d = document;
            var i = function() {
                i.c(arguments)
            };
            i.q = [];
            i.c = function(args) {
                i.q.push(args)
            };
            w.Intercom = i;

            function l() {
                var s = d.createElement('script');
                s.type = 'text/javascript';
                s.async = true;
                s.src = 'https://widget.intercom.io/widget/juh7du4f';
                var x = d.getElementsByTagName('script')[0];
                x.parentNode.insertBefore(s, x);
            }
            if (w.attachEvent) {
                w.attachEvent('onload', l);
            } else {
                w.addEventListener('load', l, false);
            }
        }
    })()
</script>

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
