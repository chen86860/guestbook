<?php
/**
 * Created by PhpStorm.
 * User: 星星
 * Date: 16/7/3
 * Time: 13:38
 */
//echo rand(0,50);
//设置头像
//$path_img = "./userheader/lianmeng_header";
////$header_img = $path_img . '(' . rand(1, 50) . ').jpg';
//$header_img = $path_img.rand(1,45).'.jpg';
//echo "<img src='$header_img'>";

$str = "";
if(preg_match("/^[\x{4e00}-\x{9fa5}A-Za-z0-9_]+$/u",$str)) //UTF-8汉字字母数字下划线正则表达式
{
    echo "字符匹配";
}
else{
    echo "ERROR";
}