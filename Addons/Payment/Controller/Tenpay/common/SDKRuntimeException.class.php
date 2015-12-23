<?php
//---------------------------------------------------------
//自定义异常处理类
//---------------------------------------------------------

namespace Addons\Payment\Controller;
class  SDKRuntimeException extends \Exception {
	public function errorMessage()
	{
		return $this->getMessage();
	}

}

?>