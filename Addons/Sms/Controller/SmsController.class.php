<?php

namespace Addons\Sms\Controller;
use Home\Controller\AddonsController;

class SmsController extends AddonsController{
	function config() {
		$this -> assign('normal_tips','填写信息前请先到服务商平台开通账号：<a href="http://www.yuntongxun.com/" target="_blank">云之讯官网</a>,<a href="http://www.ucpaas.com/" target="_blank">云通讯官网</a>');
		parent::config ();
	}
}
