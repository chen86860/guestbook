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


//提交评论
if (isset($_POST['comment_submit'])) {
    if (isset($_POST['comment']) && isset($_POST['__guest_nickname'])) {
//数据验证
//用户名
        $patten_user = '/^[\x{4e00}-\x{9fa5}A-Za-z0-9_\s]+$/u';
        if (isset($_POST['nickname'])) {
            if (preg_match($patten_user, $_POST['nickname'])) {
                $nickname = $_POST['nickname'];
            } else {
                setcookie('commnet_guest_status', '0');
//                echo "user_err";
                redirect("index.php#comment_failed");
                exit;
            }
        } else {
            setcookie('commnet_guest_status', '0');
            redirect("index.php#comment_failed");
//            echo "usre_err";
            exit;
        }

//email
        $patten_email = '/\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/';
        if (isset($_POST['email'])) {
            if (preg_match($patten_email, $_POST['email'])) {
                $email = $_POST['email'];
            } else {
                setcookie('commnet_guest_status', '0');
                redirect("index.php#comment_failed");
                exit;
            }
        } else {
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
        }

//comment
//UTF-8汉字字母数字下划线正则表达式！！！
        $patten_comment = '/^[\x{4e00}-\x{9fa5}A-Za-z0-9_\s]{1,150}+$/u';
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
            setcookie('commnet_guest_status', '0');
            redirect("index.php#comment_failed");
//            echo "comment2 error";
            exit;
        }

//获取IP
        $ip_info = get_ip_data();
        $region_city = $ip_info['region'] . $ip_info['city'];

//设置头像
        $path_img = "/guestbook/userheader/lianmeng_header";
        $header_img = $path_img . rand(1, 45) . '.jpg';

//连接数据库
        include('conn.php');

        //当前时间
        date_default_timezone_set('PRC');
        $time_now = date("Y-m-d H:i:s", time());
        if (!isset($_SESSION['username'])) {
//判断用户是否存在
            $sql_check_user_exit = <<<mia
select header from comment where email = '$email' and nickname = '$nickname';
mia;
            $res = mysqli_fetch_array(mysqli_query($link, $sql_check_user_exit));
            if ($res != null) {
                $header_img = $res['header'];
            }

            //插入用户数据
            $sql_commnet_insert = <<<mia
insert into comment(nickname,email,site,comment_content,header,time,region_city,comment_type) values('$nickname','$email','$site','$comment','$header_img','$time_now','$region_city',2)
mia;
            mysqli_query($link, $sql_commnet_insert);
            if (mysqli_affected_rows($link)) {
                setcookie('commnet_guest_status', '1');
                setcookie('commnet_guest_name', $nickname);
                setcookie('commnet_guest_email', $email);
                setcookie('commnet_guest_header', $header_img);
            } else {
                setcookie('commnet_guest_status', '0');
                $_COOKIE['commnet_guest_name'] = $nickname;
                $_COOKIE['commnet_guest_email'] = $email;
                redirect("index.php#comment_failed");
                exit;
            }
        } else {
            //管理员不需要插入新的评论，只需插入到comment_guest中即可。
            $user_header = $_SESSION['header'];
            $region_city = "UFO";
        }

        $__guest_nickname = $_POST['__guest_nickname'];

        //comment_id ：既是楼层数
        $comment_id = $_POST['comment_id'];
        $view_page = $_POST['view_page'];
        //构建sql语句
        //判断是否是管理员
        if (isset($_SESSION['username'])) {
            $user_header = $_SESSION['header'];
            $view_page = "admin/admin.php?page=" . $view_page;
            $session_user = $_SESSION['nickname'];
            $sql_admin_insert_comment = <<<mia
insert into comment_guest(comment_floor,comment_host_name,comment_nick_name,comment_content,comment_time,comment_header,region_city)  values('$comment_id','$__guest_nickname','$session_user',' $comment','$time_now','$user_header','$region_city')
mia;
        } else {
            $user_header = $header_img;
            $view_page = "index.php?page=" . $view_page;
            $cookie_user = $nickname;
            $sql_admin_insert_comment = <<<mia
insert into comment_guest(comment_floor,comment_host_name,comment_nick_name,comment_content,comment_time,comment_header,region_city)  values('$comment_id','$__guest_nickname','$cookie_user',' $comment','$time_now','$user_header','$region_city')
mia;
        }
        mysqli_query($link, $sql_admin_insert_comment);
        if (mysqli_affected_rows($link)) {
            $_SESSION['comment_status'] = '1';
            setcookie('commnet_guest_status', '1');
            $redirPage =     $view_page . "#comment_" . $comment_id;
            redirect($redirPage);
            exit;
        } else {
            setcookie('commnet_guest_status', '0');
            $_SESSION['comment_status'] = '1';
            redirect($view_page);
            exit;
        }
    }
    exit;
}

if (isset($_POST['submit'])) {

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
    $patten_comment = '/^[\x{4e00}-\x{9fa5}A-Za-z0-9_\s]{1,150}+$/u';
    if (isset($_POST['comment'])) {
//        if (get_magic_quotes_gpc()) {
//            $comment= stripslashes($_POST['comment']);
//        }
//        else {
//            $comment= $_POST['comment'];
//        }
//        $comment= mysqli_real_escape_string($_POST['comment']);
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


    $ip_info = get_ip_data();
    $region_city = $ip_info['region'] . $ip_info['city'];
    $view_page = $_POST['view_page'];
//设置头像
    $path_img = "/guestbook/userheader/lianmeng_header";
    $header_img = $path_img . rand(1, 45) . '.jpg';

//连接数据库
    include('conn.php');

//判断用户是否存在
    $sql_check_user_exit = <<<mia
select header from comment where email = '$email' and nickname = '$nickname';
mia;
    $res = mysqli_fetch_array(mysqli_query($link, $sql_check_user_exit));

    if ($res != null) {
        $header_img = $res['header'];
    }


    $sql_commnet_insert = <<<mia
insert into comment(nickname,email,site,comment_content,header,time,region_city,comment_type) values('$nickname','$email','$site','$comment','$header_img','$time_now','$region_city',1)
mia;

    mysqli_query($link, $sql_commnet_insert);

    if (mysqli_affected_rows($link)) {
        setcookie('commnet_guest_status', '1');
        setcookie('commnet_guest_name', $nickname);
        setcookie('commnet_guest_email', $email);
        setcookie('commnet_guest_header', $header_img);
        $redirPage = "index.php?page=" . $view_page . "#comment_succeed";
        redirect($redirPage);

    } else {
        setcookie('commnet_guest_status', '0');
        $_COOKIE['commnet_guest_name'] = $nickname;
        $_COOKIE['commnet_guest_email'] = $email;
        redirect("index.php#comment_failed");
    }

} else {
    setcookie('commnet_guest_status', '0');
    redirect("index.php");
    exit;
}
?>