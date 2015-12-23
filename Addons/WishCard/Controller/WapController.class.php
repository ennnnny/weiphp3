<?php

namespace Addons\WishCard\Controller;
use Home\Controller\AddonsController;

class WapController extends BaseController{
	var $user;
	function _initialize() {
		parent::_initialize();
		$openid = get_openid();
		$this -> user = getWeixinUserInfo($openid); 
		$this -> user['uid'] = get_mid();
		$this -> assign('user',$this->user);
	}
	//贺卡类型
	function card_type(){
		$map['token'] = get_token();
		$cate_data = M('WishCardContentCate') -> where($map) -> select();
		$this -> assign('cate_data',$cate_data);
		//dump($this->user);
		$this -> display();
	}
	//贺卡列表
	function wish_list(){
		$map['content_cate_id'] = $id = I('id');
		$wish_list = M('WishCardContent') -> where($map) -> select();
		$this -> assign('wish_list',$wish_list);
		$this -> display();
	}
	function write_info(){
		$content = I('post.content');
		$this -> assign('content',trim($content));
		$this -> display();
	}
	//选择模板
	function choose_template(){
		if(IS_POST){
			$postData = $_POST;
			session('write_info',$postData);
		}
		$template = $this -> _getTemplateByDir();
		$this -> assign('template',$template);
		$this -> display();
		
	}
	//贺卡
	function card_show(){
		$model = $this -> getModel('wish_card');
		$Model = D ( parse_name ( get_table_name ( $model ['id'] ), 1 ) );
		if(IS_POST){
			//保存贺卡
			
			$write_info = session('write_info');
			$post_data = array_merge($write_info,$_POST);
			$post_data['mid'] = get_mid();
			$post_data['token'] = get_token();
			$post_data['create_time'] = time();
			if($id = $Model-> add($post_data)){
				$data = $post_data;
				$isMake = 1;
			}
		}else{
			$id = I('id');
			$data = $Model -> find($id);
			$Model -> where('id='.$id)->setInc('read_count',1); //统计浏览数加1 
			$isMake = 0;
		}
		if($data){
			$sendUser = get_followinfo($data['mid']);
			$this -> assign('sendUser',$sendUser);
			$this -> assign('data',$data);
			$shareUrl = addons_url('WishCard://Wap/card_show',array('id'=>$id));
			$makeUrl = addons_url('WishCard://Wap/card_type');
			$this -> assign('shareUrl',$shareUrl);
			$this -> assign('makeUrl',$makeUrl);
			$this -> assign('isMake',$isMake);//是否制作的
			//dump($shareUrl);
			$template = ONETHINK_ADDON_PATH . 'WishCard/View/default/Template/' . $data['template_cate'] .'/'.$data['template']. '/index.html';
			$this->assign("assetsPath",SITE_URL . '/Addons/WishCard/View/default/Template/'. $data['template_cate'] .'/'.$data['template']);
			$this -> display($template);
		}else{
			$this -> error("没有该模板或模板保存不成功！");
		}
		
	}
}
