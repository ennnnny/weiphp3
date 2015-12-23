<?php

namespace Addons\WishCard\Controller;
use Home\Controller\AddonsController;

class WishCardContentController extends BaseController{
	var $model;
	function _initialize() {
		$this->model = $this->getModel ( 'wish_card_content' );
		
		$res['url'] = addons_url ( 'WishCard://WishCardContent/lists' );
		$res['title'] = "祝福语"; 
		$res['class'] = "cur";
		$sub_nav[] = $res;
		$res['url'] = addons_url ( 'WishCard://WishCardContentCate/lists' );;
		$res['title'] = "类别"; 
		$res['class'] = "";
		$sub_nav[] = $res;
		$this -> assign('sub_nav',$sub_nav);
		
		parent::_initialize ();
	}
	// 通用插件的列表模型
	function lists() {
		$map ['token'] = get_token ();
		session ( 'common_condition', $map );
	
		$list_data = $this->_get_model_list ( $this->model );
		$this->assign ( $list_data );
		$templateFile = $this->model ['template_list'] ? $this->model ['template_list'] : '';
		$this->display ( $templateFile );
	}
	function add() {
		$map ['token'] = get_token ();
		$model = $this->getModel('wish_card_content');
		
		if (IS_POST) {
			$Model = D ( parse_name ( get_table_name ( $model ['id'] ), 1 ) );
			//读分类名称
			$cate_data['id'] = $_POST['content_cate_id'];
			$_POST['content_cate'] = M('WishCardContentCate')->where($cate_data)->getField('content_cate_name');
			// 获取模型的字段信息
			$Model = $this->checkAttr ( $Model, $model ['id'] );
			if ($Model->create () && $id = $Model->add ()) {
				$this->_saveKeyword ( $model, $id );
			}
			$this->success ( '保存' . $model ['title'] . '成功！', U ( 'lists?model=' . $model ['name'] ) );
		} else {
			$cate = D('WishCardContentCate')->where($map)->select();
			$this -> assign('content_cate',$cate);
			$this->display (T ( 'Addons://WishCard@WishCard/addWishContent' ));
		}
	}
	function edit() {
		$cateMap ['token'] = $map['token'] = get_token ();
		$map ['id'] = $id = I ('id');
		$model = $this->getModel('wish_card_content');
		if (IS_POST) {
			$Model = D ( parse_name ( get_table_name ( $model ['id'] ), 1 ) );
			//读分类名称
			$cate_data['id'] = $_POST['content_cate_id'];
			$_POST['content_cate'] = M('WishCardContentCate')->where($cate_data)->getField('content_cate_name');
			// 获取模型的字段信息
			$Model = $this->checkAttr ( $Model, $model ['id'] );
			if ($Model->create () && $Model->save ()) {
				$this->_saveKeyword ( $model, $id );
			}
			// 清空缓存
			method_exists ( $Model, 'clear' ) && $Model->clear ( $id, 'edit' );
			$this->success ( '保存' . $model ['title'] . '成功！', U ( 'lists?model=' . $model ['name'] ) );
		} else {
			$cate = D('WishCardContentCate')->where($cateMap)->select();
			$data = D('WishCardContent')->find($id);
			for($i=0;$i<count($cate);$i++){
				if($cate[$i]['id']==$data['content_cate_id']){
					$cate[$i]['checked'] = true;
				}
			}
			
			$this -> assign('content_cate',$cate);
			$this -> assign('data',$data);
			//dump($cate);
			$this->display (T ( 'Addons://WishCard@WishCard/editWishContent' ));
		}
	}
	//获取祝福语列表
	function content_list() {
		$type = I('type');
		$cateId = I('cateId')==""?0:I('cateId');
		$map ['token'] = get_token ();
		$cate_data = M('WishCardContentCate') -> where($map) -> select();
		if($cateId == 0){
			$content_data = M('WishCardContent') -> where($map)-> select();
		}else{
			$map['content_cate_id'] = $cateId;
			$content_data = M('WishCardContent') -> where($map)-> select();
		}
		if($type == 'ajax'){
			$data['cate'] = $cate_data==null?array():$cate_data;
			$data['content'] = $content_data ==null?array():$content_data;
			$this -> AjaxReturn($data,'JSON');
			exit;
		}else{
			
		}
	}
}
