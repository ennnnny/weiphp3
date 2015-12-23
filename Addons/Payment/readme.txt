一、欢迎使用weiphp的支付通插件，以下是配置说明：

1、在系统后台安装插件
2、在管理前台配置支付所需的参数
3、在配置页面里点击测试支付功能，测试下是否联通，如有误请确认参数是否配置正确

二、开发调用说明

目前支付通功能是通过跳转URL的方式集成其它插件或者功能。因此需要用到支付功能时，只需要按照支付通提供的接口地址组装好参数再跳转过来就，支付完后会跳转回指定的回调地址上。

1、接口地址格式：

addons_url ( 'Payment://Alipay/pay', array (
					'from' => 支付成功后的回调地址，格式是addons_url的格式，如Payment://Payment/playok，或者 Shop://Wap/afterPlay,
					'orderName' => 订单名称,
					'price' => 支付金额,
					'token' => 公众号token,
					'wecha_id' => 用户openid,
					'paytype' => 支付类型，具体的类型参数见下面的说明,
					'orderid' => 订单编号,
					'bid' => 扩展ID,
					'sid' => 扩展ID
			) )


2、支付类型有：

<select name="zftype">                          
	<option value="1">支付宝</option>
	<option value="2">财付通(WAP手机接口)</option>
	<option value="3">财付通(及时到帐)</option>
	<option value="0">微信支付</option>
	<option value="4">银联在线</option>
</select>


3、调用的PHP demo如下：


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
			$from = "Payment://Payment/playok";
			$bid = "";
			$sid = "";
			redirect ( addons_url ( 'Payment://Alipay/pay', array (
					'from' => $from,
					'orderName' => $orderName,
					'price' => $price,
					'token' => $token,
					'wecha_id' => $openid,
					'paytype' => $zftype,
					'orderid' => $orderid,
					'bid' => $bid,
					'sid' => $sid 
			) ), 1, '您好,准备跳转到支付页面,请不要重复刷新页面,请耐心等待...' );
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