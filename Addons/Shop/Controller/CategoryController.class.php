<?php

namespace Addons\Shop\Controller;

use Addons\Shop\Controller\BaseController;

class CategoryController extends BaseController {
	var $model;
	function _initialize() {
		$this->model = $this->getModel ( 'shop_goods_category' );
		parent::_initialize ();
	}
	// 通用插件的列表模型
	public function lists() {
		$map ['token'] = get_token ();
		$map['shop_id'] = $this->shop_id;;
		session ( 'common_condition', $map );
		
		$list_data = $this->_get_model_list ( $this->model );
		$this->assign ( $list_data );
		// dump ( $list_data );
		
		$templateFile = $this->model ['template_list'] ? $this->model ['template_list'] : '';
		$this->display ( $templateFile );
	}
	// 通用插件的编辑模型
	public function edit() {
		$_POST['shop_id'] = $this -> shop_id;
		parent::common_edit ( $this->model );
	}
	
	// 通用插件的增加模型
	public function add() {
		$_POST['shop_id'] = $this -> shop_id;
		parent::common_add ( $this->model );
	}
	
	// 通用插件的删除模型
	public function del() {
		parent::common_del ( $this->model );
	}
}
