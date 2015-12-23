<?php

namespace Addons\Reserve\Controller;

use Addons\Reserve\Controller\BaseController;

class ReserveValueController extends BaseController {
	var $model;
	var $reserve_id;
	function _initialize() {
		parent::_initialize ();
		
		$this->model = $this->getModel ( 'reserve_value' );
		
		// $param ['reserve_id'] = $this->reserve_id = intval ( $_REQUEST ['reserve_id'] );
		$param ['reserve_id'] = $this->reserve_id = intval ( I ( 'reserve_id' ) );
		$res ['title'] = '微预约';
		$res ['url'] = addons_url ( 'Reserve://Reserve/lists' );
		$res ['class'] = '';
		$nav [] = $res;
		
		$res ['title'] = '预约列表';
		$res ['url'] = addons_url ( 'Reserve://ReserveValue/lists', $param );
		$res ['class'] = 'current';
		$nav [] = $res;
		
		$this->assign ( 'nav', $nav );
	}
	
	// 通用插件的列表模型
	public function lists() {
		// 解析列表规则
		$fields [] = 'openid';
		$fields [] = 'cTime';
		$fields [] = 'reserve_id';
		
		$girds ['field'] = 'uid';
		$girds ['title'] = '用户';
		$list_data ['list_grids'] [] = $girds;
		
		$girds ['field'] = 'cTime|time_format';
		$girds ['title'] = '增加时间';
		$list_data ['list_grids'] [] = $girds;
		
		$map ['reserve_id'] = $this->reserve_id;
		$attribute = M ( 'reserve_attribute' )->where ( $map )->order ( 'sort asc, id asc' )->select ();
		foreach ( $attribute as &$fd ) {
			$fd ['name'] = 'field_' . $fd ['id'];
		}
		foreach ( $attribute as $vo ) {
			$girds ['field'] = $fields [] = $vo ['name'];
			$girds ['title'] = $vo ['title'];
			$list_data ['list_grids'] [] = $girds;
			
			$attr [$vo ['name']] ['type'] = $vo ['type'];
			
			if ($vo ['type'] == 'radio' || $vo ['type'] == 'checkbox' || $vo ['type'] == 'select') {
				$extra = parse_config_attr ( $vo ['extra'] );
				if (is_array ( $extra ) && ! empty ( $extra )) {
					$attr [$vo ['name']] ['extra'] = $extra;
				}
			} elseif ($vo ['type'] == 'cascade' || $vo ['type'] == 'dynamic_select') {
				$attr [$vo ['name']] ['extra'] = $vo ['extra'];
			}
		}
		
		$fields [] = 'id';
		$girds ['field'] [0] = 'id';
		$girds ['title'] = '操作';
		$girds ['href'] = '[EDIT]&reserve_id=[reserve_id]&id=[id]|编辑,[DELETE]&reserve_id=[reserve_id]&id=[id]|	删除';
		$list_data ['list_grids'] [] = $girds;
		
		$list_data ['fields'] = $fields;
		
		$param ['reserve_id'] = $this->reserve_id;
		$param ['model'] = $this->model ['id'];
		$add_url = U ( 'add', $param );
		$this->assign ( 'add_url', $add_url );
		
		// 搜索条件
		$map = $this->_search_map ( $this->model, $fields );
		
		$page = I ( 'p', 1, 'intval' );
		$row = 20;
		
		$name = parse_name ( get_table_name ( $this->model ['id'] ), true );
		$list = M ( $name )->where ( $map )->order ( 'id DESC' )->selectPage ();
		$list_data = array_merge ( $list_data, $list );
		foreach ( $list_data ['list_data'] as &$vo ) {
			$value = unserialize ( $vo ['value'] );
			foreach ( $value as $n => &$d ) {
				$type = $attr [$n] ['type'];
				$extra = $attr [$n] ['extra'];
				if ($type == 'radio' || $type == 'select') {
					
					if ($extra) {
						$extArr = explode ( ' ', $extra [0] );
						$d = $extArr [$d];
					}
				} elseif ($type == 'checkbox') {
					$extArr = explode ( ' ', $extra [0] );
					foreach ( $d as &$v ) {
						if (isset ( $extArr [$v] )) {
							$v = $extArr [$v];
						}
					}
					$d = implode ( ', ', $d );
				} elseif ($type == 'datetime') {
					$d = time_format ( $d );
				} elseif ($type == 'picture') {
					$d = get_cover_url ( $d );
				} elseif ($type == 'cascade') {
					$d = getCascadeTitle ( $d, $extra );
				}
			}
			
			unset ( $vo ['value'] );
			$vo = array_merge ( $vo, $value );
			$vo ['uid'] = get_nickname ( $vo ['uid'] );
		}
		
		$this->assign ( $list_data );
		// dump ( $list_data );
		
		$this->display ();
	}
	
	// 通用插件的编辑模型
	public function edit() {
		$this->add ();
	}
	function detail() {
		$id = I ( 'id' );
		// $reserve = M ( 'reserve' )->find ( $id );
		$reserve = D ( 'Reserve' )->getInfo ( $id );
		$reserve ['cover'] = ! empty ( $reserve ['cover'] ) ? get_cover_url ( $reserve ['cover'] ) : ADDON_PUBLIC_PATH . '/background.png';
		$this->assign ( 'reserve', $reserve );
		
		$this->display ();
	}
	
	// 通用插件的删除模型
	public function del() {
		parent::common_del ( $this->model );
	}
}
