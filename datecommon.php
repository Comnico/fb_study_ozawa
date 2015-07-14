<?php
/**
*datetime.php
*日付関連を管理する機能をまとめたクラス"DateCommon"をまとめたファイル
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

class DateCommon
{
    private $datetime;

    public function __construct($datetime)
    {
        $this->datetime = $datetime;
    }

    public function outputDate()
    {
        return $this->datetime;
    }

    /**
    *UTCを日本時間(JST)に変更し、
    *$datetimeプロパティに格納する。
    *@param str $datetime
    */
    public function changeToJST()
    {
        $jst = new DateTime($this->datetime);
        $jst->setTimeZone(new DateTimeZone('Asia/Tokyo'));
        $this->datetime = $jst->format('Y-m-d H:i:s');
    }
    public static function change()
    {

    }
}
