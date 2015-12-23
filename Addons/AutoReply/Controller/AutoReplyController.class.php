<?php

namespace Addons\AutoReply\Controller;

use Home\Controller\AddonsController;

class AutoReplyController extends AddonsController {
	function _initialize() {
		$act = strtolower ( _ACTION );
		$type = I ( 'type' );
		
		$res ['title'] = '文本消息';
		$res ['url'] = addons_url ( 'AutoReply://AutoReply/lists' );
		$res ['class'] = $act == 'lists' || $type == 'text' ? 'current' : '';
		$nav [] = $res;
		
		$res ['title'] = '图文消息';
		$res ['url'] = addons_url ( 'AutoReply://AutoReply/news' );
		$res ['class'] = $act == 'news' || $type == 'news' ? 'current' : '';
		$nav [] = $res;
		
		$res ['title'] = '图片消息';
		$res ['url'] = addons_url ( 'AutoReply://AutoReply/image' );
		$res ['class'] = $act == 'image' || $type == 'image' ? 'current' : '';
		$nav [] = $res;
		
		$this->assign ( 'nav', $nav );
	}
	function lists() {
		$this->assign ( 'normal_tips', '请不设置相同的关键词，相同的关键词只回复最新的设置' );
		$list_data = $this->_get_data ( 'text' );
		unset ( $list_data ['list_grids'] ['group_id'], $list_data ['list_grids'] ['image_id'] );
		
		$this->assign ( $list_data );
		// dump($list_data);
		
		$this->display ();
	}
	function news() {
		$list_data = $this->_get_data ( 'news' );
		$this->assign ( 'normal_tips', '请不设置相同的关键词，相同的关键词只回复最新的设置' );
		unset ( $list_data ['list_grids'] ['content'], $list_data ['list_grids'] ['image_id'] );
		
		foreach ( $list_data ['list_data'] as &$d ) {
			$map2 ['group_id'] = $d ['group_id'];
			$titles = M ( 'material_news' )->where ( $map2 )->getFields ( 'title' );
			$d ['group_id'] = implode ( '<br/>', $titles );
		}
		
		$this->assign ( $list_data );
		// dump ( $list_data );
		
		$this->display ('lists');
	}
	function image() {
		$list_data = $this->_get_data ( 'image' );
		$this->assign ( 'normal_tips', '请不设置相同的关键词，相同的关键词只回复最新的设置' );
		unset ( $list_data ['list_grids'] ['group_id'], $list_data ['list_grids'] ['content'] );
		
		foreach ( $list_data ['list_data'] as &$d ) {
            if ($d['image_id']){
                $d['image_id']=url_img_html(get_cover_url($d['image_id']));
            }else if($d['image_material']){
    			$map2 ['id'] = $d ['image_material'];
    			$url = M ( 'material_image' )->where ( $map2 )->getField ( 'cover_url' );
    			$d['image_id'] = url_img_html ( $url );
            }
		}
		
		$this->assign ( $list_data );
		//dump($list_data);
		
		$this->display ('lists');
	}
	function _get_data($type) {
		$model = $this->getModel ( 'AutoReply' );
		
		$page = I ( 'p', 1, 'intval' ); // 默认显示第一页数据
		                                
		// 解析列表规则
		$list_data = $this->_list_grid ( $model );
		
		// 搜索条件
		$map = $this->_search_map ( $model, $fields );
		$map ['msg_type'] = $type;
		
		$row = empty ( $model ['list_row'] ) ? 20 : $model ['list_row'];
		
		// 读取模型数据列表
		$name = parse_name ( get_table_name ( $model ['id'] ), true );
		$data = M ( $name )->field ( true )->where ( $map )->order ( $order )->page ( $page, $row )->select ();
		
		/* 查询记录总数 */
		$count = M ( $name )->where ( $map )->count ();
		
		$list_data ['list_data'] = $data;
		
		// 分页
		if ($count > $row) {
			$page = new \Think\Page ( $count, $row );
			$page->setConfig ( 'theme', '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%' );
			$list_data ['_page'] = $page->show ();
		}
		
		$this->assign ( 'add_url', U ( 'add?type=' . $type ) );
		
		return $list_data;
	}
	// 通用插件的编辑模型
	public function edit() {
		$model = $this->getModel ( 'AutoReply' );
		$id = I ( 'id' );
		
		// 获取数据
		$data = M ( get_table_name ( $model ['id'] ) )->find ( $id );
		$data || $this->error ( '数据不存在！' );
		
		$token = get_token ();
		if (isset ( $data ['token'] ) && $token != $data ['token'] && defined ( 'ADDON_PUBLIC_PATH' )) {
			$this->error ( '非法访问！' );
		}
		
		if (IS_POST) {
			$Model = D ( parse_name ( get_table_name ( $model ['id'] ), 1 ) );
			// 获取模型的字段信息
			$Model = $this->checkAttr ( $Model, $model ['id'] );
			if ($Model->create () && $Model->save ()) {
				$this->_saveKeyword ( $model, $id );
				
				$url = U ( 'lists' );
				if ($data ['msg_type'] == 'news') {
					$url = U ( 'news' );
				} elseif ($data ['msg_type'] == 'image') {
					$url = U ( 'image' );
				}
				$this->success ( '保存' . $model ['title'] . '成功！', $url );
			} else {
				$this->error ( $Model->getError () );
			}
		} else {
			$fields = get_model_attribute ( $model ['id'] );
			$fields = $this->_deal_fields ( $fields, $data ['msg_type'] );
			$this->assign ( 'fields', $fields );
			if($data['image_material']){
			    $map2 ['id'] = $data ['image_material'];
			    $url = M ( 'material_image' )->where ( $map2 )->getField ( 'cover_url' );
			    $data ['cover_url'] =  $url ;
			}
			$this->assign ( 'data', $data );
// 			dump($fields);
			$this->display ();
		}
	}
	
	// 通用插件的增加模型
	public function add() {
		$type = I ( 'get.type', 'text' );
		$model = $this->getModel ( 'AutoReply' );
		if (IS_POST) {
			$_POST ['msg_type'] = $type;
			$Model = D ( parse_name ( get_table_name ( $model ['id'] ), 1 ) );
			// 获取模型的字段信息
			$Model = $this->checkAttr ( $Model, $model ['id'] );
			if ($Model->create () && $id = $Model->add ()) {
				$this->_saveKeyword ( $model, $id );
				$url = U ( 'lists' );
				if ($type == 'news') {
					$url = U ( 'news' );
				} elseif ($type == 'image') {
					$url = U ( 'image' );
				}
				$this->success ( '添加' . $model ['title'] . '成功！', $url );
			} else {
				$this->error ( $Model->getError () );
			}
		} else {
			$fields = get_model_attribute ( $model ['id'] );
			$fields = $this->_deal_fields ( $fields, $type );
			$this->assign ( 'fields', $fields );
			$postUrl=U('add',array('model'=>$model['id'],'type'=>$type));
			$this->assign('post_url',$postUrl);
			$this->display ('edit');
		}
	}
	function _deal_fields($fields, $type) {
		// dump ( $type );
		switch ($type) {
			case 'news' :
				unset ( $fields ['content'], $fields ['image_id'] );
				break;
			case 'image' :
				unset ( $fields ['group_id'], $fields ['content'] );
				break;
			
			default :
				unset ( $fields ['group_id'], $fields ['image_id'] );
		}
		// dump ( $fields );
		return $fields;
	}
}
