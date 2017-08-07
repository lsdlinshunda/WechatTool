<?php
//getWxHis.php 当前页面为公众号历史消息时，读取这个程序
//linshunda@baixing.com
//2017.8.6

$workPath = dirname(dirname(__FILE__));
$urlFile = "{$workPath}\\temp\\url.txt";

$f = fopen($urlFile, "r");
$url = fgets($f);     //读取链接列表里的第一条记录
$url = str_replace("\n", "", $url);
$url = str_replace("\r", "", $url);

//将第一条记录之后的内容写回列表
ob_start();
fpassthru($f);
fclose($f);
file_put_contents($urlFile, ob_get_clean() );

//将下一个将要跳转的$url变成js脚本，由anyproxy注入到微信页面中。
echo $url;
if ($url != "")
	echo "<script>setTimeout(function(){window.location.href='".$url."';},1000);</script>";
?>

