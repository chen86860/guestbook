<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Document</title>
</head>
<body>
<form action="index.php" method="post">
    <input type="text" placeholder="你的大名" required name="nickname">
    <input type="email" placeholder="E-mail" required name="email">
    <input type="text" name="site" placeholder="你的网站">
    <textarea required placeholder="写下你的评论..." name="comment"></textarea>
    <input type="submit" name="submit" value="提交评论">
</form>
</body>
</html>
<?php
/**
 * Created by PhpStorm.
 * User: 星星
 * Date: 16/7/3
 * Time: 12:25
 */
if (!isset($_POST['submit'])) {
    exit;
}

//数据验证
//用户名
$patten_user = '/^[a-zA-z][a-zA-Z0-9_][\u4E00-\u9FA5]{3,9}$/';
if (isset($_POST['nickname'])) {
    if (preg_match($patten_user, $_POST['nickname'])) {
        $nickname = $_POST['nickname'];
    } else {
        $_SESSION['error'] = '1';
        exit;
    }
} else {
    exit;
}
//email
$patten_user = '/\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/';
if (isset($_POST['email'])) {
    if (preg_match($patten_user, $_POST['email'])) {
        $email = $_POST['email'];
    } else {
        $_SESSION['error'] = '1';
        exit;
    }
} else {
    exit;
}

//site
$patten_user = '/[a-zA-z]+://[^\s]*/';
if (isset($_POST['site'])) {
    if (preg_match($patten_user, $_POST['site'])) {
        $site = $_POST['site'];
    } else {
        $_SESSION['error'] = '1';
        exit;
    }
} else {
    exit;
}

//comment
$patten_user = '/^[a-zA-z][a-zA-Z0-9_][\u4E00-\u9FA5]{1,}$/';
if (isset($_POST['comment'])) {
    if (preg_match($patten_user, $_POST['comment'])) {
        $comment = $_POST['comment'];
    } else {
        $_SESSION['error'] = '1';
        exit;
    }
} else {
    exit;
}

//当前时间
date_default_timezone_set('PRC');
$time_now = date("Y-m-d_H:i:s", time());
include ('conn.php');



?>