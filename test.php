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

/*
Author:默默
Date :2006-12-03
*/

/*
首先咱们要获取数据库中到底有多少数据，才能判断具体要分多少页，总页数 具体的公式就是
总数据数 除以 每页显示的条数，有余进一 。
也就是说10/3=3.3333=4 有余数就要进一。
*/
include "conn.php";
$page=isset($_GET['page'])?intval($_GET['page']):1;        //这句就是获取page=18中的page的值，假如不存在page，那么页数就是1。
$num=10;         //每页显示10条数据
//选择要操作的数据库
/** @var 选择行数 $sql_count_row */
$sql_count_row= "select * from comment";
$total=mysqli_num_rows(mysqli_query($link,$sql_count_row)); //查询数据的总数total
$pagenum=ceil($total/$num);      //获得总页数 pagenum
//假如传入的页数参数apge 大于总页数 pagenum，则显示错误信息
If($page>$pagenum || $page == 0){
    echo "Error : Can Not Found The page .";
    exit;
}
$offset=($page-1)*$num;         //获取limit的第一个参数的值 offset ，假如第一页则为(1-1)*10=0,第二页为(2-1)*10=10。             (传入的页数-1) * 每页的数据 得到limit第一个参数的值
$info=mysqli_query($link,"select * from comment limit $offset,$num ");   //获取相应页数所需要显示的数据
while($it=mysqli_fetch_array($info)){
    echo $it['id']."<br />";
}                                                              //显示数据

for($i=1;$i<=$pagenum;$i++){
    $show=($i!=$page)?"<a href='index.php?page=".$i."'>$i</a>":"<b>$i</b>";
    echo $show." ";
}
/*显示分页信息，假如是当页则显示粗体的数字，其余的页数则为超连接，假如当前为第三页则显示如下
1 2 3 4 5 6
*/
?>