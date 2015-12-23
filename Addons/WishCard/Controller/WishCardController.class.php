<?php

namespace Addons\WishCard\Controller;
use Home\Controller\AddonsController;

class WishCardController extends BaseController{
	function add() {
		$normal_tips = "贺卡模板目前仅支持在贺卡插件目录下由制作人员添加";
		$this -> assign('normal_tips',$normal_tips);
		$map ['token'] = get_token ();
		$model = $this->getModel('wish_card');
		if (IS_POST) {
			$Model = D ( parse_name ( get_table_name ( $model ['id'] ), 1 ) );
			// 获取模型的字段信息
			$Model = $this->checkAttr ( $Model, $model ['id'] );
			if ($Model->create () && $id = $Model->add ()) {
				$this->_saveKeyword ( $model, $id );
			}
			$this->success ( '保存' . $model ['title'] . '成功！', U ( 'lists?model=' . $model ['name'] ) );
		} else {
			$this->display ();
		}
	}
	function edit() {
		$map ['id'] = $id = I ('id');
		$map ['token'] = get_token ();
		$model = $this->getModel();
		if (IS_POST) {
			$Model = D ( parse_name ( get_table_name ( $model ['id'] ), 1 ) );
			// 获取模型的字段信息
			$Model = $this->checkAttr ( $Model, $model ['id'] );
			if ($Model->create () && $Model->save ()) {
				$this->_saveKeyword ( $model, $id );
			}
			// 清空缓存
			method_exists ( $Model, 'clear' ) && $Model->clear ( $id, 'edit' );
			$this->success ( '保存' . $model ['title'] . '成功！', U ( 'lists?model=' . $model ['name'] ) );
		} else {
			$normal_tips = "贺卡模板目前仅支持在贺卡插件目录下由制作人员添加";
			$this -> assign('normal_tips',$normal_tips);
			$data = D('WishCard')->find($id);
			$this -> assign('data',$data);
			$template = $this -> _getTemplateInfo($data['template_cate'],$data['template']);
			$this -> assign('template',$template);
			$this->display ();
		}
	}
	function getTemplist(){
		$cateFile = I('cateFile');
		$data = $this -> _getTemplateByDir($cateFile);
		$this -> ajaxReturn($data,'JSON');
	}
	
}
