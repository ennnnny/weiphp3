<?php

namespace Addons\Coupon\Controller;

use Home\Controller\AddonsController;

class CouponController extends AddonsController {
	function _initialize() {
		parent::_initialize ();
		
		$res ['title'] = '优惠券';
		$res ['url'] = addons_url ( 'Coupon://Coupon/lists' );
		$res ['class'] = 'current';
		$nav [] = $res;
		
		$res ['title'] = '门店管理';
		$res ['url'] = addons_url ( 'Coupon://Shop/lists', array (
		'coupon_id' => I ( 'coupon_id' )
		) );
		$res ['class'] = '';
		$nav [] = $res;
		
		$this->assign ( 'nav', $nav );
		
		if (_ACTION == 'package')
			$GLOBALS ['is_wap'] = true;
	}
	function lists() {
	    $isAjax = I ( 'isAjax' );
	    $isRadio = I ( 'isRadio' );
	    
	    
		// 更新延时插入的缓存
		D ( 'Common/SnCode' )->delayAdd ();
		
		$dao = D ( 'Coupon' );
		$page = I ( 'p', 1, 'intval' ); // 默认显示第一页数据
		$order = 'id desc';
		$model = $this->getModel ();
		
		// 解析列表规则
		$list_data = $this->_list_grid ( $model );
		
		// 搜索条件
		$map = $this->_search_map ( $model, $list_data ['fields'] );
		$row = empty ( $model ['list_row'] ) ? 20 : $model ['list_row'];
		$map['is_del']=0;
		// 读取模型数据列表
		$list = $dao->field ( 'id' )->where ( $map )->order ( $order )->page ( $page, $row )->select ();
		foreach ( $list as $d ) {
			$datas [] = $dao->getInfo ( $d ['id'] );
		}
		
		/* 查询记录总数 */
		$count = $dao->where ( $map )->count ();
		$list_data ['list_data'] = $datas;
		
		// 分页
		if ($count > $row) {
			$page = new \Think\Page ( $count, $row );
			$page->setConfig ( 'theme', '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%' );
			$list_data ['_page'] = $page->show ();
		}
		
		if ($isAjax) {
		    $this->assign('isRadio',$isRadio);
		    $this->assign ( $list_data );
		    $this->display ( 'ajax_lists_data' );
		} else {
		    $this->assign ( $list_data );
		    $this->display ();
		}
		
	}
	
	function edit() {
		$id = I ( 'id' );
		$model = $this->getModel ();
		if (IS_POST) {
			
			$this->checkPostData ();
			
			$this->save_shop ( $id, $_POST ['shop_id'] );
			
			// $_POST ['update_time'] = NOW_TIME;
			$Model = D ( parse_name ( get_table_name ( $model ['id'] ), 1 ) );
			// 获取模型的字段信息
			$Model = $this->checkAttr ( $Model, $model ['id'] );
			if ($Model->create () && $Model->save ()) {
				$this->_saveKeyword ( $model, $id );
			}
			// 清空缓存
			method_exists ( $Model, 'clear' ) && $Model->clear ( $id, 'edit' );
			
			$this->success ( '保存' . $model ['title'] . '成功！', U ( 'lists?model=' . $model ['name'] ) );
		} else {
			$fields = get_model_attribute ( $model ['id'] );
			
			// 获取数据
			$data = D ( 'Coupon' )->getInfo ( $id );
			$data || $this->error ( '数据不存在！' );
			
			$token = get_token ();
			if (isset ( $data ['token'] ) && $token != $data ['token'] && defined ( 'ADDON_PUBLIC_PATH' )) {
				$this->error ( '非法访问！' );
			}
			
			$maps ['coupon_id'] = $id;
			$list = M ( 'coupon_shop_link' )->where ( $maps )->select ();
			$shop_ids = getSubByKey ( $list, 'shop_id' );
			if (! empty ( $shop_ids )) {
				$map_shop ['id'] = array (
						'in',
						$shop_ids 
				);
				$shop_list = M ( 'coupon_shop' )->where ( $map_shop )->select ();
				$this->assign ( 'shop_list', $shop_list );
			}
			$data ['member'] = explode ( ',', $data ['member'] );
			$levelData = $this->get_card_level ();
			$this->assign ( 'level', $levelData );
			$this->assign ( 'fields', $fields );
			$this->assign ( 'data', $data );
			$this->meta_title = '编辑' . $model ['title'];
			
			$this->_deal_data ();
			
			$this->display ();
		}
	}
	function add() {
		$model = $this->getModel ();
		if (IS_POST) {
			$this->checkPostData ();
			
			$Model = D ( parse_name ( get_table_name ( $model ['id'] ), 1 ) );
			// 获取模型的字段信息
			$Model = $this->checkAttr ( $Model, $model ['id'] );
			if ($Model->create () && $id = $Model->add ()) {
				$this->_saveKeyword ( $model, $id );
				$this->save_shop ( $id, $_POST ['shop_id'] );
				
				// 清空缓存
				method_exists ( $Model, 'clear' ) && $Model->clear ( $id, 'add' );
				
				$this->success ( '添加' . $model ['title'] . '成功！', U ( 'lists?model=' . $model ['name'] ) );
			} else {
				$this->error ( $Model->getError () );
			}
		} else {
			$fields = get_model_attribute ( $model ['id'] );
			$levelData = $this->get_card_level ();
			$this->assign ( 'level', $levelData );
			
			$this->assign ( 'fields', $fields );
			$this->_deal_data ();
			
			$this->display ();
		}
		
		// $this->display ();
	}
	function save_shop($coupon_id, $shop_ids = array()) {
		$map ['coupon_id'] = $coupon_id;
		M ( 'coupon_shop_link' )->where ( $map )->delete ();
		
		$shop_ids = array_filter ( $shop_ids );
		
		foreach ( $shop_ids as $id ) {
			$map ['shop_id'] = $id;
			
			M ( 'coupon_shop_link' )->add ( $map );
		}
	}
	
