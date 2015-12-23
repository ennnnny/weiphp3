<?php

namespace Addons\Draw\Controller;

use Home\Controller\AddonsController;

class GamesController extends AddonsController {
	function _initialize() {
		parent::_initialize ();
		$controller = strtolower ( _CONTROLLER );
		$res ['title'] = '抽奖游戏';
		$res ['url'] = addons_url ( 'Draw://Games/lists' );
		$res ['class'] = $controller == 'games' ? 'current' : '';
		$nav [] = $res;
		
		$res ['title'] = '奖品库管理';
		$res ['url'] = addons_url ( 'Draw://Award/lists' );
		$res ['class'] = $controller == 'award' ? 'current' : '';
		$nav [] = $res;
		
		$res ['title'] = '中奖人列表';
		$res ['url'] = addons_url ( 'Draw://LuckyFollow/games_lucky_lists' );
		$res ['class'] = $controller == 'luckyfollow' ? 'current' : '';
		$nav [] = $res;
		
		$this->assign ( 'nav', $nav );
	}
	function lists() {
	    $isAjax = I ( 'isAjax' );
	    $isRadio = I ( 'isRadio' );
		$this->assign ( 'search_button', false );
		$this->assign ( 'del_button', false );
		$this->assign ( 'check_all', false );
		$model = $this->getModel ( 'lottery_games' );
		$list_data = $this->_get_model_list ( $model, 0, 'id desc', true );
// 		判断该活动是否已经设置投票调查
		$dao = D ( 'Addons://Draw/Games' );
        
		foreach ( $list_data ['list_data'] as &$vo ) {
		    if ($vo['status']==0){
		        $vo['status']=='已关闭';
		    }else{
		        if ($vo ['start_time'] > NOW_TIME) {
		            $vo ['status'] = '未开始';
		        } elseif ($vo ['end_time'] < NOW_TIME) {
		            $vo ['status'] = '已结束';
		        } else {
		            $vo ['status'] = '进行中';
		        }
		    }
		    $vo['attend_num']=$dao->getAttendNum($vo['id']);
		    $winUrl=addons_url("Draw://LuckyFollow/games_lucky_lists",array('games_id'=>$vo['id']));
            $vo['winners_list']="<a href='".$winUrl."' >中奖人列表</a>"	   ; 
		}
		if ($isAjax) {
		    $this->assign('isRadio',$isRadio);
		    $this->assign ( $list_data );
		    $this->display ( 'ajax_lists_data' );
		} else {
		    $this->assign ( $list_data );
		    $this->display ();
		}
	}
	function edit() {
		$model = $this->getModel ( 'lottery_games' );
		$id = I ( 'id' );
		
		// 获取数据
		$data = M ( get_table_name ( $model ['id'] ) )->find ( $id );
		$data || $this->error ( '数据不存在！' );
		if (IS_POST) {
			$this->checkTime ( strtotime ( $_POST ['start_time'] ), strtotime ( $_POST ['end_time'] ), $id );
			$Model = D ( parse_name ( get_table_name ( $model ['id'] ), 1 ) );
			// 获取模型的字段信息
			$Model = $this->checkAttr ( $Model, $model ['id'] );
			$res=$this->_add_award($id, $_POST);
			if ($Model->create () && $Model->save () || $res) {
			   
			    $this->_saveKeyword ( $model, $id );
				// 清空缓存
				method_exists ( $Model, 'clear' ) && $Model->clear ( $id, 'edit' );
				D ( 'Games' )->getInfo ( $id, true );
				$this->success ( '保存' . $model ['title'] . '成功！', U ( 'lists?model=' . $model ['name'], $this->get_param ) );
			} else {
				$this->error ( $Model->getError () );
			}
		} else {
			$fields = get_model_attribute ( $model ['id'] );
			$awardList=D('Addons://Draw/LotteryGamesAwardLink')->getGamesAwardlists($id);
			$this->assign('award_list',$awardList);
			$this->assign ( 'fields', $fields );
			$this->assign ( 'data', $data );
			
			$this->display ();
		}
	}
	function add() {
		$model = $this->getModel ( 'lottery_games' );
		if (IS_POST) {
			$this->_add_award($id, $_POST);
			$this->checkTime ( strtotime ( $_POST ['start_time'] ), strtotime ( $_POST ['end_time'] ) );
			
			$Model = D ( parse_name ( get_table_name ( $model ['id'] ), 1 ) );
			// 获取模型的字段信息
			$Model = $this->checkAttr ( $Model, $model ['id'] );
			if ($Model->create () && $id = $Model->add ()) {
			    //$this->_add_award($id, $_POST);
			    $this->_saveKeyword ( $model, $id );
				// 清空缓存
				method_exists ( $Model, 'clear' ) && $Model->clear ( $id, 'add' );
				
				$this->success ( '添加' . $model ['title'] . '成功！', U ( 'lists?model=' . $model ['name'], $this->get_param ) );
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
// 	    dump($_POST);die;
		if ($endTime < $startTime) {
			$this->error ( '结束时间不能大于开始时间！' );
			exit ();
		}
// 		$timeData = M ( 'lottery_games' )->field ( 'id,start_time,end_time' )->select ();
// // 		dump($id);
// 		if (! empty ( $timeData )) {
// 			foreach ( $timeData as $t ) {
// 				if ($t ['id'] != $id) {
// // 					dump($startTime);
// // 					dump($t);
// 					if(!($endTime<$t['start_time']||$startTime>$t['end_time'])){
// 						$this->error ( '该时间段已设有其它活动！' );
// 						exit ();
// 					}
// 				}
// 			}
// 		}
	}
	function _add_award($gameId,$postData){
	    $awardIdArr=$postData['award_id'];
	    if (empty($awardIdArr)){
	        $this->error('请添加奖品');
	    }
	    $gradeArr=$postData['grade'];
	    $numArr=$postData['num'];
	    $maxCountArr=$postData['max_count'];
	    $awardDao=D ( 'Addons://Draw/Award' );
	    $map['games_id']=$gameId;
	    $map['token']= get_token();
	    
	    $lotteryData=M('lottery_games_award_link')->where($map)->getFields('award_id,id,grade,num,max_count');
	    foreach ($awardIdArr as $awardId){
	        $award=$awardDao->getInfo($awardId);
	        if (!$gradeArr[$awardId]){
	            $this->error($award['name'].' 的等级名称不能为空');
	        }
	        if (!$numArr[$awardId]){
	            $this->error($award['name'].' 的奖品数量不能为空');
	        }
	        if (!$maxCountArr[$awardId]){
	            $this->error($award['name'].' 的最多抽奖数必须大于1');
	        }
	        if ($numArr[$awardId]<0){
	            $this->error($award['name'].' 的奖品数量不能小于0');
	        }
	        if ($maxCountArr[$awardId]<$numArr[$awardId]){
	            $this->error($award['name'].' 的最多抽奖数不能小于奖品数量');
	        }
	        if ($award['coupon_num'] && $award['coupon_num'] <$numArr[$awardId]){
	            $this->error($award['name'].' 的奖品数量不能超过赠送券的数量 '.$award['coupon_num']);
	        }
	        if ($lotteryData[$awardId]){
	            //保存
	            $saveData['grade']=$gradeArr[$awardId];
	            $saveData['num']=$numArr[$awardId];
	            $saveData['max_count']=$maxCountArr[$awardId];
	            $map['award_id']=$awardId;
	            $res=M('lottery_games_award_link')->where($map)->save($saveData);
	        }else{
	            //添加 
	            $addData['games_id']=$gameId;
	            $addData['award_id']=$awardId;
	            $addData['token']=$map['token'];
	            $addData['grade']=$gradeArr[$awardId];
	            $addData['num']=$numArr[$awardId];
	            $addData['max_count']=$maxCountArr[$awardId];
	            $addDatas[]=$addData;
	        }
	    }
	    if (!empty($addDatas)){
	        $res=M('lottery_games_award_link')->addAll($addDatas);
	    }
	    
	    foreach ($lotteryData as $key=>$v){
	        if (!in_array($key, $awardIdArr)){
	            $ids[]=$v['id'];
	        }
	    }
	    if (!empty($ids)){
	        $map1['id']=array('in',$ids);
	        $res=M('lottery_games_award_link')->where($map1)->delete();
	    }
	    if ($res){
	        $awardList=D('Addons://Draw/LotteryGamesAwardLink')->getGamesAwardlists($gameId,true);
	    }
	    return $res;
	    
	}
	/* 预览 */
	function preview(){
	    $id = I('games_id',0,intval);
	    $url = addons_url('Draw://Wap/index',array('games_id'=>$id));
	    $this->assign('url', $url);
	    $this->display(SITE_PATH . '/Application/Home/View/default/Addons/preview.html');
	}
}
