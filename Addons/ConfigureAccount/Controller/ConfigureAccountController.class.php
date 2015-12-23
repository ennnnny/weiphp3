<?php

namespace Addons\ConfigureAccount\Controller;

use Home\Controller\AddonsController;

class ConfigureAccountController extends AddonsController {
	function lists() {
		$url = U ( 'Home/Public/add', array('from'=>4) );
		redirect ( $url );
	}
}
