<?php
/**
*dbquery.php
*SQLクエリの作成等をまとめたクラス。
*作成中。
*/
class dbQuery
{
    private $table;
    private $type;
    private $column_array;

    public function __construct($table, $type, $column_array = null)
    {
        $this->table = $table;
        $this->column_array = $column_array;
        $this->type = $this->checkType($type);
    }

    private function checkType($type)
    {
        if ($type = 'insert' || 'select') {
            return $type;
        } else {
            print("クエリのタイプが不正です。'insert'か、'select'を選んでください。");
        }
    }

    private function arrayToStr($array)
    {
        foreach ($array as $value) {
            $str  .= $value;
            $str .= ',';
        }
        return substr(0, -1, $str);
    }

    private function arrayToPlaceHolder($array)
    {
        foreach ($array as $value) {
            $str  .= ':' . $value;
            $str .= ',';
        }
        return substr(0, -1, $str);
    }

    private function insertQuery()
    {
            $query .= 'INSERT IGNORE INTO ';
            $query .= $table;
            $query .= $column_array_key;
            $query .= 'VALUES ';
            $query .= $column_array_value;
            $query .= ')';
            return $query;
    }

    private function selectQuery()
    {
            $query .= 'SELECT ';
            $query .= $column_array_key;
            $query .= 'FROM ';
            $query .= $table;
            return $query;
    }
}