	// 增加或者编辑时公共部分
	function _deal_data() {
		return false;
		$normal_tips = '插件场景限制参数说明：格式：[插件名:id],如<br/>
				[投票:10]，表示对ID为10的投票投完对能领取<br/>
				[投票:*]，表示只要投过票就可以领取<br/>
				[微调研:15]，表示完成ID为15的调研就能领取<br/>
				[微考试:10]，表示完成ID为10的考试就能领取<br/>';
		$this->assign ( 'normal_tips', $normal_tips );
	}
	function checkPostData() {
		if (! I ( 'post.title' )) {
			$this->error ( '优惠劵标题不能为空' );
		}
		/*
		 * if (! I ( 'post.shop_name' )) {
		 * $this->error ( '商家名称不能为空' );
		 * }
		 */
		if (I ( 'post.num' ) <= 0) {
			$this->error ( '优惠券数量必须大于0' );
		}
		if (I ( 'post.max_num' ) < 0) {
			$this->error ( '每人最多领取数量不能小于0' );
		}
		
		if (strtotime ( I ( 'post.start_time' ) ) > strtotime ( I ( 'post.end_time' ) )) {
			$this->error ( '领取优惠券开始时间不能大于结束时间' );
		}
		
		if (! I ( 'post.use_start_time' )) {
			$this->error ( '请选择优惠券使用开始时间' );
		} else if (! I ( 'post.over_time' )) {
			$this->error ( '请选择优惠券使用结束时间' );
		} else if (strtotime ( I ( 'post.use_start_time' ) ) > strtotime ( I ( 'post.over_time' ) )) {
			$this->error ( '优惠券使用开始时间不能大于结束时间' );
		}
	}
	function preview() {
		$id = I ( 'id', 0, 'intval' );
		$url = addons_url ( 'Coupon://Wap/index', array (
				'id' => $id 
		) );
		$this->assign ( 'url', $url );
		$this->display ( SITE_PATH . '/Application/Home/View/default/Addons/preview.html' );
	}
	// 获取会员等级
	function get_card_level() {
	    if (M ( 'addons' )->where ( 'name="Card"' )->find ()) {
	        $map ['token'] = get_token ();
	        $data = M ( 'card_level' )->where ( $map )->getFields ( 'id,level' );
	        return $data;
	    }
	}
	function sncode_lists() {
		$id = $hpmap ['id'] = I ( 'id' );
		
		$info = D ( 'Coupon' )->getInfo ( $id );
		
		$list_data ["list_grids"] = array (
				"nickname" => array (
						"field" => "nickname",
						"title" => "用户" 
				),
				"content" => array (
						"field" => "content",
						"title" => " 详细信息" 
				),
				"sn" => array (
						"field" => "sn",
						"title" => " SN码" 
				),
				"admin_uid" => array (
						"field" => "admin_uid",
						"title" => "工作人员" 
				),
				"use_time" => array (
						"field" => "use_time",
						"title" => "核销时间" 
				) 
		);
		
		$px = C ( 'DB_PREFIX' );
		$page = I ( 'p', 1, 'intval' ); // 默认显示第一页数据
		                                
		// 搜索条件
		$where = "is_use=1 AND addon='Coupon' AND target_id=" . $id;
		
		$start_time = I ( 'start_time' );
		if ($start_time) {
			$where .= " AND s.use_time>" . strtotime ( $start_time );
			$this->assign ( 'start_time', $start_time );
		}
		
		$end_time = I ( 'end_time' );
		if ($end_time) {
			$where .= " AND s.use_time<" . strtotime ( $end_time );
			$this->assign ( 'end_time', $start_time );
		}
		
		$search_nickname = I ( 'search_nickname' );
		if (! empty ( $search_nickname )) {
			$where .= " AND s.uid IN(" . D ( 'Common/User' )->searchUser ( $search_nickname ) . ")";
			
			$this->assign ( 'search_nickname', $search_nickname );
		}
		
		// 读取模型数据列表
		$data = D ( 'Common/SnCode' )->field ( true )->where ( $where )->order ( 'use_time DESC' )->page ( $page, 20 )->select ();
		// dump ( $data );
		foreach ( $data as &$vo ) {
			$vo ['nickname'] = get_nickname ( $vo ['uid'] );
			$vo ['use_time'] = time_format ( $vo ['use_time'] );
			$vo ['admin_uid'] = get_nickname ( $vo ['admin_uid'] );
			
			$vo ['content'] = '核销优惠券： ' . $info ['title'];
		}
		
		/* 查询记录总数 */
		$count = D ( 'Common/SnCode' )->where ( $where )->count ();
		$list_data ['list_data'] = $data;
		
		// 分页
		if ($count > $row) {
			$page = new \Think\Page ( $count, $row );
			$page->setConfig ( 'theme', '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%' );
			$list_data ['_page'] = $page->show ();
		}
		
		$this->assign ( $list_data );
		// dump($list_data);
		
		$this->display ();
	}
	function export() {
		set_time_limit ( 0 );
		
		$id = $hpmap ['id'] = I ( 'id' );
		$info = D ( 'Coupon' )->getInfo ( $id );
		
		$dataArr [0] = array (
				0 => "用户",
				1 => " 详细信息",
				2 => " SN码",
				3 => "工作人员",
				4 => "核销时间" 
		);
		
		$px = C ( 'DB_PREFIX' );
		$page = I ( 'p', 1, 'intval' ); // 默认显示第一页数据
		                                
		// 搜索条件
		$where = "is_use=1 AND addon='Coupon' AND target_id=" . $id;
		
		$start_time = I ( 'start_time' );
		if ($start_time) {
			$where .= " AND s.use_time>" . strtotime ( $start_time );
		}
		
		$end_time = I ( 'end_time' );
		if ($end_time) {
			$where .= " AND s.use_time<" . strtotime ( $end_time );
		}
		
		$search_nickname = I ( 'search_nickname' );
		if (! empty ( $search_nickname )) {
			$where .= " AND s.uid IN(" . D ( 'Common/User' )->searchUser ( $search_nickname ) . ")";
		}
		
		// 读取模型数据列表
		$data = D ( 'Common/SnCode' )->field ( true )->where ( $where )->order ( 'use_time DESC' )->limit ( 5000 )->select ();
		// dump ( $data );
		foreach ( $data as $k => $vo ) {
			$vo ['content'] = '核销优惠券： ' . $info ['title'];
			
			$dataArr [$k + 1] = array (
					0 => get_nickname ( $vo ['uid'] ),
					1 => $vo ['content'],
					2 => $vo ['sn'],
					3 => get_nickname ( $vo ['admin_uid'] ),
					4 => time_format ( $vo ['use_time'] ) 
			);
		}
		
		outExcel ( $dataArr, 'Coupon_' . $id );
	}
	function del(){
	    $ids=I('ids');
	    $id=I('id');
	    if ($id){
	        $map['id']=$id;
	    }
	    if ($ids){
	        $map['id']=array('in',$ids);
	    }
	    $save['is_del']=1;
	    $res=M('coupon')->where($map)->save($save);
	    if ($res){
	        $this->success('删除成功');
	    }else {
	        $this->error('删除失败');
	    }
	}
}
