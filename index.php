<?php
$comment_status = '';
if (isset($_COOKIE['commnet_guest_status']) && $_COOKIE['commnet_guest_status'] == '1') {
    setcookie('commnet_guest_status', '1', time() - 1);
    $comment_status = '1';  //1表示成功评论
} elseif (isset($_COOKIE['commnet_guest_status']) && $_COOKIE['commnet_guest_status'] == '0') {
    setcookie('commnet_guest_status', '0', time() - 1);
    $comment_status = '0';  //0表示失败评论
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>留言墙</title>
    <link href="css/index.css" rel="stylesheet" type="text/css">
    <script src="./js/jquery-3.0.0.min.js" type="text/javascript"></script>
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
</head>
<body>

<div class="warp">
    <header>
        <p>留言墙</p>
    </header>
    <div class="comment_warp">
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
        include 'conn.php';
        //选择要操作的数据库
        $sql_count_row = "select * from comment where comment_type = 1";
        $total = mysqli_num_rows(mysqli_query($link, $sql_count_row)); //查询数据的总数total
        $pagenum = ceil($total / $num);      //获得总页数 pagenum
        //假如传入的页数参数apge 大于总页数 pagenum，则显示错误信息
        if ($page > $pagenum || $page == 0) {
            $page = 1;

        }
        $offset = ($page - 1) * $num;         //获取limit的第一个参数的值 offset ，假如第一页则为(1-1)*10=0,第二页为(2-1)*10=10。             (传入的页数-1) * 每页的数据 得到limit第一个参数的值

        $sql_check_all_comment = <<<mia
select id,nickname,comment_content,time,region_city,header from comment where comment_type=1 order by time  limit $offset,$num 
mia;


        $result = mysqli_query($link, $sql_check_all_comment);
        $res = array();
        while ($my_result = mysqli_fetch_array($result)) {
            $res[] = $my_result;
        }
        mysqli_free_result($result);

        //读取存在cookie的用户名
        if (isset($_COOKIE['commnet_guest_name'])) {
            $commnet_guest_name = $_COOKIE['commnet_guest_name'];
        } else {
            $commnet_guest_name = "";
        }
        if (isset($_COOKIE['commnet_guest_email'])) {
            $commnet_guest_email = $_COOKIE['commnet_guest_email'];
        } else {
            $commnet_guest_email = "";
        }

        foreach ($res as $item) {
            echo "<div class='comment_body'" . " id=comment_" . $item['id'] . "><div class='comment_meta'>";
            echo "<div class='header_box'><img src=" . $item['header'] . "></div><div class='nickname_show'>" . $item['nickname'] . "</div></div><div class='comment_content'>";
            echo '<div class="dot_left"></span></div><span class="comment_text">' . $item['comment_content'] . '</span>';
            echo '<div class="comment_info">' .
                '<span class="clock_img"><img src="img/clock.png"></span>' .
                '<span class="time">' . $item['time'] . '  </span>' .
                '<span class="region_city">' . $item['region_city'] . '</span>' .
                '<span class="reply_item">' . '<a href=#' . $item['id'] . "  onclick=onRelpy(this,'" . $item['nickname'] . "'," . $page . ",'" . $commnet_guest_name . "','" . $commnet_guest_email . "','" . $item['id'] . "'" . ')>回复</a></span>' .
                '</div>';
            echo '</div>';


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
                echo '<div class="dot_top"></span></div><span class="admin_comment_text">' . $item_reply['comment_content'] . '</span>';
                echo '<div class="comment_info">' .
                    '<span class="clock_img"><img src="./img/clock.png"></span>' .
                    '<span class="time">' . $item_reply['comment_time'] . '  </span>' .
                    '<span class="region_city">' . $item_reply['region_city'] . '</span>' .
                    '</div>';
                echo '</div></div>';
            }
            echo '</div>';
            mysqli_free_result($result_reply);

        }
        //print_r($res);
        mysqli_close($link);

        //页码打印
        echo "<div class='page_rows'><p>";
        $page_priv = intval($page) - 1;
        $page_bak = intval($page) + 1;
        $page_end = intval($pagenum);

        //首页
        if (intval($page) == '1') {
            echo '<a' . ' disabled="true"><span><<</span></a>';
        } else {
            echo '<a href=' . 'index.php?page=1' . '><span><<</span></a>';
        }
        //前一页
        if (intval($page) == '1') {
            echo '<a' . ' disabled="true"><span><</span></a>';
        } else {
            echo '<a href=' . 'index.php?page=' . $page_priv . '><</a>';
        }

        //打印页面
        for ($i = 1; $i <= $pagenum; $i++) {
            $show = ($i != $page) ? "<a href='index.php?page=" . $i . "'>$i</a>" : "<span class='page_selectd'>$i</span>";
            echo $show . " ";
        }

        //后一页
        if ($page == $page_end) {
            echo '<a' . ' disabled="true"><span>></span></a>';
        } else {
            echo "<a href=" . "index.php?page=" . $page_bak . ">></a>";
        }

        //尾页
        if ($page == $page_end) {
            echo "<a " . "'><span>>></span></a></p></div>";
        } else {
            echo "<a href='index.php?page=" . $page_end . "'><span>>></span></a></p></div>";
        }
        if ($comment_status == '1') {
            echo "<div class='comment_succeed' id='comment_succeed'><p>评论成功!^_^</p></div>";
        } elseif ($comment_status == '0') {
            echo "<div class='comment_faild' id='comment_failed'><p>评论失败，请检查输入:)</p></div>";
        }
        ?>
    </div>
    <form action="wp-comments-post.php" method="post" class="form_post" onkeydown="keydown()">
        <div class="form_content">
            <span class="dot_top"></span>
            <span class="squre_dot"></span>
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
                <input type='text' class='e_hidden' name='view_page' value='<?php echo $pagenum ?>'>
                <p class="btn_sub"><input type="submit" name="submit" value="留个足迹" alt='留个足迹'>
                </p></div>

        </div>
    </form>
    <footer>©Jack Chen.2016---</footer>
