<?php
namespace Addons\Payment\Controller;

use Home\Controller\AddonsController;

class WeixinController extends AddonsController
{

    public $token;

    public $wecha_id;

    public $payConfig;

    public function __construct()
    {
        parent::__construct();
        
        $this->token = get_token();
        $this->wecha_id = get_openid();
        // 读取配置
        $pay_config_db = M('payment_set');
        $paymentSet = $pay_config_db->where(array(
            'token' => $this->token
        ))->find();
        if ($paymentSet['wx_cert_pem'] && $paymentSet['wx_key_pem']){
            $ids[]=$paymentSet['wx_cert_pem'];
            $ids[]=$paymentSet['wx_key_pem'];
            $map['id']=array('in',$ids);
            $fileData=M('file')->where($map)->select();
            $downloadConfig=C(DOWNLOAD_UPLOAD);
            foreach ($fileData as $f){
                if ($paymentSet['wx_cert_pem']==$f['id']){
                    
                    $certpath=SITE_PATH.str_replace('/', '\\', substr($downloadConfig['rootPath'],1).$f['savepath'].$f['savename']);
                }else{
                    $keypath=SITE_PATH.str_replace('/', '\\', substr($downloadConfig['rootPath'],1).$f['savepath'].$f['savename']);
                }
                
            }
            $paymentSet['cert_path']=$certpath;
            $paymentSet['key_path']=$keypath;
        }
        $this->payConfig=$paymentSet;
       
        session('paymentinfo', $this->payConfig);
    }
    
    // 处理from URL字符串
    // private function doFromStr($from){
    // if($from){
    // $fromstr=str_replace('_', '/', $from);
    // }
    // return $fromstr;
    // }
    // protected function create_noncestr( $length = 16 ) {
    // $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    // $str ="";
    // for ( $i = 0; $i < $length; $i++ ) {
    // $str.= substr($chars, mt_rand(0, strlen($chars)-1), 1);
    // //$str .= $chars[ mt_rand(0, strlen($chars) - 1) ];
    // }
    // return $str;
    // }
    function getPaymentOpenid()
    { // echo '444';
        $callback = GetCurUrl();
        if ((defined('IN_WEIXIN') && IN_WEIXIN) || isset($_GET['is_stree']))
            return false;
        
        $callback = urldecode($callback);
        $isWeixinBrowser = isWeixinBrowser(); // echo '555';die();
                                              // $info = get_token_appinfo ( $token );
        
        if (strpos($callback, '?') === false) {
            $callback .= '?';
        } else {
            $callback .= '&';
        }
        
        // if (! $isWeixinBrowser || $info ['type'] != 2 || empty ( $info ['appid'] )) {
        // redirect ( $callback . 'openid=-1' );
        // }
        // $map['token'] = get_token();
        
        // $info=M ( 'payment_set' )->where($map)->find();
        $param['appid'] = $this->payConfig['wxappid'];
        
        if (! isset($_GET['getOpenId'])) {
            $param['redirect_uri'] = $callback . 'getOpenId=1';
            $param['response_type'] = 'code';
            $param['scope'] = 'snsapi_base';
            $param['state'] = 123;
            
            $url = 'https://open.weixin.qq.com/connect/oauth2/authorize?' . http_build_query($param) . '#wechat_redirect';
            redirect($url);
        } else if ($_GET['state']) {
                $param['secret'] = $this->payConfig['wxappsecret'];
                $param['code'] = I('code');
                $param['grant_type'] = 'authorization_code';
                
                $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?' . http_build_query($param);
                $content = file_get_contents($url);
                $content = json_decode($content, true);
                return $content['openid'];
            }
    }

