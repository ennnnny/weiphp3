<?php

namespace Addons\Coupon\Controller;

use Home\Controller\AddonsController;

class SnController extends AddonsController {
	var $table = 'sn_code';
	var $addon = 'Coupon';
	function _initialize() {
		parent::_initialize ();
		
		$controller = strtolower ( _CONTROLLER );
		
		$res ['title'] = '优惠券';
		$res ['url'] = addons_url ( 'Coupon://Coupon/lists' );
		$res ['class'] = $controller == 'coupon' ? 'current' : '';
		$nav [] = $res;
		
		$this->assign ( 'nav', $nav );
	}
	function lists() {
		$this->assign ( 'add_button', false );
		$this->assign ( 'del_button', false );
		$this->assign ( 'search_button', false );
		$this->assign ( 'check_all', false );
		
		$top_more_button [] = array (
				'title' => '导出数据',
				'url' => U ( 'export', array (
						'target_id' => I ( 'target_id' ) 
				) ) 
		);
		$this->assign ( 'top_more_button', $top_more_button );
		
		$model = $this->getModel ( $this->table );
		$page = I ( 'p', 1, 'intval' ); // 默认显示第一页数据
		                                
		// 解析列表规则
		$list_data = $this->_list_grid ( $model );
		unset ( $list_data ['list_grids'] [2] );
		$grids = $list_data ['list_grids'];
		$fields = $list_data ['fields'];
		
		// 搜索条件
		$map ['addon'] = $this->addon;
		$map ['target_id'] = I ( 'target_id' );
		$map ['token'] = get_token ();
		session ( 'common_condition', $map );
		$map = $this->_search_map ( $model, $fields );
		
		$row = empty ( $model ['list_row'] ) ? 20 : $model ['list_row'];
		
		empty ( $fields ) || in_array ( 'id', $fields ) || array_push ( $fields, 'id' );
		$name = parse_name ( get_table_name ( $model ['id'] ), true );
		$data = M ( $name )->field ( empty ( $fields ) ? true : $fields )->where ( $map )->order ( 'id DESC' )->page ( $page, $row )->select ();
		
		/* 查询记录总数 */
		$count = M ( $name )->where ( $map )->count ();
		$list_data ['list_data'] = $data;
		
		// 分页
		if ($count > $row) {
			$page = new \Think\Page ( $count, $row );
			$page->setConfig ( 'theme', '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%' );
			$list_data ['_page'] = $page->show ();
		}
		$this->assign ( $list_data );
		// dump($list_data);
		
		$this->display ();
	}
	function export() {
		$model = $this->getModel ( 'sn_code' );
		
		// 搜索条件
		$map ['addon'] = $this->addon;
		$map ['target_id'] = I ( 'target_id' );
		$map ['token'] = get_token ();
		session ( 'common_condition', $map );
		
		parent::common_export ( $model );
	}
	function del() {
		$model = $this->getModel ( 'sn_code' );
		parent::del ( $model );
	}
	function test3() {
		$id = I ( 'id' );
		$res = D ( 'Common/SnCode' )->set_use ( $id );
		if ($res == - 1) {
			$this->error ( '数据不存在' );
		} elseif ($res) {
			$map ['is_use'] = 1;
			$map ['target_id'] = $data ['target_id'];
			$map ['addon'] = 'Coupon';
			$save ['use_count'] = intval ( D ( 'Common/SnCode' )->where ( $map )->count () );
			D ( 'Coupon' )->update ( $data ['target_id'], $save );
			$this->success ( '设置成功' );
		} else {
			$this->error ( '设置失败' );
		}
	}
	function set_use() {
	    $id = I ( 'id' );
	    $dao = D ( 'Common/SnCode' );
	    $data = $dao->getInfoById ( $id );
	    if (! $data) {
	        $this->error ( '数据不存在' );
	    }
	
	    if ($data ['is_use']) {
	        $data ['is_use'] = 0;
	        $data ['use_time'] = '';
	    } else {
	        $data ['is_use'] = 1;
	        $data ['use_time'] = time ();
	    }
	
	    $res = $dao->update ( $id, $data );
	    if ($res) {
	        $map ['is_use'] = 1;
	        $map ['target_id'] = $data ['target_id'];
	        $map ['addon'] = 'Coupon';
	        $save ['use_count'] = intval ( $dao->where ( $map )->count () );
	        D ( 'Coupon' )->update ( $data ['target_id'], $save );
	        $this->success ( '设置成功' );
	    } else {
	        $this->error ( '设置失败' );
	    }
	}
}
