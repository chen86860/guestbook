<?php
session_start();
/**
 * Created by PhpStorm.
 * User: 星星
 * Date: 16/7/3
 * Time: 18:10
 */

//网站跳转
function redirect($string)
{
    echo '<script language = \'javascript\' type = \'text/javascript\' > ';
    echo "window.location.href = '$string' ";
    echo '</script>';
}

if (isset($_POST['admin_comment_submit'])) {
    if (isset($_POST['admin_comment']) && isset($_POST['guest_id'])) {
        $admin_post = $_POST['admin_comment'];
        $guest_id = $_POST['guest_id'];
        $view_page = "admin/admin.php?page=" . $_POST['view_page'];

        //当前时间
        date_default_timezone_set('PRC');
        $time_now = date("Y-m-d H:i:s", time());

        include "conn.php";
        $sql_admin_insert_comment = <<<mia
update comment set admin_comment_content='$admin_post', admin_comment_flag='1',admin_comment_time='$time_now' where id = $guest_id
mia;
        mysqli_query($link, $sql_admin_insert_comment);
        if (mysqli_affected_rows($link)) {
            $_SESSION['comment_status'] = '1';
            redirect($view_page);
            exit;
        } else {
            $_SESSION['comment_status'] = '1';
            redirect($view_page);
            exit;
        }
    }
}
if (!isset($_POST['submit'])) {
    setcookie('commnet_guest_status', '0');
    redirect("index.php");
    exit;
}

//数据验证
//用户名
$patten_user = '/^[\x{4e00}-\x{9fa5}A-Za-z0-9_\s]+$/u';
if (isset($_POST['nickname'])) {
    if (preg_match($patten_user, $_POST['nickname'])) {
        $nickname = $_POST['nickname'];
    } else {
//        echo "nickname error";
        setcookie('commnet_guest_status', '0');
        redirect("index.php#comment_failed");
        exit;
    }
} else {
    setcookie('commnet_guest_status', '0');
    redirect("index.php#comment_failed");
    exit;
}
//email
$patten_email = '/\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/';
if (isset($_POST['email'])) {
    if (preg_match($patten_email, $_POST['email'])) {
        $email = $_POST['email'];
    } else {
//        echo "email ERROR";
        setcookie('commnet_guest_status', '0');
        redirect("index.php#comment_failed");
        exit;
    }
} else {
//    echo "email2 ERROR";
    setcookie('commnet_guest_status', '0');
    redirect("index.php#comment_failed");
    exit;
}

//site
//PHP的正则匹配要求两端对称！
// 可以用~ @ # 来标志开头和结尾
$patten_site = '~[a-zA-z./:][a-zA-z]*~';
if (isset($_POST['site'])) {
    if ($_POST['site'] != "") {
        if (preg_match($patten_site, $_POST['site'])) {
            $site = $_POST['site'];
        } else {
//            echo "site ERROR";
            setcookie('commnet_guest_status', '0');
            redirect("index.php#comment_failed");
            exit;
        }
    } else {
        $site = "";
    }
} else {
    $site = "";
    exit;
}

//comment
//UTF-8汉字字母数字下划线正则表达式！！！
$patten_comment = '/^[\x{4e00}-\x{9fa5}A-Za-z0-9_]{1,100}+$/u';
if (isset($_POST['comment'])) {
    if (preg_match($patten_comment, $_POST['comment'])) {
        $comment = $_POST['comment'];
    } else {
//        echo "comment error";
        setcookie('commnet_guest_status', '0');
        redirect("index.php#comment_failed");
        exit;
    }
} else {
    redirect("index.php#comment_failed");
    echo "comment2 error";
    exit;
}

//当前时间
date_default_timezone_set('PRC');
$time_now = date("Y-m-d H:i:s", time());


//获取ip信息
function get_ip_data()
{
    $ip = file_get_contents("http://ip.taobao.com/service/getIpInfo.php?ip=" . get_client_ip());
    $ip = json_decode($ip);
    if ($ip->code) {
        return false;
    }
    $data = (array)$ip->data;
    return $data;

}

//取客户端 ip
function get_client_ip()
{
//    if (isset($_SERVER['HTTP_CLIENT_IP']) and !empty($_SERVER['HTTP_CLIENT_IP'])) {
//        return $_SERVER['HTTP_CLIENT_IP'];
//    }
//    if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) and !empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
//        return strtok($_SERVER['HTTP_X_FORWARDED_FOR'], ',');
//    }
//    if (isset($_SERVER['HTTP_PROXY_USER']) and !empty($_SERVER['HTTP_PROXY_USER'])) {
//        return $_SERVER['HTTP_PROXY_USER'];
//    }
//    if (isset($_SERVER['REMOTE_ADDR']) and !empty($_SERVER['REMOTE_ADDR'])) {
//        return $_SERVER['REMOTE_ADDR'];
//    } else {
//        return "0.0.0.0";
//    }
    return "27.45.36.179";
}

$ip_info = get_ip_data();
$region_city = $ip_info['region'] . $ip_info['city'];

//设置头像
$path_img = "./userheader/lianmeng_header";
$header_img = $path_img . rand(1, 45) . '.jpg';

//连接数据库
include('conn.php');

$num = 10;//每页显示10条数据
$sql_count_row = "select * from comment";
$total = mysqli_num_rows(mysqli_query($link, $sql_count_row)); //查询数据的总数total
$pagenum = intval(ceil($total / $num));      //获得总页数 pagenum

$sql_commnet_insert = <<<mia
insert into comment(nickname,email,site,comment_content,header,time,region_city) values('$nickname','$email','$site','$comment','$header_img','$time_now','$region_city')
mia;

mysqli_query($link, $sql_commnet_insert);
if (mysqli_affected_rows($link)) {
    setcookie('commnet_guest_status', '1');
    setcookie('commnet_guest_name', $nickname);
    setcookie('commnet_guest_email', $email);
    $redirPage = "index.php?page=" . $pagenum . "#comment_succeed";
    redirect($redirPage);

} else {
    setcookie('commnet_guest_status', '0');
    $_COOKIE['commnet_guest_name'] = $nickname;
    $_COOKIE['commnet_guest_email'] = $email;
    redirect("index.php#comment_failed");
}
?>