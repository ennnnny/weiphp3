<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------
namespace Home\Controller;

/**
 * 前台首页控制器
 * 主要获取首页聚合数据
 */
class AuthGroupController extends HomeController {
	var $model;
	var $syc_wechat = true; // 是否需要与微信端同步，目前只有认证的订阅号和认证的服务号可以同步
	var $qr_code = true; // 是否有创建微信带参数的二维码权限
	function _initialize() {
		$act = strtolower ( ACTION_NAME );
		$nav = array ();
		$res ['title'] = '用户组配置';
		$res ['url'] = U ( 'AuthGroup/lists' );
		$res ['class'] = $act == 'lists' ? 'current' : '';
		$nav [] = $res;
		
		$this->assign ( 'nav', $nav );
		$this->model = $this->getModel ( 'AuthGroup' );
		
		$this->syc_wechat = C ( 'USER_LIST' );
		$this->qr_code = C ( 'QRCODE' );
	}
	public function lists() {
		$normal_tips = '';
		if ($this->syc_wechat) {
			$this->updateWechatGroup ();
			
			$normal_tips = '温馨提示：当前用户组数据会与微信端的用户组实时同步，需要删除用户组请到微信后台删除。';
			// 搜索按钮
			$search_url = U ( 'AuthGroup/lists' ) . '&mdm=96|122';
			$this->assign ( 'search_url', $search_url );
			
			$this->assign ( 'check_all', false );
			$this->assign ( 'del_button', false );
		}
		$map ['token'] = get_token ();
		$map ['manager_id'] = $this->mid;
		$map ['is_del'] = 0;
		session ( 'common_condition', $map );
		
		$list_data = $this->_get_model_list ( $this->model, $page, $order );
		if ($this->qr_code) {
			foreach ( $list_data ['list_data'] as &$vo ) {
				if (! empty ( $vo ['qr_code'] )) {
					$vo ['qr_code'] = "<a target='_blank' href='{$vo[qr_code]}'><img src='{$vo[qr_code]}' class='list_img'></a>";
					continue;
				}
				
				$res = D ( 'Home/QrCode' )->add_qr_code ( 'QR_LIMIT_SCENE', 'UserCenter', $vo ['id'] );
				if (! ($res < 0)) {
					$map2 ['id'] = $vo ['id'];
					M ( 'auth_group' )->where ( $map2 )->setField ( 'qr_code', $res );
					$vo ['qr_code'] = $res;
					$vo ['qr_code'] = "<a target='_blank' href='{$vo[qr_code]}'><img src='{$vo[qr_code]}' class='list_img'></a>";
				}
			}
			$normal_tips .= '当用户微信扫分组里的二维码时，用户会自动移到该分组中';
		} else {
			// 删除二维码一栏
			unset ( $list_data ['list_grids'] [2] );
		}
		if ($this->syc_wechat) {
			$grid = array_pop ( $list_data ['list_grids'] );
			$grid ['href'] = str_replace ( ',[DELETE]|删除', '', $grid ['href'] );
			array_push ( $list_data ['list_grids'], $grid );
		}
		
		$this->assign ( $list_data );
		$this->assign ( 'normal_tips', $normal_tips );
		
		$this->display ( 'Addons/lists' );
	}
	public function add($model = null, $templateFile = '') {
		is_array ( $model ) || $model = $this->model;
		if (IS_POST) {
			$_POST ['type'] = 1; // 目前只能增加微信管理组
			$_POST ['manager_id'] = $this->mid;
			$_POST ['token'] = get_token ();
			$has=$this->checkTitle($_POST['title']);
			if ($has > 0){
			    $this->error('该分组名已经存在！');
			}
			$Model = D ( parse_name ( get_table_name ( $model ['id'] ), 1 ) );
			// 获取模型的字段信息
			$Model = $this->checkAttr ( $Model, $model ['id'] );
			if ($Model->create () && $id = $Model->add ()) {
				$this->success ( '添加' . $model ['title'] . '成功！', U ( 'lists?model=' . $model ['name'], $this->get_param ) );
			} else {
				$this->error ( $Model->getError () );
			}
		} else {
			$fields = get_model_attribute ( $model ['id'] );
			$this->assign ( 'fields', $fields );
			$this->meta_title = '新增' . $model ['title'];
			
			$this->display ( 'Addons/add' );
		}
	}
    function checkTitle($title,$id=0){
        $map['title']=$title;
        $map['manager_id']=$this->mid;
        $map['token']=get_token();
        if ($id){
            $map['id']=array('neq',$id);
        }
        $count=M('auth_group')->where($map)->count();
        return intval($count);
    }
	public function edit($id = 0) {
		$model = $this->model;
		$id || $id = I ( 'id' );
		
		// 获取数据
		$data = M ( get_table_name ( $model ['id'] ) )->find ( $id );
		$data || $this->error ( '数据不存在！' );
		
		if (IS_POST) {
			$act = 'save';
			$has=$this->checkTitle($_POST['title'],$id);
			if ($has > 0){
			    $this->error('该分组名已经存在！');
			}
			$Model = D ( parse_name ( get_table_name ( $model ['id'] ), 1 ) );
			// 获取模型的字段信息
			$Model = $this->checkAttr ( $Model, $model ['id'] );
			if ($Model->create () && $Model->$act ()) {
				
				$title = I ( 'title' );
				if ($this->syc_wechat && $title != $data ['title'] && ! empty ( $data ['wechat_group_id'] )) {
					// 修改的用户组名同步到微信端
					$url = 'https://api.weixin.qq.com/cgi-bin/groups/update?access_token=' . $access_token;
					
					$param ['group'] ['id'] = $data ['wechat_group_id'];
					$param ['group'] ['name'] = $title;
					$param = JSON ( $param );
					$res = post_data ( $url, $param );
				}
				
				$this->success ( '保存' . $model ['title'] . '成功！', U ( 'lists?model=' . $model ['name'] ) );
			} else {
				$this->error ( $Model->getError () );
			}
		} else {
			$fields = get_model_attribute ( $model ['id'] );
			
			$this->assign ( 'fields', $fields );
			$this->assign ( 'data', $data );
			$this->meta_title = '编辑' . $model ['title'];
			
			$this->display ( 'Addons/edit' );
		}
	}
	public function del($model = null, $ids = null) {
		is_array ( $model ) || $model = $this->model;
		
		! empty ( $ids ) || $ids = I ( 'id' );
		! empty ( $ids ) || $ids = array_filter ( array_unique ( ( array ) I ( 'ids', 0 ) ) );
		! empty ( $ids ) || $this->error ( '请选择要操作的数据!' );
		
		$Model = M ( get_table_name ( $model ['id'] ) );
		$map ['id'] = $map2 ['a.group_id'] = $map3 ['group_id'] = array (
				'in',
				$ids 
		);
		
		// 插件里的操作自动加上Token限制
		$token = get_token ();
		if (defined ( 'ADDON_PUBLIC_PATH' ) && ! empty ( $token )) {
			$map ['token'] = $map2 ['f.token'] = $token;
		}
		
		if ($this->syc_wechat) {
			$res = $Model->where ( $map )->setField ( 'is_del', 0 );
		} else {
			$res = $Model->where ( $map )->delete ();
		}
		
		if ($res) {
			$px = C ( 'DB_PREFIX' );
			$follow_list = M ()->table ( $px . 'auth_group_access a' )->join ( $px . 'public_follow f ON a.uid=f.uid' )->where ( $map2 )->field ( 'DISTINCT f.openid' )->select ();
			if (! empty ( $follow_list )) {
				
				// 微信端用户归组到未分组
				if ($this->syc_wechat) {
					$gmap ['manager_id'] = $this->mid;
					$gmap ['token'] = get_token ();
					$gmap ['wechat_group_id'] = 0;
					$gid = M ( 'auth_group' )->where ( $gmap )->getField ( 'id' );
					
					M ( 'auth_group_access' )->where ( $map3 )->setField ( 'group_id', $gid );
					
					$url = 'https://api.weixin.qq.com/cgi-bin/groups/members/update?access_token=' . get_access_token ();
					foreach ( $follow_list as $follow ) {
						$param ['openid'] = $follow ['openid'];
						$param ['to_groupid'] = 0;
						$param = JSON ( $param );
						$res = post_data ( $url, $param );
					}
				} else {
					M ( 'auth_group_access' )->where ( $map3 )->delete ();
				}
			}
			
			$this->success ( '删除成功' );
		} else {
			$this->error ( '删除失败！' );
		}
	}
	// 与微信的用户组保持同步
	function updateWechatGroup() {
		// 先取当前用户组数据
		$map ['token'] = get_token ();
		$map ['manager_id'] = $this->mid;
		$map ['type'] = 1;
		$group_list = M ( 'auth_group' )->where ( $map )->field ( 'id,title,wechat_group_id,wechat_group_name,wechat_group_count' )->select ();
		foreach ( $group_list as $g ) {
			$groups [$g ['wechat_group_id']] = $g;
		}
		$url = 'https://api.weixin.qq.com/cgi-bin/groups/get?access_token=' . get_access_token ();
		$data = wp_file_get_contents ( $url );
		$data = json_decode ( $data, true );
		if (!isset($data['errcode'])){
		    foreach ( $data ['groups'] as $d ) {
		        $save ['wechat_group_id'] = $map ['wechat_group_id'] = $d ['id'];
		        $save ['wechat_group_name'] = $d ['name'];
		        $save ['wechat_group_count'] = $d ['count'];
		        	
		        if (isset ( $groups [$d ['id']] )) {
		            // 更新本地数据
		            $old = $groups [$d ['id']];
		            if ($old['title'] != $d['name']){
		                $old['wechat_group_name']=$old['title'];
		                $save ['wechat_group_name']=$old['title'];
		                //修改微信端的数据
		                $updateUrl="https://api.weixin.qq.com/cgi-bin/groups/update?access_token=".get_access_token();
		                $newGroup['group']['id']=$d['id'];
		                $newGroup['group']['name']=$save ['wechat_group_name'];
		                $res= post_data($updateUrl, $newGroup);
		            }
		            if ($old ['wechat_group_name'] != $d ['name'] || $old ['wechat_group_count'] != $d ['count']) {
		                // 					$save['title']=$save['wechat_group_name'];
		                M ( 'auth_group' )->where ( $map )->save ( $save );
		            }
		            unset ( $groups [$d ['id']] );
		        } else {
		            // 增加本地数据
		            $save = array_merge ( $save, $map );
		            $save ['title'] = $d ['name'];
		            $save ['qr_code'] = '';
		            M ( 'auth_group' )->add ( $save );
		        }
		    }
		    foreach ( $groups as $v ) {
		        $map2 ['id'] = $map3 ['group_id'] = $v ['id'];
		        $wechat_group_id = intval ( $v ['wechat_group_id'] );
		        if ($wechat_group_id == -1) {
		            // 增加微信端的数据
		            $url = 'https://api.weixin.qq.com/cgi-bin/groups/create?access_token=' . get_access_token ();
		    
		            $param ['group'] ['name'] = $v ['title'];
		            $param = JSON ( $param );
		            $res = post_data ( $url, $param );
		            if (! empty ( $res ['group'] ['id'] )) {
		                $info ['wechat_group_id'] = $save ['wechat_group_id'] = $res ['group'] ['id'];
		                $save ['wechat_group_name'] = $res ['group'] ['name'];
		                M ( 'auth_group' )->where ( $map2 )->save ( $save );
		            }
		        } else {
		            // 删除本地数据
		            M ( 'auth_group' )->where ( $map2 )->delete ();
		            M ( 'auth_group_access' )->where ( $map3 )->delete ();
		        }
		    }
		}
		
		if (isset ( $_GET ['need_return'] )) {
			redirect ( addons_url ( 'UserCenter://UserCenter/syc_openid' ) );
		}
	}
	public function qrcode() {
		$id = intval ( $_GET ['id'] );
		$res = D ( 'Home/QrCode' )->add_qr_code ( 'QR_LIMIT_SCENE', 'UserCenter', $id );
	}
	function follows() {
		redirect ( addons_url ( 'UserCenter://UserCenter/lists', array (
				'group_id' => I ( 'id' ) 
		) ) );
	}
	function export() {
		set_time_limit ( 0 );
		
		$umap ['u.status'] = array (
				'gt',
				0 
		);
		$umap ['f.token'] = get_token ();
		
		$gid = I ( 'id', 0 );
		if ($gid) {
			$map ['group_id'] = $gid;
			$uids = M ( 'auth_group_access' )->where ( $map )->getFields ( 'uid' );
			if (! empty ( $uids )) {
				$umap ['u.uid'] = array (
						'in',
						$uids 
				);
			} else {
				$umap ['u.uid'] = 0;
			}
		}
		
		$order = 'u.uid asc';
		$px = C ( 'DB_PREFIX' );
		$field = 'u.uid,nickname,truename,mobile,sex,province,city,score,experience,f.openid';
		$data = M ()->table ( $px . 'public_follow as f' )->join ( $px . 'user as u ON f.uid=u.uid' )->field ( $field )->where ( $umap )->order ( $order )->select ();
		
		$sexArr = array (
				0 => '保密',
				1 => '男',
				2 => '女' 
		);
		foreach ( $data as $k => &$vo ) {
			$vo ['sex'] = $sexArr [$vo ['sex']];
			$vo ['nickname'] = deal_emoji ( $vo ['nickname'], 1 );
		}
		
		$ht = array (
				'用户编号',
				'昵称',
				'姓名',
				'联系电话',
				'性别',
				'省份',
				'城市',
				'金币值',
				'经验值',
				'OpenID' 
		);
		$dataArr [0] = $ht;
		$dataArr = array_merge ( $dataArr, ( array ) $data );
// 		dump($dataArr);
		outExcel ( $dataArr, $map ['module'] );
// 		vendor ( 'out-csv' );
// 		export_csv ( $dataArr, 'user_export' );
	}
	
