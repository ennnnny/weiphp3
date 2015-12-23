<?php

namespace Addons\Payment\Controller;

use Home\Controller\AddonsController;

class PaymentOrderController extends AddonsController {
	var $model;
	function _initialize() {
		$this->model = $this->getModel ( 'payment_order' );
		parent::_initialize ();
	}
	// 通用插件的列表模型
	public function lists() {
		
		$this->assign ( 'add_button', false );
		$this->assign ( 'del_button', false );
		$this->assign ( 'check_all', false );
		$param['mdm']=$_GET['mdm'];
		$res['title']='订单管理';
		$res['url']=addons_url('Shop://Order/lists',$param);
		$res ['class'] = _CONTROLLER == 'Order' ? 'current' : '';
		$nav[]=$res;
			
		$res['title']='支付记录';
		$res['url']=addons_url('Payment://PaymentOrder/lists',$param);
		$res ['class'] = _CONTROLLER == 'PaymentOrder' ? 'current' : '';
		$nav[]=$res;
		$this->assign('nav',$nav);
		
		$top_more_button [] = array (
				'title' => '导出',
				'url' => U ( 'output', $param ) 
		);
		
		$this->assign ( 'top_more_button', $top_more_button );
		
		$map ['token'] = get_token ();
		$orders=D('Addons://Shop/Order')->where($map)->getFields('order_number,id');
        $follows=M('public_follow')->where($map)->getFields('openid,uid');
		
		$payStatus=I('pay_status');
		if ($payStatus){
		    if ($payStatus==3){
		        $map['status']=0;
		    }else{
		        $map['status']=$payStatus;
		    }
		}
		$payType=I('pay_type');
		if ($payType){
		    $map['paytype']=$payType;
		}
		$isPrice=I('is_price');
		if ($isPrice){
		    $minVal=I('min_value',0,'intval');
		    $maxVal=I('max_value',0,'intval');
		    if($minVal && $maxVal){
		        $minVal<$maxVal && $map['price']=array('between',array($minVal,$maxVal));
		        $minVal>$maxVal && $map['price']=array('between',array($maxVal,$minVal));
		        $minVal==$maxVal && $map['price']=$minVal;
		    }else if (!empty($minVal)){
		        $map['price']=array('egt',$minVal);
		    }else if (!empty($maxVal)){
		        $map['price']=array('elt',$maxVal);
		    }
		}
		$search=$_REQUEST['single_orderid'];
		if ($search) {
		    $this->assign ( 'search', $search );
	        $map ['single_orderid'] = array (
	          'like',
	          '%' . htmlspecialchars ( $search ) . '%'
	        );
		    unset ( $_REQUEST ['single_orderid'] );
		}
		session ( 'common_condition', $map );
		$list_data = $this->_get_model_list ( $this->model );
		$paytypearr=array(
		    'Weixin'=>'微信支付',
		    'Alipaytype'=>'支付宝支付',
		    'Tenpay'=>'财付通WAP支付',
		    'TenpayComputer'=>'财付通支付',
		    'Quickpay'=>'银联支付'
		);
		foreach ( $list_data ['list_data'] as &$vo ) {
		    $vo['wecha_id']=get_userinfo($follows[$vo['wecha_id']],'nickname');
		    
		    $vo['orderName']=urldecode($vo['orderName']);
		    $vo['price']='￥'.wp_money_format($vo['price']);
		    $vo['paytype']=$paytypearr[$vo['paytype']];
		    $param['id']=$orders[$vo['single_orderid']];
		    $vo ['single_orderid'] = '<a href="' . addons_url ( 'Shop://Order/detail' ,$param) . '">' . $vo ['single_orderid'] . '</a>';
		    
		}
		$this->assign ( $list_data );
		
		$templateFile = $this->model ['template_list'] ? $this->model ['template_list'] : '';
		$this->display ( $templateFile );
	}
	// 通用插件的编辑模型
	public function edit() {
		$model = $this->model;
		$id = I ( 'id' );
		
		if (IS_POST) {
			$Model = D ( parse_name ( get_table_name ( $model ['id'] ), 1 ) );
			// 获取模型的字段信息
			$Model = $this->checkAttr ( $Model, $model ['id'] );
			if ($Model->create () && $Model->save ()) {
				D ( 'Common/Keyword' )->set ( $_POST ['keyword'], _ADDONS, $id, $_POST ['keyword_type'], 'custom_reply_news' );
				
				$this->success ( '保存' . $model ['title'] . '成功！', U ( 'lists?model=' . $model ['name'] ) );
			} else {
				$this->error ( $Model->getError () );
			}
		} else {
			$fields = get_model_attribute ( $model ['id'] );
			
			$extra = $this->getCateData ();
			if (! empty ( $extra )) {
				foreach ( $fields as &$vo ) {
					if ($vo ['name'] == 'cate_id') {
						$vo ['extra'] .= "\r\n" . $extra;
					}
				}
			}
			
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
	
	// 通用插件的增加模型
	public function add() {
		$model = $this->model;
		$Model = D ( parse_name ( get_table_name ( $model ['id'] ), 1 ) );
		
		if (IS_POST) {
			// 获取模型的字段信息
			$Model = $this->checkAttr ( $Model, $model ['id'] );
			if ($Model->create () && $id = $Model->add ()) {
				D ( 'Common/Keyword' )->set ( $_POST ['keyword'], _ADDONS, $id, $_POST ['keyword_type'], 'custom_reply_news' );
				
				$this->success ( '添加' . $model ['title'] . '成功！', U ( 'lists?model=' . $model ['name'] ) );
			} else {
				$this->error ( $Model->getError () );
			}
		} else {
			$fields = get_model_attribute ( $model ['id'] );
			
			$extra = $this->getCateData ();
			if (! empty ( $extra )) {
				foreach ( $fields as &$vo ) {
					if ($vo ['name'] == 'cate_id') {
						$vo ['extra'] .= "\r\n" . $extra;
					}
				}
			}
			
			$this->assign ( 'fields', $fields );
			$this->meta_title = '新增' . $model ['title'];
			
			$this->display ();
		}
	}
	
	// 通用插件的删除模型
	public function del() {
		parent::common_del ( $this->model );
	}
	
	function output() {
	    $model = $this->model;
	    
	    $map ['token'] = get_token ();
	    $orders=D('Addons://Shop/Order')->where($map)->getFields('order_number,id');
	    $follows=M('public_follow')->where($map)->getFields('openid,uid');
	    
	    $payStatus=I('get.pay_status');
	    if ($payStatus){
	        if ($payStatus==3){
	            $map['status']=0;
	        }else{
	            $map['status']=$payStatus;
	        }
	    }
	    $payType=I('get.pay_type');
	    if ($payType){
	        $map['paytype']=$payType;
	    }
	    $isPrice=I('get.is_price');
	    if ($isPrice){
	        $minVal=I('get.min_value',0,'intval');
	        $maxVal=I('get.max_value',0,'intval');
	        if($minVal && $maxVal){
	            $minVal<$maxVal && $map['price']=array('between',array($minVal,$maxVal));
	            $minVal>$maxVal && $map['price']=array('between',array($maxVal,$minVal));
	            $minVal==$maxVal && $map['price']=$minVal;
	        }else if (!empty($minVal)){
	            $map['price']=array('egt',$minVal);
	        }else if (!empty($maxVal)){
	            $map['price']=array('elt',$maxVal);
	        }
	    }
	    $search=$_REQUEST['single_orderid'];
	    if ($search) {
	        $this->assign ( 'search', $search );
	        $map ['single_orderid'] = array (
	            'like',
	            '%' . htmlspecialchars ( $search ) . '%'
	        );
	        unset ( $_REQUEST ['single_orderid'] );
	    }
	    session ( 'common_condition', $map );
	    $list_data = $this->_get_model_list ( $this->model );
	    foreach ($list_data['list_grids'] as $v){
	        $titleArr[]=$v['title'];
	    }
	    $dataArr[]=$titleArr;
	    $paytypearr=array(
	        'Weixin'=>'微信支付',
	        'Alipaytype'=>'支付宝支付',
	        'Tenpay'=>'财付通WAP支付',
	        'TenpayComputer'=>'财付通支付',
	        'Quickpay'=>'银联支付'
	    );
	    foreach ( $list_data ['list_data'] as &$vo ) {
	        $vo['wecha_id']=get_userinfo($follows[$vo['wecha_id']],'nickname');
	        $vo['orderName']=urldecode($vo['orderName']);
	        $vo['price']='￥'.wp_money_format($vo['price']);
	        $vo['status']=$vo['status']==0?'未支付':'已支付';
	        $vo['paytype']=$paytypearr[$vo['paytype']];
	        $param['id']=$orders[$vo['single_orderid']];
	        $vo ['single_orderid'] =  $vo ['single_orderid'];
	        unset($vo['id']);
	        $dataArr[]=$vo;
	    }
	    outExcel ( $dataArr, $map ['module'] );
	}
}