<?php

namespace Addons\BusinessCard\Controller;

use Home\Controller\AddonsController;

class WapController extends AddonsController {
	function detail($temp = '', $is_perview = false) {
		if (! isset ( $_GET ['uid'] )) {
			redirect ( U ( 'detail', array (
					'uid' => $this->mid 
			) ) );
		}
		$info = $this->_getInfo ( $this->uid );
		// dump($info);
		// exit;
		if ($this->mid != $this->uid) {
			$map_collect ['from_uid'] = $this->mid;
			$map_collect ['to_uid'] = $this->uid;
			$isExchange = M ( 'business_card_collect' )->where ( $map_collect )->getField ( 'id' ); // 是否交换了名片
			if ($isExchange) {
				$isExchange = 1;
			}
			$this->assign ( 'isExchange', $isExchange );
			
		}
		if ($isExchange == 0 && $this->uid != $this->mid) {
			$this->assign ( 'isShowBtn', 1 );
		}
		if ($isExchange == 1 && $this->uid != $this->mid) {
			$this->assign ( 'needDeleteBtn', 1 );
		}
		if ($this->uid == $this->mid){
		    $this->assign('isself',1);
		    $map['uid']=$this->uid;
		    $map['aim_id']=$info['id'];
		    $map['aim_table']='business_card';
		    $comments=D('Addons://Comment/Comment')->where($map)->select();
		    foreach ($comments as &$c){
		        $userInfo=get_followinfo($c['follow_id']);
		        $c['truename']=$userInfo['truename'];
		        $c['mobile']=$userInfo['mobile'];
		        $c['headimgurl']=$userInfo['headimgurl'];
		    }
		    $this->assign('comments',$comments);
		}
		//名片栏目
		$colunm=$this->_getBusinessCardColunm($this->uid);
		$this->assign('card_colunm',$colunm);
		$this->assign ( 'is_perview', $is_perview );
		$this->display ( ONETHINK_ADDON_PATH . 'BusinessCard/View/default/TemplateDetail/' . $info ['temp'] . '/detail.html' );
	}
	function _getInfo($uid) {
		$map ['uid'] = $this->uid;
		$info = M ( 'business_card' )->where ( $map )->find ();
		$isCreate = 1;
		if (! $info) {
			$isCreate = 0;
			if ($this->uid == $this->mid) {
				$this->error ( '请先创建您的名片', U ( 'add' ) );
			}
		}
		
		$userInfo = getUserInfo ( $this->uid );
		
		$info = array_merge ( $userInfo, $info );
		// dump ( $userInfo );
		empty ( $info ['template'] ) && $info ['template'] = 'default';
		$info ['temp'] = $info ['template'];
		$this->assign ( 'info', $info );
		$this->assign ( 'isCreate', $isCreate );
		$this->assign ( 'page_title', $info ['truename'] . '的互联网名片' );
		
		return $info;
	}
	//获取栏目信息
	function _getBusinessCardColunm($uid){
	    $map['uid']=$uid;
	    $mediaCategory=M('we_media_category')->field('id,title')->select();
	    foreach ($mediaCategory as $m){
	        $cate[$m['id']]=$m['title'];
	    }
	    $businessCardColunm=M('business_card_column')->where($map)->order('sort asc')->select();
	    foreach ($businessCardColunm as &$c){
	        if ($c['type']==1){
	            $c['cate_title']=$cate[$c['cate_id']];
	            $c['cate_url']=addons_url('WeMedia://Wap/lists',array('uid'=>$uid,'cate_id'=>$c['cate_id']));
	        }
	    }
	    return $businessCardColunm;
	}
	
