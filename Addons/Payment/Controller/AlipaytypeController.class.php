<?php

namespace Addons\Payment\Controller;

use Home\Controller\AddonsController;

class AlipaytypeController extends AddonsController {
	public $token;
	public $wecha_id;
	public $alipayConfig;
	public function __construct() {
		$this->token = get_token ();
		$this->wecha_id = get_openid ();
		
		// 读取支付配置
		$alipay_config_db = M ( 'payment_set' );
		$this->alipayConfig = $alipay_config_db->where ( array (
				'token' => $this->token 
		) )->find ();
	}
	public function pay() {
		// 参数数据
		// 订单名称,没有取当前 Unix 时间戳和微秒数
		$orderName = $_GET ['orderName'];
		if (! $orderName) {
			$orderName = microtime ();
		}
		// 订单编号
		$orderid = $_GET ['orderid'];
		if ($orderid == "") {
			$orderid = $_GET ['single_orderid'];
		}
		// 开始支付
		$price = $_GET ['price'];
		//
		$alipayConfig = $this->alipayConfig;
		//
		if (! $price) {
			exit ( '必须有价格才能支付' );
		}
		;
		
		// 支付类型
		$payment_type = "1";
		// 必填，不能修改
		// 服务器异步通知页面路径
		$notify_url = addons_url ( 'Payment://Alipaytype/notify_url' );
		// 需http://格式的完整路径，不能加?id=123这类自定义参数
		// 页面跳转同步通知页面路径
		$return_url = addons_url ( 'Payment://Alipaytype/return_url', array (
				"token" => $_GET ['token'],
				"wecha_id" => $_GET ['wecha_id'],
				"from" => $_GET ['from'] 
		) );
		// 需http://格式的完整路径，不能加?id=123这类自定义参数，不能写成http://localhost/
		// 卖家支付宝帐户
		$seller_email = trim ( $alipayConfig ['zfbname'] );
		// 商户订单号
		$out_trade_no = $orderid;
		// 商户网站订单系统中唯一订单号，必填
		// 订单名称
		$subject = $orderName;
		// 必填
		// 付款金额
		$total_fee = floatval ( $price );
		
		$body = $orderName;
		// 商品展示地址
		$show_url = C ( 'site_url' ) . U ( 'Home/Index/price' );
		// 需以http://开头的完整路径，例如：http://www.xxx.com/myorder.html
		
		// 防钓鱼时间戳
		$anti_phishing_key = "";
		// 若要使用请调用类文件submit中的query_timestamp函数
		
		// 客户端的IP地址
		$exter_invoke_ip = "";
		// 非局域网的外网IP地址，如：221.0.0.1
		$body = $subject;
		$show_url = rtrim ( C ( 'site_url' ), '/' );
		// 构造要请求的参数数组，无需改动
		$parameter = array (
				"service" => "create_direct_pay_by_user",
				"partner" => trim ( $alipayConfig ['pid'] ),
				"payment_type" => $payment_type,
				"notify_url" => $notify_url,
				"return_url" => $return_url,
				"seller_email" => $seller_email,
				"out_trade_no" => $out_trade_no,
				"subject" => $subject,
				"total_fee" => $total_fee,
				"body" => $body,
				"show_url" => $show_url,
				"anti_phishing_key" => $anti_phishing_key,
				"exter_invoke_ip" => $exter_invoke_ip,
				"_input_charset" => trim ( strtolower ( 'utf-8' ) ) 
		);
		
		require_once ("Alipay/AlipaySubmit.class.php");
		// 建立请求
		$alipaySubmit = new AlipaySubmit ( $this->setconfig () );
		$html_text = $alipaySubmit->buildRequestForm ( $parameter, "get", "进行支付" );
		header ( "Content-type: text/html; charset=utf-8" );
		echo '正在跳转到支付宝进行支付...<div style="display:none">' . $html_text . '</div>';
	}
	public function setconfig() {
		$alipay_config ['partner'] = trim ( $this->alipayConfig ['pid'] );
		// 安全检验码，以数字和字母组成的32位字符
		$alipay_config ['key'] = trim ( $this->alipayConfig ['key'] );
		// ↑↑↑↑↑↑↑↑↑↑请在这里配置您的基本信息↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑
		// 签名方式 不需修改
		$alipay_config ['sign_type'] = strtoupper ( 'MD5' );
		// 字符编码格式 目前支持 gbk 或 utf-8
		$alipay_config ['input_charset'] = strtolower ( 'utf-8' );
		// ca证书路径地址，用于curl中ssl校验
		// 请保证cacert.pem文件在当前文件夹目录中
		$alipay_config ['cacert'] = getcwd () . '\\Addons\\Payment\\Controller\\Alipay\\cacert.pem';
		// 访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
		$alipay_config ['transport'] = 'http';
		return $alipay_config;
	}
	// 同步数据处理
	public function return_url() {
		require_once ("Alipay/AlipayNotify.class.php");
		
		$alipayNotify = new AlipayNotify ( $this->setconfig () );
		$verify_result = $alipayNotify->verifyReturn ();
		if ($verify_result) {
			$out_trade_no = $this->_get ( 'out_trade_no' );
			// 支付宝交易号
			$trade_no = $this->_get ( 'trade_no' );
			// 交易状态
			$trade_status = $this->_get ( 'trade_status' );
			if ($this->_get ( 'trade_status' ) == 'TRADE_FINISHED' || $this->_get ( 'trade_status' ) == 'TRADE_SUCCESS') {
				$okurl = addons_url ( $_GET ['from'], array (
						"token" => $_GET ['token'],
						"wecha_id" => $_GET ['wecha_id'],
						"orderid" => $out_trade_no 
				) );
				redirect ( $okurl );
			} else {
				exit ( '付款失败' );
			}
		} else {
			exit ( '不存在的订单' );
		}
	}
	public function notify_url() {
		echo "success";
		eixt ();
	}
}
?>