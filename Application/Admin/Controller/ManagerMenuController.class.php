<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 凡星
// +----------------------------------------------------------------------
namespace Admin\Controller;

/**
 * 模型数据管理控制器
 *
 * @author 凡星
 */
class ManagerMenuController extends AdminController {
	
	/**
	 * 显示指定模型列表数据
	 *
	 * @param String $model
	 *        	模型标识
	 * @author 凡星
	 */
	public function lists($model = null, $p = 0) {
		$model = $this->getModel ( 'manager_menu' );
		
		$map ['uid'] = I ( 'uid' );
		session ( 'common_condition', $map );
		
		$list_data = $this->_get_model_list ( $model, $p );
		if (empty ( $list_data ['list_data'] )) {
			// 第一次进入没有配置参数时，默认从管理员菜单里复制过来
			$max_id = M ( 'manager_menu' )->getField ( 'max(id)' );
			
			$menu_map ['uid'] = C ( 'USER_ADMINISTRATOR' );
			$menus = M ( 'manager_menu' )->where ( $menu_map )->order ( 'id asc' )->select ();
			// dump ( $menus );
			
			foreach ( $menus as $vo ) {
				$vo ['id'] += $max_id;
				empty ( $vo ['pid'] ) || $vo ['pid'] += $max_id;
				$vo ['uid'] = $map ['uid'];
				
				M ( 'manager_menu' )->add ( $vo );
			}
			// 重新获取数据
			$map ['uid'] = I ( 'uid' );
			session ( 'common_condition', $map );
			
			$list_data = $this->_get_model_list ( $model, $p );
		}
		$list_data ['list_data'] = $this->_get_data ( $map );
		$list_data ['_page'] = '';
		$this->assign ( $list_data );
		
		$this->meta_title = '导航菜单管理';
		
		$this->display ();
	}
	function _get_data($map) {
		$list = M ( 'manager_menu' )->field ( true )->where ( $map )->select ();
		
		// 取一级菜单
		foreach ( $list as $k => $vo ) {
			if ($vo ['pid'] != 0)
				continue;
			
			$one_arr [$vo ['id']] = $vo;
			unset ( $list [$k] );
		}
		
		foreach ( $one_arr as $p ) {
			$data [] = $p;
			
			$two_arr = array ();
			foreach ( $list as $key => $l ) {
				if ($l ['pid'] != $p ['id'])
					continue;
				
				$l ['title'] = '├──' . $l ['title'];
				$two_arr [] = $l;
				unset ( $list [$key] );
			}
			
			$data = array_merge ( $data, $two_arr );
		}
		
		return $data;
	}
	public function edit($model = null, $id = 0) {
		if (IS_POST) {
			$_POST = $this->_check_data ( $_POST );
		} else {
			$post_url = U ( 'edit?uid=' . I ( 'uid' ) );
			$this->assign ( 'post_url', $post_url );
		}
		
		$model = $this->getModel ( 'manager_menu' );
		parent::common_edit ( $model, $id );
	}
	public function add() {
		$model = $this->getModel ( 'manager_menu' );
		if (IS_POST) {
			$_POST = $this->_check_data ( $_POST );
			$Model = D ( parse_name ( get_table_name ( $model ['id'] ), 1 ) );
			// 获取模型的字段信息
			$Model = $this->checkAttr ( $Model, $model ['id'] );
			if ($Model->create () && $id = $Model->add ()) {
				$this->success ( '添加成功！', U ( 'lists?model=' . $model ['name'], $this->get_param ) );
			} else {
				$this->error ( $Model->getError () );
			}
		} else {
			$fields = get_model_attribute ( $model ['id'] );
			$this->assign ( 'fields', $fields );
			
			$post_url = U ( 'add?uid=' . I ( 'uid' ) );
			$this->assign ( 'post_url', $post_url );
			
			$this->display ();
		}
	}
	public function del($ids = null) {
		$model = $this->getModel ( 'manager_menu' );
		parent::common_del ( $model, $ids );
	}
	private function _check_data($data) {
		if ($data ['url_type'] == 0) {
			$data ['url'] = '';
		} else {
			$data ['addon_name'] = '';
		}
		return $data;
	}
}