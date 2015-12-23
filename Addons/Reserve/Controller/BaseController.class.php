<?php

namespace Addons\Reserve\Controller;

use Home\Controller\AddonsController;

function get_reserve_id() {
	return $_REQUEST ['reserve_id'];
}
class BaseController extends AddonsController {
}
