<?php

namespace Addons\Forms\Controller;

use Home\Controller\AddonsController;

class FormsController extends AddonsController {
	var $model;
	var $forms_id;
	function lists() {
		$isAjax = I ( 'isAjax' );
		$isRadio = I ( 'isRadio' );
		$model = $this->getModel ( 'forms' );
		$page = I ( 'p', 1, 'intval' ); // 默认显示第一页数据
		                                
		// 解析列表规则
		$list_data = $this->_list_grid ( $model );
		
		// 搜索条件
		$map = $this->_search_map ( $model, $fields );
		
		$row = empty ( $model ['list_row'] ) ? 20 : $model ['list_row'];
		$order = 'id desc';
		
		// 读取模型数据列表
		$name = parse_name ( get_table_name ( $model ['id'] ), true );
		$data = M ( $name )->field ( true )->where ( $map )->order ( $order )->page ( $page, $row )->select ();
		
		/* 查询记录总数 */
		$count = M ( $name )->where ( $map )->count ();
		
		$list_data ['list_data'] = $data;
		
		// 分页
		if ($count > $row) {
			$page = new \Think\Page ( $count, $row );
			$page->setConfig ( 'theme', '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%' );
			$list_data ['_page'] = $page->show ();
		}
		if ($isAjax) {
			$this->assign ( 'isRadio', $isRadio );
			$this->assign ( $list_data );
			$this->display ( 'ajax_lists_data' );
		} else {
			$this->assign ( $list_data );
			// dump($list_data);
			
			$this->display ();
		}
	}
	function add() {
		$this->display ( 'edit' );
	}
	function edit() {
		$id = I ( 'id' );
		$model = $this->getModel ( 'forms' );
		
		if (IS_POST) {
			$act = empty ( $id ) ? 'add' : 'save';
			$Model = D ( parse_name ( get_table_name ( $model ['id'] ), 1 ) );
			// 获取模型的字段信息
			$Model = $this->checkAttr ( $Model, $model ['id'] );
			$res = false;
			$Model->create () && $res = $Model->$act ();
			if ($res !== false) {
				$act == 'add' && $id = $res;
				
				$this->_setAttr ( $id, $_POST );
				$this->_saveKeyword ( $this->model, $id ); //插入关键字
				$this->success ( '保存成功！', U ( 'lists?model=' . $model ['name'], $this->get_param ) );
			} else {
				$this->error ( $Model->getError () );
			}
		} else {
			// 获取数据
			$data = M ( get_table_name ( $model ['id'] ) )->find ( $id );
			$data || $this->error ( '数据不存在！' );
			
			$token = get_token ();
			if (isset ( $data ['token'] ) && $token != $data ['token'] && defined ( 'ADDON_PUBLIC_PATH' )) {
				$this->error ( '非法访问！' );
			}
			$this->assign ( 'data', $data );
			
			// 字段信息
			$map ['forms_id'] = $id;
			$map ['token'] = $token;
			$list = M ( 'forms_attribute' )->where ( $map )->order ( 'sort asc' )->select ();
			
			$this->assign ( 'attr_list', $list );
			
			$this->display ( 'edit' );
		}
	}
	// 保存字段信息
	function _setAttr($forms_id, $data) {
		$dao = M ( 'forms_attribute' );
		$save ['forms_id'] = $forms_id;
		
		$old_ids = $dao->where ( $save )->getFields ( 'id' );
		
		$sort = 0;
		foreach ( $data ['attr_title'] as $key => $val ) {
			$save ['title'] = safe ( $val );
			if (empty ( $save ['title'] ))
				continue;
			
			$save ['extra'] = safe ( $data ['extra'] [$key] );
			$save ['type'] = safe ( $data ['type'] [$key] );
			$save ['is_must'] = intval ( $data ['is_must'] [$key] );
			$save ['value'] = safe ( $data ['value'] [$key] );
			$save ['remark'] = safe ( $data ['remark'] [$key] );
			$save ['validate_rule'] = safe ( $data ['validate_rule'] [$key] );
			$save ['error_info'] = safe ( $data ['error_info'] [$key] );
			$save ['sort'] = $sort;
			$save ['name'] = 'attr_' . $forms_id . '_' . $sort;
			
			$id = intval ( $data ['attr_id'] [$key] );
			if (! empty ( $id )) {
				$ids [] = $map ['id'] = $id;
				$dao->where ( $map )->save ( $save );
			} else {
				$save ['token'] = get_token ();
				$ids [] = $dao->add ( $save );
			}
			
			$sort += 1;
		}
		
		$diff = array_diff ( $old_ids, $ids );
		if (! empty ( $diff )) {
			$map2 ['id'] = array (
					'in',
					$diff 
			);
			$dao->where ( $map2 )->delete ();
		}
	}
	function setStatus() {
		$map ['id'] = I ( 'id', 0, 'intval' );
		$save ['status'] = I ( 'status', 0, 'intval' );
		
		$res = M ( 'forms' )->where ( $map )->save ( $save );
		echo $res === false ? 0 : 1;
	}
	function preview() {
		$id = I ( 'id', 0, 'intval' );
		$url = addons_url ( 'Forms://Wap/index', array (
				'forms_id' => $id,
				'publicid' => get_token_appinfo ( '', 'id' ) 
		) );
		$this->assign ( 'url', $url );
		$this->display ( SITE_PATH . '/Application/Home/View/default/Addons/preview.html' );
	}
	function index() {
		$this->model = $this->getModel ( 'forms_value' );
		$this->forms_id = I ( 'id', 0 );
		
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
			// dump($value);
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
				if (($vo ['is_must'] && empty ( $value )) || (! empty ( $vo ['validate_rule'] ) && ! M ()->regex ( $value, $vo ['validate_rule'] ))) {
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
				$url = empty ( $forms ['jump_url'] ) ? U ( 'edit', $param ) : $forms ['jump_url'];
				
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
