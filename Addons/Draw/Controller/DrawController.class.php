<?php

namespace Addons\Draw\Controller;

use Home\Controller\AddonsController;

class DrawController extends AddonsController {
	function _initialize() {
		parent::_initialize ();
		
		$controller = strtolower ( _CONTROLLER );
		
		// $res['title'] = '抽奖活动';
		// $res['url'] = addons_url('Draw://Draw/lists');
		// $res['class'] = $controller == 'draw' ? 'current' : '';
		// $nav[] = $res;
		
		$res ['title'] = '奖品管理';
		$res ['url'] = addons_url ( 'Draw://Award/lists' );
		$res ['class'] = $controller == 'award' ? 'current' : '';
		$nav [] = $res;
		
		$this->assign ( 'nav', $nav );
	}
	function lists() {
		$url = addons_url ( 'Draw://Award/lists' );
		redirect ( $url );
		
		// $model = $this->getModel('luck_draw');
		// $list_data = $this->_get_model_list($model, 0, 'id desc', true);
		// $dao = D('Draw');
		// foreach ($list_data['list_data'] as &$vo) {
		// $vo = $dao->getInfo($vo['id']);
		// }
		// $this->assign($list_data);
		
		// $this->display();
	}
	// /////////靓妆///////////////
	function lzwg_activities_lists() {
		$this->assign ( 'search_button', false );
		$this->assign ( 'del_button', false );
		$this->assign ( 'check_all', false );
		$model = $this->getModel ( 'lzwg_activities' );
		$map ['uid'] = $this->mid;
		session ( 'common_condition', $map );
		$list_data = $this->_get_model_list ( $model, 0, 'id desc', true );
		// 判断该活动是否已经设置投票调查
		$lzwgVote = M ( 'lzwg_activities_vote' )->field ( 'id,lzwg_id' )->select ();
		foreach ( $lzwgVote as $v ) {
			$lzwgVoteArr [$v ['lzwg_id']] = $v ['id'];
		}
		$dao = D ( 'Draw' );
		foreach ( $list_data ['list_data'] as &$vo ) {
			$vo = $dao->getInfo ( $vo ['id'] );
			$param ['lzwg_id'] = $vo ['id'];
			
			if ($lzwgVoteArr [$vo ['id']]) {
				$param1 ['id'] = $lzwgVoteArr [$vo ['id']];
				$vote_url = addons_url ( 'LzwgVote://LzwgActivitiesVote/edit', $param1 );
			} else {
				$vote_url = addons_url ( 'LzwgVote://LzwgActivitiesVote/add', $param );
			}
			$comment_url = addons_url ( 'Comment://Comment/lists', $param );
			
			$award_url = addons_url ( 'Draw://LotteryPrizeList/add', $param );
			$lucky_follow_url = addons_url ( 'Draw://LuckyFollow/lzwg_lists', $param );
			
			$vo ['activitie_time'] = time_format ( $vo ['start_time'], 'Y/m/d' ) . '至' . time_format ( $vo ['end_time'], 'Y/m/d' );
			$vo ['comment_list'] = '<a href="' . $comment_url . '">评论列表</a>';
			$vo ['set_vote'] = '<a href="' . $vote_url . '">设置投票</a>';
			$vo ['set_award'] = '<a href="' . $award_url . '">奖品设置</a>';
			$vo ['get_prize_list'] = '<a href="' . $lucky_follow_url . '">中奖列表</a>';
		}
		
		foreach ( $list_data ['list_grids'] as &$vo ) {
			if ($vo ['field'] [0] == 'id') {
				$previewUrl = 'package?id=[id]&_controller=Sucai&_addons=Sucai&source=Lzwg&is_preview=1&target=_blank|预览';
				$downloadUrl = 'package?id=[id]&_controller=Sucai&_addons=Sucai&source=Lzwg&is_download=1|素材下载';
				$vo ['href'] = $previewUrl . "," . $downloadUrl . ",lzwg_edit?id=[id]&model=" . $model ['id'] . "|编辑,[DELETE]|删除";
			}
		}
		$this->assign ( $list_data );
		// dump($list_data);
		$this->display ();
	}
	function lzwg_edit() {
		$model = $this->getModel ( 'lzwg_activities' );
		$id = I ( 'id' );
		
		// 获取数据
		$data = M ( get_table_name ( $model ['id'] ) )->find ( $id );
		$data || $this->error ( '数据不存在！' );
		if (IS_POST) {
			$this->checkTime ( strtotime ( $_POST ['start_time'] ), strtotime ( $_POST ['end_time'] ), $id );
			$Model = D ( parse_name ( get_table_name ( $model ['id'] ), 1 ) );
			// 获取模型的字段信息
			$Model = $this->checkAttr ( $Model, $model ['id'] );
			if ($Model->create () && $Model->save ()) {
				
				// 清空缓存
				method_exists ( $Model, 'clear' ) && $Model->clear ( $id, 'edit' );
				D ( 'Draw' )->getInfo ( $id, true );
				$this->success ( '保存' . $model ['title'] . '成功！', U ( 'lzwg_activities_lists?model=' . $model ['name'], $this->get_param ) );
			} else {
				$this->error ( $Model->getError () );
			}
		} else {
			$fields = get_model_attribute ( $model ['id'] );
			$this->assign ( 'fields', $fields );
			$this->assign ( 'data', $data );
			
			$this->display ();
		}
	}
	function lzwg_add() {
		$model = $this->getModel ( 'lzwg_activities' );
		if (IS_POST) {
			$this->checkTime ( strtotime ( $_POST ['start_time'] ), strtotime ( $_POST ['end_time'] ) );
			
			$Model = D ( parse_name ( get_table_name ( $model ['id'] ), 1 ) );
			// 获取模型的字段信息
			$Model = $this->checkAttr ( $Model, $model ['id'] );
			if ($Model->create () && $id = $Model->add ()) {
				
				// 清空缓存
				method_exists ( $Model, 'clear' ) && $Model->clear ( $id, 'add' );
				
				$this->success ( '添加' . $model ['title'] . '成功！', U ( 'lzwg_activities_lists?model=' . $model ['name'], $this->get_param ) );
			} else {
				$this->error ( $Model->getError () );
			}
		} else {
			$fields = get_model_attribute ( $model ['id'] );
			$this->assign ( 'fields', $fields );
			
			$this->display ();
		}
	}
	// 判断时间是否重叠
	function checkTime($startTime, $endTime, $id = 0) {
		if ($endTime < $startTime) {
			$this->error ( '结束时间不能大于开始时间！' );
			exit ();
		}
		$timeData = M ( 'lzwg_activities' )->field ( 'id,start_time,end_time' )->select ();
// 		dump($id);
		if (! empty ( $timeData )) {
			foreach ( $timeData as $t ) {
				if ($t ['id'] != $id) {
// 					dump($startTime);
// 					dump($t);
					if(!($endTime<$t['start_time']||$startTime>$t['end_time'])){
						$this->error ( '该时间段已设有其它活动！' );
						exit ();
					}
				}
			}
		}
	}
}
