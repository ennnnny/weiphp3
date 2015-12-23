<?php

namespace Addons\CustomMenu\Controller;

use Home\Controller\AddonsController;

class CustomMenuController extends AddonsController {
	var $model_name = 'custom_menu';
	public function lists($model = null, $page = 0) {
		is_array ( $model ) || $model = $this->getModel ( $this->model_name );
		
		// 解析列表规则
		$list_data = $this->_list_grid ( $model );
		$fields = $list_data ['fields'];
		
		// 搜索条件
		$map = $this->_search_map ( $model, $fields );
		
		$list_data ['list_data'] = $this->get_data ( $map );
		foreach($list_data['list_data'] as &$v){
			$v['type'] = get_name_by_status ( $v['type'], 'type', $model ['id'] );
		}
		$this->assign ( $list_data );
		//dump($list_data);
		
		$this->display ();
	}
	function get_data($map) {
		$map ['token'] = get_token ();
		$list = M ( 'custom_menu' )->where ( $map )->order ( 'pid asc, sort asc' )->select ();
		
		// 取一级菜单
		foreach ( $list as $k => $vo ) {
			if ($vo ['pid'] != 0)
				continue;
			
			$one_arr [$vo ['id']] = $vo;
			unset ( $list [$k] );
		}
		
		foreach ( $one_arr as $p ) {
			$data [] = $p;
			
			$two_arr = array ();
			foreach ( $list as $key => $l ) {
				if ($l ['pid'] != $p ['id'])
					continue;
				
				//$l ['title'] = '├──' . $l ['title'];
				$two_arr [] = $l;
				unset ( $list [$key] );
			}
			
			$data = array_merge ( $data, $two_arr );
		}
		
		return $data;
	}
	function _deal_data($d) {
		$res ['name'] = str_replace ( '├──', '', $d ['title'] );
		
		if ($d ['type'] == 'view') {
			$search = array (
					'[website]',
					'[publicid]',
					'[token]' 
			);
			$replace = array (
					SITE_URL,
					get_token_appinfo ( $map ['token'], 'id' ),
					$map ['token'] 
			);
			
			$res ['type'] = 'view';
			$res ['url'] = str_replace ( $search, $replace, trim ( $d ['url'] ) );
		} elseif ($d ['type'] != 'none') {
			$res ['type'] = trim ( $d ['type'] );
			$res ['key'] = trim ( $d ['keyword'] );
		}
		
		return $res;
	}
	function json_encode_cn($data) {
		$data = json_encode ( $data );
		return preg_replace ( "/\\\u([0-9a-f]{4})/ie", "iconv('UCS-2BE', 'UTF-8', pack('H*', '$1'));", $data );
	}
	// 发送菜单到微信
	function send_menu() {
		$data = $this->get_data ();
		foreach ( $data as $k => $d ) {
			if ($d ['pid'] != 0)
				continue;
			$tree ['button'] [$d ['id']] = $this->_deal_data ( $d );
			unset ( $data [$k] );
		}
		foreach ( $data as $k => $d ) {
			$tree ['button'] [$d ['pid']] ['sub_button'] [] = $this->_deal_data ( $d );
			unset ( $data [$k] );
		}
		$tree2 = array ();
		$tree2 ['button'] = array ();
		
		foreach ( $tree ['button'] as $k => $d ) {
			$tree2 ['button'] [] = $d;
		}

		$tree = $this->json_encode_cn ( $tree2 );
		$access_token = get_access_token ();
		
		file_get_contents ( 'https://api.weixin.qq.com/cgi-bin/menu/delete?access_token=' . $access_token );
		
		$url = 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token=' . $access_token;
		$header [] = "content-type: application/x-www-form-urlencoded; charset=UTF-8";
		
		$ch = curl_init ();
		curl_setopt ( $ch, CURLOPT_URL, $url );
		curl_setopt ( $ch, CURLOPT_CUSTOMREQUEST, "POST" );
		curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );
		curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, FALSE );
		curl_setopt ( $ch, CURLOPT_HTTPHEADER, $header );
		curl_setopt ( $ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)' );
		curl_setopt ( $ch, CURLOPT_FOLLOWLOCATION, 1 );
		curl_setopt ( $ch, CURLOPT_AUTOREFERER, 1 );
		curl_setopt ( $ch, CURLOPT_POSTFIELDS, $tree );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
		$res = curl_exec ( $ch );
		curl_close ( $ch );
		$res = json_decode ( $res, true );
		if ($res ['errcode'] == 0) {
			$this->success ( '发送菜单成功' );
		} else {
			$this->error ( error_msg ( $res ) );
		}
	}
	public function edit($model = null, $id = 0) {
		is_array ( $model ) || $model = $this->getModel ( $this->model_name );
		$Model = D ( parse_name ( get_table_name ( $model ['id'] ), 1 ) );
		$id || $id = I ( 'id' );
		
		if (IS_POST) {
			$_POST = $this->_deal_post ( $_POST );
			// 获取模型的字段信息
			$Model = $this->checkAttr ( $Model, $model ['id'] );
			if ($Model->create () && $Model->save ()) {
// 			    $this->_saveKeyword ( $model, $id );
				$this->success ( '保存' . $model ['title'] . '成功！', U ( 'lists?model=' . $model ['name'] ) );
			} else {
				$this->error ( $Model->getError () );
			}
		} else {
			// 获取一级菜单
			$map ['token'] = get_token ();
			$map ['pid'] = 0;
			$map ['id'] = array (
					'not in',
					$id 
			);
			$list = $Model->where ( $map )->select ();
			foreach ( $list as $v ) {
				$extra .= $v ['id'] . ':' . $v ['title'] . "\r\n";
			}
			
			$fields = get_model_attribute ( $model ['id'] );
			if (! empty ( $extra )) {
				foreach ( $fields as &$vo ) {
					if ($vo ['name'] == 'pid') {
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
			
// 			$addons_list = array (
// 			    'Shop:微商城',
// 			    'ShopCoupon:代金券',
// 			    'Coupon:优惠券',
// 			    'Card:会员卡',
// 			    'HelpOpen:9+1红包',
// 			    'Seckill:秒杀活动',
// 			    'ShopVote:微投票',
// 			    'SingIn:微签到',
// 			    'Reserve:微预约',
// 			    'Draw:游戏活动'
// 			);
// 			$addonStr=implode(chr(10), $addons_list);
// 			foreach ($fields as $k=>&$f){
// 			    if ($k=='addon'){
// 			        $f['extra'].=chr(10).$addonStr;
// 			    }
// 			}
			$this->assign ( 'fields', $fields );
			$this->assign ( 'data', $data );
			
			$this->assign ( 'normal_tips', '为了方便移植,可以用以下特殊字符代替常用地址参数：<br>
				[website]: ' . SITE_URL . '<br>[publicid]: ' . get_token_appinfo ( '', 'id' ) . '<br>[token]: ' . get_token () . '<br>
				用法例如：微网站：[website]/?s=/addon/WeiSite/WeiSite/index/publicid/[publicid]' );
			
			$this->display ();
		}
	}
	public function add($model = null) {
		is_array ( $model ) || $model = $this->getModel ( $this->model_name );
		$Model = D ( parse_name ( get_table_name ( $model ['id'] ), 1 ) );
		
		if (IS_POST) {
		  
			$_POST = $this->_deal_post ( $_POST );
// 			dump($_POST);die;
			// 获取模型的字段信息
			$Model = $this->checkAttr ( $Model, $model ['id'] );
			if ($Model->create () && $id = $Model->add ()) {
// 			    $this->_saveKeyword ( $model, $id );
				$this->success ( '添加' . $model ['title'] . '成功！', U ( 'lists?model=' . $model ['name'] ) );
			} else {
				$this->error ( $Model->getError () );
			}
		} else {
			// 要先填写appid
			$map ['token'] = get_token ();
			$info = get_token_appinfo ( $map ['token'] );
			
			if (empty ( $info ['appid'] ) || empty ( $info ['secret'] )) {
				$this->error ( '请先配置appid和secret', U ( 'home/Public/edit', 'id=' . $info ['id'] ) );
			}
			// 获取一级菜单
			$map ['pid'] = 0;
			$list = $Model->where ( $map )->select ();
			foreach ( $list as $v ) {
				$extra .= $v ['id'] . ':' . $v ['title'] . "\r\n";
			}
			
			$fields = get_model_attribute ( $model ['id'] );
			if (! empty ( $extra )) {
				foreach ( $fields as &$vo ) {
					if ($vo ['name'] == 'pid') {
						$vo ['extra'] .= "\r\n" . $extra;
					}
				}
			}
// 			$addons_list = array (
// 				'Shop:微商城',
// 				'ShopCoupon:代金券',
// 				'Coupon:优惠券',
// 				'Card:会员卡',
// 				'HelpOpen:9+1红包',
// 				'Seckill:秒杀活动',
// 				'ShopVote:微投票',
// 				'SingIn:微签到',
// 				'Reserve:微预约',
// 				'Draw:游戏活动' 
// 		  );
// 			$addonStr=implode(chr(10), $addons_list);
// 			foreach ($fields as $k=>&$f){
// 			    if ($k=='addon'){
// 			        $f['extra'].=chr(10).$addonStr;
// 			    }
// 			}
			$this->assign ( 'fields', $fields );
			$this->meta_title = '新增' . $model ['title'];
			
			$this->assign ( 'normal_tips', '为了方便移植,可以用以下特殊字符代替常用地址参数：<br>
				[website]: ' . SITE_URL . '<br>[publicid]: ' . get_token_appinfo ( '', 'id' ) . '<br>[token]: ' . get_token () . '<br>
				用法例如：微网站：[website]/?s=/addon/WeiSite/WeiSite/index/publicid/[publicid]' );
			
			$this->display ();
		}
	}
	
	function _deal_post($data) {
		// click:点击推事件 |keyword@show,url@hide
		// scancode_push:扫码推事件 |keyword@show,url@hide
		// scancode_waitmsg:扫码带提示 |keyword@show,url@hide
		// pic_sysphoto:弹出系统拍照发图 |keyword@show,url@hide
		// pic_photo_or_album:弹出拍照或者相册发图|keyword@show,url@hide
		// pic_weixin:弹出微信相册发图器 |keyword@show,url@hide
		// location_select:弹出地理位置选择器|keyword@show,url@hide
		
		// view:跳转URL|keyword@hide,url@show
		
		// none:无事件的一级菜单|keyword@hide,url@hide
		if ($data ['type'] == 'none') {
			$data ['keyword'] = '';
			$data ['url'] = '';
		} elseif ($data ['type'] == 'view') {
			$data ['keyword'] = '';
		} else {
			$data ['url'] = '';
		}
		return $data;
	}
	public function del() {
		$model = $this->getModel ( $this->model_name );
		parent::common_del ( $model );
	}
	function get_menu() {
		$url = 'https://api.weixin.qq.com/cgi-bin/menu/get?access_token=' . get_access_token ();
		$content = file_get_contents ( $url );
	}
	
// 	function get_target(){
// 	    $addon=I('addon_name');
// 	    $public_info=get_token_appinfo();
// 	    if ($addon == '0' ){
// 	        $data['id']=0;
// 	        $data['title']='请选择';
// 	        $returnData[]=$data;
// 	    }else {
// 	        switch ($addon){
// 	            case 'Shop':
// 	                // 获取当前登录的用户的商城
// 	                $map ['token'] = get_token ();
// 	                $map ['manager_id'] = $this->mid;
// 	                $currentShopInfo = M ( 'shop' )->where ( $map )->find ();
// 	                $data['id']=$currentShopInfo['id'];
// 	                $data['title']=$currentShopInfo['title'];
// 	                $data['url']=addons_url('Shop://Wap/index',array('shop_id'=>$data['id'],'publicid'=>$public_info['id'],'uid'=>get_mid()));
//                     $returnData[]=$data;
// 	                break;
// 	            case 'ShopCoupon':
// 	                $data['id']='-1';
// 	                $data['title']='代金券列表';
// 	                $data['url']=addons_url('ShopCoupon://Wap/lists',array('publicid'=>$public_info['id']));
// 	                $returnData[]=$data;
// 	                break;
// 	            case 'Coupon':
// 	                $data['id']='-1';
// 	                $data['title']='优惠券列表';
// 	                $data['url']=addons_url('Coupon://Wap/lists',array('publicid'=>$public_info['id']));
// 	                $returnData[]=$data;
// 	                break;
// 	            case 'Card':
// 	                $data['id']='-1';
// 	                $data['title']='微会员';
// 	                $data['url']=addons_url('Card://Wap/index',array('publicid'=>$public_info['id']));
// 	                $returnData[]=$data;
// 	                break;
// 	            case 'SingIn':
// 	                $data['id']='-1';
// 	                $data['title']='微签到';
// 	                $data['url']=addons_url('Card://Wap/signin',array('publicid'=>$public_info['id']));
// 	                $returnData[]=$data;
// 	                break;
// 	            default:
// 	                $addon_name=$addon;
// 	                $start_time='start_time';
// 	                $end_time='end_time';
// 	                $token='token';
// 	                $status='';
// 	                $status_val=1;
// 	                $param_name='id';
// 	                if ($addon == 'Vote'){
// 	                    $param_name='vote_id';
// 	                    $addon='ShopVote';
// 	                }else if($addon =='HelpOpen'){
// 	                    $status='status';
// 	                }else if($addon == 'Reserve'){
// 	                    $param_name='reserve_id';
// 	                    $status='status';
// 	                }else if($addon == 'Draw'){
// 	                    $addon='Games';
// 	                    $status='status';
// 	                    $param_name='games_id';
// 	                }
// 	                $returnData=D('Addons://CustomMenu/CustomMenu')->getListData($addon_name,$addon,$start_time,$end_time,$token,$status,$status_val);
// //                     dump($returnData);
//                     $param['publicid']=$public_info['id'];
//                     foreach ($returnData as &$v){
//                         $param[$param_name]=$v['id'];
//                         $v['url']=addons_url("$addon_name://Wap/index",$param);
//                     }
// 	                break;
// 	        }
// 	    }
// 	    $this->ajaxReturn($returnData);
// 	}
	
	function get_addons_lists_url(){
	    $addonName=I('addon_name');
	    $jumpUrl='';
	    $public_info=get_token_appinfo();
	    if($addonName=='Shop'){
	        $url='';
	        // 获取当前登录的用户的商城
	        $map ['token'] = get_token ();
	        $map ['manager_id'] = $this->mid;
	        $currentShopInfo = M ( 'shop' )->where ( $map )->find ();
	        $id=$currentShopInfo['id'];
	        $jumpUrl=addons_url('Shop://Wap/index',array('shop_id'=>$id,'publicid'=>$public_info['id'],'uid'=>get_mid()));
	    }else if($addonName=='WishCard'){
	        $url='';
	        $jumpUrl=addons_url('WishCard://Wap/card_type',array('publicid'=>$public_info['id']));
	    }else{
	        $url=addons_url("$addonName://$addonName/lists");
	    }
	    $data['url']=$url;
	    $data['jump_url']=$jumpUrl;
	    $this->ajaxReturn($data);
	}
	function get_addons_name(){
        $jumpType=I('jump_type',0,'intval');
        if ($jumpType == 1){
            $data['key']='Shop';
            $data['title']='微商城';
            $datas[]=$data;
            $data['key']='WishCard';
            $data['title']='微贺卡';
            $datas[]=$data;
        }
        $data['key']='Vote';
        $data['title']='投票';
        $datas[]=$data;
        $data['key']='Survey';
        $data['title']='微调研';
        $datas[]=$data;
        $data['key']='Scratch';
        $data['title']='刮刮卡';
        $datas[]=$data;
        $data['key']='Xydzp';
        $data['title']='大转盘';
        $datas[]=$data;
        $data['key']='Guess';
        $data['title']='竞猜';
        $datas[]=$data;
        $data['key']='CardVouchers';
        $data['title']='微信卡券';
        $datas[]=$data;
        $data['key']='Coupon';
        $data['title']='优惠券';
        $datas[]=$data;
        $data['key']='RedBag';
        $data['title']='微信红包';
        $datas[]=$data;
        $data['key']='Invite';
        $data['title']='微邀约';
        $datas[]=$data;
        $this->ajaxReturn($datas);
	}
	function get_addon_jump_url(){
	    $addonName=I('addon_name');
	    $id=I('id');
	    $jumpType=I('jump_type');
	    $public_info=get_token_appinfo();
	    $url='';
	    switch ($addonName){
	        case 'Shop':
	            // 获取当前登录的用户的商城
	            // 获取当前登录的用户的商城
	            $map ['token'] = get_token ();
	            $map ['manager_id'] = $this->mid;
	            $currentShopInfo = M ( 'shop' )->where ( $map )->find ();
	            $id=$currentShopInfo['id'];
	            $url=addons_url('Shop://Wap/index',array('shop_id'=>$id,'publicid'=>$public_info['id'],'uid'=>get_mid()));
	            break;
	        case 'Coupon':
	            if($jumpType==0){
	                $url=addons_url('Coupon://Wap/lists',array('publicid'=>$public_info['id'],'id'=>$id));
	            }else{
	                $url=addons_url('Coupon://Wap/lists',array('publicid'=>$public_info['id']));
	            }
	            break;
	        case 'WishCard':
	            $url=addons_url('WishCard://Wap/card_type',array('publicid'=>$public_info['id']));
	            break;
	        case 'CardVouchers':
	            $url=addons_url('CardVouchers://Wap/index',array('publicid'=>$public_info['id'],'id'=>$id));
	            break;
	        case 'RedBag':
	            $url=addons_url('RedBag://Wap/index',array('publicid'=>$public_info['id'],'id'=>$id));
	            break;
	        case 'Invite':
                $url=addons_url('Invite://Wap/index',array('publicid'=>$public_info['id'],'id'=>$id));
                break;
	        default:
	            $param_name='id';
	            if ( ! $url){
	                $param['publicid']=$public_info['id'];
	                $param[$param_name]=$id;
	                $url=addons_url("$addonName://$addonName/index",$param);
	            }
	            break;
	    }
	    echo $url;
	}
}
