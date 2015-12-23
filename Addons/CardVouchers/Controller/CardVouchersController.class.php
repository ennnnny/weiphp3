<?php

namespace Addons\CardVouchers\Controller;

use Home\Controller\AddonsController;

class CardVouchersController extends AddonsController {
	function edit() {
		$id = I ( 'id' );
		$model = $this->getModel ();
		if (IS_POST) {
			//$_POST ['update_time'] = NOW_TIME;
			$Model = D ( parse_name ( get_table_name ( $model ['id'] ), 1 ) );
			// 获取模型的字段信息
			$Model = $this->checkAttr ( $Model, $model ['id'] );
			if ($Model->create () && $Model->save ()) {
				$this->_saveKeyword ( $model, $id );
			}
			// 清空缓存
			method_exists ( $Model, 'clear' ) && $Model->clear ( $id, 'edit' );
			$this->success ( '保存' . $model ['title'] . '成功！', U ( 'lists?model=' . $model ['name'] ) );
		} else {
			$fields = get_model_attribute ( $model ['id'] );
			
			// 获取数据
			$data = D ( 'CardVouchers' )->getInfo ( $id );
			$data || $this->error ( '数据不存在！' );
			
			$token = get_token ();
			if (isset ( $data ['token'] ) && $token != $data ['token'] && defined ( 'ADDON_PUBLIC_PATH' )) {
				$this->error ( '非法访问！' );
			}
			
			$this->assign ( 'fields', $fields );
			$this->assign ( 'data', $data );
			$this->meta_title = '编辑' . $model ['title'];
			
			
			$this->display ();
		}
	}
	function preview(){
		$id = I ( 'id', 0, 'intval' );
		$url = addons_url('CardVouchers://Wap/index',array('id'=>$id));
		$this -> assign('url',$url);
		$this->display ( SITE_PATH . '/Application/Home/View/default/Addons/preview.html' );
	}
	
	function lists() {
	    $isAjax = I ( 'isAjax' );
	    $isRadio = I ( 'isRadio' );
	    $model = $this->getModel ( 'card_vouchers' );
	    $list_data = $this->_get_model_list ( $model, 0, 'id desc', true );
	    // 		判断该活动是否已经设置投票调查
	    if ($isAjax) {
	        $this->assign('isRadio',$isRadio);
	        $this->assign ( $list_data );
	        $this->display ( 'ajax_lists_data' );
	    } else {
	        $this->assign ( $list_data );
	        $this->display ();
	    }
	}
}
