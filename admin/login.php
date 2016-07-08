<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>登录</title>
    <script src="../js/jquery-3.0.0.min.js" type="text/javascript"></script>
    <link href="../css/login.css" type="text/css" rel="stylesheet">
</head>
<body>

<form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) ?>" name="LoginForm"
      onkeydown="keydown()">
    <div class="content">
        <p class="submit_head">登录</p>
        <ul>
            <li>
                <p>
                    <span><input type="text" name="username" id="username" placeholder="用户名/邮箱"
                            <?php
                            if (isset($_SESSION['tmp_username'])) {
                                $tmp_user = $_SESSION['tmp_username'];
                                echo "value=$tmp_user";
                            }
                            ?>
                                 required><label>
                        </label></span></p>
            </li>
            <li>
                <p>
                    <span><input type="password" name="passwords" id="password" placeholder="密码"
                                 required><label></label></span>
                </p>
            </li>
            <li>
                <p>
                    <span class="erro_style">
                        <?php
                        if (isset($_SESSION["error"]) && $_SESSION['error'] == '1') {
                            $_SESSION['error'] = '';
                            echo("用户名或密码错误！");
                        }
                        ?>
                    </span>
                    <span><input type="submit" class="submit_btn" value="登录" name="submit"></span></p>
            </li>
        </ul>
    </div>
</form>
<script type="text/javascript">
    $(document).ready(function () {
        if ($("#username").val() == "") {
            $("#username").focus();
        } else {
            $("#password").focus()
        }

        $(".erro_style").animate({
            opacity: '0'
        }, 2600, "easeInOutExpo")
    });
    function keydown() {
        if (event.keycode == 13) {
            event.returnvalue = false;  //不刷新界面
            form.btnok.click(); //表单提交
        }
    }

</script>
</body>
</html>
<?php
/**
 * Created by PhpStorm.
 * User: 星星
 * Date: 16/6/27
 * Time: 13:53
 */

function redirect($string)
{
    echo '<script language = \'javascript\' type = \'text/javascript\' > ';
    echo "window.location.href = '$string' ";
    echo '</script>';
}

//注销登录
if (isset($_GET['action'])) {
    if ($_GET['action'] == "logout") {
        if (isset($_SESSION['username'])) {
            unset($_SESSION['username']);
            unset($_SESSION['id']);
        }
        exit;
    }
}

//数据验证
//用户名
$patten_user = '/^[a-zA-z][a-zA-Z0-9_]{3,9}$/';
if (isset($_POST['username'])) {
    if (preg_match($patten_user, $_POST['username'])) {
        $username = $_POST['username'];
        $_SESSION['tmp_username'] = $username;
    } else {
        $_SESSION['error'] = '1';
        redirect("login.php");
        exit;
    }
} else {
    exit;
}
//匹配密码
$patten_psw = '/^[\@A-Za-z0-9\!\#\$\%\^\&\*\.\~]{6,22}$/';
if (isset($_POST['username'])) {
    if (preg_match($patten_psw, $_POST['passwords'])) {
        $passcode = $_POST['passwords'];
    } else {
        $_SESSION['error'] = '1';
        redirect("login.php");
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

//数据库连接
include "../conn.php";

//检测用户名及密码是否正确
$check_query = mysqli_query($link, /** @lang 选择用户名 */
    "select username,nickname,header from userdata where username='$username' and passwords='$passcode' limit 1");
if ($result = mysqli_fetch_array($check_query)) {
    //登录成功
    $_SESSION['username'] = $username;
    $_SESSION['nickname'] = $result['nickname'];
    $_SESSION['header'] = $result['header'];
    redirect("admin.php");
    exit;
} else {
    $_SESSION['error'] = '1';
    redirect("login.php");
}
?>
