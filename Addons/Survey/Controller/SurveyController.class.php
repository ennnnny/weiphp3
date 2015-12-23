<?php

namespace Addons\Survey\Controller;

use Home\Controller\AddonsController;

class SurveyController extends AddonsController {
	function survey_question() {
		$param ['survey_id'] = I ( 'id', 0, 'intval' );
		$url = addons_url ( 'Survey://Question/lists', $param );
		// dump($url);
		redirect ( $url );
	}
	function survey_answer() {
		$param ['survey_id'] = I ( 'id', 0, 'intval' );
		$url = addons_url ( 'Survey://Answer/lists', $param );
		// dump($url);
		redirect ( $url );
	}
	function preview() {
		$id = I ( 'id', 0, 'intval' );
		$url = U ( 'index', array (
				'id' => $id 
		) );
		$this->assign ( 'url', $url );
		$this->display ( SITE_PATH . '/Application/Home/View/default/Addons/preview.html' );
	}
	function survey() {
		$id = I ( 'get.id', 0, 'intval' );
		$num = I ( 'num', 1, 'intval' );
		$token = get_token ();
		$survey = D ( 'Survey' )->getSurveyInfo ( $id );
		$list = D ( 'SurveyQuestion' )->getQuestionInfo ( $id );
		if (IS_POST) {
			$follow_id = $this->mid;
			$question_id = I ( 'post.question_id', 0, 'intval' );
			$answer = D ( 'SurveyAnswer' )->getAnswerInfo ( $id, $question_id, $follow_id );
			
			$data ['cTime'] = time ();
			$data ['answer'] = serialize ( $_POST ['answer'] );
			if ($answer) {
				D ( 'SurveyAnswer' )->updateAnswer ( $id, $question_id, $follow_id, $data );
			} else {
				$data ['survey_id'] = $id;
				$data ['token'] = $token;
				$data ['question_id'] = $question_id;
				$data ['uid'] = $follow_id;
				$data ['openid'] = get_openid ();
				M ( 'survey_answer' )->add ( $data );
				D ( 'SurveyAnswer' )->getAnswerInfo ( $id, $question_id, $follow_id, true );
			}
			$num = $num + 1;
		}
		$question_id = I ( 'post.next_id', 0, 'intval' );
		if ($question_id == '-1') {
			redirect ( U ( 'finish', 'survey_id=' . $id ) );
		}
		if (empty ( $question_id )) {
			$question = $list [0];
			$next_id = isset ( $list [1] ['id'] ) ? $list [1] ['id'] : '-1';
		} else {
			foreach ( $list as $k => $vo ) {
				if ($vo ['id'] == $question_id) {
					$question = $vo;
					$next_id = isset ( $list [$k + 1] ['id'] ) ? $list [$k + 1] ['id'] : '-1';
				}
			}
		}
		
		$extra = parse_config_attr ( $question ['extra'] );
		$this->assign ( 'survey', $survey );
		$this->assign ( 'question', $question );
		$this->assign ( 'next_id', $next_id );
		$this->assign ( 'extra', $extra );
		$this->assign ( 'num', $num );
		
		$this->display ();
	}
	function index() {
		$id = $map ['id'] = I ( 'id', 0, 'intval' );
		$map ['token'] = get_token ();
		$public_info = get_token_appinfo ( $map ['token'] );
		$overtime = $this->_is_overtime ( $id );
		$overtime = $overtime ? '1' : '0';
		$this->assign ( 'overtime', $overtime );
		$info = M ( 'survey' )->where ( $map )->find ();
		$this->assign ( 'info', $info );
		$this->assign ( 'public_info', $public_info );
		$this->display ();
	}
	function finish() {
		$survey_id = I ( 'survey_id', 0, 'intval' );
		// $map ['token'] = get_token ();
		// $info = M ( 'survey' )->where ( $map )->find ();
		$info = D ( 'Survey' )->getSurveyInfo ( $survey_id );
		$this->assign ( 'info', $info );
		
		// 增加积分
		add_credit ( 'survey' );
		$this->display ();
	}
	
