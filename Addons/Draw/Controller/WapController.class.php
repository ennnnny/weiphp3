<?php

namespace Addons\Draw\Controller;

use Home\Controller\AddonsController;

class WapController extends AddonsController {
	function _initialize() {
		parent::_initialize ();
		
		
	}
	function index(){
	    $gameId=I('games_id',0,'intval');
	    if(empty($gameId)){
	        $this->error('还没有配置活动');
	    }
	    $info=D('Addons://Draw/Games')->getInfo($gameId);
	    if ($info['status']==0){
	        $info['status']='已关闭';
	    }else {
	        if (NOW_TIME>=$info['end_time']){
	            $info['status']='已结束';
	        }else if(NOW_TIME < $info['start_time']){
	            $info['status']='未开始';
	        }else{
	            $info['status']='进行中';
	        }
	    }
		//分享数据
		$shareData ['title'] = $info['title'];
		$shareData ['desc'] = $info['title'];
		switch($info['game_type']){
			case 1:$shareData ['imgUrl'] =    SITE_URL.'/Addons/Draw/View/default/Public/guaguale_cover.jpg';
			break;
			case 2:$shareData ['imgUrl'] =    SITE_URL.'/Addons/Draw/View/default/Public/dzp_cover.jpg';
			break;
			case 3:$shareData ['imgUrl'] =   SITE_URL.'/Addons/Draw/View/default/Public/zjd_cover.jpg';
			break;
			case 4:$shareData ['imgUrl'] =    SITE_URL.'/Addons/Draw/View/default/Public/nine_cover.jpg';
			break;
			
		}
		
		$shareData ['link'] = U ( 'index', array (
				'games_id' => $info['id']
		) );
		$this->assign ( 'shareData', $shareData );
	    //奖品列表
	    $awardLists=D('Addons://Draw/LotteryGamesAwardLink')->getGamesAwardlists($gameId);
	    foreach ($awardLists as $v){
	        $jp['title']=$v['grade'];
	        $jp['pic']=$v['img'];
	        $jp['picUrl']=$v['img_url'];
	        $jp['award_id']=$v['award_id'];
	        
	        $jplist[]=$jp;
	    }
	   
	    
	    //所有获奖列表
	    $luckLists=D('Addons://Draw/LuckyFollow')->getGamesLuckyLists($gameId);
	    //个人获奖列表
	    $uid=$this->mid;
	    $userLucky=D('Addons://Draw/LuckyFollow')->getGamesLuckyLists($gameId,$uid,0);
	    $hasPrize=0;
	    if (!empty($userLucky)){
	        $hasPrize=1;
	    }
// 	    dump($awardLists);
	    $tmp='';
	    switch ($info['game_type']){
	        case 1:
	            //刮刮乐
	            $tmp='guaguale';
	            break;
	        case 2:
	            $jp['title']='加油';
	            $jp['picUrl']=  ADDON_PUBLIC_PATH. '/ungeted_pic.png';
	            $jp['award_id']=0;
	            $jplist[]=$jp;
	            //大转盘
	            $tmp='dzp';
	            break;
	        case 3:
	            //砸金蛋
	            $tmp='zajindan';
	            break;
	        case 4:
	            //九宫格
	            $count=count($jplist);
	            $num=10-$count;
	            if ($num > 0){
	                for($i=0 ;$i<$num;$i++){
	                    $jp['title']='加油';
	                    $jp['picUrl']=  ADDON_PUBLIC_PATH. '/ungeted_pic.png';
	                    $jp['award_id']=0;
	                    $jplist[]=$jp;
	                }
	            }
	            shuffle($jplist);
	            $tmp='ninegrid';
	            break;
	    }
	    $jplist=JSON($jplist);
	    
	    $joinUrl=addons_url('Draw://Wap/draw_lottery',array('games_id'=>$gameId));
	    $this->assign('joinurl',$joinUrl);
	    $this->assign('has_prize',$hasPrize);
	    $this->assign('jplist',$jplist);
	    $this->assign('luck_lists',$luckLists);
	    $this->assign('award_lists',$awardLists);
	    $this->assign('info',$info);
	    $this->display($tmp);
	}
	//抽奖方法
	function draw_lottery(){
	    $gameId=I('get.games_id',0,'intval');
	    $msg='';
	    $status=0;
	    $uid=$this->mid;
	    $awardId=0;
	    $angle=360;
	    if(empty($gameId)){
	        $status=0;
	        $msg='活动已结束！';
	    }
	    $info=D('Addons://Draw/Games')->getInfo($gameId);
	    if (empty($msg)){
	        if ($info['status']==0){
	            $status=0;
	            $msg='活动已关闭';
	        }else {
	            if (NOW_TIME>=$info['end_time']){
	                $status=0;
	                $msg='活动已结束！';
	            }else if(NOW_TIME < $info['start_time']){
	                $status=0;
	                $msg='活动未开始！';
	            }
	        }
	    }
	    if (empty($msg)){
	        //每天最多中奖人数
	        if ($info['day_winners_count']){
	            $day_winners_count=D('Addons://Draw/LuckyFollow')->get_day_winners_count($gameId,time());
	            if ($day_winners_count > $info['day_winners_count']){
	                $status=0;
	                $msg='今天奖品已抽完，明天再来吧!';
	            }
	        }
	        //每人总共中奖次数
	        if ($info['win_limit']){
	            $win_limit=D('Addons://Draw/LuckyFollow')->get_user_win_count($gameId,$uid);
	            if ($win_limit > $info['win_limit']){
	                //超过此限制点击抽奖，抽奖者将无概率中奖
	                $status=0;
	                $msg='没有抽中,继续努力';
	                $awardId=0;
	            }
	        }
	        //每人每天中奖次数
	        if ($info['day_win_limit']){
	            $day_win_limit=D('Addons://Draw/LuckyFollow')->get_user_win_count($gameId,$uid,time());
	            if ($day_win_limit > $info['day_win_limit']){
	                //抽奖者将无概率中奖
	                $status=0;
	                $msg='没有抽中,继续努力';
	                $awardId=0;
	            }
	        }
	        //每人总共抽奖次数
	        if ($info['attend_limit']){
	            $attend_limit=D('Addons://Draw/DrawFollowLog')->get_user_attend_count($gameId,$uid);
	            if ($attend_limit > $info['attend_limit']){
	                $status=0;
	                $msg='您的所有抽奖次数已用完!';
	            }
	        }
	         
	        //每人每天抽奖次数
	        if ($info['day_attend_limit']){
	            $day_attend_limit=D('Addons://Draw/DrawFollowLog')->get_user_attend_count($gameId,$uid,time());
	            if ($day_attend_limit > $info['day_attend_limit']){
	                $status=0;
	                $msg='您今天的抽奖次数已经用完!';
	            }
	        }
	    }
	   
	    if (empty($msg)){
	        //保存抽奖记录
	        $drawLog['follow_id']=$uid;
	        $drawLog['sports_id']=$gameId;
	        $drawLog['count']=1;
	        $drawLog['cTime']=time();
	        $drawLog['token']=get_token();
	        M('draw_follow_log')->add($drawLog);
	        //参与次数++
	        $info['attend_num']++;
	        $key='Games_getInfo_'.$gameId;
	        S ( $key, $info, 86400 );
	        
	        //抽奖，获取奖品id
	        $lotteryData=$this->_do_lottery($gameId);
	        $angle=$lotteryData['angle'];
	        $awardId=$lotteryData['prize_id'];
	        if ($awardId==0){
	            $status=0;
	            $msg='没有抽中,继续努力';
	        }else{
	            $awardInfo=D('Addons://Draw/Award')->getInfo($awardId);
	                //保存中奖信息
	                $res=$this->save_zjInfo($gameId, $awardId, $uid);
	                if ($res['id']){
	                    $status=1;
	                    $msg='恭喜，您中了 '.$awardInfo['name'];
	                    $img=get_cover_url($awardInfo['img']);
	                    if ($res['other'] ==1){
	                        //领取奖品
	                        $jumpUrl=addons_url('Draw://Wap/get_prize',array('id'=>$res['id']));
	                    }else if($res['other']==2){
	                        //优惠详情
	                        $jumpUrl=addons_url('Coupon://Wap/show',array('id'=>$awardInfo['coupon_id'],'sn_id'=>$res['sn_id']));
	                    }else if($res['other']==3){
	                        $jumpUrl=addons_url('ShopCoupon://Wap/show',array('id'=>$awardInfo['coupon_id'],'sn_id'=>$res['sn_id']));
	                    }
	                    
	                }else{
	                    $awardId=0;
	                    $status=0;
	                    $msg='没有抽中,继续努力';
	                }
	            }
	    }
	    $returnData['angle']=$angle;
	    $returnData['status']=$status;
	    $returnData['msg']=$msg;
	    $returnData['img']=$img;
	    $returnData['jump_url']=$jumpUrl;
	    $returnData['award_id']=$awardId;
// 	    dump($returnData);
	    $this->ajaxReturn($returnData);
	}
	    
	   
	   
