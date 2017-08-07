<?php
//getMsgJson.php 当前页面为公众号历史消息时，抓取推送列表里推送的链接
//linshunda@baixing.com
//2017.8.6

$workPath = dirname(dirname(__FILE__));
$accountInfoPath = "{$workPath}\\temp\\accountInfo.txt";
$configPath = "{$workPath}\\config.json";
$urlFile = "{$workPath}\\temp\\url.txt";
$magListRecord = "{$workPath}\\temp\\magListJson.json";

//先获取到两个POST变量
$str = $_POST['str'];
$url = $_POST['url'];

//先针对url参数进行操作
parse_str(parse_url(htmlspecialchars_decode(urldecode($url)),PHP_URL_QUERY ),$query);//解析url地址
$accountInfo = file_get_contents($accountInfoPath);     //读取公众号抓取记录
$accountInfo = json_decode($accountInfo);
$config = file_get_contents($configPath);				//读取每个公众号抓取的推送数
$config = str_replace("'","\"",$config);
$config = json_decode($config);
$maxPostNum = $config->max_url_num ?? 10;

$biz = $query['__biz'];    //得到公众号的biz
if (!isset($accountInfo->{$biz})) {          //未抓取过推送链接的公众号
	$json = json_decode(urldecode($str), true);  //首先进行json_decode
	if (!$json) {
		$json = json_decode(htmlspecialchars_decode(urldecode($str)), true);//如果不成功，就增加一步htmlspecialchars_decode
	}
	file_put_contents($magListRecord, json_encode($json, JSON_UNESCAPED_UNICODE));
//	file_put_contents("url.txt", "");     //清空url列表

	$content_urls = [];
	$content_count = 0;
	foreach ($json['list'] as $k => $v) {
		$type = $v['comm_msg_info']['type'];
		if ($type == 49) {//type=49代表是图文消息
			$content_url = str_replace(
				"\\",
				"",
				htmlspecialchars_decode($v['app_msg_ext_info']['content_url'])
			);            //获得图文消息的链接地址
			if ($content_count++ < $maxPostNum) {
				file_put_contents($urlFile, $content_url . "\n", FILE_APPEND);
			}
			$is_multi = $v['app_msg_ext_info']['is_multi'];    //是否是多图文消息
			if ($is_multi == 1) {	 	//如果是多图文消息
				foreach ($v['app_msg_ext_info']['multi_app_msg_item_list'] as $kk => $vv) {//循环后面的图文消息
					$content_url = str_replace(
						"\\",
						"",
						htmlspecialchars_decode($vv['content_url'])
					);      //图文消息链接地址
					if ($content_count++ < $maxPostNum) {
						file_put_contents($urlFile, $content_url . "\n", FILE_APPEND);
					}
				}
			}
		}
	}
	$accountInfo->{$biz} =  $biz;			 //在抓取记录中添加当前公众号
}

file_put_contents($accountInfoPath, json_encode($accountInfo));
?>