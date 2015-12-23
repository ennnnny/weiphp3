<?php

namespace Addons\Draw\Model;
use Think\Model;

/**
 * 抽奖游戏活动模型
 */
class GamesModel extends Model{
    protected $tableName ='lottery_games';
    function getInfo($id, $update = false, $data = array()) {
        $key = 'Games_getInfo_' . $id;
        $info = S ( $key );
        if ($info === false || $update) {
            $info = ( array ) (empty ( $data ) ? $this->find ( $id ) : $data);
            if (!empty($info)){
                $info['attend_num']=D('Addons://Draw/DrawFollowLog')->get_user_attend_count($id);
            }
            S ( $key, $info, 86400 );
        }
        return $info;
    }
    function getAttendNum($id){
        $num=D('Addons://Draw/DrawFollowLog')->get_user_attend_count($id);
        $info=$this->getInfo($id);
        if ($info['attend_num'] != $num){
            $save['attend_num']=$num;
            $this->update($id,$save);
        }
        return $num;
    }
    function update($id,$data=array()){
    	$map['id']=$id;
    	$res=$this->where($map)->save($data);
    	if($res){
    		$this->clear($id);
    	}
    	return $res;
    }
    function clear($id) {
        $this->getInfo ( $id, true );
    }
}