	//保存中奖信息
	function save_zjInfo($gameId,$awardId,$uid){
	    $res['other']=0;
	    
	    $data['draw_id']=$gameId;
	    $data['token']=get_token();
	    $data['aim_table']='lottery_games';
	    $data['zjtime']=time();
	    $data['num']=1;
	    $data['follow_id']=$uid;
	    $data['award_id']=$awardId;
	    $awardInfo=D('Addons://Draw/Award')->getInfo($awardId);
	    switch ($awardInfo['award_type']){
	        case 0:
	            $data['state']=1;
	            $data['djtime']=time();
	            //虚拟物品，积分奖励
	            $credit['score']=$awardInfo['score'];
	            $credit['title']='抽奖游戏活动';
	            $credit['uid']=$uid;
	            add_credit('lottery_games',0,$credit);
	            
	            break;
	        case 1:
	            //实物
	            $data['state']=0;
	            $res['other']=1;
	            $str=time();
	            $rand=rand(1000, 9999);
	            $str.=$rand;
	            $data['scan_code']=$str;
	            break;
	        case 2:
	            $data['state']=1;
	            $data['djtime']=time();
	            $res1=D ( 'Addons://Coupon/Coupon' )->sendCoupon ( $awardInfo['coupon_id'], $this->mid );
	            $res['sn_id']=$res1;
	            $res['other']=2;
	            //优惠券
	            break;
	        case 3:
	            $data['state']=1;
	            $data['djtime']=time();
	            $res['other'];
	            $res1= D ( 'Addons://ShopCoupon/Coupon' )->sendCoupon ( $awardInfo['coupon_id'], $this->mid );
	            $res['sn_id']=$res1;
	            //代金券
	            break;
	        case 4:
	            $data['state']=1;
// 	            $data['djtime']=time();
	            $map1['uid']=$uid;
	            $map1['token']=get_token();
	            
	            $cardMember=M('card_member')->where($map1)->field('id,recharge')->find();
	            if ($cardMember){
	                //直接加入会员卡
	                $save['recharge']=$cardMember['recharge']+$awardInfo['money'];
	                M('card_member')->where($map1)->save($save);
	            }else {
	                //没有会员卡，
	                $res['other']=4;
	            }
	            //返现
	            break;
	    }
	    $res['id']=M('lucky_follow')->add($data);
	    return $res;
	}
	//获取奖品id
	function _do_lottery($event_id){
	    //奖品列表
	    $awardLists=D('Addons://Draw/LotteryGamesAwardLink')->getGamesAwardlists($event_id);
	    
	    //各奖品抽中发放数量
	    $token=get_token();
	    $fafangjp=D('Addons://Draw/LuckyFollow')->getLzwgAwardNum($event_id,$token);
	    //各奖品剩余数量
	    //最大抽奖总次数
	    $maxCount=0;
	    $awardNums=0;
	    foreach ($awardLists as $v){
	        //大转盘获取angle
	        $jp['title']=$v['grade'];
	        $jp['pic']=$v['img'];
	        $jp['picUrl']=$v['img_url'];
	        $jp['id']=$v['award_id'];
	        $jplist[]=$jp;
	       if ($fafangjp [$v ['award_id']]) {
	            $v ['num'] = $v ['num'] - $fafangjp [$v ['award_id']];
	        }
	        if ($v ['num'] > 0) {
	            $d [] = $v;
	        }
	        $maxCount+=$v['max_count'];
	        $awardNums+=$v['num'];
	    }
	    if ($awardNums <=0){
	        //奖品数量为0，关闭活动
// 	        $save['status']=0;
// 	        D('Addons://Draw/Games')->update($event_id,$save);
// 	        return 0;
            $prizeid=0;
	    }
	    foreach ( $d as $v ) {
	        $prizeArr [] = array (
	            'prize_id' => $v ['award_id'],
	            'prize_num' => $v ['num']
	        );
	    }
// 	    dump($prizeArr);
	    $info=D('Addons://Draw/Games')->getInfo($event_id);
// 	    dump($info);
	    $attendCount=$info['attend_num'];
	    $drawCount=$maxCount-$attendCount;
// 	    dump($drawCount);
// 	    dump($awardNums);
// 	    dump($d);
	    if ($drawCount<=0 && empty($d)){
	        $prizeid=0;
	    }else if ($drawCount <=0 && $awardNums >$drawCount){
	        //最大抽奖次数用完，直接返回奖品
	        $prizeid=$d[0]['award_id'];
	    }else {
	        $prizeid=$this->lottery($prizeArr, $drawCount);
	    }
	    
// 	    dump($prizeid);
	    //时间
// 	    $start_time=$info['start_time'];
// 	    $end_time=$info['end_time'];
	    
// 	    $data=get_lottery1($prizeArr, $start_time, $end_time,$event_id,0,false,$token);
// 	    $prizeid=0;
// 	    foreach ( $data as $k => $v ) {
// 	        // echo NOW_TIME;
// 	        if ($k <= NOW_TIME) {
// 	            if ($v != 0) {
// 	                // if ($hasAward [0] != $prizeInfo ['award_type']) {
// 	                $prizeid = $v;
// 	                $del_lottery_time = $k;
// 	                del_lottery1 ( $del_lottery_time, $event_id, 0,$token );
// 	                break;
// 	            }
// 	        }
// 	    }
// 	    $prizeid=25;
	    $flat=true;
	    //根据优惠券的限制数发放
	    if ($prizeid !=0){
	        $awardInfo=D('Addons://Draw/Award')->getInfo($prizeid);
	        if ($awardInfo['award_type']==2){
	            $info=D('Addons://Coupon/Coupon')->getInfo($awardInfo['coupon_id']);
	            if ($info ['collect_count'] >= $info ['num']) {
	                $flat = false;
	            } else if (! empty ( $info ['start_time'] ) && $info ['start_time'] > NOW_TIME) {
	                $flat = false;
	            } else if (! empty ( $info ['end_time'] ) && $info ['end_time'] < NOW_TIME) {
	                $flat = false;
	            }
	             
	            $list = D ( 'Common/SnCode' )->getMyList ( $this->mid, $awardInfo['coupon_id'], 'Coupon' );
	            $my_count = count ( $list );
	             
	            if ($info ['max_num'] > 0 && $my_count >= $info ['max_num']) {
	                $flat = false;
	            }
	        }else if($awardInfo['award_type']==3){
	            $info=D('Addons://ShopCoupon/Coupon')->getInfo($awardInfo['coupon_id']);
	            if ($info ['collect_count'] >= $info ['num']) {
	                $flat = false;
	            } else if (! empty ( $info ['start_time'] ) && $info ['start_time'] > NOW_TIME) {
	                $flat = false;
	            } else if (! empty ( $info ['end_time'] ) && $info ['end_time'] < NOW_TIME) {
	                $flat = false;
	            }
	            $list = D ( 'Common/SnCode' )->getMyList ( $this->mid, $awardInfo['coupon_id'], 'ShopCoupon' );
	            $my_count = count ( $list );
	             
	            if ($info ['limit_num'] > 0 && $my_count >= $info ['limit_num']) {
	                $flat = false;
	            }
	        }
	        if (!$flat){
	            $prizeid=0;
	        }
	    }
	    
	    $rid=0;
	    $jp['id']=0;
	    $jplist[]=$jp;
	   foreach ($jplist as $k => $vo){
	            if ($vo['id']==$prizeid){
	               $rid=$k; 
	            }
	        }
	    
	    // 计算中奖角度的位置
	    $result['angle'] = 360 - (360 / sizeof($jplist) / 2) - (360 / sizeof($jplist) * $rid) - 90;
	    $result['angle'] == 0 && $result['angle'] = 360;
	    $result['prize_id']=$prizeid;
	    return $result;
	}
	//根据抽奖最大数抽奖算法
	//$prizeArr: 奖品数组（奖品id,剩余数量）
	//$drawCount： 剩余最大抽奖次数
	function lottery($prizeArr,$drawCount){
	    $prize=0;
	    if(!empty($prizeArr)){
	        $i=0;
	        foreach ($prizeArr as $p){
	            $rand=rand(1, $drawCount);
// 	            dump($rand);
	            if ($i==0){
	                if ($rand<=$p['prize_num']){
	                    $prize=$p['prize_id'];
	                    break;
	                }else {
	                    $i=$p['prize_num'];
	                    continue;
	                }
	            }else{
	                $n=$i+$p['prize_num'];
	                if ($i< $rand && $rand <= $n){
	                    $prize=$p['prize_id'];
	                    break;
	                }else {
	                    $i=$p['prize_num']+$i;
	                    continue;
	                }
	            }
	        }
	    }
	    return $prize;
	}
	
	
	
