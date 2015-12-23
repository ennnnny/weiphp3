<?php
//---------------------------------------------------------
//订单查询响应
//---------------------------------------------------------
namespace Addons\Payment\Controller;
require_once ("common/CommonResponse.class.php");
class OrderQueryResponse extends CommonResponse {
	
	function OrderQueryResponse($paraMap, $secretKey) {
		$this->CommonResponse($paraMap, $secretKey);
	}
	
}


?>