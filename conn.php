<?php
/**
 * Created by PhpStorm.
 * User: 星星
 * Date: 16/7/3
 * Time: 12:37
 */

//数据库连接
$host = "127.0.0.1";
$user = "root";
$passwords = "";
$database = "guestbook";

$link = mysqli_connect($host, $user, $passwords) or die("数据库连接失败");
mysqli_select_db($link, $database) or die("数据库选择失败");



?>
