<?php

namespace Addons\CustomMenu\Model;
use Think\Model;

/**
 * CustomMenu模型
 */
class CustomMenuModel extends Model{

    // 获取进行中的活动
    function getListData($addon,$model,$stime_col='',$etime_col='',$token_col='',$state_col='',$state_val=1) {
        if ($token_col){
            $map [$token_col] = get_token ();
        }
        if ($stime_col){
            $map[$stime_col]=array('elt',NOW_TIME);
        }
        if ($etime_col){
            $map[$etime_col]=array('gt',NOW_TIME);
        }
        if ($state_col){
            $map[$state_col]=$state_val;
        }
        $data_list = D("Addons://$addon/$model")->where ( $map )->field ( 'id,title' )->order ( 'id desc' )->select ();
        return $data_list;
    }
    
}
