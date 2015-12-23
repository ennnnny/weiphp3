<?php

namespace Addons\Payment\Controller;

use Addons\Payment\Controller\BaseController;

class PaymentController extends BaseController {
	function _initialize() {
		parent::_initialize ();
		
		$controller = strtolower ( _CONTROLLER );
		$action = strtolower ( _ACTION );
		
		if ($action != "config") {
			$config = getAddonConfig ( 'Payment' );
			
			if ($config ["isopenwx"] == 1) {
				$res ['title'] = '微信支付配置';
				$res ['url'] = addons_url ( 'Payment://Payment/lists' ,array('mdm'=>I('mdm')));
				$res ['class'] = $action == 'lists' ? 'cur' : '';
				$nav [] = $res;
			}
			
			if ($config ["isopenzfb"] == 1) {
				$res ['title'] = '支付宝配置';
				$res ['url'] = addons_url ( 'Payment://Payment/zfbpay',array('mdm'=>I('mdm')));
				$res ['class'] = $action == 'zfbpay' ? 'cur' : '';
				$nav [] = $res;
			}
			
			if ($config ["isopencftwap"] == 1) {
				$res ['title'] = '财付通(WAP手机接口)配置';
				$res ['url'] = addons_url ( 'Payment://Payment/cftwappay',array('mdm'=>I('mdm')));
				$res ['class'] = $action == 'cftwappay' ? 'cur' : '';
				$nav [] = $res;
			}
			
			if ($config ["isopencft"] == 1) {
				$res ['title'] = '财付通(即时到账接口)配置';
				$res ['url'] = addons_url ( 'Payment://Payment/ctfpay' ,array('mdm'=>I('mdm')));
				$res ['class'] = $action == 'ctfpay' ? 'cur' : '';
				$nav [] = $res;
			}
			
			if ($config ["isopenyl"] == 1) {
				$res ['title'] = '银联在线配置';
				$res ['url'] = addons_url ( 'Payment://Payment/quickpay',array('mdm'=>I('mdm')) );
				$res ['class'] = $action == 'quickpay' ? 'cur' : '';
				$nav [] = $res;
			}
			if ($config ["isopenshop"] == 1) {
				$res ['title'] = '到店支付配置';
				$res ['url'] = addons_url ( 'Payment://Payment/shoppay',array('mdm'=>I('mdm')) );
				$res ['class'] = $action == 'shoppay' ? 'cur' : '';
				$nav [] = $res;
			}
			
			$this->assign ( 'sub_nav', $nav );
		}
	}
	protected $model;
	public function __construct() {
		parent::__construct ();
		$this->model = M ( 'Model' )->getByName ( 'payment_set' );
		$this->model || $this->error ( '模型不存在！' );
		$this->assign ( 'model', $this->model );
	}
	//复制上传的证书到其他位置，暂时没被调用
	function uploadCert($fileId){
        $managerId=$this->mid;
	    $path=SITE_PATH.'\\Cert\\wxzf\\'.$managerId;

	    $file = M ( 'file' )->find ($fileId);
	    $file ['savepath']=substr($file['savepath'],0, strlen($file['savepath'])-1);
	    $filename = SITE_PATH . '\\Uploads\\Download\\' . $file ['savepath'] .'\\'. $file ['savename'];
//         dump($file);
//         dump($filename);
//         dump(file_exists($filename));
//         die;
	    if (! file_exists ( $filename )) {
	        $this->error('上传失败');
	        exit();
	    }
// 	    $extend = $file ['ext'];
// 	    if (! ($extend == 'pem' || $extend == 'p12' )) {
// 	        $this->error('上传证书格式有误，扩展名应为 pem');
// 	        exit();
// 	    }
	    
	    $mkres =  mkdirs ( $path );
	    $content = wp_file_get_contents ( $filename );
	    $res = file_put_contents ( $path.'\\'.$file['name'], $content );
	    return $res;
	}
	public function lists($type = 0) {
		// $normal_tips = '支持微信支付、财付通（WAP接口或者即时到帐接口）和支付宝支付（即时到帐接口）。请填写真实信息，否则支付中可能会出现错误<br/>&nbsp;&nbsp;&nbsp;&nbsp;
		// <a href="' . U ( 'testpay' ) . '">测试支付功能</a>';
		// $this->assign ( 'normal_tips', $normal_tips );
		$token = get_token ();
		
		// 获取模型信息
		$payid = M ( "payment_set" )->where ( array (
				"token" => $token 
		) )->field ( 'id' )->find ();
		$id = 0;
		if (! empty ( $payid )) {
			$id = $payid ["id"];
		}
		if (IS_POST) {
			$isadd = I ( 'get.isadd', 0, 'intval' );
			$savetype = I ( 'get.savetype', 0, 'intval' );
// 			dump($_POST);
// 			dump(get_file_title($_POST['wx_cert_pem']));
// 			die;
			if (strpos ( $_POST ["id"], "*" ) != false) {
				unset ( $_POST ["id"] );
			}
			if (strpos ( $_POST ["token"], "*" ) != false) {
				unset ( $_POST ["token"] );
			}
			if (strpos ( $_POST ["ctime"], "*" ) != false) {
				unset ( $_POST ["ctime"] );
			}
			if (strpos ( $_POST ["wxappid"], "*" ) != false) {
				unset ( $_POST ["wxappid"] );
			}
			if (strpos ( $_POST ["wxpaysignkey"], "*" ) != false) {
				unset ( $_POST ["wxpaysignkey"] );
			}
			if (strpos ( $_POST ["wxappsecret"], "*" ) != false) {
				unset ( $_POST ["wxappsecret"] );
			}
			if (strpos ( $_POST ["zfbname"], "*" ) != false) {
				unset ( $_POST ["zfbname"] );
			}
			if (strpos ( $_POST ["pid"], "*" ) != false) {
				unset ( $_POST ["pid"] );
			}
			if (strpos ( $_POST ["key"], "*" ) != false) {
				unset ( $_POST ["key"] );
			}
			if (strpos ( $_POST ["partnerid"], "*" ) != false) {
				unset ( $_POST ["partnerid"] );
			}
			if (strpos ( $_POST ["partnerkey"], "*" ) != false) {
				unset ( $_POST ["partnerkey"] );
			}
			if (strpos ( $_POST ["wappartnerid"], "*" ) != false) {
				unset ( $_POST ["wappartnerid"] );
			}
			if (strpos ( $_POST ["wappartnerkey"], "*" ) != false) {
				unset ( $_POST ["wappartnerkey"] );
			}
			if (strpos ( $_POST ["wxpartnerkey"], "*" ) != false) {
				unset ( $_POST ["wxpartnerkey"] );
			}
			if (strpos ( $_POST ["wxpartnerid"], "*" ) != false) {
				unset ( $_POST ["wxpartnerid"] );
			}
			if (strpos ( $_POST ["quick_security_key"], "*" ) != false) {
				unset ( $_POST ["quick_security_key"] );
			}
			if (strpos ( $_POST ["quick_merid"], "*" ) != false) {
				unset ( $_POST ["quick_merid"] );
			}
			if (strpos ( $_POST ["quick_merabbr"], "*" ) != false) {
				unset ( $_POST ["quick_merabbr"] );
			}
			if (strpos ( $_POST ["wxmchid"], "*" ) != false) {
			    unset ( $_POST ["wxmchid"] );
			}
			if (strpos ( $_POST ["wx_cert_pem"], "*" ) != false) {
			    unset ( $_POST ["wx_cert_pem"] );
			}
			if (strpos ( $_POST ["wx_key_pem"], "*" ) != false) {
			    unset ( $_POST ["wx_key_pem"] );
			}
			
// 			if ($_POST['wx_cert_pem']){
// 			    $this->uploadCert($_POST['wx_cert_pem']);
// 			}
			
			
			$Model = D ( parse_name ( get_table_name ( $this->model ['id'] ), 1 ) );
			if ($isadd == 1) {
				// 自动补充token
				$_POST ['token'] = $token;
				
				// 获取模型的字段信息
				$Model = $this->checkAttr ( $Model, $this->model ['id'] );
				if ($Model->create () && $micsetid = $Model->add ()) {
					switch ($savetype) {
						case 0 : // 微信支付
							$this->success ( '保存' . $this->model ['title'] . '成功！', U ( 'lists' ,array('mdm'=>I('mdm'))) );
							break;
						case 1 : // 支付宝
							$this->success ( '保存' . $this->model ['title'] . '成功！', U ( 'zfbpay' ,array('mdm'=>I('mdm'))) );
							break;
						case 2 : // 财付通wap
							$this->success ( '保存' . $this->model ['title'] . '成功！', U ( 'cftwappay' ,array('mdm'=>I('mdm'))) );
							break;
						case 3 : // 财付通
							$this->success ( '保存' . $this->model ['title'] . '成功！', U ( 'ctfpay' ,array('mdm'=>I('mdm'))) );
							break;
						case 4 : // 银联在线
							$this->success ( '保存' . $this->model ['title'] . '成功！', U ( 'quickpay' ,array('mdm'=>I('mdm'))) );
							break;
						case 5 : // 到店支付
							$this->success ( '保存' . $this->model ['title'] . '成功！', U ( 'shoppay' ,array('mdm'=>I('mdm'))) );
							break;
					}
				} else {
					$this->error ( $Model->getError () );
				}
			} else {
				// 获取模型的字段信息
				$Model = $this->checkAttr ( $Model, $this->model ['id'] );
				if ($Model->create () && $Model->save ()) {
					switch ($savetype) {
						case 0 : // 微信支付
							$this->success ( '保存' . $this->model ['title'] . '成功！', U ( 'lists' ,array('mdm'=>I('mdm'))) );
							break;
						case 1 : // 支付宝
							$this->success ( '保存' . $this->model ['title'] . '成功！', U ( 'zfbpay' ,array('mdm'=>I('mdm'))) );
							break;
						case 2 : // 财付通wap
							$this->success ( '保存' . $this->model ['title'] . '成功！', U ( 'cftwappay' ,array('mdm'=>I('mdm'))) );
							break;
						case 3 : // 财付通
							$this->success ( '保存' . $this->model ['title'] . '成功！', U ( 'ctfpay' ,array('mdm'=>I('mdm'))) );
							break;
						case 4 : // 银联在线
							$this->success ( '保存' . $this->model ['title'] . '成功！', U ( 'quickpay' ,array('mdm'=>I('mdm'))) );
							break;
						case 5 : // 到店支付
							$this->success ( '保存' . $this->model ['title'] . '成功！', U ( 'shoppay' ,array('mdm'=>I('mdm'))) );
							break;
					}
				} else {
					$this->error ( $Model->getError () );
				}
			}
		} else {
			$fields = get_model_attribute ( $this->model ['id'] );
			// 获取数据
			$data = M ( get_table_name ( $this->model ['id'] ) )->find ( $id );
			if ($data['wx_cert_pem'] && $data['wx_key_pem']){
			    $certFile = M ( 'file' )->where(array('id'=>$data['wx_cert_pem']))->getfield('name');
			    $keyFile = M ( 'file' )->where(array('id'=>$data['wx_key_pem']))->getfield('name');
			    $this->assign('certfile',$certFile);
			    $this->assign('keyfile',$keyFile);
			}
			// 是否新增
			$isadd = 0;
			if (empty ( $data )) {
				$isadd = 1;
			}
			$this->assign ( 'isadd', $isadd );
			$this->assign ( 'savetype', $savetype );
			// 排除字段
			switch ($type) {
				case 0 : // 微信支付
					unset ( $fields ["pid"] );
					unset ( $fields ["key"] );
					unset ( $fields ["zfbname"] );
					unset ( $fields ["partnerid"] );
					unset ( $fields ["partnerkey"] );
					unset ( $fields ["wappartnerid"] );
					unset ( $fields ["wappartnerkey"] );
					unset ( $fields ["quick_security_key"] );
					unset ( $fields ["quick_merid"] );
					unset ( $fields ["quick_merabbr"] );
					unset ( $fields ["shop_pay_score"] );
					break;
				case 1 : // 支付宝
					unset ( $fields ["wxappsecret"] );
					unset ( $fields ["wxpaysignkey"] );
					unset ( $fields ["wxappid"] );
					unset ( $fields ["wxmchid"] );
					unset($fields["wx_cert_pem"]);
					unset($fields["wx_key_pem"]);
					unset ( $fields ["partnerid"] );
					unset ( $fields ["partnerkey"] );
					unset ( $fields ["wappartnerid"] );
					unset ( $fields ["wappartnerkey"] );
					unset ( $fields ["wxpartnerid"] );
					unset ( $fields ["wxpartnerkey"] );
					unset ( $fields ["quick_security_key"] );
					unset ( $fields ["quick_merid"] );
					unset ( $fields ["quick_merabbr"] );
					unset ( $fields ["shop_pay_score"] );
					break;
				case 2 : // 财付通wap
					unset ( $fields ["pid"] );
					unset ( $fields ["key"] );
					unset ( $fields ["wxappsecret"] );
					unset ( $fields ["wxpaysignkey"] );
					unset ( $fields ["wxappid"] );
					unset ( $fields ["wxmchid"] );
					unset($fields["wx_cert_pem"]);
					unset($fields["wx_key_pem"]);
					unset ( $fields ["zfbname"] );
					unset ( $fields ["wappartnerid"] );
					unset ( $fields ["wappartnerkey"] );
					unset ( $fields ["wxpartnerkey"] );
					unset ( $fields ["wxpartnerid"] );
					unset ( $fields ["quick_security_key"] );
					unset ( $fields ["quick_merid"] );
					unset ( $fields ["quick_merabbr"] );
					unset ( $fields ["shop_pay_score"] );
					break;
				case 3 : // 财付通
					unset ( $fields ["pid"] );
					unset ( $fields ["key"] );
					unset ( $fields ["wxappsecret"] );
					unset ( $fields ["wxpaysignkey"] );
					unset ( $fields ["wxappid"] );
					unset ( $fields ["wxmchid"] );
					unset($fields["wx_cert_pem"]);
					unset($fields["wx_key_pem"]);
					unset ( $fields ["zfbname"] );
					unset ( $fields ["partnerid"] );
					unset ( $fields ["partnerkey"] );
					unset ( $fields ["wxpartnerkey"] );
					unset ( $fields ["wxpartnerid"] );
					unset ( $fields ["quick_security_key"] );
					unset ( $fields ["quick_merid"] );
					unset ( $fields ["quick_merabbr"] );
					unset ( $fields ["shop_pay_score"] );
					break;
				case 4 :
					unset ( $fields ["pid"] );
					unset ( $fields ["key"] );
					unset ( $fields ["wxappsecret"] );
					unset ( $fields ["wxpaysignkey"] );
					unset ( $fields ["wxappid"] );
					unset ( $fields ["wxmchid"] );
					unset($fields["wx_cert_pem"]);
					unset($fields["wx_key_pem"]);
					unset ( $fields ["zfbname"] );
					unset ( $fields ["wappartnerid"] );
					unset ( $fields ["wappartnerkey"] );
					unset ( $fields ["partnerid"] );
					unset ( $fields ["partnerkey"] );
					unset ( $fields ["wxpartnerkey"] );
					unset ( $fields ["wxpartnerid"] );
					unset ( $fields ["shop_pay_score"] );
					break;
				case 5 :
					unset ( $fields ["pid"] );
					unset ( $fields ["key"] );
					unset ( $fields ["wxappsecret"] );
					unset ( $fields ["wxpaysignkey"] );
					unset ( $fields ["wxappid"] );
					unset ( $fields ["wxmchid"] );
					unset($fields["wx_cert_pem"]);
					unset($fields["wx_key_pem"]);
					unset ( $fields ["zfbname"] );
					unset ( $fields ["wappartnerid"] );
					unset ( $fields ["wappartnerkey"] );
					unset ( $fields ["partnerid"] );
					unset ( $fields ["partnerkey"] );
					unset ( $fields ["wxpartnerkey"] );
					unset ( $fields ["wxpartnerid"] );
					unset ( $fields ["quick_security_key"] );
					unset ( $fields ["quick_merid"] );
					unset ( $fields ["quick_merabbr"] );
					
					
					break;
			}
			// dump ( $fields );
			$this->assign ( 'fields', $fields );
			
			$newdata = array ();
			foreach ( $data as $key => $v ) {
				// 加密处理
				if ($key == "id" || $key == "shop_pay_score") {
					$newdata [$key] = $v;
				} else {
					$newdata [$key] = $this->hideStr ( $v, (strlen ( $v ) / 3), (strlen ( $v ) / 3) );
				}
			}
			$this->assign ( 'data', $newdata );
			$this->meta_title = '编辑' . $this->model ['title'];
		}
		// dump($data);
		// dump($fields);
		// die;
		$this->display ( "wxpay" );
	}
	public function zfbpay() {
		$this->lists ( 1 );
	}
	public function cftwappay() {
		$this->lists ( 2 );
	}
	public function ctfpay() {
		$this->lists ( 3 );
	}
	public function quickpay() {
		$this->lists ( 4 );
	}
	public function shoppay() {
		$this->lists ( 5 );
	}
	