</div>
<script type="text/javascript">
    function keydown() {
        if (event.keycode == 13) {
            event.returnvalue = false;  //不刷新界面
            form.btnok.click(); //表单提交
        }
    }
    function onRelpy(node, guest_nickname, page, commnet_guest_name, commnet_guest_email, comment_id) {
        var domtree = $(node).parent().parent().parent().parent();
        _commnet_guest_name = commnet_guest_name;
        _commnet_guest_email = commnet_guest_email;
        _page = page;
        var form_context = "<form action='wp-comments-post.php' method='post' class='form_post' onkeydown='keydown()'>" +
            "<div class='form_content'> <span class='dot_top'></span><div class='form_left'><input type='text' placeholder='你的大名' value='" + commnet_guest_name + "'  name='nickname' required>" +
            "<input type='email' placeholder='E-mail' required name='email' value=" + commnet_guest_email + ">" +
            "<input type='text' name='site' placeholder='你的网站(可选)'>" +
            "</div> <div class='form_right'><textarea required placeholder='写下你的评论...' name='comment'></textarea>" +
            "<input type='text' class='e_hidden' name='__guest_nickname' value='" + guest_nickname + "'>" +
            "<input type='text' class='e_hidden' name='view_page' value='" + page + "'>" +
            "<input type='text' class='e_hidden' name='comment_id' value='" + comment_id + "'>" +
            "<p class='btn_sub'>" +
            "<a  href='#' onclick='onRestore()'>撤销</a>" +
            "<input type='submit' name='comment_submit'  alt='留个足迹' value='留个足迹'></p></div></div></form>";
//        console.log(form_context);
        //先移除，再添加！！！
        $("form").remove();
        domtree.after(form_context);
    }
    function onRestore(commnet_guest_name, commnet_guest_email) {
        var form_context = "<form action='wp-comments-post.php' method='post' class='form_post' onkeydown='keydown()'>" +
            "<div class='form_content'>  <span class='dot_top'></span><div class='form_left'><input type='text' placeholder='你的大名' value=" + _commnet_guest_name + "  name='nickname' required>" +
            "<input type='email' placeholder='E-mail' required name='email' value=" + _commnet_guest_email + ">" +
            "<input type='text' name='site' placeholder='你的网站(可选)'>" +
            "</div> <div class='form_right'><textarea required placeholder='写下你的评论...' name='comment'></textarea>" +
            "<p class='btn_sub'>" +
            "<input type='text' class='e_hidden' name='view_page' value='" + _page + "'>" +
            "<input type='submit' name='submit' value='留个足迹' alt='留个足迹' ></p></div></div></form>";
        $("form").remove();
        $(".comment_warp").after(form_context);
    }
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
</script>
</body>
</html>
