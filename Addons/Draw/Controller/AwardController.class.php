<?php

namespace Addons\Draw\Controller;
use Home\Controller\AddonsController;

class AwardController extends AddonsController{
    function _initialize() {
        parent::_initialize ();
        $controller = strtolower ( _CONTROLLER );
        $res ['title'] = '抽奖游戏';
        $res ['url'] = addons_url ( 'Draw://Games/lists' );
        $res ['class'] = $controller == 'games' ? 'current' : '';
        $nav [] = $res;
        
        $res ['title'] = '奖品库管理';
        $res ['url'] = addons_url ( 'Draw://Award/lists' );
        $res ['class'] = $controller == 'award' ? 'current' : '';
        $nav [] = $res;
        
        $res ['title'] = '中奖人列表';
        $res ['url'] = addons_url ( 'Draw://LuckyFollow/games_lucky_lists' );
        $res ['class'] = $controller == 'luckyfollow' ? 'current' : '';
        $nav [] = $res;
        $this->assign ( 'nav', $nav );
         
    }

    // 通用插件的列表模型
    public function lists($model = null, $page = 0) {
    	
    	$model = $this->getModel('sport_award');
    	$map['uid']=$this->mid;
    	$map['token']=get_token();
    	$map['aim_table']='lottery_games';
    	session ( 'common_condition', $map );
    	$list_data = $this->_get_model_list($model, 0, 'id desc', true);
    	$dao = D('Award');
    	foreach ($list_data['list_data'] as &$vo) {
    		$vo = $dao->getInfo($vo['id']);
    	}
    	$this->assign($list_data);
    	
    	$this->display();
    }
  
    
    function export($model = null) {
        is_array ( $model ) || $model = $this->getModel ( 'sport_award' );
        parent::common_export ( $this->model );
    }
    
    function add() {
    	$model = $this->getModel ( 'sport_award' );
    	if (IS_POST) {
    		$this->checkPostData();
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
    		$this->assign ( 'fields', $fields );
    			
    		$this->display ();
    	}
    }
    
