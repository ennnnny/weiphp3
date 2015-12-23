<?php

namespace Addons\Draw\Model;
use Think\Model;

/**
 * Award奖品库模型
 */
class AwardModel extends Model{
    protected $tableName ='sport_award';
    function getInfo($id, $update = false, $data = array()) {
        $key = 'Award_getInfo_' . $id;
        $info = S ( $key );
        if ($info === false || $update) {
            $info = ( array ) (empty ( $data ) ? $this->find ( $id ) : $data);
            if (count($info)!=0){
                $model=getModelByName($this->tableName);
                $info['img_url']=get_cover_url($info['img'],100,100);
                $info['type_name']=get_name_by_status($info['award_type'], 'award_type', $model['id']);
            	if($info['award_type']==0){
//             		$info['price']=$info['score'].'积分';
                    $info['award_title']=$info['score'].'积分';
            	}else if($info['award_type']==1){
//             		$info['price'] = $info['price']==0?'未报价':$info['price'];
                    $info['award_title']=$info['price']==0?'':'价值 '.intval($info['price']).'元';
            	}else if ($info['award_type']==2){
            	    $coupon=D('Addons://Coupon/Coupon')->getInfo($info['coupon_id']);
            	    $info['award_title']=$coupon['title'];
            	    $info['coupon_num']=$coupon['num'];
            	}else if ($info['award_type']==3){
            	    $coupon=D('Addons://ShopCoupon/Coupon')->getInfo($info['coupon_id']);
            	    $info['coupon_num']=$coupon['num'];
            	    $info['award_title']=$coupon['title'];
            	}else if($info['award_type']==4){
            	    $info['award_title']='返现金额 '.$info['money'].'元';
            	}
            }
            S ( $key, $info, 86400 );
        }
        return $info;
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
