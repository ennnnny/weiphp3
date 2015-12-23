<?php
// error_reporting ( E_ALL );
// ini_set ( 'display_errors', true );

error_reporting ( 0 );
ini_set ( 'display_errors', false );

$content = file_get_contents ( 'php://input' );
$data = new \SimpleXMLElement ( $content );
// $data || die ( '参数获取失败' );
foreach ( $data as $key => $value ) {
	$dataArr [$key] = strval ( $value );
}

echo '<xml>
<ToUserName><![CDATA[' . $dataArr ['FromUserName'] . ']]></ToUserName>
<FromUserName><![CDATA[' . $dataArr ['ToUserName'] . ']]></FromUserName>
<CreateTime>' . time () . '</CreateTime>
<MsgType><![CDATA[text]]></MsgType>
<Content><![CDATA[你好,这是从第三方返回的数据]]></Content>
</xml>';