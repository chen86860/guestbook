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
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
</head>
<body>
<header>
    <p>留言墙</p>
</header>
<div class="warp">
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
    /** @var 选择行数 $sql_count_row */
    $sql_count_row = "select * from comment";
    $total = mysqli_num_rows(mysqli_query($link, $sql_count_row)); //查询数据的总数total
    $pagenum = ceil($total / $num);      //获得总页数 pagenum
    //假如传入的页数参数apge 大于总页数 pagenum，则显示错误信息
    if ($page > $pagenum || $page == 0) {
//        echo "error : Can Not Found The page .";exit;
        $page = 1;

    }
    $offset = ($page - 1) * $num;         //获取limit的第一个参数的值 offset ，假如第一页则为(1-1)*10=0,第二页为(2-1)*10=10。             (传入的页数-1) * 每页的数据 得到limit第一个参数的值

    $sql_check_all_comment = <<<mia
select id,nickname,comment_content,time,region_city,header from comment order by id limit $offset,$num 
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
                <p class="btn_sub"><input type="submit"  name="submit_S" value="留个足迹">
                </p></div>

        </div>
    </form>
</div>
</body>
</html>
