<?php
//getWxHis.php 当前页面为推送时，获取公众号名称，微信号，阅读点赞量等
//linshunda@baixing.com
//2017.8.6

$workPath = dirname(dirname(__FILE__));
$accountInfoPath = "{$workPath}\\temp\\accountInfo.txt"; //以抓取的公众号
$outputPath = "{$workPath}\\result.csv";                //输出的csv路径
$queryRecordPath = "{$workPath}\\temp\\queryUrl.json";  //存储请求参数
$recordPath = "{$workPath}\\temp\\record.txt";			//结果的备份

//先获取到两个POST变量
$str = $_POST['str'];
$url = $_POST['url'];

//先针对url参数进行操作
parse_str(parse_url(htmlspecialchars_decode(urldecode($url)),PHP_URL_QUERY ),$query);//解析url地址
file_put_contents($queryRecordPath, json_encode($query,JSON_UNESCAPED_UNICODE),FILE_APPEND);

//获取url参数
$biz = $query['__biz'];    //得到公众号的biz
$sn = $query['sn'];		   //文章的标识
$title = $query['title'];  //文章标题
$idx = $query['idx'];
$mid = $query['mid'];
$article_url = "http://mp.weixin.qq.com/s?__biz={$biz}&mid={$mid}&idx={$idx}&sn={$sn}";    //拼接出文章链接
$content = file_get_contents($article_url);   //获取文章html内容

//利用xpath获取公众号名称和微信号
$dom = new DOMDocument();
$dom ->loadHTML($content);
$xml = simplexml_import_dom($dom);
$name = $xml->xpath('//strong[@class="profile_nickname"]')[0];    //名称
$id = $xml->xpath('//span[@class="profile_meta_value"]')[0];		//微信号

//再解析str变量
$json = json_decode(urldecode($str),true);//进行json_decode
$read_num = $json['appmsgstat']['read_num'];  //阅读量
$like_num = $json['appmsgstat']['like_num'];  //点赞量

//结果进行输出
$result = [$name,$id,$read_num,$like_num,$title,$article_url];
$output = fopen($outputPath, 'a');
fwrite($output,chr(0xEF).chr(0xBB).chr(0xBF));   //先发送BOM解决csv中文乱码问题
fputcsv($output, $result, ",");
file_put_contents($recordPath,implode(",",$result)."\n",FILE_APPEND);

//当前微信号添加至以抓取列表
$accountInfo = file_get_contents($accountInfoPath);     //读取公众号抓取记录
$accountInfo = json_decode($accountInfo);
$accountInfo->{$biz} = $id;								//将微信号写入记录中
file_put_contents($accountInfoPath, json_encode($accountInfo));
?>