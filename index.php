<?php
$hostname = php_uname("n");
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf8">
    <title>
        FB Study
    </title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>

<body>
    <div class="header">
        <div id="title">FB Study</div>
    </div>
    <div class="contents">
            <?php print($hostname); ?>
        ソーシャルアカウントでログイン<br/>
        <div id="button"><a href="login.php">Facebookログイン</a></div>
    </div>

    <div class="footer">
        &copy; 2015 comnico inc.
    </div>

</body>

</html>
