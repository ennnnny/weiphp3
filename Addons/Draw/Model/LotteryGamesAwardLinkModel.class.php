<?php

namespace Addons\Draw\Model;
use Think\Model;

/**
 * 抽奖游戏活动奖品列表模型
 */
class LotteryGamesAwardLinkModel extends Model{
    
    //获取抽奖活动设置的奖品
    function getGamesAwardlists($gamesId,$update=false){
        $key = 'LotteryGamesAwardLink_getGamesAwardlists_' . $gamesId;
        $info = S ( $key );
        if ($info === false || $update) {
            $map['games_id']=$gamesId;
            $map['token']=get_token();
            $awardDao=D('Addons://Draw/Award');
            $info=$this->where($map)->order('num asc')->select();
            foreach ($info as &$v){
                $award=$awardDao->getInfo($v['award_id']);
                unset($award['id']);
                $v=array_merge($v,$award);
            }
            S ( $key, $info, 86400 );
        }
        return $info;
    }
    
}
