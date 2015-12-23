<?php

namespace Addons\Survey\Controller;

use Home\Controller\AddonsController;

class AnswerController extends AddonsController {
	var $model;
	var $survey_id;
	function _initialize() {
		parent::_initialize ();
		
		$this->model = $this->getModel ( 'survey_answer' );
		
		$param ['survey_id'] = $this->survey_id = intval ( $_REQUEST ['survey_id'] );
		
		$res ['title'] = '微调研';
		$res ['url'] = addons_url ( 'Survey://Survey/lists' );
		$res ['class'] = '';
		$nav [] = $res;
		
		$res ['title'] = '数据管理';
		$res ['url'] = addons_url ( 'Survey://Answer/lists', $param );
		$res ['class'] = 'current';
		$nav [] = $res;
		
		$this->assign ( 'nav', $nav );
	}
	// 通用插件的列表模型
	public function lists() {
		$this->assign ( 'add_button', false );
		$this->assign ( 'search_button', false );
		$this->assign ( 'del_button', false );
		$this->assign ( 'check_all', false );
		
		// 解析列表规则
		$data = $this->_list_grid ( $this->model );
		$this->assign ( $data );
		
		// 搜索条件
		$map = $this->_search_map ( $this->model, $data ['fields'] );
		
		$name = parse_name ( get_table_name ( $this->model ['id'] ), true );
		
		$list = M ( $name )->where ( $map )->order ( 'id DESC' )->group ( 'uid' )->selectPage ();
		foreach ( $list ['list_data'] as &$vo ) {
			$user = getUserInfo ( $vo ['uid'] );
			
			$vo ['truename'] = $user ['nickname'];
			$vo ['mobile'] = $user ['mobile'];
		}
		
		$this->assign ( $list );
		
		$this->display ();
	}
	function detail() {
		$this->assign ( 'add_button', false );
		$this->assign ( 'search_button', false );
		$this->assign ( 'del_button', false );
		$this->assign ( 'check_all', false );
		$model = $this->getModel ( 'survey_answer' );
		// 解析列表规则
		$fields [] = 'question';
		$fields [] = 'answer';
		
		$girds ['field'] = 'question';
		$girds ['title'] = '问题';
		$list_data ['list_grids'] ['question'] = $girds;
		
		$girds ['field'] = 'answer';
		$girds ['title'] = '回答内容';
		$list_data ['list_grids'] ['answer'] = $girds;
		
		$list_data ['fields'] = $fields;
		$this->assign ( $list_data );
		
		$map ['survey_id'] = intval ( $_REQUEST ['survey_id'] );
		$questions = M ( 'survey_question' )->where ( $map )->select ();
		foreach ( $questions as $q ) {
			$title [$q ['id']] = $q ['title'];
			$type [$q ['id']] = $q ['type'];
			$extra [$q ['id']] = parse_config_attr ( $q ['extra'] );
		}
		
		$map ['uid'] = intval ( $_REQUEST ['uid'] );
		$answers = M ( 'survey_answer' )->where ( $map )->select ();
		foreach ( $answers as $a ) {
			$qid = $a ['question_id'];
			$data ['question'] = $title [$qid];
			$value = unserialize ( $a ['answer'] );
			switch ($type [$qid]) {
				case 'radio' :
					$data ['answer'] = $extra [$qid] [$value];
					break;
				case 'checkbox' :
					foreach ( $value as $v ) {
						$data ['answer'] [] = $extra [$qid] [$v];
					}
					$data ['answer'] = implode ( ',', $data ['answer'] );
					break;
				default :
					$data ['answer'] = $value;
			}
			$list [] = $data;
			unset ( $data );
		}
		$this->assign ( 'list_data', $list );
		
		$this->display ( T ( 'lists' ) );
	}
	
	// 通用插件的删除模型
	public function del() {
		parent::common_del ( $this->model );
	}
}
