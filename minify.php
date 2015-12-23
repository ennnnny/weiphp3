<?php
/*
 * load: css, js 静态文件
 * 启用 gz压缩、缓存处理、过期处理、文件合并等优化操作
 */
if (extension_loaded ( 'zlib' )) {
	// 检查服务器是否开启了zlib拓展
	ob_start ( 'ob_gzhandler' );
}
define ( 'IS_CGI', (0 === strpos ( PHP_SAPI, 'cgi' ) || false !== strpos ( PHP_SAPI, 'fcgi' )) ? 1 : 0 );
if (IS_CGI) {
	// CGI/FASTCGI模式下
	$_temp = explode ( '.php', $_SERVER ['PHP_SELF'] );
	define ( '_PHP_FILE_', rtrim ( str_replace ( $_SERVER ['HTTP_HOST'], '', $_temp [0] . '.php' ), '/' ) );
} else {
	define ( '_PHP_FILE_', rtrim ( $_SERVER ['SCRIPT_NAME'], '/' ) );
}
$_root = rtrim ( dirname ( _PHP_FILE_ ), '/' );
define ( '__ROOT__', (($_root == '/' || $_root == '\\') ? '' : $_root) );

$allowed_content_types = array (
		'js',
		'css' 
);

$f = strip_tags ( $_GET ['f'] );
$getfiles = explode ( ',', $f );

// 解析参数
$gettype = (isset ( $_GET ['t'] ) && $_GET ['t'] == 'css') ? 'css' : 'js';

if ($gettype == 'css') {
	$content_type = 'text/css';
} elseif ($gettype == 'js') {
	$content_type = 'application/x-javascript';
} else {
	die ( 'not allowed content type' );
}

header ( "content-type: " . $content_type . "; charset: utf-8" ); // 注意修改到你的编码
header ( "cache-control: must-revalidate" ); //
header ( "expires: " . gmdate ( "D, d M Y H:i:s", time () + 86400 ) . " GMT" ); // 过期时间

ob_start ( "compress" );
function compress($buffer) { // 去除文件中的注释
	$buffer = preg_replace ( '!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer );
	return $buffer;
}

foreach ( $getfiles as $file ) {
	$fileType = strtolower ( substr ( $file, strrpos ( $file, '.' ) + 1 ) );
	if (in_array ( $fileType, $allowed_content_types )) {
		// 包含你的全部css文档
		$file = str_replace ( __ROOT__, '', $file );
		$file = ltrim ( $file, '/' );
		readfile ( $file );
	} else {
		echo 'not allowed file type:' . $file;
	}
}

// 输出buffer中的内容，即压缩后的css文件
if (extension_loaded ( 'zlib' )) {
	ob_end_flush ();
}
?>