	// 已过期返回 true ,否则返回 false
	private function _is_overtime($survey_id) {
		// 先看看投票期限过期与否
		$the_survey = M ( "survey" )->find ( $survey_id );
		
		if (! empty ( $the_survey ['start_time'] ) && $the_survey ['start_time'] > NOW_TIME)
			return ture;
			
			// $deadline = $the_survey ['end_date'] + 86400;
		if (! empty ( $the_survey ['end_time'] ) && $the_survey ['end_time'] <= NOW_TIME)
			return ture;
		
		return false;
	}
	function lists() {
		$isAjax = I ( 'isAjax' );
		$isRadio = I ( 'isRadio' );
		$model = $this->getModel ( 'survey' );
		$list_data = $this->_get_model_list ( $model, 0, 'id desc', true );
		// 判断该活动是否已经设置投票调查
		if ($isAjax) {
			$this->assign ( 'isRadio', $isRadio );
			$this->assign ( $list_data );
			$this->display ( 'ajax_lists_data' );
		} else {
			$this->assign ( $list_data );
			$this->display ();
		}
	}
	function add() {
		$this->display ( 'edit' );
	}
	function edit() {
		$id = I ( 'id', 0, 'intval' );
		$model = $this->getModel ( 'survey' );
		
		if (IS_POST) {
			$this->checkDate();
			$act = empty ( $id ) ? 'add' : 'save';
			$Model = D ( parse_name ( get_table_name ( $model ['id'] ), 1 ) );
			// 获取模型的字段信息
			$Model = $this->checkAttr ( $Model, $model ['id'] );
			$res = false;
			$Model->create () && $res = $Model->$act ();
			if ($res !== false) {
				$act == 'add' && $id = $res;
				
				$this->_setAttr ( $id, $_POST );
				
				$this->success ( '保存成功！', U ( 'lists?model=' . $model ['name'], $this->get_param ) );
			} else {
				$this->error ( $Model->getError () );
			}
		} else {
			// 获取数据
			$data = M ( get_table_name ( $model ['id'] ) )->find ( $id );
			$data || $this->error ( '数据不存在！' );
			
			$token = get_token ();
			if (isset ( $data ['token'] ) && $token != $data ['token'] && defined ( 'ADDON_PUBLIC_PATH' )) {
				$this->error ( '非法访问！' );
			}
			$this->assign ( 'data', $data );
			
			// 字段信息
			$map ['survey_id'] = $id;
			$map ['token'] = $token;
			$list = M ( 'survey_question' )->where ( $map )->order ( 'sort asc' )->select ();
			
			$this->assign ( 'attr_list', $list );
			
			$this->display ( 'edit' );
		}
	}
	// 保存字段信息
	function _setAttr($forms_id, $data) {
		$dao = M ( 'survey_question' );
		$save ['survey_id'] = $forms_id;
		
		$old_ids = $dao->where ( $save )->getFields ( 'id' );
		
		$sort = 0;
		foreach ( $data ['attr_title'] as $key => $val ) {
			$save ['title'] = safe ( $val );
			if (empty ( $save ['title'] ))
				continue;
			
			$save ['extra'] = safe ( $data ['extra'] [$key] );
			$save ['type'] = safe ( $data ['type'] [$key] );
			$save ['is_must'] = intval ( $data ['is_must'] [$key] );
			$save ['value'] = safe ( $data ['value'] [$key] );
			$save ['remark'] = safe ( $data ['remark'] [$key] );
			$save ['validate_rule'] = safe ( $data ['validate_rule'] [$key] );
			$save ['error_info'] = safe ( $data ['error_info'] [$key] );
			$save ['sort'] = $sort;
			
			$id = intval ( $data ['attr_id'] [$key] );
			if (! empty ( $id )) {
				$ids [] = $map ['id'] = $id;
				$dao->where ( $map )->save ( $save );
			} else {
				$save ['token'] = get_token ();
				$ids [] = $dao->add ( $save );
			}
			
			$sort += 1;
		}
		
		$diff = array_diff ( $old_ids, $ids );
		if (! empty ( $diff )) {
			$map2 ['id'] = array (
					'in',
					$diff 
			);
			$dao->where ( $map2 )->delete ();
		}
	}
	
	function checkDate(){
		// 判断时间选择是否正确
		if (! I ( 'post.start_time' )) {
			$this->error ( '请选择开始时间' );
		} else if (! I ( 'post.end_time' )) {
			$this->error ( '请选择结束时间' );
		} else if (strtotime ( I ( 'post.start_time' ) ) > strtotime ( I ( 'post.end_time' ) )) {
			$this->error ( '开始时间不能大于结束时间' );
		}
	}
}
