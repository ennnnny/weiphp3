<?php

namespace Addons\Survey\Model;

use Think\Model;

/**
 * Survey模型
 */
class SurveyQuestionModel extends Model {
	function getQuestionInfo($survey_id, $update = false, $data = array()) {
		$key = 'SurveyQuestion_getQuestionInfo_' . $survey_id;
		$info = S ( $key );
		if ($info === false || $update) {
			$map ['survey_id'] = $survey_id;
			$info = ( array ) (empty ( $data ) ? $this->where ( $map )->order ( 'sort asc, id asc' )->select () : $data);
			
			S ( $key, $info, 86400 );
		}
		
		return $info;
	}
}
