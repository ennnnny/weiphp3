<?php

namespace Addons\Ask\Controller;

use Home\Controller\AddonsController;

class QuestionController extends AddonsController {
	var $model;
	var $ask_id;
	function _initialize() {
		parent::_initialize ();
		
		$this->model = $this->getModel ( 'ask_question' );
		
		$param ['ask_id'] = $this->ask_id = intval ( $_REQUEST ['ask_id'] );
		
		$res ['title'] = '微抢答';
		$res ['url'] = addons_url ( 'Ask://Ask/lists' );
		$res ['class'] = '';
		$nav [] = $res;
		
		$res ['title'] = '问题管理';
		$res ['url'] = addons_url ( 'Ask://Question/lists', $param );
		$res ['class'] = 'current';
		$nav [] = $res;
		
		$this->assign ( 'nav', $nav );
	}
	// 通用插件的列表模型
	public function lists() {
		$param ['ask_id'] = $this->ask_id;
		$param ['model'] = $this->model ['id'];
		$add_url = U ( 'add', $param );
		$this->assign ( 'add_url', $add_url );
		
		$map ['ask_id'] = $this->ask_id;
		session ( 'common_condition', $map );
		
		parent::common_lists ( $this->model, 0, '', $order = 'sort asc,id asc' );
	}
	// 通用插件的编辑模型
	public function edit() {
		$id = I ( 'id' );
		
		if (IS_POST) {
			$Model = D ( parse_name ( get_table_name ( $this->model ['id'] ), 1 ) );
			// 获取模型的字段信息
			$Model = $this->checkAttr ( $Model, $this->model ['id'] );
			if ($Model->create () && $Model->save ()) {
				D ( 'AskQuestion' )->setLastQuestion ( $this->ask_id );
				$param ['ask_id'] = $this->ask_id;
				$param ['model'] = $this->model ['id'];
				$url = U ( 'lists', $param );
				$this->success ( '保存' . $this->model ['title'] . '成功！', $url );
			} else {
				$this->error ( $Model->getError () );
			}
		}
		
		parent::common_edit ( $this->model, $id );
	}
	
	// 通用插件的增加模型
	public function add() {
		if (IS_POST) {
			$Model = D ( parse_name ( get_table_name ( $this->model ['id'] ), 1 ) );
			// 获取模型的字段信息
			$Model = $this->checkAttr ( $Model, $this->model ['id'] );
			if ($Model->create () && $id = $Model->add ()) {
				D ( 'AskQuestion' )->setLastQuestion ( $this->ask_id );
				$param ['ask_id'] = $this->ask_id;
				$param ['model'] = $this->model ['id'];
				$url = U ( 'lists', $param );
				$this->success ( '添加' . $this->model ['title'] . '成功！', $url );
			} else {
				$this->error ( $Model->getError () );
			}
			exit ();
		}
		
		$normal_tips = '字段类型为单选、多选的参数格式第行一项，每项的值和标题用英文冒号分开。如：<br/>A:男<br/>B:女<br/>C:保密';
		$this->assign ( 'normal_tips', $normal_tips );
		
		parent::common_add ( $this->model );
	}
	
	// 通用插件的删除模型
	public function del() {
		$model = $this->model;

		! empty ( $ids ) || $ids = I ( 'id' );
		! empty ( $ids ) || $ids = array_filter ( array_unique ( ( array ) I ( 'ids', 0 ) ) );
		! empty ( $ids ) || $this->error ( '请选择要操作的数据!' );
		
		$Model = M ( get_table_name ( $model ['id'] ) );
		$map ['id'] = array (
				'in',
				$ids 
		);

		// 插件里的操作自动加上Token限制
		$token = get_token ();
		if (defined ( 'ADDON_PUBLIC_PATH' ) && ! empty ( $token )) {
			$map ['token'] = $token;
		}
		
		if ($Model->where ( $map )->delete ()) {
			D ( 'AskQuestion' )->setLastQuestion ( $this->ask_id );
			
			$this->success ( '删除成功' );
		} else {
			$this->error ( '删除失败！' );
		}
	}
}
