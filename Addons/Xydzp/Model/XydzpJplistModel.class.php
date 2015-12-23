<?php

namespace Addons\Xydzp\Model;

use Think\Model;

/**
 * CardVouchers模型
 */
class XydzpJplistModel extends Model {
	function getXydzpJplistInfo($id, $update = false, $data = array()) {
		$key = 'XydzpJplist_getXydzpJplistInfo_' . $id;
		$info = S ( $key );
		if ($info === false || $update) {
			$info = ( array ) (empty ( $data ) ? $this->find ( $id ) : $data);
			S ( $key, $info );
		}
		return $info;
	}
	
	function getXydzpJplists($xydzp_id,$update=false){
		$key = 'XydzpJplist_getXydzpJplists_' . $xydzp_id;
		$info = S ( $key );
		if ($info === false || $update) {
			$map ['xydzp_id']=$xydzp_id;
			$info = ( array )  M ( 'xydzp_jplist' )->where ( $map )->select ();
			S ( $key, $info );
		}
		return $info;
	}
	
	// 查询奖品列表
	function getJplistdetail($xydzp_id,$update=false){
		$key = 'XydzpJplist_getJplistdetail_' . $xydzp_id;
		$info = S ( $key );
		if ($info === false || $update) {
			$fix = C ( "DB_PREFIX" );
			$map['xydzp_id']=$xydzp_id;
			$info= (array )M ( 'xydzp_jplist' )->join ( 'left join ' . $fix . 'xydzp_option on ' . $fix . 'xydzp_jplist.xydzp_option_id=' . $fix . 'xydzp_option.id' )->field ( $fix . "xydzp_option.title,pic," . $fix . "xydzp_option.miaoshu," . $fix . "xydzp_option.isdf, ".$fix."xydzp_option.card_url,".$fix."xydzp_option.experience,".$fix."xydzp_option.coupon_id,".$fix."xydzp_option.num as kcnum,".$fix."xydzp_option.jptype,gailv,xydzp_id,xydzp_option_id" )->where ($map)->order ( "type asc,gailv asc" )->select ();
			S($key,$info);
		}
		return $info;
	}
}
