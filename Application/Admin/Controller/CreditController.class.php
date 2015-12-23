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
class CreditController extends AdminController {
	
	/**
	 * 显示指定模型列表数据
	 *
	 * @param String $model
	 *        	模型标识
	 * @author 凡星
	 */
	public function lists() {
		$model = $this->getModel ( 'credit_config' );
		
		$map ['token'] = '0';
		session ( 'common_condition', $map );
		$list_data = $this->_get_model_list ( $model );
		
		$this->assign ( $list_data );
		
		$this->meta_title = $model ['title'] . '列表';
		
		$this->display ( 'Think:lists' );
	}
	public function edit($model = null, $id = 0) {
		D ( 'Common/Credit' )->clear ();
		is_array ( $model ) || $model = $this->getModel ( 'credit_confit' );
		$this->meta_title = '编辑' . $model ['title'];
		parent::common_edit ( $model, $id, 'Think:edit' );
	}
	public function add($model = null) {
		is_array ( $model ) || $model = $this->getModel ( 'credit_confit' );
		$this->meta_title = '新增' . $model ['title'];
		parent::common_add ( $model, 'Think:add' );
	}
	public function del($model = null, $ids = null) {
		D ( 'Common/Credit' )->clear ();
		is_array ( $model ) || $model = $this->getModel ( 'credit_confit' );
		parent::common_del ( $model, $ids );
	}
}