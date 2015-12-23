<?php 
// ini_set('date.timezone','Asia/Shanghai');
// error_reporting(E_ERROR);
// require_once "lib/WxPay.Data.php";
// require_once "lib/WxPay.Api.php";
// require_once "unit/WxPay.JsApiPay.php";
// require_once 'unit/log.php';

// 初始化日志
// $logHandler= new CLogFileHandler("./logs/".date('Y-m-d').'.log');
// $log = Log::Init($logHandler, 15);
// function printf_info($data)
// {
//     foreach($data as $key=>$value){
//         echo "<font color='#00ff55;'>$key</font> : $value <br/>";
//     }
// }

if(!empty($_GET)){
// 	$body=$_GET['body'];
// 	$out_trade_no=$_GET['out_trade_no'];
	$totalfee=$_GET['totalfee'];
// 	$openId=$_GET['openid'];
	$returnUrl=$_GET['returnurl'];
	$jsApiParameters=$_GET['jsApiParameters'];
	$paymentId=$_GET['paymentId'];
}
//echo $_GET['code'].'<br/>';
// // //获取用户openid
// $tools = new JsApiPay();
// // $openId = $tools->GetOpenid();
// // echo '<br/>openid:'.$openId.'<br/>';
// $input = new WxPayUnifiedOrder();
// $input->SetBody($body);
// $input->SetOut_trade_no($out_trade_no);
// $input->SetTotal_fee($totalfee*100);
// $input->SetNotify_url("http://project.weiphp.cn/weishi/WxpayAPI/notify.php");
// $input->SetTrade_type("JSAPI");
// $input->SetOpenid($openId);
// $order = WxPayApi::unifiedOrder($input);
// //echo '<font color="#f00"><b>统一下单支付单信息</b></font><br/>';
// //printf_info($order);
// $jsApiParameters = $tools->GetJsApiParameters($order);
//echo $jsApiParameters;

// // //统一下单

// $tools = new JsApiPay();
// $openId = $tools->GetOpenid();
// echo '<br/>openid:'.$openId.'<br/>';
// $input = new WxPayUnifiedOrder();
// $input->SetBody("test");
// $input->SetAttach("test");
// $input->SetOut_trade_no(WxPayConfig::MCHID.date("YmdHis"));
// $input->SetTotal_fee("1");
// $input->SetTime_start(date("YmdHis"));
// $input->SetTime_expire(date("YmdHis", time() + 600));
// $input->SetGoods_tag("test");
// $input->SetNotify_url("http://project.weiphp.cn/weishi/WxpayAPI/notify.php");
// $input->SetTrade_type("JSAPI");
// $input->SetOpenid($openId);
// $order = WxPayApi::unifiedOrder($input);
// echo '<font color="#f00"><b>统一下单支付单信息</b></font><br/>';
// printf_info($order);
// $jsApiParameters = $tools->GetJsApiParameters($order);
// echo $jsApiParameters;
?>

<html>
<head>
    <meta http-equiv="content-type" content="text/html;charset=utf-8"/>
    <meta name="viewport"content="width=device-width,height=device-height,inital-scale=1.0,maximum-scale=1.0,user-scalable=no;">
	<meta name="apple-mobile-web-app-capable"content="yes">
    <meta name="apple-mobile-web-app-status-bar-style"content="black">
    <meta name="format-detection"content="telephone=no">
    <title>微信支付</title>
    <style type="text/css">
		*{ padding:0; margin:0;}
    	.payHead{ background:#096; padding:60px; text-align:center; color:#fff}
		.payHead .span1{ font-size:16px;}
		.payHead .price{ font-size:30px; line-height:40px; font-weight:bold;}
		.button{ color:#fff; font-size:16px; background:#0C3; border-radius:5px; padding:12px 0; text-align:center; display:block; margin:20px; -webkit-appearance:none; border:none; text-decoration:none;}
		.failMsg{ padding:15px; margin:20px; background:#FFC; text-align:left; color:red;}
    </style>
    <script type="text/javascript">

		//调用微信JS api 支付
		function jsApiCall()
		{
			WeixinJSBridge.invoke(
				'getBrandWCPayRequest',
				<?php echo $jsApiParameters; ?>,
				function(res){
					WeixinJSBridge.log(res.err_msg);
// 					alert(res.err_code+res.err_desc+res.err_msg);
					if(res.err_msg=='get_brand_wcpay_request:ok'){
	 					document.getElementById('payDom').style.display='none';
	 					document.getElementById('successDom').style.display='block';
// 	 					setTimeout(function(){
	 						window.location.href = '<?php echo $returnUrl.'&ispay=1&paymentId='.$paymentId; ?>';	
// 	 					},2000);
	 				}else{
	 					document.getElementById('payDom').style.display='none';
	 					document.getElementById('failDom').style.display='block';
	 					document.getElementById('failRt').innerHTML='错误提示：'+res.err_msg;
	 				}
				}
			);
		}

		function callpay()
		{
			if (typeof WeixinJSBridge == "undefined"){
			    if( document.addEventListener ){
			        document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
			    }else if (document.attachEvent){
			        document.attachEvent('WeixinJSBridgeReady', jsApiCall); 
			        document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
			    }
			}else{
			    jsApiCall();
			}
		}
	</script>
</head>
<body>
	<div>
    	<div class="payHead">
        	<span class="span1">支付信息</span><br/>
            <span class="price">金额:<?php echo $totalfee; ?>元</span>
        </div>	
        <div class="footReturn" id="payDom">
        	<a href="javascript:void(0);" class="button" onClick="callpay()" >点击进行微信支付</a>
        </div>
    </div>
    <div id="failDom" style="display:none">
    	<div class="failMsg">
            支付结果:支付失败
            <div id="failRt">
            </div>
        </div>
        <div id="footReturn">
            <a href="javascript:void(0);" class="button" onClick="callpay()">重新进行支付</a>
        </div>
    </div>
    <div id="successDom" style="display:none">
    	<span>支付成功</span>
        <span>您已支付成功，页面正在跳转...</span>
    </div>
</body>
</html>