	/**
	 * ********************测试支付，这个方法可以放在其它任何一个插件或者功能里***********************
	 */
	public function testpay() {
		if (IS_POST) {
			header ( "Content-type: text/html; charset=utf-8" );
			// token
			$token = get_token ();
			// 微信用户ID
			$openid = get_openid ();
			// 订单名称
			$orderName = urlencode ( "我是测试的订单" );
			// 订单ID
			$orderid = date ( 'Ymd' ) . substr ( implode ( NULL, array_map ( 'ord', str_split ( substr ( uniqid (), 7, 13 ), 1 ) ) ), 0, 8 );
			// 支付金额
			$price = $_POST ['zfje'];
			// 支付类型
			$zftype = $_POST ['zftype'];
			/*
			 * 成功后返回调用的方法 addons_url的格式
			 * 返回GET参数:token,wecha_id,orderid
			 * 以下用playok的方法来说明，其实这个地址也是由开发者随意定的
			 */
			$from = "Payment:__Payment_playok";
			$bid = "";
			$sid = "";
			$url = addons_url ( 'Payment://Alipay/pay', array (
					'from' => $from,
					'orderName' => $orderName,
					'price' => $price,
					'token' => $token,
					'wecha_id' => $openid,
					'paytype' => $zftype,
					'orderNumber' => $orderid,
					'bid' => $bid,
					'sid' => $sid 
			) );
			
			redirect ( $url, 1, '您好,准备跳转到支付页面,请不要重复刷新页面,请耐心等待...' );
		} else {
			$normal_tips = '测试支付功能';
			$this->assign ( 'normal_tips', $normal_tips );
			$this->display ( "testpay" );
		}
	}
	public function playok() {
		// 支付成功后能得到的参数有：
		$token = I ( 'token' );
		$openid = I ( 'wecha_id' );
		$orderid = I ( 'orderid' );
		
		// TODO 在这里开发者可以加支付成功的处理程序
		
		$this->success ( '支付成功！', U ( 'lists' ) );
	}
	/**
	 * ********************测试支付***********************
	 */
	/**
	 * +----------------------------------------------------------
	 * 将一个字符串部分字符用*替代隐藏
	 * +----------------------------------------------------------
	 *
	 * @param string $string
	 *        	待转换的字符串
	 * @param int $bengin
	 *        	起始位置，从0开始计数，当$type=4时，表示左侧保留长度
	 * @param int $len
	 *        	需要转换成*的字符个数，当$type=4时，表示右侧保留长度
	 * @param int $type
	 *        	转换类型：0，从左向右隐藏；1，从右向左隐藏；2，从指定字符位置分割前由右向左隐藏；3，从指定字符位置分割后由左向右隐藏；4，保留首末指定字符串
	 * @param string $glue
	 *        	分割符
	 *        	+----------------------------------------------------------
	 * @return string 处理后的字符串
	 *         +----------------------------------------------------------
	 */
	function hideStr($string, $bengin = 0, $len = 4, $type = 2, $glue = "@") {
		if (empty ( $string ))
			return false;
		$array = array ();
		if ($type == 0 || $type == 1 || $type == 4) {
			$strlen = $length = mb_strlen ( $string );
			while ( $strlen ) {
				$array [] = mb_substr ( $string, 0, 1, "utf8" );
				$string = mb_substr ( $string, 1, $strlen, "utf8" );
				$strlen = mb_strlen ( $string );
			}
		}
		if ($type == 0) {
			for($i = $bengin; $i < ($bengin + $len); $i ++) {
				if (isset ( $array [$i] ))
					$array [$i] = "*";
			}
			$string = implode ( "", $array );
		} else if ($type == 1) {
			$array = array_reverse ( $array );
			for($i = $bengin; $i < ($bengin + $len); $i ++) {
				if (isset ( $array [$i] ))
					$array [$i] = "*";
			}
			$string = implode ( "", array_reverse ( $array ) );
		} else if ($type == 2) {
			$array = explode ( $glue, $string );
			$array [0] = $this->hideStr ( $array [0], $bengin, $len, 1 );
			$string = implode ( $glue, $array );
		} else if ($type == 3) {
			$array = explode ( $glue, $string );
			$array [1] = $this->hideStr ( $array [1], $bengin, $len, 0 );
			$string = implode ( $glue, $array );
		} else if ($type == 4) {
			$left = $bengin;
			$right = $len;
			$tem = array ();
			for($i = 0; $i < ($length - $right); $i ++) {
				if (isset ( $array [$i] ))
					$tem [] = $i >= $left ? "*" : $array [$i];
			}
			$array = array_chunk ( array_reverse ( $array ), $right );
			$array = array_reverse ( $array [0] );
			for($i = 0; $i < $right; $i ++) {
				$tem [] = $array [$i];
			}
			$string = implode ( "", $tem );
		}
		return $string;
	}
	public function config() {
		// 使用提示
		// $normal_tips = '';
		// $this->assign ( 'normal_tips', $normal_tips );
		if (IS_POST) {
			$flag = D ( 'Common/AddonConfig' )->set ( _ADDONS, $_POST ['config'] );
			
			if ($flag !== false) {
				$this->success ( '保存成功' );
			} else {
				$this->error ( '保存失败' );
			}
			exit ();
		}
		
		parent::config ();
	}
	//通用的支付确认界面
	function confirm(){
	    $price=I('price');
	    $aimId=I('aim_id');
	    $from=I('from');
	    $title=I('title');
	    $publicInfo=get_token_appinfo();
	    $orderNumber= date ( 'YmdHis' ) . substr ( uniqid (), 4 );
	    $token=get_token();
	    $openid=get_openid();
	    $param = array (
	        'aim_id'=>$aimId,
	        'from' => $from,
	        'orderName' => $title,
	        'orderNumber' => $orderNumber,
	        'price' => $price,
	        'token' => $token,
	        'wecha_id' => $openid,
	        'paytype' =>0
	    );
	    $url=addons_url('Payment://Alipay/pay',$param);
	    $this->assign('url',$url);
	    $this->assign('pubilc_name',$publicInfo['public_name']);
	    $this->assign('title',$title);
	    $this->assign('price',$price);
		$this -> display();
	}
}