    function edit() {
    	$model = $this->getModel ( 'sport_award' );
    	$id = I ( 'id' );
    
    	// 获取数据
    	$data = M ( get_table_name ( $model ['id'] ) )->find ( $id );
    	$data || $this->error ( '数据不存在！' );
    	if (IS_POST) {
    			
    		$this->checkPostData();
    		$Model = D ( parse_name ( get_table_name ( $model ['id'] ), 1 ) );
    		// 获取模型的字段信息
    		$Model = $this->checkAttr ( $Model, $model ['id'] );
    		if ($Model->create () && $Model->save ()) {
    			// 清空缓存
    			method_exists ( $Model, 'clear' ) && $Model->clear ( $id, 'edit' );
    			D('Award')->getInfo($id,true);
    			$this->success ( '保存' . $model ['title'] . '成功！',U ( 'lists?model=' . $model ['name'], $this->get_param ));
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

    // 通用插件的删除模型
    public function del($model = null, $ids = null) {
        parent::common_del ( 'sport_award', $ids );
    }
    
    function checkPostData(){
        $_POST['aim_table']='lottery_games';
//     	if ($_POST['count']<0){
//     		$this->error ( ' 奖品数量不能低于0' );
//     		exit ();
//     	}
    	if ($_POST['award_type']==1){
    		//实物奖品
    		if ($_POST['price']<0){
    			$this->error ( ' 奖品价格不能低于0' );
    			exit ();
    		}
    		 
    	}else if($_POST['award_type']==0){
    		//虚拟奖品
    		if (!$_POST['score']){
    			$this->error ( '设置奖品积分不能为空' );
    			exit ();
    		}
    		if ($_POST['score']<0){
    			$this->error ( '设置奖品积分不能小于0' );
    			exit ();
    		}
    	}else if($_POST['award_type']==4){
    	    //返现
    	    if (!$_POST['money']){
    	        $this->error ( '返现金额不能为空' );
    	        exit ();
    	    }
    	    if ($_POST['money']<0){
    	        $this->error ( '返现金额不能小于0' );
    	        exit ();
    	    }
    	}else{
    	    if (!$_POST['coupon_id']){
    	        $this->error('没有可赠送券');
    	        exit();
    	    }
    	}
    }
    function get_coupon(){
        $awardType=I('award_type');
        $list=$this->_coupon($awardType);
        $this->ajaxReturn($list);
    }
    
    function _coupon($awardType=2){
        $map ['end_time'] = array (
            'gt',
            NOW_TIME
        );
        $map ['token'] = get_token ();
        
        if($awardType==2){
            //优惠券
            $list = M ( 'coupon' )->where ( $map )->field ( 'id,title' )->order ( 'id desc' )->select ();
        }else if($awardType == 3){
            //代金券
            $list = M ( 'shop_coupon' )->where ( $map )->field ( 'id,title' )->order ( 'id desc' )->select ();
        }
        return $list;
    }
    
    function list_data() {
        $page = I ( 'p', 1, 'intval' );
        $map['token']=get_token();
        $map['aim_table']='lottery_games';
        $dao=D ( 'Addons://Draw/Award' );
        $list_data =$dao->where($map)->field('id')->order ( 'id DESC' )->page ( $page, 20 )->selectPage ( 20 );
       
        foreach ($list_data['list_data'] as &$v){
            $v=$dao->getInfo($v['id']);
        }
//         dump ( $list_data );
        $this->ajaxReturn( $list_data ,'JSON');
    }
    ////////////////////靓妆///////////////////////
    // 通用插件的列表模型
    public function lzwg_lists($model = null, $page = 0) {
    	$controller = strtolower ( _CONTROLLER );
    	$res ['title'] = '奖品库管理';
    	$res ['url'] = addons_url ( 'Draw://Award/lzwg_lists' );
    	$res ['class'] = $controller == 'award' ? 'current' : '';
    	$nav [] = $res;
    
    	$res ['title'] = '中奖列表';
    	$res ['url'] = addons_url ( 'Draw://LuckyFollow/lzwg_lists' );
    	$res ['class'] = $controller == 'luckyfollow' ? 'current' : '';
    	$nav [] = $res;
    	$this->assign ( 'nav', $nav );
    	 
    	$model = $this->getModel('sport_award');
    	$map['uid']=$this->mid;
    	session ( 'common_condition', $map );
    	$list_data = $this->_get_model_list($model, 0, 'id desc', true);
    	$dao = D('Award');
    	foreach ($list_data['list_data'] as &$vo) {
    		$vo = $dao->getInfo($vo['id']);
    	}
    	 
    	$list_data['list_grids'][6]['href']="lzwg_edit?id=[id]&model=".$model['id']."|编辑,[DELETE]|删除,getlzwgListByAwardId?awardId=[id]&_controller=LuckyFollow|中奖者列表";
    	//     	dump($list_data);
    	$this->assign($list_data);
    
    	$this->display();
    }
    
    function lzwg_edit() {
    	$model = $this->getModel ( 'sport_award' );
    	$id = I ( 'id' );
    
    	// 获取数据
    	$data = M ( get_table_name ( $model ['id'] ) )->find ( $id );
    	$data || $this->error ( '数据不存在！' );
    
    	// $token = get_token ();
    	// if (isset ( $data ['token'] ) && $token != $data ['token'] && defined ( 'ADDON_PUBLIC_PATH' )) {
    	// $this->error ( '非法访问！' );
    	// }
    
    	if (IS_POST) {
    		 
    		$this->checkPostData();
    		$Model = D ( parse_name ( get_table_name ( $model ['id'] ), 1 ) );
    		// 获取模型的字段信息
    		$Model = $this->checkAttr ( $Model, $model ['id'] );
    		if ($Model->create () && $Model->save ()) {
    
    			// 清空缓存
    			method_exists ( $Model, 'clear' ) && $Model->clear ( $id, 'edit' );
    			D('Award')->getInfo($id,true);
    			$this->success ( '保存' . $model ['title'] . '成功！', U ( 'lzwg_lists?model=' . $model ['name'], $this->get_param ) );
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
    
    function lzwg_add() {
    	$model = $this->getModel ( 'sport_award' );
    	if (IS_POST) {
    
    		$this->checkPostData();
    		$Model = D ( parse_name ( get_table_name ( $model ['id'] ), 1 ) );
    		// 获取模型的字段信息
    		$Model = $this->checkAttr ( $Model, $model ['id'] );
    		if ($Model->create () && $id = $Model->add ()) {
    
    			// 清空缓存
    			method_exists ( $Model, 'clear' ) && $Model->clear ( $id, 'add' );
    
    			$this->success ( '添加' . $model ['title'] . '成功！', U ( 'lzwg_lists?model=' . $model ['name'], $this->get_param ) );
    		} else {
    			$this->error ( $Model->getError () );
    		}
    	} else {
    		$fields = get_model_attribute ( $model ['id'] );
    		$this->assign ( 'fields', $fields );
    		 
    		$this->display ();
    	}
    }
    
    
}
