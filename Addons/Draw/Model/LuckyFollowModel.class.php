<?php

namespace Addons\Draw\Model;

use Think\Model;

/**
 * 中奖者信息模型
 */
class LuckyFollowModel extends Model {
    /*
     * 获取抽奖游戏活动的中奖人列表
     * $uid 不为0 时，则获取个人用户uid 的中奖信息
     */
    function getGamesLuckyLists($gamesId,$uid=0,$state='-1',$aim_table='lottery_games'){
        $map['token']=get_token();
        $map['aim_table']=$aim_table;
        $map['draw_id']=$gamesId;
        if ($uid !=0){
            $map['follow_id']=$uid;
        }
        if ($state != '-1'){
            $map['state']=$state;
        }
        $lists=$this->where($map)->order('id desc')->select();
        $awardLists=D('Addons://Draw/LotteryGamesAwardLink')->getGamesAwardlists($gamesId);
        foreach ($awardLists as $a){
            $awardData[$a['award_id']]=$a;
        }
        foreach ($lists as &$v){
            if ($awardData[$v['award_id']]){
                $v['grade']=$awardData[$v['award_id']]['grade'];
                $v['award_name']=$awardData[$v['award_id']]['name'];
                $v['img']=$awardData[$v['award_id']]['img'];
            }
            $address_id=intval($v['address']);
            if ($address_id){
                $address=D ( 'Addons://Shop/Address' )->getInfo($address_id);
                $v['address']=$address['address'];
                $v['truename']=$address['truename'];
                $v['mobile']=$address['mobile'];
            }
            $user=get_userinfo($v['follow_id']);
            $v['nickname']=$user['nickname'];
            $v['headimgurl']=$user['headimgurl'];
        }
        
        return $lists;
    }
    
    function getUserAward($id, $update = false, $data = array()) {
        $key = 'LuckyFollow_getUserAward_' . $id;
        $info = S ( $key );
        if ($info === false || $update) {
            $info = ( array ) (empty ( $data ) ? $this->find ( $id ) : $data);
            $map1['id']=$info['award_id'];
            $award=D('Addons://Draw/Award')->where($map1)->field('name,img')->find();
            $info['award_name']=$award['name'];
            $info['img']=$award['img'];
            $map2['games_id']=$info['draw_id'];
            $map2['award_id']=$info['award_id'];
            $map2['token']=get_token();
            $info['grade']=D('Addons://Draw/LotteryGamesAwardLink')->where($map2)->getField('grade');
            S ( $key, $info, 86400 );
        }
        return $info;
    }
    //判断当天最多中奖人数
    function get_day_winners_count($gamesId,$time){
        $map['draw_id']=$gamesId;
        $map['token']=get_token();
        $map['aim_table']='lottery_games';
        $map['zjtime']=array('egt',strtotime(time_format($time,'Y-m-d')));
        $data=$this->where($map)->field('count(distinct follow_id) num')->select();
        return $data[0]['num'];
    }
    //每人总共中奖次数
    //$time 有值时：每人每天中奖次数
    function get_user_win_count($gamesId,$uid,$time=0){
        $map['draw_id']=$gamesId;
        $map['token']=get_token();
        $map['aim_table']='lottery_games';
        $map['follow_id']=$uid;
        if ($time !=0){
            $map['zjtime']=array('egt',strtotime(time_format($time,'Y-m-d')));
        }
        
        $data=$this->where($map)->field('sum(num) totals')->select();
        return intval($data[0]['totals']);
    }
    // 获取各抽奖活动对应奖品已发放的数量
    function getLzwgAwardNum($event_id,$token='',$aim_talbe='lottery_games') {
        if ($token && $aim_talbe){
            $sql="SELECT award_id, sum(num) as num FROM `wp_lucky_follow` WHERE draw_id='$event_id' and token='$token' and aim_table='$aim_talbe' GROUP BY award_id" ;
        }else{
            $sql="SELECT award_id, sum(num) as num FROM `wp_lucky_follow` WHERE draw_id='$event_id' GROUP BY award_id" ;
        }
        $info = $this->query ($sql );
        foreach ( $info as $v ) {
            $i [$v ['award_id']] = $v ['num'];
        }
    
        return $i;
    }
    //
    
