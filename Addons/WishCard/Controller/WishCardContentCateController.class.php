<?php

namespace Addons\WishCard\Controller;
use Home\Controller\AddonsController;

class WishCardContentCateController extends BaseController{
	var $model;
	function _initialize() {
		$this->model = $this->getModel ( 'wish_card_content_cate' );
		
		$res['url'] = addons_url ( 'WishCard://WishCardContent/lists' );
		$res['title'] = "祝福语"; 
		$res['class'] = "";
		$sub_nav[] = $res;
		$res['url'] = addons_url ( 'WishCard://WishCardContentCate/lists' );;
		$res['title'] = "类别"; 
		$res['class'] = "cur";
		$sub_nav[] = $res;
		$this -> assign('sub_nav',$sub_nav);
		
		parent::_initialize ();
	}
	// 通用插件的列表模型
	public function lists() {
		$map ['token'] = get_token ();
		session ( 'common_condition', $map );
	
		$list_data = $this->_get_model_list ( $this->model );
		$this->assign ( $list_data );
		$templateFile = $this->model ['template_list'] ? $this->model ['template_list'] : '';
		$this->display ( $templateFile );
	}
}
