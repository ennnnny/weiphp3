<?php

namespace Addons\Payment\Controller;

use Home\Controller\AddonsController;

class QuickpayController extends AddonsController {
	public $token;
	public $wecha_id;
	public $payConfig;
	public function __construct() {
		parent::__construct ();
		
		$this->token = get_token ();
		$this->wecha_id = get_openid ();
		// 读取配置
		$pay_config_db = M ( 'payment_set' );
		$this->payConfig = $pay_config_db->where ( array (
				'token' => $this->token 
		) )->find ();
	}
	public function pay() {
		require_once ("Quickpay/quickpay_service.php");
		quickpay_conf::$security_key = $this->payConfig ['quick_security_key'];
		quickpay_conf::$pay_params ["merId"] = $this->payConfig ['quick_merid'];
		quickpay_conf::$pay_params ["merAbbr"] = $this->payConfig ['quick_merabbr'];
		
		$orderid = $_GET ['orderid'];
		if ($orderid == "") {
			$orderid = $_GET ['single_orderid'];
		}
		$out_trade_no = $orderid;
		$price = $_GET ['price'];
		if (! $price) {
			exit ( '必须有价格才能支付' );
		}
		$orderName = $_GET ['orderName'];
		$total_fee = floatval ( $price );
		/* 创建支付请求对象 */
		// 下面这行用于测试，以生成随机且唯一的订单号
		mt_srand ( quickpay_service::make_seed () );
		
		$param ['transType'] = quickpay_conf::CONSUME; // 交易类型，CONSUME or PRE_AUTH
		
		$param ['orderAmount'] = $price; // 交易金额
		$param ['orderNumber'] = date ( 'YmdHis' ) . strval ( mt_rand ( 100, 999 ) ); // 订单号，必须唯一
		$param ['orderTime'] = date ( 'YmdHis' ); // 交易时间, YYYYmmhhddHHMMSS
		$param ['orderCurrency'] = quickpay_conf::CURRENCY_CNY; // 交易币种，CURRENCY_CNY=>人民币
		
		$param ['customerIp'] = $_SERVER ['REMOTE_ADDR']; // 用户IP
		                                                           // 页面跳转同步通知页面路径
		$return_url = addons_url ( 'Payment://Quickpay/return_url', array (
				"token" => $_GET ['token'],
				"wecha_id" => $_GET ['wecha_id'],
				"from" => $_GET ['from'] 
		) );
		$param ['frontEndUrl'] = $return_url; // 前台回调URL
		                                               // 服务器异步通知页面路径
		$notify_url = addons_url ( 'Payment://Quickpay/notify_url' );
		$param ['backEndUrl'] = $notify_url; // 后台回调URL
		
		/*
		 * 可填空字段
		 * $param['commodityUrl'] = "http://www.example.com/product?name=商品"; //商品URL
		 * $param['commodityName'] = '商品名称'; //商品名称
		 * $param['commodityUnitPrice'] = 11000; //商品单价
		 * $param['commodityQuantity'] = 1; //商品数量
		 * //
		 */
		
		// 其余可填空的参数可以不填写
		
		$pay_service = new quickpay_service ( $param, quickpay_conf::FRONT_PAY );
		$html = $pay_service->create_html ();
		
		header ( "Content-Type: text/html; charset=" . quickpay_conf::$pay_params ['charset'] );
		echo $html; // 自动post表单
	}
	// 同步数据处理
	public function return_url() {
		require_once ('Quickpay/quickpay_service.php');
		/* 密钥 */
		quickpay_conf::$security_key = $this->payConfig ['quick_security_key'];
		quickpay_conf::$pay_params ["merId"] = $this->payConfig ['quick_merid'];
		quickpay_conf::$pay_params ["merAbbr"] = $this->payConfig ['quick_merabbr'];
		
		/* 创建支付应答对象 */
		// * 测试数据
		$response = new quickpay_service ( $_POST, quickpay_conf::RESPONSE );
		if ($response->get ( 'respCode' ) != quickpay_service::RESP_SUCCESS) {
			$err = sprintf ( "Error: %d => %s", $response->get ( 'respCode' ), $response->get ( 'respMsg' ) );
			throw new Exception ( $err );
		}
		$arr_ret = $response->get_args ();
		// 交易完成
		// 更新数据库, 设置为交易成功
		$out_trade_no = $_GET ( "orderid" ); // $arr_ret['orderNumber'];
		$okurl = addons_url ( $_GET ['from'], array (
				"token" => $_GET ['token'],
				"wecha_id" => $_GET ['wecha_id'],
				"orderid" => $out_trade_no 
		) );
		redirect ( $okurl );
	}
	public function notify_url() {
		echo "success";
		eixt ();
	}
}
?>