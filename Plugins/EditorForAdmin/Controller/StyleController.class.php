<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: huajie <banhuajie@163.com>
// +----------------------------------------------------------------------
namespace Plugins\EditorForAdmin\Controller;

use Home\Controller\AddonsController;
use Think\Upload;

class StyleController extends AddonsController {
	function get_article_style(){
		$group_id = I('group_id',0,intval);
		$groupList = M('article_style_group') -> select();
		if($groupList){
			if($group_id==0){
				$groupList[0]['class'] = "current";
				$group_id = $groupList[0]['id'];
			}else{
				foreach($groupList as &$v){
					if($v['id']==$group_id){
						$v['class'] = "current";
					}
				}
			}
			$list = M('article_style') -> where(array('group_id'=>$group_id))->selectPage(15);
		}
		$this -> assign('group_list',$groupList);
		$this -> assign('list',$list);
		$this -> display('article_style_list');
	}
}
