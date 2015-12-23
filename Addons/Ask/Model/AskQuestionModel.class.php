<?php

namespace Addons\Ask\Model;

use Think\Model;

/**
 * Ask模型
 */
class AskQuestionModel extends Model {
	function getQuestionsByAskid($ask_id, $question_id = '', $update = false) {
		$key = 'getQuestionsByAskid_' . $ask_id;
		$list = S ( $key );
		if ($list === false || $update) {
			$map ['ask_id'] = intval ( $ask_id );
			$list2 = $this->where ( $map )->order ( 'sort asc, id asc' )->select ();
			foreach ( $list2 as $v ) {
				$list [$v ['id']] = $v;
			}
			unset ( $list2 );
			S ( $key, $list, 86400 );
		}
		
		return empty ( $question_id ) ? $list : $list [$question_id];
	}
	function clear($ids, $type = '', $uid = '') {
		if (empty ( $ids ))
			return false;
		
		if (! is_array ( $ids )) {
			$ids = array (
					$ids 
			);
		}
		
		foreach ( $ids as $id ) {
			$key = 'getQuestionsByAskid_' . $id;
			S ( $key, null );
		}
	}
	function setLastQuestion($ask_id) {
		$map ['ask_id'] = $ask_id;
		$this->where ( $map )->setField ( 'is_last', 0 );
		
		$map2 ['id'] = $this->where ( $map )->order ( 'sort desc, id desc' )->getField ( 'id' );
		$map2 ['id'] && $this->where ( $map2 )->setField ( 'is_last', 1 );
		
		$this->clear ( $ask_id );
	}
	function getQuestionTitle($id, $ask_id) {
		static $_getQuestionTitle;
		
		if (! isset ( $_getQuestionTitle [$id] )) {
			$question = $this->getQuestionsByAskid ( $ask_id, $id );
			$_getQuestionTitle [$id] = $question ['title'];
		}
		
		return $_getQuestionTitle [$id];
	}
	function getQuestionAnswer($id,$ask_id){
		static $_getQuestionAnswer;
		if(!isset($_getQuestionAnswer[$id])){
			$question=$this->getQuestionsByAskid($ask_id,$id);
			$answer=$question['answer'];
			$_getQuestionAnswer[$id]=$this->getQuestionAnswerExtra($id,$ask_id,$answer);
		}
		return $_getQuestionAnswer[$id];
	}

	function getQuestionAnswerExtra($id,$ask_id,$answer){
		$_getQuestionAnswerExtra='';
		if(empty($_getQuestionAnswerExtra)){
			$question=$this->getQuestionsByAskid($ask_id,$id);
			$extra=preg_split('/[;\r\n]+/s', $question['extra']);
			foreach ($extra as $key => $value) {
				$ex=explode(':',$value);
				$e[$ex[0]]=$ex[1];
			}
			if(!empty($answer)){
				//echo $answer.'<>';
				foreach ($e as $k => $v) {
				//	echo $k.'<br/>';
					if($k==$answer){
						$_getQuestionAnswerExtra=$k.':'.$v;
					}
				}
			}
		}
		return $_getQuestionAnswerExtra;
	}
}
