<?php
/**
*facebook_core.php
*Facebook関連のファンクションをまとめたクラス"FacebookCore"を記載したファイル
*/
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

class FacebookCore
{

    /**
    *Facebookのセッション情報
    *@var  string
    */
    private $session;

    /**
    *コンストラクタ。セッション情報を格納する。
    *@param string $session
    */
    public function __construct($session = null)
    {
        $this->session = $session;
    }

    /**
    *アクセストークンを利用して、セッションを取得する。
    *
    *@param string $access_token
    *@return obj $result
    */
    public static function getSession($access_token)
    {
        $result = new FacebookSession($access_token);
        return $result;
    }

    /**
     *引数$queryをクエリ文として、Facebookへリクエストを送り、
     *返ってきたデータを連想配列として出力する。
     *
     * @param string $query
     * @return array | $data
     */
    private function requestData($query)
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


    /**
     *$page_idで指定したFacebookページのフィードを取得する。
     *
     * @param string $page_id
     * @return array | $feed
     */
    public function outputFeed($page_id)
    {
        //クエリ文をセット
        $query = "/{$page_id}/feed";
        //フィードを配列として取得
        $feed = $this->requestData($query);
        //フィードを出力
        return $feed['data'];
    }


    /**
     *$editor_idで指定したFacebookのアカウントのアイコンを取得する。
     *
     * @param string $editor_id
     * @return string | $url['url'];
     */
    public function getIcon($editor_id)
    {
        //iconを取得するクエリ文をセット
        $query = "/${editor_id}/picture?redirect=false";
        //URLを配列として取得
        $url = $this->requestData($query);
        //URLを出力
        return $url['url'];
    }
}
