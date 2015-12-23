<?php

namespace Addons\Coupon\Controller;

use Home\Controller\AddonsController;

class ShopController extends AddonsController {
	var $table = 'coupon_shop';
	function _initialize() {
		parent::_initialize ();
		
		$res ['title'] = '优惠券';
		$res ['url'] = addons_url ( 'Coupon://Coupon/lists' );
		$nav [] = $res;
		
		$res ['title'] = '门店管理';
		$res ['url'] = addons_url ( 'Coupon://Shop/lists', array (
				'coupon_id' => I ( 'coupon_id' ) 
		) );
		$res ['class'] = 'current';
		$nav [] = $res;
		
		$this->assign ( 'nav', $nav );
	}
	function lists() {
		$isAjax = I ( 'isAjax' );
		// $coupon_id = I ( 'coupon_id','' );
		// dump($coupon_id);
		$search = $_REQUEST ['name'];
		
		$top_more_button [] = array (
				'title' => '导入数据',
				'url' => U ( 'import' ,array('mdm'=>I('mdm'))) 
		);
		$top_more_button [] = array (
				'title' => '导出数据',
				'url' => U ( 'output', array (
						'name' => $search 
				) ) 
		);
		
		$this->assign ( 'top_more_button', $top_more_button );
		
		$model = $this->getModel ( $this->table );
		$page = I ( 'p', 1, 'intval' ); // 默认显示第一页数据
		                                
		// 解析列表规则
		$list_data = $this->_list_grid ( $model );
		$fields = $list_data ['fields'];
		
		// 搜索条件
		// $map ['coupon_id'] = $coupon_id;
		
		$map = $this->_search_map ( $model, $fields );
		$map ['manager_id'] = $this->mid;
		$row = empty ( $model ['list_row'] ) ? 20 : $model ['list_row'];
		
		empty ( $fields ) || in_array ( 'id', $fields ) || array_push ( $fields, 'id' );
		$name = parse_name ( get_table_name ( $model ['id'] ), true );
		$data = M ( $name )->field ( empty ( $fields ) ? true : $fields )->where ( $map )->order ( 'id DESC' )->page ( $page, $row )->select ();
		
		/* 查询记录总数 */
		$count = M ( $name )->where ( $map )->count ();
		$list_data ['list_data'] = $data;
		
		// 分页
		if ($count > $row) {
			$page = new \Think\Page ( $count, $row );
			$page->setConfig ( 'theme', '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%' );
			$list_data ['_page'] = $page->show ();
		}
		if ($isAjax) {
			unset ( $list_data ['list_grids'] ['phone'] );
			unset ( $list_data ['list_grids'] ['ids'] );
			
			$this->assign ( $list_data );
			$this->display ( 'lists_data' );
		} else {
			$this->assign ( $list_data );
			$this->display ();
		}
	}
	function list_data() {
		$page = I ( 'p', 1, 'intval' );
		$map['token']=get_token();
		$list_data = M ( 'coupon_shop' )->where($map)->order ( 'id DESC' )->page ( $page, 20 )->selectPage ( 20 );
		// dump ( $list_data );
		
		echo JSON ( $list_data );
	}
	function output() {
		$model = $this->getModel ( $this->table );
		$map ['manager_id'] = $this->mid;
		session ( 'common_condition', $map );
		// 搜索条件
		// $map ['coupon_id'] = I ( 'coupon_id' );
		// $search= I ( 'search' );
		// dump($search);
		// if ($search){
		// $this->assign('search',$search);
		// $map ['name'] = array (
		// 'like',
		// '%' . htmlspecialchars ( $search ) . '%'
		// );
		// session ( 'common_condition', $map );
		// }
		
		parent::common_export ( $model );
	}
	function import() {
		$model = $this->getModel ( 'import' );
		if (IS_POST) {
			$column = array (
					'A' => 'name',
					'B' => 'phone',
					'C' => 'address' 
			);
			
			$attach_id = I ( 'attach', 0 );
			
			$res = importFormExcel ( $attach_id, $column );
			if ($res ['status'] == 0) {
				$this->error ( $res ['data'] );
			}
			$total = count ( $res ['data'] );
			foreach ( $res ['data'] as $vo ) {
				if (empty ( $vo ['name'] )) {
					$this->error ( '店名不能为空' );
				}
				if (empty ( $vo ['address'] )) {
					$this->error ( '详细地址不能为空' );
				}
				$vo ['token'] = get_token ();
				$vo ['manager_id'] = $this->mid;
				$r = M ( 'coupon_shop' )->add ( $vo );
			}
			$msg = "共导入" . $total . "条记录";
			// dump($arr);
			// $msg = trim ( $msg, ', ' );
			// dump($msg);exit;
			
			$this->success ( $msg, U ( 'lists' ).'&mdm='.I('get.mdm') );
		} else {
			$fields = get_model_attribute ( $model ['id'] );
			$this->assign ( 'fields', $fields );
			
			$this->assign ( 'post_url', U ( 'import' ).'&mdm='.I('mdm')  );
			$this->assign ( 'import_template', 'coupon_shop.xls' );
			
			$this->display ( T ( 'Addons/import' ) );
		}
	}
	function del() {
		$model = $this->getModel ( $this->table );
// 		D('Addons://Coupon/Coupon')->getCouponShop(true);
		parent::del ( $model );
	}
	function add() {
		$model = $this->getModel ( $this->table );
		
		parent::common_add ( $model );
	}
	public function edit() {
		$model = $this->getModel ( $this->table );
		$this->assign ( 'post_url', U ( 'edit' ) );
		
		parent::common_edit ( $model, 0, 'add' );
	}

	function sence_qr(){
		$id = I('get.id',0,intval);
		if($id){
			$res['qrcode'] = D ( 'Home/QrCode' )->add_qr_code ( 'QR_SCENE', 'CouponShop', $id );
			$this->ajaxReturn($res,'JSON');
		}
	}
}