    ////////////////////////////////////////////////////////////////
// 	function getInfo($id, $update = false, $data = array()) {
// 		$key = 'LuckyFollow_getInfo_' . $id;
// 		$info = S ( $key );
// 		if ($info === false || $update) {
// 			$info = ( array ) (empty ( $data ) ? $this->find ( $id ) : $data);
// 			$award = D ( 'Addons://Draw/Award' )->getInfo ( $info ['award_id'] );
// 			$info ['prizeid'] = $info ['award_id'];
// 			$info ['award_id'] = $award ['name'];
// 			$info ['sportsid'] = $info ['sport_id'];
			
// 			// $sport=M('sports')->find($info['sport_id']);
// 			// $sportsdao = D ( 'Addons://Sports/Sports' );
// 			// $home_team = $dao->getInfo ( $info ['home_team'] );
// 			// $visit_team = $dao->getInfo ( $info ['visit_team'] );
// 			$sport = D ( 'Addons://Sports/Sports' )->getInfo ( $info ['sport_id'] );
			
// 			// $sport ['home_team'] = $home_team ['title'];
// 			// $sport ['visit_team'] = $visit_team ['title'];
// 			// $info['sport_id'] = $sport ['home_team'] . ' <br/><center>VS</center>' . $sport ['visit_team'];
// 			$info ['sport_id'] = $sport ['vs_team'];
			
// 			$follow = get_followinfo ( $info ['follow_id'] );
			
// 			// $info['nick_name']=$follow['nickname'];
// 			// $info['headimgurl']=url_img_html($follow['headimgurl']);
// 			$info ['nickname'] = url_img_html ( $follow ['headimgurl'] ) . '<br/>' . $follow ['nickname'];
// 			$info ['nickname2'] = $follow ['nickname'];
// 			$info ['headimgurl'] = $follow ['headimgurl'];
// 			$info ['truename'] = $follow ['truename'];
// 			$info ['mobile'] = $follow ['mobile'];
// 			$info ['area'] = $follow ['province'] . $follow ['city'];
// 			$info ['address'] = $info ['area'];
// 			$info ['score'] = $follow ['score'];
// 			// 擂鼓数
// 			$drumData = $this->getDrumCount ( $info ['follow_id'] );
// 			$info ['drum_count'] = intval ( $drumData [$info ['sportsid']] );
			
// 			$param ['state'] = $info ['state'];
// 			$param ['id'] = $id;
// 			$url = addons_url ( "Draw://LuckyFollow/changeState", $param );
// 			$info ['state'] = $info ['state'] == 0 ? "<a href='$url'>未兑换</a>" : '已兑换';
// 		}
// 		S ( $key, $info, 86400 );
// 		return $info;
// 	}
	
	// 根据奖品id，获取中奖者列表
	function getlistByAwardId($awardid, $update = false) {
		$key = 'LuckyFollow_getlistByAwardId_' . $awardid;
		$info = S ( $key );
		if ($info === false || $update) {
			$map ['award_id'] = $awardid;
			$info = ( array ) $this->where ( $map )->select ();
		}
		S ( $key, $info, 86400 );
		return $info;
	}
	
