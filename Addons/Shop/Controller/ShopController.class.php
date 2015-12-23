<?php

namespace Addons\Shop\Controller;

use Addons\Shop\Controller\BaseController;

class ShopController extends BaseController {
	var $model;
	function _initialize() {
		$this->model = $this->getModel ( 'shop' );
		parent::_initialize ();
	}
	function lists() {
		redirect ( U ( 'summary' ) );
	}
	function edit() {
		$id = $this->shop_id;
		$model = $this->getModel ();
		if (IS_POST) {
			// $_POST ['update_time'] = NOW_TIME;
			$Model = D ( parse_name ( get_table_name ( $model ['id'] ), 1 ) );
			if ($Model->create () && $Model->save ()) {
				$this->_saveKeyword ( $model, $id );
			}
			// 清空缓存
			method_exists ( $Model, 'clear' ) && $Model->clear ( $id, 'edit' );
			
			$this->success ( '保存' . $model ['title'] . '成功！' );
		} else {
			$fields = get_model_attribute ( $model ['id'] );
			
			// 获取数据
			$data = D ( 'Shop' )->getInfo ( $id, true );
			$data || $this->error ( '数据不存在！' );
			
			$token = get_token ();
			if (isset ( $data ['token'] ) && $token != $data ['token'] && defined ( 'ADDON_PUBLIC_PATH' )) {
				$this->error ( '非法访问！' );
			}
			
			$this->assign ( 'fields', $fields );
			$this->assign ( 'data', $data );
			
			$this->display ();
		}
	}
	function summary() {
	    $normal_tips = '若出现“redirect_uri 参数错误”,请检查微信公众平台里的“网页授权获取用户基本信息”是否配置好“授权回调页面域名”';
	    $this->assign ( 'normal_tips', $normal_tips );
	    
		$info = D ( 'Shop' )->getInfo ( $this->shop_id );
		$this->assign ( 'info', $info );
		
		$map ['shop_id'] = $this->shop_id;
		$count = M ( 'shop_goods' )->where ( $map )->field ( 'sum(is_show) as sale_count, count(1) as total_count' )->find ();
		$this->assign ( 'count', $count );
		// dump ( $count );
		
		$order = M ( 'shop_order' )->where ( $map )->field ( 'sum(is_new) as new_count, count(1) as total_count' )->find ();
		$this->assign ( 'order', $order );
		// dump ( $order );
		
		$publicid = get_token_appinfo ( '', 'id' );
		$time = NOW_TIME - 86400;
		$px = C ( 'DB_PREFIX' );
		$sql = "SELECT count(1) as cc,FROM_UNIXTIME(cTime, '%H') as hh FROM `{$px}visit_log` WHERE module_name='Shop' and publicid='{$publicid}' AND cTime>'$time' GROUP BY hh";
		$list = M ()->query ( $sql );
		foreach ( $list as $vo ) {
			$log_data [$vo ['hh']] = $vo ['cc'];
		}
		
		$sql = "SELECT count(1) as cc,FROM_UNIXTIME(cTime, '%H') as hh FROM `{$px}shop_order` WHERE shop_id='{$this->shop_id}' AND cTime>'$time' GROUP BY hh";
		$list = M ()->query ( $sql );
		foreach ( $list as $vo ) {
			$order_data [$vo ['hh']] = $vo ['cc'];
		}
		for($i = 23; $i >= 0; $i --) {
			$hh = date ( 'H', NOW_TIME - $i * 3600 );
			$highcharts ['xAxis'] [] = $hh;
			$highcharts ['series'] [] = intval ( $log_data [$hh] );
			$highcharts ['series2'] [] = intval ( $order_data [$hh] );
		}
		
		$highcharts ['xAxis'] = implode ( ',', $highcharts ['xAxis'] );
		$highcharts ['series'] = implode ( ',', $highcharts ['series'] );
		$highcharts ['series2'] = implode ( ',', $highcharts ['series2'] );
		$this->assign ( 'highcharts', $highcharts );
		
		$this->display ();
	}
	function preview() {
		$previewUrl = addons_url ( 'Shop://Wap/index', array (
				'shop_id' => $this->shop_id,
				'publicid' => get_token_appinfo ( '', 'id' ) 
		) );
		$this->assign ( 'url', $previewUrl );
		$this->display ( SITE_PATH . '/Application/Home/View/default/Addons/preview.html' );
	}
}
