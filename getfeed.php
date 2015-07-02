<?php

require_once ('constant.php'); //定数を外部ファイルから読み込み

/****************************************
*MySQLに接続し、データをtry,catchで取得する
****************************************/

//facebookとdbの差分を確認するfunction
function checkUpdate () {

}

//facebookの情報をdbに取得するfunction
function getFeedFromFacebook(){
  

}


//dbからのDataを取得し、連想配列にして出力
//取得件数は新規日付20件
//$offsetで取得する位置を指定
function getDataFromDb($offset = 0){
try {

$db = new PDO (DATABASE_NAME,DATABASE_USERNAME,DATABASE_PASSWORD);
$sqlStatement = $db->prepare ( "SELECT id,user_id,editor_id,post_id,post_date,post_message,image_url FROM fb_feed ORDER BY post_date ASC　LIMIT :offset 20" );
$sqlStatement->bindValue(':offset',$offset);
$sqlStatement->execute();
return $sqlStatement->fetchall(PDO::FETCH_ASSOC);
//PDOでエラーが発生した場合の例外処理
}catch(PDOException $e){
die('エラーが発生しました。' .$e->getMessage());
}



}