	// 判断粉丝在同一场次抽奖时抽取到同一个奖品时，奖品数加1
	function getLuckyFollow($sports_id, $award_id, $follow_id, $update = false) {
		$key = 'LuckyFollow_getLuckyFollow_' . $sports_id . '_' . $award_id . '_' . $follow_id;
		$info = S ( $key );
		if ($info === false || $update) {
			$map ['sport_id'] = $sports_id;
			$map ['award_id'] = $award_id;
			$map ['follow_id'] = $follow_id;
			$info = $this->where ( $map )->find ();
		}
		S ( $key, $info, 86400 );
		return $info;
	}
	function setNum($data) {
		$info = $this->getLuckyFollow ( $data ['sport_id'], $data ['award_id'], $data ['follow_id'] );
		if (! empty ( $info )) {
			$id = $info ['id'];
			$info ['num'] = $info ['num'] + 1;
			$info ['zjtime'] = NOW_TIME;
			$map ['sport_id'] = $data ['sport_id'];
			$map ['award_id'] = $data ['award_id'];
			$map ['follow_id'] = $data ['follow_id'];
			$res = $this->where ( $map )->save ( $info );
			
			$f ['truename'] = $data ['truename'];
			$f ['mobile'] = $data ['mobile'];
			$followDao = D ( 'Common/Follow' );
			$followDao->update ( $data ['follow_id'], $f );
			// 奖品数量减1
			// D('LotteryPrizeList')->setCount($data['sport_id'],$data['award_id']);
			if ($res) {
				$this->getInfo ( $id, true );
				$this->getlistByAwardId ( $data ['award_id'], true );
				$this->getLuckyFollow ( $data ['sport_id'], $data ['award_id'], $data ['follow_id'], true );
				$this->getUserPrizeData ( $data ['sport_id'], $data ['follow_id'], true );
			}
			return $res;
		}
		return $info;
	}
	function delayAddData($data, $delay = 10) {
		$res = $this->delayAdd ( $data, $delay );
		// $followDao= D ( 'Common/Follow' );
		
		// if($data['truename']&&$data['mobile']){
		// $f['truename']=$data['truename'];
		// $f['mobile']=$data['mobile'];
		// $followDao->update($data['follow_id'],$f);
		// }else{
		// $award=D('Award')->getInfo($data['award_id']);
		// $followInfo=get_followinfo( $data ['follow_id']);
		// $new_score=$followInfo['score']+intval($award['score']);
		// D ( 'Common/Follow' )->updateField($data ['follow_id'],'score',$new_score);
		// //奖品数量减1
		// D('LotteryPrizeList')->setCount($data['sport_id'],$data['award_id']);
		
		// }
		$this->getlistByAwardId ( $data ['award_id'], true );
		$this->getLuckyFollow ( $data ['sport_id'], $data ['award_id'], $data ['follow_id'], true );
		$this->getUserPrizeData ( $data ['sport_id'], $data ['follow_id'], true );
		if(!empty($data['draw_id'])){
			$this->getLzwgUserPrizeData($data['draw_id'], $data['follow_id'],true);
			$this->getLzwgUserAllPrizeData($data['follow_id'],true);
			
		}
		
		return $res;
	}
	function update($id, $data = array()) {
		$map ['id'] = $id;
		$res = $this->where ( $map )->save ( $data );
		if ($res) {
			$info = $this->getInfo ( $id );
			if ($info['draw_id']){
				$this->getLzwgLuckyFollowInfo($id,true);
				$this->getLzwgUserPrizeData($info['draw_id'], $info ['follow_id'],true);
				$this->getLzwgUserAllPrizeData($info['follow_id'],true);
			}else {
				$this->getInfo ( $info ['id'], true );
				$this->getlistByAwardId ( $info ['prizeid'], true );
				$this->getLuckyFollow ( $info ['sportsid'], $info ['prizeid'], $info ['follow_id'], true );
				$this->getUserPrizeData ( $info ['sportsid'], $info ['follow_id'], true );
			}
			
		}
		return $res;
	}
	
	// 获取各活动场次对应奖品已发放的数量
	function getAwardNum($sport_id) {
		$info = $this->query ( "SELECT award_id, count(num) as num FROM `wp_lucky_follow` WHERE sport_id='$sport_id' GROUP BY award_id" );
		foreach ( $info as $v ) {
			$i [$v ['award_id']] = $v ['num'];
		}
		
		return $i;
	}
	
