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

/*******************************************************************************************/

class FacebookCore
{

    private $session;
    private $page_id;

    public function __construct($session = null, $page_id = null)
    {
        $this->session = $session;
        $this->page_id = $page_id;
    }


    //アクセストークンを利用して、セッションを取得するfunction
    public static function getSession($access_token)
    {
        $result = new FacebookSession($access_token);
        return $result;
    }

    private function getData($query)
    {
        //Graph APIへ送るセッション情報と、データ取得のための構文を指定。
        $data_request = new FacebookRequest($this->session, 'GET', $query);
         //Graph APIへ送信
         $response = $data_request->execute();
         //Facebookから返ったきたデータを、配列に変換
         $data = $response->getGraphObject()->asArray();

         //取得したデータを、配列として出力
         return $data;
    }

   //facebookの情報を配列にして出力するfunction
    public function getFeed()
    {
        //クエリ文をセット
        $query = "/{$this->page_id}/feed";
        //フィードを配列として取得
        $feed = $this->getData($query);
        //フィードを出力
         return $feed['data'];
    }



   //iconを取得するfunction
    public function getIcon($editor_id)
    {
         //Graph APIへ送るセッション情報と、feed取得のための構文を指定。
         $icon_request = new FacebookRequest($this->session, 'GET', "");
         //Graph APIへ送信
         $icon_obj = $icon_request->execute();
         //Facebookから返ったきたデータを、配列に変換
         $icon_url = $icon_obj->getGraphObject()->asArray();
         //配列を出力
         return $icon_url['url'];
    }



    $query = '/{$this->page_id}/feed  ,   /${editor_id}/picture?redirect=false '

}
