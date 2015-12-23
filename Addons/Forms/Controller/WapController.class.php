<?php

namespace Addons\Forms\Controller;

use Home\Controller\AddonsController;

class WapController extends AddonsController {
	var $model;
	var $forms_id;
	function index() {
		$this->model = $this->getModel ( 'forms_value' );
		$this->forms_id = I ( 'forms_id', 0 );
		$id = I ( 'id' );
		
		$forms = M ( 'forms' )->find ( $this->forms_id );
		$forms ['cover'] = ! empty ( $forms ['cover'] ) ? get_cover_url ( $forms ['cover'] ) : ADDON_PUBLIC_PATH . '/background.png';
		$forms ['intro'] = str_replace ( chr ( 10 ), '<br/>', $forms ['intro'] );
		$this->assign ( 'forms', $forms );
		
		if (! empty ( $id )) {
			$act = 'save';
			
			$data = M ( get_table_name ( $this->model ['id'] ) )->find ( $id );
			$data || $this->error ( '数据不存在！' );
			
			// dump($data);
			$value = unserialize ( htmlspecialchars_decode ( $data ['value'] ) );
			// dump ( $value );
			unset ( $data ['value'] );
			$data = array_merge ( $data, $value );
			
			$this->assign ( 'data', $data );
			// dump($data);
		} else {
			$act = 'add';
			if ($this->mid != 0 && $this->mid != '-1') {
				$map ['uid'] = $this->mid;
				$map ['forms_id'] = $this->forms_id;
				
				$data = M ( get_table_name ( $this->model ['id'] ) )->where ( $map )->find ();
				if ($data && $forms ['jump_url']) {
					// redirect ( $forms ['jump_url'] );
				}
			}
		}
		
		// dump ( $forms );
		$map ['forms_id'] = $this->forms_id;
		$map ['token'] = get_token ();
		$fields = M ( 'forms_attribute' )->where ( $map )->order ( 'sort asc, id asc' )->select ();
		
		if (IS_POST) {
			foreach ( $fields as $vo ) {
				$error_tip = ! empty ( $vo ['error_info'] ) ? $vo ['error_info'] : '请正确输入' . $vo ['title'] . '的值';
				$value = $_POST [$vo ['name']];
				if (($vo ['is_must'] && !isset( $value )) || (! empty ( $vo ['validate_rule'] ) && ! M ()->regex ( $value, $vo ['validate_rule'] ))) {
					$this->error ( $error_tip );
					exit ();
				}
				
				$post [$vo ['name']] = $vo ['type'] == 'datetime' ? strtotime ( $_POST [$vo ['name']] ) : $_POST [$vo ['name']];
				unset ( $_POST [$vo ['name']] );
			}
			
			$_POST ['value'] = serialize ( $post );
			$act == 'add' && $_POST ['uid'] = $this->mid;
			// dump($_POST);exit;
			$Model = D ( parse_name ( get_table_name ( $this->model ['id'] ), 1 ) );
			// 获取模型的字段信息
			$Model = $this->checkAttr ( $Model, $this->model ['id'], $fields );
			
			if ($Model->create () && $res = $Model->$act ()) {
				// 增加积分
				add_credit ( 'forms' );
				
				$param ['forms_id'] = $this->forms_id;
				$param ['id'] = $act == 'add' ? $res : $id;
				$param ['model'] = $this->model ['id'];
				$url = empty ( $forms ['jump_url'] ) ? U ( 'index', $param ) : $forms ['jump_url'];
				
				$tip = ! empty ( $forms ['finish_tip'] ) ? $forms ['finish_tip'] : '提交成功，谢谢参与';
				$this->success ( $tip, $url, 5 );
			} else {
				$this->error ( $Model->getError () );
			}
			exit ();
		}
		
		$fields [] = array (
				'is_show' => 4,
				'name' => 'forms_id',
				'value' => $this->forms_id 
		);
		
		$this->assign ( 'fields', $fields );
		
		$this->display ();
	}
}
