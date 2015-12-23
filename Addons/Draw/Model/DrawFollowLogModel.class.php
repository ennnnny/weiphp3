<?php

namespace Addons\Draw\Model;

use Think\Model;

/**
 * DrawFollowLog模型
 */
class DrawFollowLogModel extends Model {
	function getInfo($id, $update = false, $data = array()) {
		$key = 'DrawFollowLog_getInfo_' . $id;
		$info = S ( $key );
		if ($info === false || $update) {
			$info = ( array ) (empty ( $data ) ? $this->find ( $id ) : $data);
			$sports = D ( 'Addons://Sports/Sports' )->getInfo ( $info ['sports_id'] );
			$info ['sportsarr'] = $sports;
			$follow = get_followinfo ( $info ['follow_id'] );
			$info ['followarr'] = $follow;
		}
		S ( $key, $info, 86400 );
		return $info;
	}
	//每人总共抽奖次数
	//$time 有值时：每人每天抽奖次数
	function get_user_attend_count($gamesId,$uid=0,$time=0){
	    $map['sports_id']=$gamesId;
	    $map['token']=get_token();
	    if ($uid !=0){
	        $map['follow_id']=$uid;
	    }
	    
	    if ($time !=0){
	        $map['cTime']=array('egt',strtotime(time_format($time,'Y-m-d')));
	    }
	    $data=$this->where($map)->field('sum(count) totals')->select();
	    return intval($data[0]['totals']);
	}
	
	
	function hasDraw($sports_id, $follow_id, $update = false) {
		$key = 'DrawFollowLog_hasDraw_' . $sports_id . '_' . $follow_id;
		$info = S ( $key );
		if ($info === false || $update) {
			$map ['sports_id'] = $sports_id;
			$map ['follow_id'] = $follow_id;
			$map ['uid'] = session ( 'manager_id' );
			if (empty ( $map ['uid'] )) {
				$map ['uid'] = get_mid ();
			}
			$info = intval ( $this->where ( $map )->getField ( 'count' ) );
			
			S ( $key, $info, 86400 );
		}
		
		return $info;
	}
	// 获取靓妆用户当日抽奖数
	function hasDrawByDay($sports_id, $follow_id, $update = false) {
// 		$key = 'DrawFollowLog_hasDrawByDay_' . $sports_id . '_' . $follow_id;
// 		$info = S ( $key );
// 		if ($info === false || $update) {
			$user_id = session ( 'manager_id' );
			if (empty ( $user_id )) {
				$user_id = get_mid ();
			}
			$cur_date = strtotime (date ( 'Y-m-d', NOW_TIME ) );
			$info = $this->field ( 'sum(`count`) as num' )->where ( "uid=$user_id and sports_id=$sports_id and follow_id=$follow_id and cTime >= '$cur_date'" )->select ();
			
// 			S ( $key, $info, 86400 );
// 		}
		
		return $info [0] ['num'];
	}
	function delayAddData($data, $delay = 10) {
		$res = $this->delayAdd ( $data, $delay );
		$this->hasDraw ( $data ['sports_id'], $data ['follow_id'], true );
// 		$this->hasDrawByDay ( $data ['sports_id'], $data ['follow_id'], true );
		return $res;
	}
	function updateCount($sports_id, $follow_id, $count) {
		$count ++;
		$map ['sports_id'] = $sports_id;
		$map ['follow_id'] = $follow_id;
		$map ['uid'] = session ( 'manager_id' );
		if (empty ( $map ['uid'] )) {
			$map ['uid'] = get_mid ();
		}
		$res = $this->where ( $map )->setField ( 'count', $count );
		if ($res) {
			
			$this->hasDraw ( $sports_id, $follow_id, true );
		}
		return $res;
	}
	
	// //判断粉丝是否参与抽奖
	// function getFollowLog($sports_id,$follow_id,$update=false){
	// $key = 'DrawFollowLog_getFollowLog_' . $sports_id.'_'.$follow_id;
	// $info = S ( $key );
	// if ($info === false || $update) {
	// $map['sports_id']=$sports_id;
	// $map['follow_id']=$follow_id;
	// $info=$this->where($map)->find();
	// }
	// S ( $key, $info, 86400 );
	// return $info;
	// }
	// function setNum($sports_id,$follow_id){
	// $info=$this->getFollowLog($sports_id, $follow_id);
	// if (!empty($info)){
	// $info['count']=$info['count']+1;
	// $info['cTime']=NOW_TIME;
	// $map['sports_id']=$sports_id;
	// $map['follow_id']=$follow_id;
	// $res=$this->where($map)->save($info);
	// if ($res){
	// $this->getLuckyFollow($sports_id, $follow_id,true);
	// }
	// return $res;
	// }
	// }
}
