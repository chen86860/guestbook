<?php session_start(); ?>
    <!doctype html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>注册</title>
        <link href="../css/reg.css" rel="stylesheet" type="text/css"/>
    </head>
    <body>
    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="post">
        <div class="content">
            <p class="submit_head">注册</p>
            <ul>
                <li>
                    <p>
                    <span><input type="text" name="username" placeholder="用户名" required><label class="error_msg">
                            <?php
                            if (isset($_SESSION['error']) && $_SESSION['error'] == '1') {
                                $_SESSION['error'] = '';
                                echo "用户名不合规范";
                            }
                            ?>
                        </label></span></p>
                </li>
                <li>
                    <p>
                    <span><input type="text" name="nickname" placeholder="昵称" required><label class="error_msg">
                               <?php
                               if (isset($_SESSION['error']) && $_SESSION['error'] == '2') {
                                   $_SESSION['error'] = '';
                                   echo "昵称不合规范";
                               }
                               ?>
                        </label></span></p>
                </li>
                <li>
                    <p>
                    <span><input type="password" name="passwords" placeholder="密码" required><label class="error_msg">
                               <?php
                               if (isset($_SESSION['error']) && $_SESSION['error'] == '3') {
                                   $_SESSION['error'] = '';
                                   echo "密码不合规范";
                               }
                               ?>
                        </label></span></p>
                </li>
                <li>
                    <p class="p_submit">
                        <label class="error_msg sub_lable">
                            <?php
                            if (isset($_SESSION['error']) && $_SESSION['error'] == '4') {
                                $_SESSION['error'] = '';
                                echo "用户名已存在:(";
                            }
                            ?>
                        </label>
                        <span><input type="submit" class="submit_btn" value="注册" name="submit"></span></p>
                </li>
            </ul>
        </div>
    </form>
    </body>
    </html>
<?php
/**
 * Created by PhpStorm.
 * User: JACK
 * Date: 16/6/26
 * Time: 23:19
 */


function redirect($string)
{
    echo '<script language = \'javascript\' type = \'text/javascript\' > ';
    echo "window.location.href = '$string' ";
    echo '</script>';
}

//if (!isset($_POST['submit'])) {
//    echo '老大不要黑我。。。';
//    header("Location:index.html");
//    exit;
//}
//if (!isset($_POST['username']) || !isset($_POST['passwords']) || !isset($_POST['email'])) {
//    echo '老大不要黑我。。。';
//    header("Location:index.html");
//    exit;
//}

//数据验证
//用户名
$patten_user = '/^[a-zA-z][a-zA-Z0-9_]{3,9}$/';
if (isset($_POST['username'])) {
    if (preg_match($patten_user, $_POST['username'])) {
        $username = $_POST['username'];
        $_SESSION['tmp_username'] = $username;
    } else {
        $_SESSION['error'] = '1';
        redirect("reg.php");
//        exit;
        return;
    }
} else {
    exit;
}

//匹配昵称
$patten_nickname = '/^[a-zA-z][a-zA-Z0-9_]{3,9}$/';
if (preg_match($patten_nickname, $_POST['nickname'])) {
    $nickname = $_POST['nickname'];
} else {
    $_SESSION['error'] = '2';
    redirect("reg.php");
    exit;
}

//匹配密码
$patten_psw = '/^[\@A-Za-z0-9\!\#\$\%\^\&\*\.\~]{6,22}$/';
if (isset($_POST['username'])) {
    if (preg_match($patten_psw, $_POST['passwords'])) {
        $passcode = $_POST['passwords'];
    } else {
        $_SESSION['error'] = '3';
        redirect("admin.php");
        exit;
    }
} else {
    exit;
}




//用户名进行加密保存
//md5(str,username+salt)
$salt_user = $username.'0_1,z+';
$username=md5(md5($username,$salt_user));

//密码进行加密保存
//md5(str,username+salt)
$salt_paw=$username."2;`_d,'4";
$passcode=md5(md5($passcode.$salt_paw));


include "../conn.php";

//判断用户是否注册
$sql_check_user = <<<mia
select id  from userdata where username='$username'
mia;

$pre_result = mysqli_query($link, $sql_check_user);
if (mysqli_fetch_array($pre_result)!=null) {
    $_SESSION['error'] = '4';//用户名已被注册
    redirect("reg.php");
    mysqli_close($link);
    exit;
}

//设置头像
$path_img = "/guestbook/userheader/lianmeng_header";
$header_img = $path_img . rand(1, 45) . '.jpg';


$reg_sql = <<<mia
insert into userdata(username,passwords,nickname,header) VALUES ('$username','$passcode','$nickname','$header_img')
mia;

mysqli_query($link, $reg_sql);
//TODO:THERE IS SHOULD I DO

if (mysqli_affected_rows($link)) {
    $_SESSION['username'] = $username;
    $_SESSION['nickname'] = $nickname;
    $_SESSION['header'] = $header_img;
    mysqli_close($link);
    redirect("admin.php");
} else {
    echo 'failed';
}
?>