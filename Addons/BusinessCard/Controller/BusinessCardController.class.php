<?php

namespace Addons\BusinessCard\Controller;

use Home\Controller\AddonsController;

class BusinessCardController extends AddonsController {
	function lists() {
	    $isUser=get_userinfo($this->mid,'manager_id');
	    if ($isUser){
	        redirect ( addons_url ( 'BusinessCard://BusinessCard/edit'));
	    }
		$this->assign ( 'add_button', false );
		
		$model = $this->getModel ( 'BusinessCard' );
		
		$list_data = $this->_get_model_list ( $model );
		foreach ( $list_data ['list_data'] as &$vo ) {
			// $url = U ( 'detail?uid=' . $vo ['uid'] );
			
			$url = addons_url ( 'BusinessCard://Wap/detail', array (
					'uid' => $vo ['uid'] 
			) );
			$vo ['qrcode'] = "<img class='list_img' src='http://qr.liantu.com/api.php?text=$url' />";
		}
		$this->assign ( $list_data );
		$this->display ();
	}
	
	function edit() {
	    $model = $this->getModel ( 'business_card' );
	    $map['uid']=$uid=$this->mid;
	    $act='edit';
// 	    $map['uid']=11857;
	    $data=M('business_card')->where($map)->find();
// 	    $id = I ( 'id' );
	
	    // 获取数据
// 	    $data = M ( get_table_name ( $model ['id'] ) )->find ( $id );
	    $data || $act='add';
	    // $token = get_token ();
	    // if (isset ( $data ['token'] ) && $token != $data ['token'] && defined ( 'ADDON_PUBLIC_PATH' )) {
	    // $this->error ( '非法访问！' );
	    // }
	
	    
	    if (IS_POST) {
	        $_POST['uid']=$this->mid;
	        $_POST['token']=get_token();
	        $Model = D ( parse_name ( get_table_name ( $model ['id'] ), 1 ) );
	        // 获取模型的字段信息
	        $Model = $this->checkAttr ( $Model, $model ['id'] );
	        if ($act=='edit'){
	            if ($Model->create () && $Model->save ()) {
	                // 清空缓存
	                method_exists ( $Model, 'clear' ) ;
	                //     			$url=  '<script language=javascript>history.go(-1);</script>';
	                $this->success ( '保存' . $model ['title'] . '成功！');
	            } else {
	                $this->error ( $Model->getError () );
	            }
	        }else {
	            if ($Model->create () && $id = $Model->add ()) {
	                 
	                // 清空缓存
	                method_exists ( $Model, 'clear' ) && $Model->clear ( $id, 'add' );
	                 
	                $this->success ( '添加' . $model ['title'] . '成功！');
	            } else {
	                $this->error ( $Model->getError () );
	            }
	        }
	       
	    } else {
	        $fields = get_model_attribute ( $model ['id'] );
	        $this->assign ( 'fields', $fields );
	        $this->assign ( 'data', $data );
	
	        $this->display ();
	    }
	}
}
