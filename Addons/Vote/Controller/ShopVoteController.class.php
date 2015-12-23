<?php

namespace Addons\Vote\Controller;

use Home\Controller\AddonsController;

class ShopVoteController extends AddonsController {
	protected $model;
	public function __construct() {
		parent::__construct ();
		
		$this->model = $this->getModel('shop_vote');
		$this->model || $this->error ( '模型不存在！' );
	}
	/**
	 * 显示指定模型列表数据
	 */
	public function lists() {
	    $isAjax = I ( 'isAjax' );
	    $isRadio = I ( 'isRadio' );
	    $param['mdm']=$_GET['mdm'];
// 	    $_REQUEST['mdm']=$param['mdm'];
	    $res['title']='投票活动';
	    $res['url']=addons_url('Vote://ShopVote/lists',$param);
	    $res ['class'] = _CONTROLLER == 'ShopVote' ? 'current' : '';
	    $nav[]=$res;
	    $this->assign('nav',$nav);
	    $list_data = $this->_get_model_list ( $this->model );
	    if ($isAjax) {
	        $this->assign('isRadio',$isRadio);
	        $this->assign ( $list_data );
	        $this->display ( 'ajax_lists_data' );
	    } else {
	        $this->assign ( $list_data );
	        $this->display (  );
	    }
	   
	}
	public function del() {
		parent::common_del ( $this->model );
	}
	public function edit() {
		// 获取模型信息
		$id = I ( 'id', 0, 'intval' );
		
		if (IS_POST) {
			$this->checkPostData ();
			$Model = D ( parse_name ( get_table_name ( $this->model ['id'] ), 1 ) );
			// 获取模型的字段信息
			$Model = $this->checkAttr ( $Model, $this->model ['id'] );
			if ($Model->create () && $Model->save ()) {
			    D('Addons://Vote/ShopVote')->getInfo($id,true);
				$this->success ( '保存' . $this->model ['title'] . '成功！', U ( 'lists?model=' . $this->model ['name'] ) );
			} else {
				$this->error ( $Model->getError () );
			}
		} else {
			$fields = get_model_attribute ( $this->model ['id'] );
			// 获取数据
			$data = M ( get_table_name ( $this->model ['id'] ) )->find ( $id );
			$data || $this->error ( '数据不存在！' );
			
			$token = get_token ();
			if (isset ( $data ['token'] ) && $token != $data ['token'] && defined ( 'ADDON_PUBLIC_PATH' )) {
				$this->error ( '非法访问！' );
			}
			$this->assign ( 'fields', $fields );
			$this->assign ( 'data', $data );
			$this->display (  );
		}
	}
	function checkPostData() {
	     if (strtotime ( I ( 'post.start_time' ) ) > strtotime ( I ( 'post.end_time' ) )) {
			$this->error ( '开始时间不能大于结束时间' );
		}
		if(I('post.multi_num') < 0){
		    $this->error ( '多选限制数不能小于0' );
		}
		if (I('post.limit_num')<0){
		    $this->error ( '每人可投票次数不能小于0' );
		}
	}
	public function add() {
		if (IS_POST) {
			$this->checkPostData ();
			$Model = D ( parse_name ( get_table_name ( $this->model ['id'] ), 1 ) );
			// 获取模型的字段信息
			$Model = $this->checkAttr ( $Model, $this->model ['id'] );
			if ($Model->create () && $vote_id = $Model->add ()) {
				$this->success ( '添加' . $this->model ['title'] . '成功！', U ( 'lists?model=' . $this->model ['name'] ) );
			} else {
				$this->error ( $Model->getError () );
			}
		} else {
			$vote_fields = get_model_attribute ( $this->model ['id'] );
			$this->assign ( 'fields', $vote_fields );
			$this->display (  );
		}
	}
	
