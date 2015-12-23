<?php

namespace Addons\Survey\Model;

use Think\Model;

/**
 * Survey模型
 */
class SurveyAnswerModel extends Model {
	function getAnswerInfo($survey_id, $question_id,$follow_id,$update = false, $data = array()) {
		$key = 'SurveyAnswer_getAnswerInfo_' . $survey_id.'_'.$question_id.'_'.$follow_id;
		$info = S ( $key );
		if ($info === false || $update) {
			$map ['survey_id'] = $survey_id;
			$map['question_id']=$question_id;
			$map ['uid'] = $follow_id;
			$info = ( array ) (empty ( $data ) ? $this->where ( $map )->find () : $data);
			
			S ( $key, $info, 86400 );
		}
		
		return $info;
	}
	function updateAnswer($survey_id,$question_id,$follow_id,$data=array()){
		$map ['survey_id'] = $survey_id;
		$map['question_id']=$question_id;
		$map ['uid'] = $follow_id;
		$res = $this->where ( $map )->save ( $data );
		if ($res) {
			$this->getAnswerInfo ( $survey_id,$question_id,$follow_id, true );
		}
		return $res;
	}
}