    public function pay()
    {
        require_once ('Weixinpay/WxPayData.class.php');
        require_once ('Weixinpay/WxPayApi.class.php');
        require_once ('Weixinpay/WxPayJsApiPay.php');
        // require_once ('Weixinpay/log.php');
        $paymentId = $_GET['paymentId'];
        $token = $_GET['token'];
        $body = $_GET['orderName'];
        $orderNo = $_GET['orderNumber'];
        if ($orderNo == "") {
            $orderNo = $_GET['single_orderid'];
        }
        $totalFee = $_GET['price'] * 100; // 单位为分
                                          // $paytype=$_GET['paytype'];
        
        $tools = new \JsApiPay();
        // $openId = $tools->GetOpenid();
        // $openId=$_GET['wecha_id'];
        // $openId=get_openid();
        // dump($openId);
        // die();
        // // dump($openId);
//         $openId='orgF0t-HyMrDJHFOl9GAkENyu6i0';
        // dump('45456');
        $openId = $this->getPaymentOpenid();
//         dump(session('paymentinfo'));
//         dump($openId);
//         dump('1232');die;
        // 统一下单
        import('Weixinpay.WxPayData');
        $input = new \WxPayUnifiedOrder();
        $input->SetBody($body);
        // $input->SetAttach("test");
        $input->SetOut_trade_no($orderNo);
        $input->SetTotal_fee($totalFee);
        // $input->SetTime_start(date("YmdHis"));
        // $input->SetTime_expire(date("YmdHis", time() + 600));
        // $input->SetGoods_tag("test");
        $input->SetNotify_url("Weixinpay/notify.php");
        $input->SetTrade_type("JSAPI");
        $input->SetOpenid($openId);
        
        $order = \WxPayApi::unifiedOrder($input);
        
//         echo '<font color="#f00"><b>统一下单支付单信息</b></font><br/>';
         //dump($order);
//         die;
        $jsApiParameters = $tools->GetJsApiParameters($order);
       //  dump($jsApiParameters);
		 
        $returnUrl = addons_url('Payment://Weixin/payOK');
        header('Location:' . SITE_URL . '/WxpayAPI/unifiedorder.php?jsApiParameters=' . $jsApiParameters . '&returnurl=' . $returnUrl . '&totalfee=' . $_GET['price'] . '&paymentId=' . $paymentId);
        
        // echo $jsApiParameters;
        // die;
        // session('jsaparams',$jsApiParameters);
        // $_COOKIE['jsaparams']=$jsApiParameters;
        // $from=$_GET['from'];
        // if($from!='shop'){
        // $from=$this->doFromStr($_GET['from']);
        // }
        // //$returnUrl = '/index.php?g=Wap&m=' . $from . '&a=payReturn&token=' . $_GET ['token'] . '&wecha_id=' . $_GET ['wecha_id'] . '&orderid=' . $orderNo;
        // $returnUrl=addons_url('Payment://Weixin/payOK');
        // //$this->assign ( 'returnUrl', $returnUrl );
        // $this->assign ( 'jsApiParameters', $jsApiParameters );
        // $this->assign ( 'price', $_GET['price'] );
        // die;
        // header('Location:http://'.$_SERVER['HTTP_HOST'].'/weishi/WxpayAPI/unifiedorder.php?body='.$body.'&out_trade_no='.$orderNo.'&totalfee='.$totalFee.'&openid='.$openId.'&returnurl='.$returnUrl);
    }

    public function payOK()
    {
        $isPay = I('get.ispay', 0, 'intval');
        $paymentId = I('get.paymentId');
        // dump($paymentId);
        // dump($isPay);
        if ($isPay) {
            $paymentDao = D('Addons://Payment/PaymentOrder');
            $res = $paymentDao->where(array(
                'id' => $paymentId
            ))->setField('status', $isPay);
            if ($res) {
                $info = $paymentDao->getInfo($paymentId, true);
                $map['order_number'] = $info['single_orderid'];
                $orderDao=D('Addons://Shop/Order');
//                 $orderDao->where($map)->setField('pay_status', $isPay);
                $orderid=$orderDao->where($map)->getField('id');
			 	$orderInfo=$orderDao->getInfo($orderid);
                $save ['pay_status'] = 1;
				$res = $orderDao->update ( $orderid, $save );
                $orderDao->setStatusCode ( $orderid, 1 );
				if($orderInfo['auto_send']){
					$orderDao -> autoSend($orderid);
				}
            }
            $url = addons_url('Shop://Wap/orderDetail',array('id'=>$orderid));
            $this->success('支付成功,即将跳转到订单详情', $url);
        }
    }
    // 同步数据处理
    public function return_url()
    {
        S('pay', $_GET);
        $out_trade_no = $this->_get('out_trade_no');
        if (intval($_GET['total_fee']) && ! intval($_GET['trade_state'])) {
            $okurl = addons_url($_GET['from'], array(
                "token" => $_GET['token'],
                "wecha_id" => $_GET['wecha_id'],
                "orderid" => $out_trade_no
            ));
            redirect($okurl);
        } else {
            exit('付款失败');
        }
    }

    public function notify_url()
    {
        echo "success";
        exit();
    }

    function api_notice_increment($url, $data)
    {
        $ch = curl_init();
        $header = "Accept-Charset: utf-8";
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $tmpInfo = curl_exec($ch);
        $errorno = curl_errno($ch);
        if ($errorno) {
            return array(
                'rt' => false,
                'errorno' => $errorno
            );
        } else {
            $js = json_decode($tmpInfo, 1);
            if ($js['errcode'] == '0') {
                return array(
                    'rt' => true,
                    'errorno' => 0
                );
            } else {
				$this->error ( error_msg ( $js ) );                
            }
        }
    }
}
?>