<?php

/***************************************************
*Facebook認証用の定数
****************************************************/

const APP_ID = '452878368210796'; //FacebookAppのID
const APP_SECRET = 'f0e25cc2d8ce6f0a8e2e80bf35c64081'; //FacebookAppのSecret
const REDIRECT_URL = 'http://fb-study-ozawa.herokuapp.com/login.php'; //セッション情報取得後にリダイレクトするURL
const AFTER_LOGOUT_URL = 'http://fb-study-ozawa.herokuapp.com/login.php'; //ログアウトした「後」に、リダイレクトするURL

/***************************************************
*DB用の定数
*ローカル環境のdbか、本番のdbになっているか、要確認。
****************************************************/

$hostname = php_uname("s");

if ( $hostname == 'Darwin' ) {

define ('DATABASE_NAME' , 'mysql:host=localhost;dbname=fb_study_ozawa;charset=utf8');
define ('DATABASE_USERNAME' , 'phpusr');   //　ユーザー名
define ('DATABASE_PASSWORD' , 'phppass'); // パスワード

} elseif ( $hostname == 'Linux' ) {

define ('DATABASE_NAME' , 'mysql:host=us-cdbr-iron-east-02.cleardb.net;dbname=heroku_9bfd6c1ee2da5df;charset=utf8'); //データベース名、アドレス
define ('DATABASE_USERNAME' , 'ab51289b82d603b');   //　ユーザー名
define ('DATABASE_PASSWORD' , '7439084d'); // パスワード

}

/***************************************************
*FBアカウント
****************************************************/

const ME = '853981858011604';
const NIKU ='1668777220012733';
