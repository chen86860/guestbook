<?php session_start();
date_default_timezone_set('PRC');
if (!isset($_SESSION['username'])) {
    echo "<script  type='text/javascript''>";
    echo "window.location.href='../index.php'";
    echo "</script>";
    exit;
}
$comment_status = "";
if (isset($_SESSION['comment_status'])) {
    if ($_SESSION['comment_status'] == '1') {
        $comment_status = "1";
        unset($_SESSION['comment_status']);
    } else {
        unset($_SESSION['comment_status']);
        $comment_status = "0";
    }
} else {
    unset($_SESSION['comment_status']);
    $comment_status = "";
}

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>留言墙</title>
    <link href="../css/index.css" rel="stylesheet" type="text/css">
    <script src="../js/jquery-3.0.0.min.js" type="text/javascript"></script>
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <style type="text/css">
        .reply_item > a, .del_item > a {
            visibility: hidden;
            text-decoration: none;
            /*visibility: visible;*/
            color: crimson;
            margin-left: 10px;
            /*transition: visibility 1s ease;*/
        }

        .reply_item > a {
            color: #607d8b;
        }

        .e_hidden {
            display: none;
        }

        .del_status {
            display: inline-block;
            text-align: center;
            border: 1px solid #ddd;
            height: 25px;
            margin: 0 auto;
            line-height: 24px;
            width: 511px;
            font-size: 13px;
            top: 0;
            z-index: 99;
            position: fixed;
            border-radius: 3px;
        }

        .del_suc {
            background-color: #def8e2;
            color: #637564;
            border: 1px solid #9be49f;
        }

        .del_err {
            background-color: #fdbfbf;
            color: #832f2f;
            border: 1px solid #ff7c7c;
        }

        .form_content > textarea {
            width: 344px;
            border: 1px solid #ddd;
            resize: none;
            border-radius: 3px;
            padding: 3px 4px 3px;
        }

        .form_post {
            width: 78.0%;
            margin: 0 auto;
            height: 46px;
            margin-left: 91px;
            padding-right: 6px;
            padding-top: 8px;
            background-color: #f5f5f5;
            /* height: 300px; */
            margin-top: 0;
            border: 1px solid #dedede;
            border-radius: 3px;
            text-align: right;
        }

        .btn_sub {
            /*height: 38px;*/
            text-align: right;
            top: -21px;
            background-color: #ddd;
            /* margin-top: -40px; */
            position: relative;
            /*display: inline-block;*/
        }

        .btn_sub > input {
            cursor: pointer;
            height: 38px;
            top: 6px;
            border: 1px solid #2196F3;
            position: relative;
            background-color: #2196F3;
            outline: none;
            border-radius: 3px;
            color: #fff;
        }

        .form_content {
            width: 100%;
            margin: 0 auto;
            padding-top: 0;
        }
    </style>
</head>
<body>


<div class="warp">
    <header>
        <?php
        $patten_user = '/^[\x{4e00}-\x{9fa5}A-Za-z0-9_\s]+$/u';
        if (isset($_GET['del'])) {
            if (preg_match($patten_user, $_GET['del'])) {
                $del = $_GET['del'];
                include "../conn.php";
                /** @var del comment $sql_del_comment */
                $sql_del_comment = "delete from comment WHERE id='$del'";
                $sql_del_comment_guset = "delete from comment_guest WHERE comment_floor='$del'";
                mysqli_query($link, $sql_del_comment_guset);
                $result_del = mysqli_query($link, $sql_del_comment);
                if (mysqli_affected_rows($link)) {
                    echo "<span class='del_status del_suc'>删除成功^_^</span>";
                } else {
                    echo "<span class='del_status del_err'>删除失败;(</span>";
                }


            } else {
                echo "<script  type='text/javascript''>";
                echo "window.location.href='admin.php'";
                echo "</script>";
                exit;
            }

        }
        if ($comment_status != "") {
            if ($comment_status == "1") {
                $comment_status = "";
                echo "<span class='del_status del_suc'>回复成功^_^</span>";

            } elseif ($comment_status === "0") {
                $comment_status = "";
                echo "<span class='del_status del_err'>回复失败;(</span>";
            }
        }
        ?>

        <p>留言墙后台管理</p>
    </header>
    <?php
    /**
     * Created by PhpStorm.
     * User: 星星
     * Date: 16/7/3
     * Time: 12:25
     */


    //site
    //PHP的正则匹配要求两端对称！
    // 可以用~ @ # 来标志开头和结尾
    $patten_page = '/^\d{0,3}$/';
    if (isset($_GET['page'])) {
        if (preg_match($patten_page, $_GET['page'])) {
            $page = intval($_GET['page']);
        } else {
            $page = 1;
        }

    } else {
        $page = 1;
    }

    $num = 10;         //每页显示10条数据
    include '../conn.php';
    //选择要操作的数据库
    /** @var 选择行数 $sql_count_row */
    $sql_count_row = "select * from comment where comment_type = 1";
    $total = mysqli_num_rows(mysqli_query($link, $sql_count_row)); //查询数据的总数total
    $pagenum = ceil($total / $num);      //获得总页数 pagenum
    //假如传入的页数参数apge 大于总页数 pagenum，则显示错误信息
    if ($page > $pagenum || $page == 0) {
        $page = 1;

    }
    $offset = ($page - 1) * $num;         //获取limit的第一个参数的值 offset ，假如第一页则为(1-1)*10=0,第二页为(2-1)*10=10。             (传入的页数-1) * 每页的数据 得到limit第一个参数的值

    //用户评论列表
    $sql_check_all_comment = <<<mia
