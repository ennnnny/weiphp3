<?php

namespace Addons\Payment\Controller;

use Home\Controller\AddonsController;

class AlipayController extends AddonsController {
	public $token;
	public $wecha_id;
	public $alipayConfig;
	public function __construct() {
		parent::__construct ();
		
		$this->token = get_token ();
		$this->wecha_id = get_openid ();
		
		// 读取配置
		$alipay_config_db = M ( 'payment_set' );
		$this->alipayConfig = $alipay_config_db->where ( array (
				'token' => $this->token 
		) )->find ();
	}
	
// 	//处理from URL字符串
// 	private function doFromStr($from){
// 		if($from){
// 			$fromstr=str_replace('_', '/', $from);
// 		}
// 		return $fromstr;
// 	}
	public function pay() {
		//dump(C('URL_CASE_INSENSITIVE'));
		//dump($_GET);exit;
		header ( "Content-type: text/html; charset=utf-8" );
		// 参数数据
		// 订单名称,没有取当前 Unix 时间戳和微秒数
		
		$orderName = $_GET ['orderName'];
		
		$orderNumber = $_GET ['orderNumber'];
		
		$price = $_GET ['price'];
		
		$token=$_GET['token'];
		$openid=$_GET['wecha_id'];
		
		
		//
		$from = isset ( $_GET ['from'] ) ? $_GET ['from'] : 'shop';
// 		if($from!='shop'){
// 			$from=$this->doFromStr($from);
// 		}
		//
		$alipayConfig = $this->alipayConfig;
		//
		if (! $price) {
			exit ( '必须有价格才能支付' );
		}
		;
		$paytype = I ( 'get.paytype', 0, 'intval' );
		$config = getAddonConfig ( 'Payment' );
		
		if ($config ["isopen"] == 0) {
			exit ( "支付功能未启用!" );
		}
		switch ($paytype) {
			case 0 :
				$alipayConfig ['paytype'] = 'Weixin';
				if ($config ["isopenwx"] == 0) {
					exit ( "支付功能未启用!" );
				}
				break;
			case 1 :
				$alipayConfig ['paytype'] = 'Alipaytype';
				if ($config ["isopenzfb"] == 0) {
					exit ( "支付宝支付功能未启用!" );
				}
				break;
			case 2 :
				$alipayConfig ['paytype'] = 'Tenpay';
				if ($config ["isopencftwap"] == 0) {
					exit ( "财付通WAP支付功能未启用!" );
				}
				break;
			case 3 :
				$alipayConfig ['paytype'] = 'TenpayComputer';
				if ($config ["isopencft"] == 0) {
					exit ( "财付通支付功能未启用!" );
				}
				break;
			case 4 :
				$alipayConfig ['paytype'] = 'Quickpay';
				if ($config ["isopenyl"] == 0) {
					exit ( "银联支付功能未启用!" );
				}
				break;
			default :
				$alipayConfig ['paytype'] = 'Alipaytype';
				if ($config ["isopenzfb"] == 0) {
					exit ( "支付宝支付功能未启用!" );
				}
				break;
		}
		$param = array (
				'from' => $from,
				'orderName' => $orderName,
				'single_orderid' => $orderNumber,
				'price' => $price,
				'token' => $token,
				'wecha_id' => $openid,
				'paytype' => $alipayConfig ['paytype'] 
		);
		if ($alipayConfig ['paytype'] == 'Weixin') {
			$param ['showwxpaytitle'] = 1;
		}
		$map['single_orderid']=$orderNumber;
		
		if($_GET['aim_id']){
		  $param['aim_id']=$_GET['aim_id'];
		}
		$res=M('payment_order')->where($map)->getField('id');
		$param['uid']=$this->mid;
		if ($res){
		    $paymentId=$res;
		}else {
		    $paymentId=M ( 'payment_order' )->add ( $param );
		}
		if ($paymentId){
			$param['paymentId']=$paymentId;
		}
		$url = addons_url ( "Payment://" . $alipayConfig ['paytype'] . "/pay", $param );
		//dump($url);die;
		header ( 'Location:' . $url );
// 		redirect ( $url);
	}
	

}
?>