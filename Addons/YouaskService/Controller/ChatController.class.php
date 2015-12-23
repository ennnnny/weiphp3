<?php

namespace Addons\YouaskService\Controller;
use Addons\YouaskService\Controller\BaseController;

class ChatController extends BaseController{
	public function _initialize() {
		
		if(session('YouaskService_userName')==false){
			$this->error('您必须登录后才能操作',addons_url ( 'YouaskService://Login/index' ));
		}
	}
	
}
