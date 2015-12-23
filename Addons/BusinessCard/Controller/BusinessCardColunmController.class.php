<?php

namespace Addons\BusinessCard\Controller;

use Home\Controller\AddonsController;

class BusinessCardColunmController extends AddonsController {
    function _initialize() {
        parent::_initialize ();
        $controller = strtolower ( _CONTROLLER );
//         $res ['title'] = '微名片';
//         $res ['url'] = addons_url ( 'BusinessCard://BusinessCard/lists' );
//         $res ['class'] = $controller == 'businesscard' ? 'current' : '';
//         $nav [] = $res;
        
        $res ['title'] = '栏目管理';
        $res ['url'] = addons_url ( 'BusinessCard://BusinessCardColunm/lists' );
        $res ['class'] = $controller == 'businesscardcolunm' ? 'current' : '';
        $nav [] = $res;
        $this->assign ( 'nav', $nav );
    }
    
	function lists() {
        $businessCard=I('business_card_id',0,'intval');
        if ($businessCard){
            $map['business_card_id']=$businessCard;
        }else {
            $map['uid']=$this->mid;
        }
        $model=$this->getModel('business_card_column');
      
//         $map['uid']=11857;
        session ( 'common_condition', $map );
        $list_data=$this->_get_model_list($model,0,'sort asc');
        $categoryInfo=$this->_get_media_category_title();
        foreach ($list_data['list_data'] as &$vo){
            $vo['cate_id']=$vo['cate_id']==0?' ':$categoryInfo[$vo['cate_id']];
            
        }
        $this->assign($list_data);
        $this->display();
	}
	//获取文章分类
	function _get_media_category_title(){
	    $mediaCategory=M('we_media_category')->field('id,title')->select();
	    foreach ($mediaCategory as $m){
	        $cate[$m['id']]=$m['title'];
	    }
	    return $cate;
	}
	function add(){
	    $business_card_id=I('business_card_id',0,'intval');
	    $model = $this->getModel ( 'business_card_column' );
	    if (IS_POST) {
	        $map['id']=$_POST['business_card_id'];
	        $cardUid=D('BusinessCard')->where($map)->getField('uid');
	        $_POST['uid']=$cardUid;
	        
	        $Model = D ( parse_name ( get_table_name ( $model ['id'] ), 1 ) );
	        // 获取模型的字段信息
	        $Model = $this->checkAttr ( $Model, $model ['id'] );
	        if ($Model->create () && $id = $Model->add ()) {
	    
	            // 清空缓存
	            method_exists ( $Model, 'clear' ) && $Model->clear ( $id, 'add' );
	    
	            $this->success ( '添加' . $model ['title'] . '成功！', U ( 'lists?model=' . $model ['name'], $this->get_param ) );
	        } else {
	            $this->error ( $Model->getError () );
	        }
	    } else {
	        $fields = get_model_attribute ( $model ['id'] );
	        if (empty($business_card_id)){
	            $map['uid']=$this->mid;
	            $business_card_id=M('business_card')->where($map)->getField('id');
	            if (empty($business_card_id)){
	                $this->error('请先添加微名片基本信息',addons_url('BusinessCard://BusinessCard/edit'));
	            }
	        }
	        
	        $fields['business_card_id']['value']=$business_card_id;
// 	        $fields['business_card_id']['is_show']=4;
	        $this->assign ( 'fields', $fields );
	        $this->display ();
	    }
	}
	
	function edit() {
	    $model = $this->getModel ( 'business_card_column' );
	    $id = I ( 'id' );
	
	    // 获取数据
	    $data = M ( get_table_name ( $model ['id'] ) )->find ( $id );
	    $data || $this->error ( '数据不存在！' );
	
	    // $token = get_token ();
	    // if (isset ( $data ['token'] ) && $token != $data ['token'] && defined ( 'ADDON_PUBLIC_PATH' )) {
	    // $this->error ( '非法访问！' );
	    // }
	
	    if (IS_POST) {
	        
	        $map['id']=$_POST['business_card_id'];
	        $cardUid=D('BusinessCard')->where($map)->getField('uid');
	        $_POST['uid']=$cardUid;
	        $Model = D ( parse_name ( get_table_name ( $model ['id'] ), 1 ) );
	        // 获取模型的字段信息
	        $Model = $this->checkAttr ( $Model, $model ['id'] );
	        if ($Model->create () && $Model->save ()) {
	
	            // 清空缓存
	            method_exists ( $Model, 'clear' ) && $Model->clear ( $id, 'edit' );
	            //     			$url=  '<script language=javascript>history.go(-1);</script>';
	            $this->success ( '保存' . $model ['title'] . '成功！', U ( 'lists?model=' . $model ['name'], $this->get_param ));
	        } else {
	            $this->error ( $Model->getError () );
	        }
	    } else {
	        $fields = get_model_attribute ( $model ['id'] );
	        $this->assign ( 'fields', $fields );
	        $this->assign ( 'data', $data );
	         
	        $this->display ();
	    }
	}
}
