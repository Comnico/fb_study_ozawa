<?php
/**
*constant.php
*定数をまとめたファイル
*/
/***************************************************
*Facebook認証用の定数
****************************************************/

const APP_ID = '452878368210796'; //FacebookAppのID
const APP_SECRET = 'f0e25cc2d8ce6f0a8e2e80bf35c64081'; //FacebookAppのSecret
const REDIRECT_URL = 'http://fb-study-ozawa.herokuapp.com/login.php'; //セッション情報取得後にリダイレクトするURL
const AFTER_LOGOUT_URL = 'http://fb-study-ozawa.herokuapp.com/index.php'; //ログアウトした「後」に、リダイレクトするURL

/***************************************************
*DB用の定数
*開発環境と本番環境で、接続するdbをわけている
****************************************************/

//hostのOSを確認。DarwinであればMac(開発環境)、Linuxであればheroku(本番環境)
$hostname = php_uname("s");

if ($hostname == 'Darwin') {
    define('DATABASE_NAME', 'mysql:host=localhost;dbname=fb_study_ozawa;charset=utf8');
    define('DATABASE_USERNAME', 'phpusr');   //　ユーザー名
    define('DATABASE_PASSWORD', 'phppass'); // パスワード

} elseif ($hostname == 'Linux') {
    //定数"CLEARDB_DATABASE_URL"はherokuで定義された定数。
    //接続先のclearDBのURLを定数化したもの。
    $url = parse_url(getenv("CLEARDB_DATABASE_URL"));

    $server = $url["host"];
    $username = $url["user"];
    $password = $url["pass"];
    $db = substr($url["path"], 1);

    define('DATABASE_NAME', "mysql:host=${server};dbname=${db};charset=utf8"); //データベース名、アドレス
    define('DATABASE_USERNAME', $username);   //　ユーザー名
    define('DATABASE_PASSWORD', $password); // パスワード
}
