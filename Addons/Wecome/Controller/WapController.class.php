<?php

namespace Addons\Wecome\Controller;

use Home\Controller\AddonsController;

class WapController extends AddonsController {
	function bind_follow() {
		$map ['uid'] = I ( 'uid' );
		$map ['publicid'] = I ( 'publicid' );
		$info = M ( 'user_follow' )->where ( $map )->find ();
		if ($this->mid > 0) {
			M ( 'user_follow' )->where ( $map )->setField ( 'follow_id', $this->mid );
			$this->redirect ( $info ['url'] );
		} else {
			echo '绑定失败';
		}
	}
}
