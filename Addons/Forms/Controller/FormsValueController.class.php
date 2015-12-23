<?php

namespace Addons\Forms\Controller;

use Addons\Forms\Controller\BaseController;

class FormsValueController extends BaseController {
	var $model;
	var $forms_id;
	function _initialize() {
		parent::_initialize ();
		
		$this->model = $this->getModel ( 'forms_value' );
		
		// $param ['forms_id'] = $this->forms_id = intval ( $_REQUEST ['forms_id'] );
		$param ['forms_id'] = $this->forms_id = intval ( I ( 'forms_id' ) );
		$res ['title'] = '通用表单';
		$res ['url'] = addons_url ( 'Forms://Forms/lists' );
		$res ['class'] = '';
		$nav [] = $res;
		
		$res ['title'] = '数据管理';
		$res ['url'] = addons_url ( 'Forms://FormsValue/lists', $param );
		$res ['class'] = 'current';
		$nav [] = $res;
		
		$this->assign ( 'nav', $nav );
	}
	
	// 通用插件的列表模型
	public function lists() {
		$this->assign ( 'add_button', false );
		$this->assign ( 'search_button', false );
		// 解析列表规则
		$fields [] = 'openid';
		$fields [] = 'cTime';
		$fields [] = 'forms_id';
		
		$girds ['field'] = 'uid';
		$girds ['title'] = '用户';
		$list_data ['list_grids'] [] = $girds;
		
		$girds ['field'] = 'cTime|time_format';
		$girds ['title'] = '增加时间';
		$list_data ['list_grids'] [] = $girds;
		
		$map ['forms_id'] = $this->forms_id;
		$attribute = M ( 'forms_attribute' )->where ( $map )->order ( 'sort asc, id asc' )->select ();
		foreach ( $attribute as &$fd ) {
			$fd ['name'] = 'attr_' . $fd ['forms_id'] . '_' . $fd ['sort'];
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
		$girds ['href'] = '[DELETE]&forms_id=[forms_id]&id=[id]|删除';
		$list_data ['list_grids'] [] = $girds;
		
		$list_data ['fields'] = $fields;
		
		$param ['forms_id'] = $this->forms_id;
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
// 						$extArr = explode ( ' ', $extra [0] );
						$d = $extra [$d];
					}
				} elseif ($type == 'checkbox') {
					
// 					$extArr = explode ( ' ', $extra);
			
					
					//var_dump($extra[1]);
					foreach ( $d as &$v ) {
						//var_dump($d);
						if (isset ( $extra [$v] )) {
							$v = $extra [$v];
						}
					}
					$d = implode ( ', ', $d );
				} elseif ($type == 'datetime') {
					$d = time_format ( $d );
				} elseif ($type == 'picture') {
					$imgstr='';
					foreach ($d as $p){
						$imgstr.=get_img_html($p).'&nbsp;&nbsp;';
					}
					$d=$imgstr;
// 					dump($d);
// 					$d = get_cover_url ( $d );
				} elseif ($type == 'cascade') {
					$d = getCascadeTitle ( $d, $extra );
				}
			}
			
			unset ( $vo ['value'] );
			$vo = array_merge ( $vo, $value );
			$vo ['uid'] = get_nickname ( $vo ['uid'] );
		}
		
		$this->assign ( $list_data );
		//dump ( $list_data );
		
		$this->display ();
	}
	
	// 通用插件的编辑模型
	public function edit() {
		$this->add ();
	}
	function detail() {
		$id = I ( 'id' );
		// $forms = M ( 'forms' )->find ( $id );
		$forms = D ( 'Forms' )->getInfo ( $id );
		$forms ['cover'] = ! empty ( $forms ['cover'] ) ? get_cover_url ( $forms ['cover'] ) : ADDON_PUBLIC_PATH . '/background.png';
		$this->assign ( 'forms', $forms );
		
		$this->display ();
	}
	
	// 通用插件的删除模型
	public function del() {
		parent::common_del ( $this->model );
	}
}
