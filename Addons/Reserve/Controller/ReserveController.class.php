<?php

namespace Addons\Reserve\Controller;

use Home\Controller\AddonsController;

class ReserveController extends AddonsController {
	var $model;
	var $reserve_id;
	function lists() {
		$isAjax = I ( 'isAjax' );
		$isRadio = I ( 'isRadio' );
		$model = $this->getModel ( 'reserve' );
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
		
		foreach ( $data as &$vo ) {
			if (! empty ( $vo ['start_time'] ) && ! empty ( $vo ['end_time'] )) {
				$vo ['start_time'] = time_format ( $vo ['start_time'] ) . ' 至  ' . time_format ( $vo ['end_time'] );
			} elseif (! empty ( $vo ['start_time'] )) {
				$vo ['start_time'] = time_format ( $vo ['start_time'] ) . ' 开始';
			} elseif (! empty ( $vo ['end_time'] )) {
				$vo ['start_time'] = '到 ' . time_format ( $vo ['start_time'] ) . ' 结束';
			}
			
			$vo ['status_title'] = $vo ['status'] == 0 ? '已禁用' : '已启用';
		}
		
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
		$model = $this->getModel ( 'reserve' );
		
		if (IS_POST) {
			$this->checkDate();
			$act = empty ( $id ) ? 'add' : 'save';
			$Model = D ( parse_name ( get_table_name ( $model ['id'] ), 1 ) );
			// 获取模型的字段信息
			$Model = $this->checkAttr ( $Model, $model ['id'] );
			$res = false;
			$Model->create () && $res = $Model->$act ();
			if ($res !== false) {
				$act == 'add' && $id = $res;
				
				$this->_setAttr ( $id, $_POST );
				$this->_setOption ( $id, $_POST );
				
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
			
			// 预约项信息
			$map ['reserve_id'] = $id;
			$list = M ( 'reserve_option' )->where ( $map )->order ( 'id asc' )->select ();
			$this->assign ( 'option_list', $list );
			
			// 字段信息
			$list = M ( 'reserve_attribute' )->where ( $map )->order ( 'sort asc' )->select ();
			$this->assign ( 'attr_list', $list );
			
			$this->display ( 'edit' );
		}
	}
	// 保存字段信息
	function _setAttr($reserve_id, $data) {
		$dao = M ( 'reserve_attribute' );
		$save ['reserve_id'] = $reserve_id;
		
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
	// 保存预约项信息
	function _setOption($reserve_id, $data) {
		$dao = M ( 'reserve_option' );
		$save ['reserve_id'] = $reserve_id;
		
		$old_ids = $dao->where ( $save )->getFields ( 'id' );
		
		foreach ( $data ['name'] as $key => $val ) {
			$save ['name'] = safe ( $val );
			if (empty ( $save ['name'] ))
				continue;
			
			$save ['money'] = intval ( $data ['money'] [$key] );
			$save ['max_limit'] = intval ( $data ['max_limit'] [$key] );
			$save ['init_count'] = intval ( $data ['init_count'] [$key] );
			
			$id = intval ( $data ['option_id'] [$key] );
			if (! empty ( $id )) {
				$ids [] = $map ['id'] = $id;
				$dao->where ( $map )->save ( $save );
			} else {
				$ids [] = $dao->add ( $save );
			}
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
		
		$res = M ( 'reserve' )->where ( $map )->save ( $save );
		echo $res === false ? 0 : 1;
	}
	function preview() {
		$id = I ( 'id', 0, 'intval' );
		$url = addons_url ( 'Reserve://Wap/index', array (
				'reserve_id' => $id,
				'publicid' => get_token_appinfo ( '', 'id' ) 
		) );
		$this->assign ( 'url', $url );
		$this->display ( SITE_PATH . '/Application/Home/View/default/Addons/preview.html' );
	}
	function index() {
		$this->model = $this->getModel ( 'reserve_value' );
		$this->reserve_id = I ( 'id', 0 );
		
		$reserve = M ( 'reserve' )->find ( $this->reserve_id );
		$reserve ['cover'] = ! empty ( $reserve ['cover'] ) ? get_cover_url ( $reserve ['cover'] ) : ADDON_PUBLIC_PATH . '/background.png';
		$reserve ['intro'] = str_replace ( chr ( 10 ), '<br/>', $reserve ['intro'] );
		$this->assign ( 'reserve', $reserve );
		
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
				$map ['reserve_id'] = $this->reserve_id;
				
				$data = M ( get_table_name ( $this->model ['id'] ) )->where ( $map )->find ();
				if ($data && $reserve ['jump_url']) {
					// redirect ( $reserve ['jump_url'] );
				}
			}
		}
		
		// dump ( $reserve );
		
		$map ['reserve_id'] = $this->reserve_id;
		$map ['token'] = get_token ();
		$fields = M ( 'reserve_attribute' )->where ( $map )->order ( 'sort asc, id asc' )->select ();
		
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
				add_credit ( 'reserve' );
				
				$param ['reserve_id'] = $this->reserve_id;
				$param ['id'] = $act == 'add' ? $res : $id;
				$param ['model'] = $this->model ['id'];
				$url = empty ( $reserve ['jump_url'] ) ? U ( 'edit', $param ) : $reserve ['jump_url'];
				
				$tip = ! empty ( $reserve ['finish_tip'] ) ? $reserve ['finish_tip'] : '提交成功，谢谢参与';
				$this->success ( $tip, $url, 5 );
			} else {
				$this->error ( $Model->getError () );
			}
			exit ();
		}
		
		$fields [] = array (
				'is_show' => 4,
				'name' => 'reserve_id',
				'value' => $this->reserve_id 
		);
		
		$this->assign ( 'fields', $fields );
		
		$this->display ();
	}
	function checkDate(){
		// 判断时间选择是否正确
		
		 if (strtotime ( I ( 'post.start_time' ) ) > strtotime ( I ( 'post.end_time' ) )) {
			$this->error ( '开始时间不能大于结束时间' );
		}
		if(!I('post.name')){
			$this->error('预约项必须');
			
		}
		if(!I('post.attr_title')){
			$this->error('字段必须');
		}
	}
	
}
