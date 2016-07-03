<?php session_start();
if (!isset($_SESSION['username'])) {
    echo "<script  type='text/javascript''>";
    echo "window.location.href='index.php'";
    echo "</script>";
    exit;
} ?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>后台管理</title>
    <style>
        html, body {
            width: 100%;
            margin: 0;
            padding: 0;

        }

        .table_header > td {
            text-align: center;
        }

        .content {
            margin: 0 auto;
            width: 100%;
        }

        .table_content {
            margin: 0 auto;
        }

        .table_content > tbody > tr:nth-child(odd) {
            background-color: #e8e8e8;
        }

        .logout {
            border: none;
            cursor: pointer;
            text-align: center;
            color: #8a8a8a;
            background-color: #fff;
            text-decoration: underline;
        }


    </style>
</head>
<body>
<form action="logout.php" method="post">
    <input type="submit" value="log out" class="logout"></form>
<div class="content">
<!--    --><?php
//    /**
//     * Created by PhpStorm.
//     * User: 星星
//     * Date: 16/7/2
//     * Time: 14:24
//     */
//
//
//
//    $sql_query = <<<mia
//select * from useragent order by id;
//mia;
//
//    $res = array();
//    $my_result = mysqli_query($link, $sql_query);
//    echo "<table class='table_content'>
//<tr class='table_header'><td>ID</td><td>日期</td><td>IP地址</td><td>地区</td><td>省份/市</td><td>运营商</td><td>用户代理</td></tr>";
//    while ($result = mysqli_fetch_array($my_result)) {
//        $res[] = $result;
//    }
//    mysqli_free_result($my_result);
//    //print_r($res);
//    foreach ($res as $item) {
//        echo '<tr><td>' . $item['id'] . '</td>' .
//            '<td class="date">' . $item['date'] . '</td>' .
//            '<td class="ip_add">' . $item['ip_add'] . '</td>' .
//            '<td class="country_area">' . $item['country_area'] . '</td>' .
//            '<td class="region_city">' . $item['region_city'] . '</td>' .
//            '<td class="isp">' . $item['isp'] . '</td>' .
//            '<td class="userAgent">' . $item['userAgent'] . '</td>' .
//            '</tr>';
//    }
//    echo "</table>";
//    ?>
</div>
</body>
</html>