	//我的奖品
	function my_prize(){
	    $gameId=I('games_id',0,'intval');
	    $userAward=D('Addons://Draw/LuckyFollow')->getGamesLuckyLists($gameId,$this->mid);
	    $this->assign('user_award',$userAward);
	    $this -> display();
	}
	//领取
	function get_prize(){
	    $id=I('id');
	    $userAward=D('Addons://Draw/LuckyFollow')->getUserAward($id);
	    // 	    dump($userAward);
	    $this->assign('user_award',$userAward);
	     
	    $addressList = D ( 'Addons://Shop/Address' )->getUserList ( $this->mid );
	    $this->assign('address',$addressList);
	    // 	    dump($addressList);
	    $this ->display();
	}
	function save_prize_address(){
	    $map['id']=I('id');
	    $save['address']=I('address_id');
	    $res=M('lucky_follow')->where($map)->save($save);
	    if($res){
	        echo 1;
	    }else{
	        echo 0;
	    }
	     
	}
	//活动中奖记录
	function prize_log(){
	    $gameId=I('games_id',0,'intval');
	    $luckLists=D('Addons://Draw/LuckyFollow')->getGamesLuckyLists($gameId);
	    $this->assign('luck_lists',$luckLists);
	    $this ->display();
	}
	//实物奖品扫码核销
	function scan_success(){
	    $cTime=I('cTime',0,'intval');
	    $tt= NOW_TIME * 1000 - $cTime;
	    if($cTime > 0){
	        if ($tt >30000){
	            $this->error('二维码已经过期');
	        }
	    }
	    //扫码员id
	    $mid=$this->mid;
	    //授权表查询
	    $map['uid']=$mid;
	    $map['token']=get_token();
	    $map['enable']=1;
	    $role=M('servicer')->where($map)->getField('role');
	    $roleArr=explode(',', $role);
	    if (!in_array(2, $roleArr)){
 	        $this->error('你还没有扫码验证的权限');
 	        exit();
	    }
	    
	    $scanCode=I('scan_code');
	    $map1['id']= I('id');
	    $lucky=M('lucky_follow')->find($map1['id']);
	    $is_check=0;
	    if($lucky['scan_code'] == $scanCode){
	        //验证成功
	        $save['state']=2;
	        $save['djtime']=time();
	        $res=M('lucky_follow')->where($map1)->save($save);
	        if ($res){
	            $is_check=1;
	        }
	    }
	    $userAward=D('Addons://Draw/LuckyFollow')->getUserAward($map1['id'],true);
	   
	    $this->assign('user_award',$userAward);
	    $this->assign('is_check',$is_check);
	    $this -> display('get_prize');
	}
	function get_state(){
	    $id=I('id');
	    $state=M('lucky_follow')->where(array('id'=>$id))->getField('state');
	    echo $state;
	}
	//大转盘
	function dzp(){
	    $this -> display();
	}
	//刮刮乐
	function guaguale(){
	    $this -> display();
	}
	//砸金蛋
	function zajindan(){
	    $this -> display();
	}
	//九宫格
	function ninegrid(){
	    $this -> display();
	}
	//测试
	function nine(){
	    $this -> display();
	}
	
	
	
// 	function show() {
// 		$sports_id = I ( 'sports_id', 0, 'intval' );
// 		$url = addons_url ( 'Draw://Wap/setLuckyFollow' );
// 		// 奖品列表
// 		$data = D ( 'LotteryPrizeList' )->getList ( $sports_id );
// 		foreach ( $data as $v ) {
// 			if ($v ['award_num'] != 0) {
// 				// 让实物排在前面
// 				if ($v ['awardarr'] ['award_type'] == 1) {
// 					$sd [] = $v;
// 				} else {
// 					$d [] = $v;
// 				}
// 			}
// 		}
// 		// $prizes = array_chunk ( $d, 3, true );
// 		// 添加最后一项谢谢参与
// 		$unPrize ['award_pic'] = SITE_URL . "/Public/static/football/ungeted_pic.png";
// 		$unPrize ['award_name'] = "谢谢参与";
// 		$unPrize ['award_id'] = 0;
// 		// dump($sd);
// 		// dump($d);
// 		if (empty ( $d )) {
// 			$prizes = $sd;
// 		} else {
// 			$prizes = array_merge ( $sd, $d );
// 		}
// 		// dump($prizes);
// 		$prizes [] = $unPrize;
// 		$this->assign ( 'savefollow', $url );
// 		$this->assign ( 'sports_id', $sports_id );
// 		$this->assign ( 'prizelist', $prizes );
// 		$followInfo = get_followinfo ( $this->mid );
// 		// dump($followInfo);
// 		$this->assign ( 'followInfo', $followInfo );
		
// 		// dump($prizes);
// 		$this->display ();
// 	}
	
// 	// 获取随机抽取的奖品id
// 	function getChouZPrizeId() {
// 		$prizeid = 0;
// 		$follow_id = $this->mid ;
// 		$sports_id = I ( 'sports_id', 0, 'intval' );
// 		$drawFollowLog = D ( 'DrawFollowLog' );
// 		$manager_id = session ( 'manager_id' );
// 		$count = $drawFollowLog->hasDraw ( $sports_id, $follow_id );
// 		if ($count) {
// 			$drawFollowLog->updateCount ( $sports_id, $follow_id, $count );
// 		} else {
// 			$data ['sports_id'] = $sports_id;
// 			$data ['follow_id'] = $follow_id;
// 			$data ['count'] = 1;
// 			$data ['cTime'] = NOW_TIME;
// 			$data ['uid'] = $manager_id;
// 			$drawFollowLog->delayAddData ( $data );
// 		}
// 		$luckyFollowPrize = D ( 'LuckyFollow' )->getUserPrizeData ( $sports_id, $follow_id );
// 		$xuni_num = 0;
// 		$shiwu_num = 0;
// 		$numConfig = get_addon_config ( 'Sports' );
// 		foreach ( $luckyFollowPrize as $v ) {
// 			if ($v ['award_type'] == 0 || $v ['award_type'] == 1) {
// 				if ($v ['award_type'] == 0) {
// 					$xuni_num ++;
// 				}
// 				if ($v ['award_type'] == 1) {
// 					$shiwu_num ++;
// 				}
// 				// $hasAward [] = $v ['award_type'];
				
// 				if ($xuni_num == intval ( $numConfig ['xuni_limit'] ) && $shiwu_num == intval ( $numConfig ['shiwu_limit'] )) {
// 					$returnData ['award_type'] = - 1;
// 					$returnData ['prize_id'] = $prizeid;
// 					$this->ajaxReturn ( $returnData, "JSON" );
// 					exit ();
// 				}
// 			}
// 		}
// 		$prizelist = D ( 'LotteryPrizeList' )->getList ( $sports_id );
// 		foreach ( $prizelist as $v ) {
// 			if ($v ['award_num'] != 0) {
// 				if ($v ['awardarr'] ['award_type'] == 1) {
// 					$shiwu [] = $v;
// 				} else if ($v ['awardarr'] ['award_type'] == 0) {
// 					// $xuni[]=$v;
// 					$xuniPrize [$v ['award_id']] = $v ['award_num'];
// 				}
// 				// $d [] = $v;
// 			}
// 		}
// 		// dump($prizelist);
// 		// dump($xuniPrize);
// 		// $prizeid=get_jifen_lottery($xuniPrize);
// 		// dump($r);
// 		// die;
// 		foreach ( $shiwu as $v ) {
// 			$shiwuArr [] = array (
// 					'prize_id' => $v ['award_id'],
// 					'prize_num' => $v ['award_num'] 
// 			);
// 		}
// 		// foreach ( $xuni as $v ) {
// 		// $xuniArr [] = array (
// 		// 'prize_id' => $v ['award_id'],
// 		// 'prize_num' => $v ['award_num']
// 		// );
// 		// }
// 		$prizeArr ['shiwu'] = $shiwuArr;
// 		// $prizeArr['xuni']=$xuniArr;
// 		// foreach ( $d as $v ) {
// 		// $prizeArr [] = array (
// 		// 'prize_id' => $v ['award_id'],
// 		// 'prize_num' => $v ['award_num']
// 		// );
// 		// }
// 		$sports = D ( 'Addons://Sports/Sports' )->getInfo ( $sports_id );
// 		$start_time = $sports ['start_time'];
// 		$end_time = $start_time + 120 * 60;
		
// 		// if ($end_time < NOW_TIME) {
// 		// $prizeid = - 1;
// 		// } else {
// 		$data = get_lottery ( $prizeArr, $start_time, $end_time, $sports_id );
// 		// dump($data);
// 		$dao = D ( 'Addons://Draw/Award' );
// 		if (! empty ( $data ['shiwu'] )) {
// 			foreach ( $data ['shiwu'] as $k => $v ) {
// 				// echo NOW_TIME;
// 				if ($k <= NOW_TIME) {
// 					if ($v != 0) {
// 						// $prizeInfo = $dao->getInfo ( $v );
// 						if ($shiwu_num < intval ( $numConfig ['shiwu_limit'] )) {
// 							$prizeid = $v;
// 							del_lottery ( $k, $sports_id, 'shiwu' );
// 							break;
// 						} else if ($xuni_num < intval ( $numConfig ['xuni_limit'] )) {
// 							$prizeid = get_jifen_lottery ( $xuniPrize );
// 							// foreach ($data['xuni'] as $k =>$v){
// 							// $prizeid=$v;
// 							// del_lottery($k,$sports_id,'xuni');
// 							// break;
// 							// }
// 							break;
// 						}
// 					}
// 				} else {
// 					if ($xuni_num < intval ( $numConfig ['xuni_limit'] )) {
// 						$prizeid = get_jifen_lottery ( $xuniPrize );
// 						// 随机获取积分
// 						// foreach ($data['xuni'] as $k =>$v){
// 						// $prizeid=$v;
// 						// del_lottery($k,$sports_id,'xuni');
// 						// break;
// 						// }
// 					}
					
// 					break;
// 				}
// 			}
// 		} else {
// 			if ($xuni_num < intval ( $numConfig ['xuni_limit'] )) {
// 				$prizeid = get_jifen_lottery ( $xuniPrize );
// 			}
// 		}
		
// 		// foreach ( $data as $k => $v ) {
// 		// // echo NOW_TIME;
// 		// if ($k <= NOW_TIME) {
// 		// if ($v != 0) {
// 		// $prizeInfo = $dao->getInfo ( $v );
// 		// if ($hasAward [0] != $prizeInfo ['award_type']) {
// 		// $prizeid = $v;
// 		// del_lottery ( $k, $sports_id );
// 		// break;
// 		// } else {
// 		// break;
// 		// }
// 		// }
// 		// }
// 		// }
// 		// }
// 		// dump( get_lottery ( $prizeArr, $start_time, $end_time, $sports_id ));
// 		// dump(NOW_TIME);
// 		// dump($prizeid);die();
// 		$followInfo = get_followinfo ( $follow_id );
// 		if ($prizeid != 0) {
// 			$award = D ( 'Award' )->getInfo ( $prizeid );
// 			if ($award ['award_type'] == 0) {
// 				$data ['sport_id'] = $sports_id;
// 				$data ['award_id'] = $prizeid;
// 				$data ['follow_id'] = $follow_id;
// 				// $data ['truename'] = $name;
// 				// $data ['mobile'] = $phone;
// 				$data ['zjtime'] = NOW_TIME;
// 				$data ['num'] = 1;
// 				$data ['state'] = 1;
// 				$data ['djtime'] = NOW_TIME;
// 				$data ['uid'] = $manager_id;
// 				$res = D ( 'LuckyFollow' )->delayAddData ( $data );
				
// 				$new_score = $followInfo ['score'] + intval ( $award ['score'] );
// 				D ( 'Common/Follow' )->updateField ( $data ['follow_id'], 'score', $new_score );
// 				// 奖品数量减1
// 				// D ( 'LotteryPrizeList' )->setCount ( $data ['sport_id'], $data ['award_id'] );
// 			} else {
// 				$data ['sport_id'] = $sports_id;
// 				$data ['award_id'] = $prizeid;
// 				$data ['follow_id'] = $follow_id;
// 				$data ['zjtime'] = NOW_TIME;
// 				$data ['num'] = 1;
// 				$data ['state'] = 0;
// 				$data ['uid'] = $manager_id;
// 				$res = D ( 'LuckyFollow' )->delayAddData ( $data );
// 			}
// 		}
// 		if ($followInfo ['truename'] && $followInfo ['mobile']) {
// 			$returnData ['followinfo'] = 1;
// 		} else {
// 			$returnData ['followinfo'] = 0;
// 		}
		
// 		if ($award ['award_type'] != null && ($award ['award_type'] == 0 || $award ['award_type'] == 1)) {
// 			$returnData ['award_type'] = $award ['award_type'];
// 		} else {
// 			$returnData ['award_type'] = - 1;
// 		}
// 		$returnData ['prize_id'] = $prizeid;
// 		$this->ajaxReturn ( $returnData, "JSON" );
// 	}
	
// 	// 保存中奖信息信息
// 	function setLuckyFollow() {
// 		$name = I ( 'post.name' );
// 		$phone = I ( 'post.phone' );
		
// 		if (empty ( $name )) {
// 			$returnData ['msg'] = "请输入姓名";
// 			$returnData ['result'] = "fail";
// 			$this->ajaxReturn ( $returnData, "JSON" );
// 			exit ();
// 		}
// 		if (empty ( $phone ) || strlen ( $phone ) != 11) {
// 			$returnData ['msg'] = "请输入正确的手机号码";
// 			$returnData ['result'] = "fail";
// 			$this->ajaxReturn ( $returnData, "JSON" );
// 			exit ();
// 		}
		
// 		// $data ['sport_id'] = I ( 'post.sportsId' );
// 		// $data ['award_id'] = I ( 'post.thePrizeId' );
// 		$data ['follow_id'] = $this->mid;
// 		$data ['truename'] = $name;
// 		$data ['mobile'] = $phone;
// 		// $data ['zjtime'] = NOW_TIME;
// 		// $data ['num'] = 1;
		
// 		// 当粉丝在同一个场次获得同一个奖时，设置数量
// 		// $r = D ( 'LuckyFollow' )->setNum ( $data );
// 		// if (!empty($r)) {
// 		// $returnData ['msg'] = "添加成功";
// 		// $returnData ['result'] = "success";
// 		// $this->ajaxReturn ( $returnData, "JSON" );
// 		// exit ();
// 		// }
// 		// $res = D ( 'LuckyFollow' )->delayAddData ( $data );
		
// 		$res = D ( 'Common/Follow' )->update ( $data ['follow_id'], $data );
// 		if ($res) {
// 			$returnData ['msg'] = "添加成功";
// 			$returnData ['result'] = "success";
// 			$this->ajaxReturn ( $returnData, "JSON" );
// 		}
// 	}
	
	
}