	/**
	 * 投票选项列表
	 */
	 function option_lists(){
	    $map['vote_id']=$param['vote_id']=I('get.vote_id');
	    if (empty($param['vote_id'])){
	        $this->error('获取不到投票活动');
	    }
	    $param1['mdm']=$param['mdm']=$_GET['mdm'];
	    $model=$this->getModel('shop_vote_option');
	    $addUrl=addons_url('Vote://ShopVote/option_add',$param);
	    $this->assign('add_url',$addUrl);
	    $search_url=addons_url('Vote://ShopVote/option_lists',$param);
	    $this->assign('search_url',$search_url);
	    
	    $res['title']='投票活动';
	    $res['url']=addons_url('Vote://ShopVote/lists',$param1);
	    $res ['class'] = _ACTION == 'lists' ? 'current' : '';
	    $nav[]=$res;
	    
	    $res['title']='投票选项';
	    $res['url']=addons_url('Vote://ShopVote/option_lists',$param);
	    $res ['class'] = _ACTION == 'option_lists' ? 'current' : '';
	    $nav[]=$res;
	    
	    $this->assign('nav',$nav);
	     
	    $map ['token'] = get_token ();
	    session ( 'common_condition', $map );
	   
	    $list_data = $this->_get_model_list ( $model );
// 	    $list_data['list_grids']['ids']['href']="option_edit&id=[id]&mdm=".$_GET['mdm']."|编辑,option_del&id=[id]|删除,show_log&option_id=[id]|投票记录";
// dump($list_data);
	    $this->assign ( $list_data );
	    $this->display ('lists'  );
	}
	function option_add(){
	    $param['vote_id']=I('vote_id');
	    $model=$this->getModel('shop_vote_option');
	    $param['mdm']=$_GET['mdm'];
	    $postUrl=addons_url('Vote://ShopVote/option_add',$param);
	    $this->assign('post_url',$postUrl);
	    
	    $res['title']='投票活动';
	    $res['url']=addons_url('Vote://ShopVote/lists');
	    $res ['class'] = _ACTION == 'lists' ? 'current' : '';
	    $nav[]=$res;
	     
	    $res['title']='投票选项';
	    $res['url']=addons_url('Vote://ShopVote/option_lists',$param);
	    $res ['class'] = _ACTION == 'option_lists' ? 'current' : '';
	    $nav[]=$res;
	     
	    $res['title']='添加选项';
// 	    $res['url']=addons_url('Vote://ShopVote/option_lists',$param);
	    $res ['class'] = _ACTION == 'option_add' ? 'current' : '';
	    $nav[]=$res;
	    
	    $this->assign('nav',$nav);
	    
	    
	    if (IS_POST) {
	        $num=D('Addons://Vote/ShopVoteOption')->getNumber($_POST['vote_id']);
	        $_POST['number']=$num;
	        $Model = D ( parse_name ( get_table_name ( $model ['id'] ), 1 ) );
	        // 获取模型的字段信息
	        $Model = $this->checkAttr ( $Model, $model['id'] );
	        if ($Model->create () && $vote_id = $Model->add ()) {
	            D('Addons://Vote/ShopVoteOption')->getOptions($_POST['vote_id'],true);
	            $this->success ( '添加' . $model ['title'] . '成功！', U ( 'option_lists?model=' . $model ['name'].'&vote_id='. $_POST['vote_id'].'&mdm='.$_GET['mdm']) );
	        } else {
	            $this->error ( $Model->getError () );
	        }
	    } else {
	        $fields = get_model_attribute ( $model ['id'] );
	        $this->assign ( 'fields', $fields );
	        //var_dump($fields);
	        $this->display ( 'add' );
	    }
	}
	
	function option_edit(){
	    $model=$this->getModel('shop_vote_option');
	    $param['mdm']=$_GET['mdm'];
	   
	    $res['title']='投票活动';
	    $res['url']=addons_url('Vote://ShopVote/lists',$param);
	    $res ['class'] = _ACTION == 'lists' ? 'current' : '';
	    $nav[]=$res;
	    
	    $param['vote_id']=I('vote_id');
	    $res['title']='投票选项';
	    $res['url']=addons_url('Vote://ShopVote/option_lists',$param);
	    $res ['class'] = _ACTION == 'option_lists' ? 'current' : '';
	    $nav[]=$res;
	    
	    $res['title']='编辑选项';
	    $res['url']=addons_url('Vote://ShopVote/option_edit',$param);
	    $res ['class'] = _ACTION == 'option_edit' ? 'current' : '';
	    $nav[]=$res;
	     
	    $this->assign('nav',$nav);
	    
	    // 获取模型信息
	    $id = I ( 'id', 0, 'intval' );
	    $postUrl=addons_url('Vote://ShopVote/option_edit',$param);
	    $this->assign('post_url',$postUrl);
	    if (IS_POST) {
	        $Model = D ( parse_name ( get_table_name ( $model['id'] ), 1 ) );
	        // 获取模型的字段信息
	        $Model = $this->checkAttr ( $Model, $model ['id'] );
	        if ($Model->create () && $Model->save ()) {
	            D('Addons://Vote/ShopVoteOption')->getInfo($id,true);
	            D('Addons://Vote/ShopVoteOption')->getOptions($_POST['vote_id'],true);
	            $this->success ( '保存' . $model ['title'] . '成功！', U ( 'option_lists?model=' . $model ['name'].'&vote_id='. $_POST['vote_id'].'&mdm='.$_GET['mdm']) ); 
	        } else {
	            $this->error ( $Model->getError () );
	        }
	    } else {
	        $fields = get_model_attribute ( $model['id'] );
	        // 获取数据
	        $data = M ( get_table_name ( $model ['id'] ) )->find ( $id );
	        $data || $this->error ( '数据不存在！' );
	        	
	        $token = get_token ();
	        if (isset ( $data ['token'] ) && $token != $data ['token'] && defined ( 'ADDON_PUBLIC_PATH' )) {
	            $this->error ( '非法访问！' );
	        }
	        $this->assign ( 'fields', $fields );
	        $this->assign ( 'data', $data );
	        $this->display ('edit');
	    }
	}
	public function option_del() {
	    $model=$this->getModel('shop_vote_option');
	    parent::common_del ( $model );
	}
	// 显示投票记录
	function show_log() {
	    
	    $model=$this->getModel('shop_vote_log');
	    $param1['mdm']=$param['mdm']=$_GET['mdm'];
	    $vote_id=I('vote_id');
	    $vote_id && $param['vote_id']=$map['vote_id']=$vote_id;
	    $opt_id=I('option_id');
	    $opt_id && $param['option_id'] = $map['option_id']=$opt_id;
	    $map ['token'] = get_token ();
	    
	    $search_url=addons_url('Vote://ShopVote/show_log',$param);
	    $this->assign('search_url',$search_url);
	     
	  
	    $res['title']='投票活动';
	    $res['url']=addons_url('Vote://ShopVote/lists',$param1);
	    $res ['class'] = _ACTION == 'lists' ? 'current' : '';
	    $nav[]=$res;
	     
	    $res['title']='投票记录';
	    $res['url']=addons_url('Vote://ShopVote/show_log',$param);
	    $res ['class'] = _ACTION == 'show_log' ? 'current' : '';
	    $nav[]=$res;
	    
	    $this->assign('nav',$nav);
	    $this->assign('add_button',false);
	    $this->assign('del_button',false);
	    $this->assign('check_all',false);
	    $btn['url']=U('option_lists',$param);
	    $btn['title']='返回';
	    $returnbtn[]=$btn;
	    $this->assign('top_more_button',$returnbtn);
	    
	    $nickname = I ( 'truename' );
	    if ($nickname){
	        $uidstr=D ( 'Common/User' )->searchUser ( $nickname );
	        if ($uidstr) {
	            $map ['uid'] = array (
	                'in',
	                $uidstr
	            );
	        }else{
	            $map['uid']=0;
	        }
	    }
	    
	    
	    session ( 'common_condition', $map );
// 	    $shopVote=D('Addons://Vote/ShopVote')->getInfo($vote_id);
	   
	    $list_data = $this->_get_model_list ( $model );
	    foreach ($list_data['list_data'] as &$vo){
// 	        $vo['vote_id']=$shopVote['title'];
	        $user=get_userinfo($vo['uid']);
	        $vo['vote_id']=url_img_html($user['headimgurl']);
	        $vo['uid']=$user['nickname'];
	        $shopOption=D('Addons://Vote/ShopVoteOption')->getInfo($vo['option_id']);
	        $vo['option_id']=$shopOption['truename'];
	    }
	    $this->assign ( $list_data );
	    $this->display ('lists'  );
	}
	
	
	
	
	
