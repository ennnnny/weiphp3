<?php

namespace Addons\RealPrize\Controller;

use Home\Controller\AddonsController;

class RealPrizeController extends AddonsController {
	var $r_prize = 'real_prize';
	var $p_address = 'prize_address';
	function edit() {
		$id = I ( 'id' );
		$model = $this->getModel ();
		
		if (IS_POST) {
			
			$this->checkPostData ();
			$Model = D ( parse_name ( get_table_name ( $model ['id'] ), 1 ) );
			// 获取模型的字段信息
			$Model = $this->checkAttr ( $Model, $model ['id'] );
			if ($Model->create () && $Model->save ()) {
				$this->_saveKeyword ( $model, $id );
				// 清空缓存
				method_exists ( $Model, 'clear' ) && $Model->clear ( $id, 'edit' );
				D ( 'RealPrize' )->getInfo ( $id, true );
				$this->success ( '保存' . $model ['title'] . '成功！', U ( 'lists?model=' . $model ['name'] ) );
			} else {
				$this->error ( $Model->getError () );
			}
		} else {
			$fields = get_model_attribute ( $model ['id'] );
			
			// 获取数据
			$data = M ( get_table_name ( $model ['id'] ) )->find ( $id );
			$data || $this->error ( '数据不存在！' );
			
			$token = get_token ();
			if (isset ( $data ['token'] ) && $token != $data ['token'] && defined ( 'ADDON_PUBLIC_PATH' )) {
				$this->error ( '非法访问！' );
			}
			
			$this->assign ( 'fields', $fields );
			$this->assign ( 'data', $data );
			$this->meta_title = '编辑' . $model ['title'];
			
			$this->display ();
		}
	}
	function checkPostData() {
		if (! I ( 'post.prize_title' )) {
			$this->error ( '活动名称不能为空' );
		}
		if (! I ( 'post.prize_name' )) {
			$this->error ( '奖品名称不能为空' );
		}
		if (! I ( 'post.prize_conditions' )) {
			$this->error ( '活动说明不能为空' );
		}
		if (intval ( I ( 'post.prize_count' ) ) <= 0) {
			$this->error ( '奖品个数应大于0' );
		}
		if (! I ( 'post.prize_image' )) {
			$this->error ( '请选择奖品图片' );
		}
		if (! I ( 'post.use_content' )) {
			$this->error ( '使用说明不能为空' );
		}
		if (! I ( 'post.fail_content' )) {
			$this->error ( '领取提示不能为空' );
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
				
				// 清空缓存
				method_exists ( $Model, 'clear' ) && $Model->clear ( $id, 'edit' );
				D ( 'RealPrize' )->getInfo ( $id, true );
				$this->success ( '添加' . $model ['title'] . '成功！', U ( 'lists?model=' . $model ['name'] ) );
			} else {
				$this->error ( $Model->getError () );
			}
		} else {
			$fields = get_model_attribute ( $model ['id'] );
			
			$this->assign ( 'fields', $fields );
			$this->meta_title = '新增' . $model ['title'];
			
			$this->display ();
		}
	}
	function index() {
		$id = I ( 'id' );
		$param ['prizeid'] = $id;
		$info = get_token_appinfo ();
		$param ['publicid'] = $info ['id'];
		$data = D ( 'RealPrize' )->getInfo ( $id );
		$this->assign ( 'data', $data );
		// 设置奖品页面领取对应的跳转链接
		$prizetype = $data ['prize_type'];
		if ($prizetype == '0') {
			$url = addons_url ( "RealPrize://RealPrize/save_address", $param );
		} else {
			$url = addons_url ( "RealPrize://RealPrize/address", $param );
		}
		$this->assign ( 'jumpurl', $url );
		
		// 获取奖品类型名称，方便显示
		$tname = $prizetype == '0' ? '虚拟物品' : '实体物品';
		$this->assign ( 'tname', $tname );
		// 服务号信息
		$service_info = get_token_appinfo ();
		$this->assign ( 'service_info', $service_info );
		$this -> display();
	}
	function preview(){
		$id = I ( 'id', 0, 'intval' );
		$url = U('index',array('id'=>$id));
		$this -> assign('url',$url);
		$this->display ( SITE_PATH . '/Application/Home/View/default/Addons/preview.html' );
	}
	function address($prizeid) {
		$data = D ( 'Addons://RealPrize/RealPrize' )->getInfo ( $prizeid );
		if ($data ['prize_count'] > 0) {
			if (IS_POST) {
				$this->save_address ( $prizeid );
			} else {
				$this->assign ( 'prizeid', $prizeid );
				$url = addons_url ( "RealPrize://RealPrize/address?prizeid=$prizeid" );
				$this->assign ( 'url', $url );
				$this->display ( 'address' );
			}
		} else {
			$res ['result'] = "fail";
			$res ['msg'] = "抱歉手太慢，奖品被领取完了";
			$this->assign ( "res", $res );
			$this->display ( 'result' );
		}
	}
	// 增加收货地址
	function save_address($prizeid) {
		$uid = get_mid ();
		$data = D ( 'Addons://RealPrize/RealPrize' )->getInfo ( $prizeid );
		// $num = M ( 'prize_address' )->where ( "uid = $uid and prizeid = $prizeid" )->find ();
		$num = D ( 'PrizeAddress' )->getAddressInfo ( $uid, $prizeid );
		$this->assign ( "data", $data );
		// 判断是否领取
		if (! empty ( $num )) {
			$res ['result'] = "fail";
			$res ['msg'] = "您已经领取该奖品了,请不要重复领取";
			$this->assign ( "res", $res );
			$this->display ( 'result' );
			exit ();
		} else {
			$data = D ( 'Addons://RealPrize/RealPrize' )->getInfo ( $prizeid );
			if ($data ['prize_count'] > 0) {
				$model = $this->getModel ( 'prize_address' );
				// 实体奖品保存收货地址
				if (IS_POST) {
					$Model = D ( parse_name ( get_table_name ( $model ['id'] ), 1 ) ); // dump($model);die();
					                                                                   // 获取模型的字段信息
					$Model = $this->checkAttr ( $Model, $model ['id'] );
					if ($Model->create () && $id = $Model->add ()) {
						// 清空缓存
						method_exists ( $Model, 'clear' ) && $Model->clear ( $id, 'add' );
						D ( 'PrizeAddress' )->getAddressInfo ( $uid, $prizeid, true );
						// 减1
						// M ( 'prize_address' )->where ( "prizeid = $prizeid" )->setDec ( 'prize_count' );
						D ( 'RealPrize' )->updatePrizeCount ( $prizeid );
						// 结果
						$res ['result'] = "success";
						$res ['msg'] = "恭喜你，领取成功！";
						$this->assign ( "res", $res );
						$this->assign ( 'address', $_POST );
						$this->display ( 'result' );
						exit ();
					}
				} else {
					// 虚拟奖品保存uid
					// $data ['address'] = '';
					// $data ['city'] = '';
					// $data ['mobile'] = '';
					// $data ['uid'] = $uid;
					// $data ['remark'] = '';
					// $data ['prizeid'] = $prizeid;
					// $result = M ( 'prize_address' )->add ( $data );
					// D('PrizeAddress')->getAddressInfo($uid,$prizeid,true);
					// 减1
					// M ( 'prize_address' )->where ( "prizeid = $prizeid" )->setDec ( 'prize_count' );
					D ( 'RealPrize' )->updatePrizeCount ( $prizeid );
					// 结果
					$res ['result'] = "success";
					$res ['msg'] = "恭喜你，领取成功！";
					$this->assign ( "res", $res );
					$this->display ( 'result' );
					exit ();
				}
			} else {
				$res ['result'] = "fail";
				$res ['msg'] = "抱歉手太慢，奖品被领取完了";
				$this->assign ( "res", $res );
				$this->display ( 'result' );
				exit ();
			}
		}
		// $this->display ();
	}
	// 显示实物奖品对应的收货地址
	function address_lists() {
		$nav [0] ['title'] = "实物奖励";
		$nav [0] ['class'] = "";
		$nav [0] ['url'] = U ( "lists" );
		$nav [1] ['title'] = "收货地址";
		$nav [1] ['class'] = "current";
		$this->assign ( 'nav', $nav );
		$model = $this->getModel ( 'prize_address' );
		$page = I ( 'p', 1, 'intval' ); // 默认显示第一页数据
		$this->assign('add_button',false);
		// 解析列表规则
		$list_data = $this->_list_grid ( $model );
		
		// unset ( $list_data ['list_grids'] [2] );
		
		$grids = $list_data ['list_grids'];
		$fields = $list_data ['fields'];
		
		// 搜索条件
		// $map ['addon'] = $this->addon;
		$param['target_id']=$map ['prizeid'] = I ( 'target_id' );
		$map ['token'] = get_token ();
		session ( 'common_condition', $map );

		$search_url=U('address_lists',$param);
		$this->assign('search_url',$search_url);
		
		$map = $this->_search_map ( $model, $fields );
		
		$row = empty ( $model ['list_row'] ) ? 20 : $model ['list_row'];
		
		empty ( $fields ) || in_array ( 'id', $fields ) || array_push ( $fields, 'id' );
		
		$name = parse_name ( get_table_name ( $model ['id'] ), true );
		$data = M ( $name )->field ( empty ( $fields ) ? true : $fields )->where ( $map )->order ( 'id DESC' )->page ( $page, $row )->select ();
		
		// 获取prizeid对应的奖品名称
		$map2 [id] = I ( 'target_id' );
		$pname = M ( 'real_prize' )->where ( $map2 )->getField ( 'prize_name' );
		foreach ( $data as &$v ) {
			$v ['prizeid'] = $pname;
		}
		
		/* 查询记录总数 */
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
	
	function address_edit() {
	    $id = I ( 'id' );
	    $model = $this->getModel ('prize_address');
	    if (IS_POST) {
	        $Model = D ( parse_name ( get_table_name ( $model ['id'] ), 1 ) );
	        // 获取模型的字段信息
	        $Model = $this->checkAttr ( $Model, $model ['id'] );
	        if ($Model->create () && $Model->save ()) {
	            $this->_saveKeyword ( $model, $id );
	            // 清空缓存
	            method_exists ( $Model, 'clear' ) && $Model->clear ( $id, 'edit' );
	            $this->success ( '保存' . $model ['title'] . '成功！', U ( 'address_lists?model=' . $model ['name'].'&target_id='.$_POST['prizeid'] ) );
	        } else {
	            $this->error ( $Model->getError () );
	        }
	    } else {
	        $fields = get_model_attribute ( $model ['id'] );
	        // 获取数据
	        $data = M ( get_table_name ( $model ['id'] ) )->find ( $id );
	        $data || $this->error ( '数据不存在！' );
	        	
	        $token = get_token ();
	        if (isset ( $data ['token'] ) && $token != $data ['token'] && defined ( 'ADDON_PUBLIC_PATH' )) {
	            $this->error ( '非法访问！' );
	        }
	        $param['mdm']=$_GET['mdm'];
	        $postUrl=U('address_edit',$param);
	        $this->assign('post_url',$postUrl);
	        
	        $this->assign ( 'fields', $fields );
	        $this->assign ( 'data', $data );
	        $this->meta_title = '编辑' . $model ['title'];
	        $this->display (SITE_PATH . '/Application/Home/View/default/Addons/edit.html');
	    }
	}
}
