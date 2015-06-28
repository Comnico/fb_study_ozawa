<?php
//時間表記を"1時間前""2日前""3ヶ月前"4年前"　などに変更するfunction
//2015-05-02T14:44:35+0000
function dateDiff($date){
  $array = preg_split('/[\s:\/'T'\-\+]/',$date);
  print_r($array);
}
dateDiff('2015-05-02T14:44:35+0000');

?>
