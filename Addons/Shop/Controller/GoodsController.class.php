<?php

namespace Addons\Shop\Controller;

use Addons\Shop\Controller\BaseController;

class GoodsController extends BaseController {
	var $model;
	function _initialize() {
		$this->model = $this->getModel ( 'shop_goods' );
		parent::_initialize ();
	}
	// 通用插件的列表模型
	public function lists() {
		$map ['token'] = get_token ();
		$map ['shop_id'] = $this->shop_id;
		session ( 'common_condition', $map );
		$list_data = $this->_get_model_list ( $this->model );
		// 分类数据
		$map ['is_show'] = 1;
		$list = M ( 'shop_goods_category' )->where ( $map )->field ( 'id,title' )->select ();
		$cate [0] = '';
		foreach ( $list as $vo ) {
			$cate [$vo ['id']] = $vo ['title'];
		}
		foreach ( $list_data ['list_data'] as &$vo ) {
			$vo ['category_id'] = intval ( $vo ['category_id'] );
			$vo ['category_id'] = $cate [$vo ['category_id']];
		}
		$this->assign ( $list_data );
		$templateFile = $this->model ['template_list'] ? $this->model ['template_list'] : '';
		$this->display ( $templateFile );
	}
	// 通用插件的编辑模型
	public function edit() {
		$model = $this->model;
		$id = I ( 'id' );
		$shop_id = $this->shop_id;
		
		if (IS_POST) {
			if ($_POST ['imgs'] && count ( $_POST ['imgs'] ) > 0) {
				$_POST ['imgs'] = implode ( ',', $_POST ['imgs'] );
			}
			;
			$Model = D ( parse_name ( get_table_name ( $model ['id'] ), 1 ) );
			// 获取模型的字段信息
			$Model = $this->checkAttr ( $Model, $model ['id'] );
			if ($Model->create () && $Model->save ()) {
				D ( 'Common/Keyword' )->set ( $_POST ['keyword'], _ADDONS, $id, $_POST ['keyword_type'], 'custom_reply_news' );
				
				$this->success ( '保存' . $model ['title'] . '成功！', U ( 'lists?model=' . $model ['name'] . '&shop_id=' . $shop_id ) );
			} else {
				$this->error ( $Model->getError () );
			}
		} else {
			$fields = get_model_attribute ( $model ['id'] );
			
			$extra = $this->getCateData ();
			if (! empty ( $extra )) {
				foreach ( $fields as &$vo ) {
					if ($vo ['name'] == 'category_id') {
						$vo ['extra'] .= "\r\n" . $extra;
					}
				}
			}
			
			// 获取数据
			$data = M ( get_table_name ( $model ['id'] ) )->find ( $id );
			$data || $this->error ( '数据不存在！' );
			
			$token = get_token ();
			if (isset ( $data ['token'] ) && $token != $data ['token'] && defined ( 'ADDON_PUBLIC_PATH' )) {
				$this->error ( '非法访问！' );
			}
			$data ['imgs'] = explode ( ',', $data ['imgs'] );
			$this->assign ( 'fields', $fields );
			$this->assign ( 'data', $data );
			$this->meta_title = '编辑' . $model ['title'];
			
			$this->display ();
		}
	}
	
	// 通用插件的增加模型
	public function add() {
		$model = $this->model;
		$Model = D ( parse_name ( get_table_name ( $model ['id'] ), 1 ) );
		$shop_id = $_POST ['shop_id'] = $this->shop_id;
		;
		if (IS_POST) {
			if ($_POST ['imgs'] && count ( $_POST ['imgs'] ) > 0) {
				$_POST ['imgs'] = implode ( ',', $_POST ['imgs'] );
			}
			;
			// 获取模型的字段信息
			$Model = $this->checkAttr ( $Model, $model ['id'] );
			if ($Model->create () && $id = $Model->add ()) {
				D ( 'Common/Keyword' )->set ( $_POST ['keyword'], _ADDONS, $id, $_POST ['keyword_type'], 'custom_reply_news' );
				
				$this->success ( '添加' . $model ['title'] . '成功！', U ( 'lists?model=' . $model ['name'] . '&shop_id=' . $shop_id ) );
			} else {
				$this->error ( $Model->getError () );
			}
		} else {
			$fields = get_model_attribute ( $model ['id'] );
			
			$extra = $this->getCateData ();
			
			if (! empty ( $extra )) {
				foreach ( $fields as &$vo ) {
					if ($vo ['name'] == 'category_id') {
						$vo ['extra'] .= "\r\n" . $extra;
					}
				}
			}
			$this->assign ( 'fields', $fields );
			$this->meta_title = '新增' . $model ['title'];
			
			$this->display ();
		}
	}
	
	// 通用插件的删除模型
	public function del() {
	    $id=I('id');
	    $ids=I('ids');
	    if (!empty($id)){
	        $key = 'Goods_getInfo_' . $id;
	        S ( $key, null );
	    }else {
	         foreach ($ids as $i){
	           $key = 'Goods_getInfo_' . $i;
	           S ( $key, null );
	         }
	    }
		parent::common_del ( $this->model );
	}
	
	// 获取所属分类
	function getCateData() {
		$map ['is_show'] = 1;
		$map ['token'] = get_token ();
		$map['shop_id']=$this->shop_id;
		$list = M ( 'shop_goods_category' )->where ( $map )->select ();
		foreach ( $list as $v ) {
			$extra .= $v ['id'] . ':' . $v ['title'] . "\r\n";
		}
		return $extra;
	}
	function set_show() {
		$save ['is_show'] = 1 - I ( 'is_show' );
		$map ['shop_id'] = $this->shop_id;
		$map['id']=I('id');
		$res = M ( 'shop_goods' )->where ( $map )->save ( $save );
		$this->success ( '操作成功' );
	}
}