	// 移动用户到所在分组
	function tongbu_follow() {
		$map ['manager_id'] = $this->mid;
		$map ['token'] = get_token ();
		
		$list = M ( 'auth_group' )->where ( $map )->select ();
		foreach ( $list as $v ) {
			$arr [$v ['id']] = $v ['wechat_group_id'];
		}
		
		$id = I ( 'id', 0, 'intval' );
		$map ['id'] = array (
				'gt',
				$id 
		);
		$map ['has_subscribe'] = 1;
		$map ['token'] = get_token ();
		$follow_list = M ( 'public_follow' )->where ( $map )->order ( 'id asc' )->limit ( 5 )->select ();
		if (! $follow_list) {
			echo 'update over!';
			exit ();
		}
		
		$access_token = get_access_token ();
		$url = 'https://api.weixin.qq.com/cgi-bin/groups/members/update?access_token=' . $access_token;
		foreach ( $follow_list as $follow ) {
			$param ['openid'] = $follow ['openid'];
			$param ['to_groupid'] = intval ( $arr [$follow ['group']] );
			$param = JSON ( $param );
			$res = post_data ( $url, $param );
			
			$has_subscribe = $res ['errcode'] == 43004 ? 0 : 1;
			M ( 'public_follow' )->where ( 'id=' . $follow ['id'] )->setField ( 'has_subscribe', $has_subscribe );
		}
		
		$param2 ['id'] = $follow ['id'];
		$url = U ( 'tongbu_follow', $param2 );
		
		$url = addons_url ( 'tongbu_follow' );
		$this->success ( '同步用户数据中，请勿关闭', $url );
		
		// echo 'update follow_id: ' . $follow ['id'] . ', please wait!';
		// echo '<script>window.location.href="' . $url . '";</script>';
	}
}