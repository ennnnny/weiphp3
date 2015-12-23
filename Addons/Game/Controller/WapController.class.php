<?php

namespace Addons\Game\Controller;
use Home\Controller\AddonsController;

class WapController extends AddonsController{
	function main(){
		//游戏中心主界面
		$this -> display();	
	}
	function lists(){
		//游戏列表
		$this -> display();
	}
}
