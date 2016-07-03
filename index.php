<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Document</title>
    <style>
        html, body {
            margin: 0;
            padding: 0;
            font-family: "Open Sans", "lucida grande", "Segoe UI", arial, verdana, "lucida sans unicode", tahoma, sans-serif;
        }

        .warp {
            margin: 0 auto;
            width: 520px;
        }

        .comment_body {
            width: 500px;
            /*background-color: #f9f9f9;*/
            /*border: 1px solid #e2e2e2;*/
            margin: 6px 0 6px;
            padding: 3px;
            border-radius: 4px;
            clear: both;
            display: inline-block;
        }

        .comment_meta {
            width: 80px;
            float: left;
            margin: 3px;
        }

        .comment_content {
            width: 390px;
            padding: 10px;
            position: relative;
            float: right;
            border: 1px solid #dcdcdc;
            background-color: #f9f9f9;
            border-radius: 3px;
        }

        .header_box > img {
            width: 54px;
            text-align: center;
            margin: 0 auto;
            display: block;
        }

        .comment_text {
            clear: both;
            position: relative;
            color: #4e4e4e;
            font-size: 14px;

        }

        .comment_info {
            position: relative;
            bottom: 0;
            left: 0;
            color: #c6c6c6;
            font-size: 13px;
            padding: 10px 0 0 0;
        }

        .nickname_show {
            font-size: 12px;
            text-align: center;
            color: #585858;
        }

        .dot1, .dot2 {
            display: inline-block;
            border: 6px solid #dcdcdc;
            width: 1px;
            border-top: 6px solid transparent;
            border-bottom: 6px solid transparent;
            border-left: 6px solid transparent;
            position: relative;
            left: -23px;
        }

        .dot2 {
            left: -34px;
            border: 6px solid #f9f9f9;
            width: 1px;
            border-top: 6px solid transparent;
            border-bottom: 6px solid transparent;
            border-left: 6px solid transparent;
            position: relative;
        }

        .dot {
            display: inline-block;
            /* height: 0; */
            position: absolute;
        }

        .comment_succeed, .comment_faild {
            width: 94%;
            height: auto;
            text-align: center;
            font-size: 14px;
            margin: 0 auto
        }

        .comment_succeed > p, .comment_faild > p {
            margin: 5px;
            padding: 0;
        }

        .comment_succeed {
            background-color: #def8e2;
            color: #637564;
            border: 1px solid #9be49f;

        }

        .comment_faild {
            background-color: #fdbfbf;
            color: #832f2f;
            border: 2px solid #ff7c7c;
        }

        .form_post {
            width: 94%;
            margin: 0 auto;
            height: 186px;
            background-color: #f5f5f5;
            /* height: 300px; */
            margin-top: 10px;
            border: 1px solid #dedede;
            border-radius: 3px;
        }

        .form_content {
            width: 100%;
            margin: 0 auto;
        }

        .form_left {
            width: 225px;
            float: left;
            margin-left: 10px;
        }

        .form_left > input {
            width: 200px;
            height: 20px;
            margin: 10px 0 0 0;
            border: 1px solid #c3c3c3;
            border-radius: 3px;
            padding: 2px 5px 2px;
        }

        .form_right {
            width: 238px;
            float: left;
        }

        .form_right > textarea {
            width: 222px;
            border: 1px solid #c3c3c3;
            border-radius: 3px;
            height: 88px;
            margin-top: 10px;
            /* background-color: white; */
            resize: none;
            padding: 4px 6px 4px;
        }

        .form_right > p > input[type=submit] {
            position: relative;
            width: 90px;
            right: 3px;
            height: 29px;
            background-color: #2196F3;
            border: 1px solid #03A9F4;
            color: #fff;
            border-radius: 4px;
            font-family: "Open Sans", "lucida grande", "Segoe UI", arial, verdana, "lucida sans unicode", tahoma, sans-serif;
            cursor: pointer;

        }

        .btn_sub {
            text-align: right;

        }
    </style>
</head>
<body>
<div class="warp">
    <?php
    /**
     * Created by PhpStorm.
     * User: 星星
     * Date: 16/7/3
     * Time: 12:25
     */
    include 'conn.php';

    $sql_check_all_comment = <<<mia
select id,nickname,comment_content,time,region_city,header from comment order by id
mia;

    $result = mysqli_query($link, $sql_check_all_comment);
    $res = array();
    while ($my_result = mysqli_fetch_array($result)) {
        $res[] = $my_result;
    }
    mysqli_free_result($result);


    foreach ($res as $item) {
        echo '<div class="comment_body"><div class="comment_meta">';
        echo "<div class='header_box'><img src=" . $item['header'] . "></div><div class='nickname_show'>" . $item['nickname'] . "</div></div><div class='comment_content'>";
        echo '<div class="dot"><span class="dot1"></span><span class="dot2"></span></div><span class="comment_text">' . $item['comment_content'] . '</span>';
        echo '<div class="comment_info">' .
            '<span class="time">' . $item['time'] . '  </span>' .
            '<span class="region_city">' . $item['region_city'] . '</span>' .
            '</div>';
        echo '</div></div>';
    }
    //print_r($res);
    mysqli_close($link);
    ?>
    <?php
    if (isset($_COOKIE['commnet_guest_status']) && $_COOKIE['commnet_guest_status'] == '1') {
        $_COOKIE['commnet_guest_status'] = '';
        echo "<div class='comment_succeed'><p>评论成功!^_^</p></div>";
    } elseif (isset($_COOKIE['commnet_guest_status']) && $_COOKIE['commnet_guest_status'] == '0') {
        $_COOKIE['commnet_guest_status'] = '';
        echo "<div class='comment_faild'><p>评论失败，请检查输入:)</p></div>";
    }
    ?>
    <form action="wp-comments-post.php" method="post" class="form_post">
        <div class="form_content">
            <div class="form_left">
                <input type="text" placeholder="你的大名" required name="nickname" <?php
                if (isset($_COOKIE['commnet_guest_name'])) {
                    echo "value=" . $_COOKIE['commnet_guest_name'];
                }
                ?> >
                <input type="email" placeholder="E-mail" required name="email"
                    <?php
                    if (isset($_COOKIE['commnet_guest_email'])) {
                        echo "value=" . $_COOKIE['commnet_guest_email'];
                    }
                    ?> >
                <input type="text" name="site" placeholder="你的网站(可选)">
            </div>
            <div class="form_right">
                <textarea required placeholder="写下你的评论..." name="comment"></textarea>
                <p class="btn_sub"><input type="submit" name="submit" value="留个足迹"></div>
            </p>
        </div>
    </form>
    <?php
    if (isset($_COOKIE['commnet_guest_status']) && $_COOKIE['commnet_guest_status'] == '1') {
        $_COOKIE['commnet_guest_status'] = '';
        echo "<p>评论成功!^_^</p>";
    } elseif (isset($_COOKIE['commnet_guest_status']) && $_COOKIE['commnet_guest_status'] == '0') {
        $_COOKIE['commnet_guest_status'] = '';
        echo "<p>评论失败，请检查输入:)</p>";
    }
    ?>
</div>
</body>
</html>