	// 获取粉丝在每场活动的擂鼓数
	function getDrumCount($follow_id) {
		$list = $this->query ( "SELECT sports_id, sum(drum_count) as num FROM `wp_sports_drum` WHERE follow_id='$follow_id' GROUP BY sports_id" );
		foreach ( $list as $v ) {
			$countArr [$v ['sports_id']] = $v ['num'];
		}
		return $countArr;
	}
	// 获取粉丝每场活动中奖数
	function getUserPrizeData($sport_id, $follow_id, $update = false) {
		$key = 'LuckyFollow_getUserPrizeData_' . $sport_id . '_' . $follow_id;
		$info = S ( $key );
		if ($info === false || $update) {
			$map ['follow_id'] = $follow_id;
			$map ['sport_id'] = $sport_id;
			$map['uid']=session('manager_id');
			if (empty($map['uid'])){
				$map['uid']=get_mid();
			}
			$info = M ( 'lucky_follow' )->where ( $map )->field ( 'award_id,state' )->select ();
			$awardDao = D ( 'Addons://Draw/Award' );
			foreach ( $info as &$i ) {
				$award = $awardDao->getInfo ( $i ['award_id'] );
				$i = array_merge ( $i, $award );
			}
		}
		S ( $key, $info, 86400 );
		return $info;
	}
	/////////////靓妆////////////////
	function getLzwgLuckyFollowInfo($id, $update = false, $data = array()) {
		$key = 'LuckyFollow_getLzwgLuckyFollowInfo_' . $id;
		$info = S ( $key );
		if ($info === false || $update) {
			$info = ( array ) (empty ( $data ) ? $this->find ( $id ) : $data);
			$award = D ( 'Addons://Draw/Award' )->getInfo ( $info ['award_id'] );
			$info ['prizeid'] = $info ['award_id'];
			$info ['award_id'] = $award ['name'];
// 			$info ['sportsid'] = $info ['sport_id'];
				
			// $sport=M('sports')->find($info['sport_id']);
			// $sportsdao = D ( 'Addons://Sports/Sports' );
			// $home_team = $dao->getInfo ( $info ['home_team'] );
			// $visit_team = $dao->getInfo ( $info ['visit_team'] );
// 			$sport = D ( 'Addons://Sports/Sports' )->getInfo ( $info ['sport_id'] );
				
			// $sport ['home_team'] = $home_team ['title'];
			// $sport ['visit_team'] = $visit_team ['title'];
			// $info['sport_id'] = $sport ['home_team'] . ' <br/><center>VS</center>' . $sport ['visit_team'];
			$lzwg=D('Addons://Draw/Draw')->getInfo($info['draw_id']);
			$info ['sport_id'] = $lzwg ['title'];
				
			$follow = get_followinfo ( $info ['follow_id'] );
				
			// $info['nick_name']=$follow['nickname'];
			// $info['headimgurl']=url_img_html($follow['headimgurl']);
			$info ['nickname'] = url_img_html ( $follow ['headimgurl'] ) . '<br/>' . $follow ['nickname'];
			$info ['nickname2'] = $follow ['nickname'];
			$info ['headimgurl'] = $follow ['headimgurl'];
			$info ['truename'] = $follow ['truename'];
			$info ['mobile'] = $follow ['mobile'];
			$info ['area'] = $follow ['province'] . $follow ['city'];
			$info ['address'] = $info ['area'];
			$info ['score'] = $follow ['score'];
			// 擂鼓数
// 			$drumData = $this->getDrumCount ( $info ['follow_id'] );
// 			$info ['drum_count'] = intval ( $drumData [$info ['sportsid']] );
				
			$param ['state'] = $info ['state'];
			$param ['id'] = $id;
			$url = addons_url ( "Draw://LuckyFollow/lzwgChangeState", $param );
			$info ['state'] = $info ['state'] == 0 ? "<a href='$url'>未兑换</a>" : '已兑换';
		}
		S ( $key, $info, 86400 );
		return $info;
	}
	
	function getLzwgUserPrizeData($lzwg_id, $follow_id, $update = false) {
		$key = 'LuckyFollow_getLzwgUserPrizeData_' . $lzwg_id . '_' . $follow_id;
		$info = S ( $key );
		if ($info === false || $update) {
			$map ['follow_id'] = $follow_id;
			$map ['draw_id'] = $lzwg_id;
			$map['uid']=session('manager_id');
			if (empty($map['uid'])){
				$map['uid']=get_mid();
			}
			$info = M ( 'lucky_follow' )->where ( $map )->field ( 'award_id,state' )->select ();
			$awardDao = D ( 'Addons://Draw/Award' );
			foreach ( $info as &$i ) {
				$award = $awardDao->getInfo ( $i ['award_id'] );
				$i = array_merge ( $i, $award );
			}
		}
		S ( $key, $info, 86400 );
		return $info;
	}
	//获取靓装用户所有的中奖
	function getLzwgUserAllPrizeData($follow_id, $update = false) {
		$key = 'LuckyFollow_getLzwgUserAllPrizeData_' . $follow_id;
		$info = S ( $key );
		if ($info === false || $update) {
			$map ['follow_id'] = $follow_id;
			$map['uid']=session('manager_id');
			if (empty($map['uid'])){
				$map['uid']=get_mid();
			}
			$info = M ( 'lucky_follow' )->where ( $map )->field ( 'award_id,state,zjtime' )->order('zjtime desc')->select ();
			$awardDao = D ( 'Addons://Draw/Award' );
			foreach ( $info as &$i ) {
				$award = $awardDao->getInfo ( $i ['award_id'] );
				$i = array_merge ( $i, $award );
			}
		}
		S ( $key, $info, 86400 );
		return $info;
	}
	
	
	
	
}