select id,nickname,comment_content,time,region_city,header from comment where comment_type=1 order by time  limit $offset,$num 
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
            '<span class="clock_img"><img src="../img/clock.png"></span>' .
            '<span class="time">' . $item['time'] . '  </span>' .
            '<span class="region_city">' . $item['region_city'] . '</span>' .
            '<span class="del_item">' . '<a href=./admin.php?page=' . $page . '&del=' . $item['id'] . ">删除" . "</a></span>" .
            '<span class="reply_item">' . '<a href=#' . $item['id'] . "  onclick=onRelpy(this,'" . $item['nickname'] . "','" . $page . "','" . $item['id'] . "')>回复</a></span>" .
            '</div></div>';


        //用户回复列表
        $comment_id = $item['id'];
        $sql_check_all_others_comment = <<<mia
select comment_nick_name,comment_content,comment_time,comment_header,region_city from comment_guest where comment_floor='$comment_id' order by comment_time
mia;
        $result_reply = mysqli_query($link, $sql_check_all_others_comment);
        $res_reply = array();

//        if (!empty(mysqli_fetch_array($result_reply))) {
        while ($my_result_reply = mysqli_fetch_array($result_reply)) {
            $res_reply[] = $my_result_reply;
        }
        foreach ($res_reply as $item_reply) {
            echo '<div class="admin_comment_body"><div class="admin_comment_meta">';
            echo "<div class='header_box'><img src=" . $item_reply['comment_header'] . "></div><div class='nickname_show'>" . $item_reply['comment_nick_name'] . "</div></div><div class='admin_comment_content'>";
            echo '<div class="dot"><span class="dot3"></span><span class="dot4"></span></div><span class="admin_comment_text">' . $item_reply['comment_content'] . '</span>';
            echo '<div class="comment_info">' .
                '<span class="clock_img"><img src="../img/clock.png"></span>' .
                '<span class="time">' . $item_reply['comment_time'] . '  </span>' .
                '</div>';
            echo '</div></div>';
        }
        echo '</div>';
        mysqli_free_result($result_reply);
    }
    //print_r($res);


    //页码打印
    echo "<div class='page_rows'><p>";
    $page_priv = intval($page) - 1;
    $page_bak = intval($page) + 1;
    $page_end = intval($pagenum);

    //首页
    if (intval($page) == '1') {
        echo '<a' . ' disabled="true"><span><<</span></a>';
    } else {
        echo '<a href=' . 'admin.php?page=1' . '><span><<</span></a>';
    }
    //前一页
    if (intval($page) == '1') {
        echo '<a' . ' disabled="true"><span><</span></a>';
    } else {
        echo '<a href=' . 'admin.php?page=' . $page_priv . '><</a>';
    }

    //打印页面
    for ($i = 1; $i <= $pagenum; $i++) {
        $show = ($i != $page) ? "<a href='admin.php?page=" . $i . "'>$i</a>" : "<span class='page_selectd'>$i</span>";
        echo $show . " ";
    }

    //后一页
    if ($page == $page_end) {
        echo '<a' . ' disabled="true"><span>></span></a>';
    } else {
        echo "<a href=" . "admin.php?page=" . $page_bak . ">></a>";
    }

    //尾页
    if ($page == $page_end) {
        echo "<a " . "'><span>>></span></a></p></div>";
    } else {
        echo "<a href='admin.php?page=" . $page_end . "'><span>>></span></a></p></div>";
    }

    mysqli_close($link);
    ?>

</div>
<script type="text/javascript">
    $(document).ready(
        $(".comment_content").bind('mouseenter', function () {
            $(this).find("a").css('visibility', 'visible')
        })
    )
    $(document).ready(
        $(".comment_content").bind("mouseleave", function () {
            $(this).find("a").css('visibility', 'hidden')
        })
    )
    $(document).ready(function () {
            $(".del_status").animate({
                opacity: '0'
            }, 4000, "easeOutQuad")
        }
    )

    function onRelpy(node, guest_nickname, page, comment_id) {
        var domtree = $(node).parent().parent().parent().parent();
        console.log(domtree);
        var form_context = "" +
            "<form action=" + "'../wp-comments-post.php' " + "method='post' class='form_post'>" +
            "<div class='form_content'>" +
            "<textarea required placeholder='请指示...' name='comment'></textarea>" +
            "<span class='btn_sub'>" +
            "<input type='text' class='e_hidden' name='__guest_nickname' value='" + guest_nickname + "'>" +
            "<input type='text' class='e_hidden' name='view_page' value='" + page + "'>" +
            "<input type='text' class='e_hidden' name='comment_id' value='" + comment_id + "'>" +
            "<input type='text' class='e_hidden' name='email' value='admin@admin.com'>" +
            "<input type='text' class='e_hidden' name='nickname' value='jack'>" +
            "<input type='submit' name='comment_submit' value='回复'>" +
            "</span>" + "</div>" + "</form>";
//        console.log(form_context);
        //先移除，再添加！！！
        $("form").remove();
        domtree.after(form_context);
    }

</script>
</body>
</html>