	// 统计各选项的投票次数
	function showCount() {
		$vote_id = I ( 'id' );
		
		$nav [0] ['title'] = "投票列表";
		$nav [0] ['class'] = "";
		$nav [0] ['url'] = U ( "lists" );
		$nav [1] ['title'] = "投票记录";
		$nav [1] ['class'] = "";
		$nav [1] ['url'] = addons_url ( 'Vote://Vote/showLog?id=' . $vote_id );
		$nav [2] ['title'] = "选项票数";
		$nav [2] ['class'] = "current";
		$this->assign ( 'nav', $nav );
		
		$this->assign ( 'add_button', false );
		$this->assign ( 'del_button', false );
		$this->assign ( 'check_all', false );
		$this->assign ( 'search_button', false );
		
		// 将缓存的数据，写入数据库
		D ( 'VoteOption' )->updateOptCount ( $vote_id, null );
		
		$page = I ( 'p', 1, 'intval' );
		$model = $this->option;
		
		$list_data = $this->_list_grid ( $model );
		$fields = $list_data ['fields'];
		$grids = $list_data ['list_grids'];
		// 查询条件
		$map ['vote_id'] = $vote_id;
		// $map['token']=get_token();
		session ( 'common_condition', $map );
		// $map = $this->_search_map ( $model, $fields );
		
		$row = empty ( $model ['list_row'] ) ? 20 : $model ['list_row'];
		empty ( $fields ) || in_array ( 'id', $fields ) || array_push ( $fields, 'id' );
		$name = parse_name ( get_table_name ( $model ['id'] ), true );
		$data = M ( $name )->field ( empty ( $fields ) ? true : $fields )->where ( $map )->order ( 'id DESC' )->page ( $page, $row )->select ();
		$count = M ( $name )->where ( $map )->count ();
		$list_data ['list_data'] = $data;
		
		// 分页
		if ($count > $row) {
			$page = new \Think\Page ( $count, $row );
			$page->setConfig ( 'theme', '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%' );
			$list_data ['_page'] = $page->show ();
		}
		$this->assign ( $list_data );
		// dump($list_data);
		$this->display ( './Application/Home/View/default/Addons/lists.html' );
	}
	/* 预览 */
	function preview(){
		$id = I('vote_id',0,intval);
		$url = addons_url('Vote://Wap/index',array('vote_id'=>$id));
		$this->assign('url', $url);
        $this->display(SITE_PATH . '/Application/Home/View/default/Addons/preview.html');
	}
	/**
	 * **************************微信上的操作功能************************************
	 */
	
}