	function add() {
		$model = $this->getModel ( 'BusinessCard' );
		
		if (IS_POST) {
			$_POST ['uid'] = $this->mid;
			$Model = D ( parse_name ( get_table_name ( $model ['id'] ), 1 ) );
			// 获取模型的字段信息
			$Model = $this->checkAttr ( $Model, $model ['id'] );
			if ($Model->create () && $id = $Model->add ()) {
				
				redirect ( U ( 'choose_template', array (
						'id' => $id 
				) ) );
			} else {
				$this->error ( $Model->getError () );
			}
		} else {
			$fields = get_model_attribute ( $model ['id'] );
			$this->assign ( 'fields', $fields );
			
			$this->display ( 'moblieForm' );
		}
	}
	function edit() {
		$model = $this->getModel ( 'BusinessCard' );
		$id = I ( 'id' );
		
		// 获取数据
		$data = M ( get_table_name ( $model ['id'] ) )->find ( $id );
		$data || $this->error ( '数据不存在！' );
		
		if (IS_POST) {
			$Model = D ( parse_name ( get_table_name ( $model ['id'] ), 1 ) );
			// 获取模型的字段信息
			$Model = $this->checkAttr ( $Model, $model ['id'] );
			if ($Model->create () && $Model->save ()) {
				$this->success ( '保存成功！', U ( 'detail' ) );
			} else {
				$this->error ( $Model->getError () );
			}
		} else {
			$fields = get_model_attribute ( $model ['id'] );
			$this->assign ( 'fields', $fields );
			$this->assign ( 'data', $data );
			
			$this->assign ( 'post_url', U ( 'edit' ) );
			$this->display ( 'moblieForm' );
		}
	}
	function choose_template() {
		$map ['uid'] = $this->uid;
		$info = M ( 'business_card' )->where ( $map )->find ();
		$default = $info ['template'];
		$this->_getTemplateByDir ( 'TemplateDetail', $default );
		
		$this->assign ( 'info', $info );
		
		$this->display ();
	}
	function save_template() {
		// $model = M( 'BusinessCard' );
		$data ['template'] = I ( 'post.temp' );
		$id = I ( 'get.id' );
		$res = M ( 'business_card' )->where ( array (
				'id' => $id 
		) )->save ( $data );
		if ($res) {
			echo 1;
		} else {
			echo 0;
		}
	}
	function preview() {
		$temp = I ( 'temp' );
		
		$this->detail ( $temp, true );
	}
	// 交换名片
	function add_collect() {
		$map ['to_uid'] = I ( 'get.to_uid', 0, 'intval' );
		if (empty ( $map ['to_uid'] )) {
			$this->success ( '参数非法' );
			exit ();
		}
		
		$map ['from_uid'] = $this->mid;
		
		if (M ( 'business_card_collect' )->where ( $map )->getField ( 'id' )) {
			$res = M ( 'business_card_collect' )->where ( $map )->getField ( 'cTime', NOW_TIME );
		} else {
			$map ['cTime'] = NOW_TIME;
			$res = M ( 'business_card_collect' )->add ( $map );
		}
		if ($res) {
			echo 1;
		} else {
			echo 0;
		}
	}
	// 删除收藏
	function del_collect() {
		$map ['to_uid'] = I ( 'get.to_uid', 0, 'intval' );
		if (empty ( $map ['to_uid'] )) {
			$this->success ( '参数非法' );
			exit ();
		}
		
		$map ['from_uid'] = $this->mid;
		$res = M ( 'business_card_collect' )->where ( $map )->delete ();
		if ($res) {
			// $this->success ( '删除成功', U ( 'collected' ) );
			echo 1;
		} else {
			// $this->success ( '删除失败' );
			echo 0;
		}
	}
	// 我收藏的名片列表
	function collected() {
		$map ['from_uid'] = $this->mid;
		$list = M ( 'business_card_collect' )->where ( $map )->order ( 'cTime desc' )->selectPage ();
		foreach ( $list ['list_data'] as &$v ) {
			$map2 ['uid'] = $v ['to_uid'];
			$info = M ( 'business_card' )->where ( $map2 )->find ();
			$userInfo = getUserInfo ( $map2 ['uid'] );
			$v = array_merge ( $userInfo, $info );
		}
		$this->assign ( $list );
		$this->assign ( 'type', 'collected' );
		
		$this->_getInfo ( $this->mid );
		
		$this->display ( 'collect' );
	}
	// 收藏我的名片列表
	function collecting() {
		$map ['to_uid'] = $this->mid;
		$list = M ( 'business_card_collect' )->where ( $map )->order ( 'cTime desc' )->selectPage ();
		foreach ( $list ['list_data'] as &$v ) {
			$map2 ['uid'] = $v ['from_uid'];
			$info = ( array ) M ( 'business_card' )->where ( $map2 )->find ();
			
			$userInfo = getUserInfo ( $map2 ['uid'] );
			$v = array_merge ( $userInfo, $info );
		}
		$this->assign ( $list );
		
		$this->assign ( 'type', 'collecting' );
		
		$this->_getInfo ( $this->mid );
		
		$this->display ( 'collect' );
	}
	// 获取目录下的所有模板
	function _getTemplateByDir($type = 'TemplateDetail', $default = "default") {
		$action = strtolower ( _ACTION );
		// dump($default);
		$dir = ONETHINK_ADDON_PATH . _ADDONS . '/View/default/' . $type;
		$url = SITE_URL . '/Addons/' . _ADDONS . '/View/default/' . $type;
		
		$dirObj = opendir ( $dir );
		while ( $file = readdir ( $dirObj ) ) {
			if ($file === '.' || $file == '..' || $file == '.svn' || is_file ( $dir . '/' . $file ))
				continue;
			
			$res ['dirName'] = $res ['title'] = $file;
			
			// 获取配置文件
			if (file_exists ( $dir . '/' . $file . '/info.php' )) {
				$info = require_once $dir . '/' . $file . '/info.php';
				$res = array_merge ( $res, $info );
			}
			
			// 获取效果图
			if (file_exists ( $dir . '/' . $file . '/info.php' )) {
				$res ['icon'] = __ROOT__ . '/Addons/BusinessCard/View/default/' . $type . '/' . $file . '/icon.jpg';
			} else {
				$res ['icon'] = ADDON_PUBLIC_PATH . '/default.png';
			}
			
			// 默认选中
			if ($default == $file) {
				$res ['class'] = 'selected';
				$res ['checked'] = 'checked="checked"';
			}
			
			$tempList [] = $res;
			unset ( $res );
		}
		closedir ( $dir );
		
		// dump ( $tempList );
		
		$this->assign ( 'tempList', $tempList );
	}
	
	//删除留言
	function delComment(){
	    $map['id']=I('id');
	    $res=D('Addons://Comment/Comment')->where($map)->delete();
	    if ($res){
	        echo $res;
	    }else {
	        echo 0;
	    }
	}
}
