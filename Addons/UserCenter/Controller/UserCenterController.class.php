<?php

namespace Addons\UserCenter\Controller;

use Home\Controller\AddonsController;

class UserCenterController extends AddonsController {
	var $syc_wechat = true;
	// 是否需要与微信端同步，目前只有认证的订阅号和认证的服务号可以同步
	function _initialize() {
		parent::_initialize ();
		$this->syc_wechat = C ( 'USER_LIST' );
	}
	
	/**
	 * 显示微信用户列表数据
	 */
	public function lists() {
		$model = $this->getModel ( 'user' );
		
		$page = I ( 'p', 1, 'intval' ); // 默认显示第一页数据
		
		$isAjax = I ( 'isAjax' );
		$isRadio = I ( 'isRadio' );
		
		// 解析列表规则
		$list_data = $this->_list_grid ( $model );
		$fields = $list_data ['fields'];
		
		// 搜索条件
		$map ['u.status'] = array (
				'gt',
				0 
		);
		$map ['f.token'] = get_token ();
		
		$group_id = I ( 'group_id', 0, 'intval' );
		$this->assign ( 'group_id', $group_id );
		if ($group_id) {
			$map2 ['group_id'] = $group_id;
			$uids = M ( 'auth_group_access' )->where ( $map2 )->getFields ( 'uid' );
			if (empty ( $uids )) {
				$map ['f.uid'] = 0;
			} else {
				$map ['f.uid'] = array (
						'in',
						$uids 
				);
			}
		}
		$nickname = I ( 'nickname' );
		if ($nickname) {
			$uidstr=D ( 'Common/User' )->searchUser ( $nickname );
			if ($uidstr){
				$map ['u.uid'] = array (
						'in',
						$uidstr
				);
			}else{
				$map['u.uid']=0;
			}
		}
		$row = empty ( $model ['list_row'] ) ? 20 : $model ['list_row'];
		$order = 'u.uid desc';
		// 读取模型数据列表
		$px = C ( 'DB_PREFIX' );
		$data = M ()->table ( $px . 'public_follow as f' )->join ( $px . 'user as u ON f.uid=u.uid' )->field ( 'u.uid,f.openid' )->where ( $map )->order ( $order )->page ( $page, $row )->select ();
		foreach ( $data as $k => $d ) {
			$user = getUserInfo ( $d ['uid'] );
			$user ['openid'] = $d ['openid'];
			$user ['group'] = implode ( ', ', getSubByKey ( $user ['groups'], 'title' ) );
			// dump ( $user );
			$data [$k] = $user;
		}
		/* 查询记录总数 */
		$count = M ()->table ( $px . 'public_follow as f' )->join ( $px . 'user as u ON f.uid=u.uid' )->where ( $map )->count ();
		
		$list_data ['list_data'] = $data;
		
		// 分页
		if ($count > $row) {
			$page = new \Think\Page ( $count, $row );
			$page->setConfig ( 'theme', '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%' );
			$list_data ['_page'] = $page->show ();
		}
		
		// 用户组
		$gmap ['token'] = get_token ();
		$gmap ['manager_id'] = $this->mid;
		$auth_group = M ( 'auth_group' )->where ( $gmap )->select ();
		$this->assign ( 'auth_group', $auth_group );
		$this->assign ( 'syc_wechat', $this->syc_wechat );
		if ($this->syc_wechat) {
			$this->assign ( 'noraml_tips', '请定期手动点击“一键同步微信公众号粉丝”按钮同步微信数据' );
		}
		if ($isAjax) {
			$this->assign ( 'isRadio', $isRadio );
			$this->assign ( $list_data );
			$this->display ( 'lists_data' );
		} else {
			$this->assign ( $list_data );
			$this->display ();
		}
	}
	function getUserRemark(){
	    $uid=I('uid');
	    $remark='';
	    $user=get_userinfo($uid);
	    $token=get_token();
	    if ($user['remarks'][$token]){
	        $remark=$user['remarks'][$token];
	    }
	    echo $remark;
	}
	public function admin_lists() {
		$model = $this->getModel ( 'user' );
		
		$page = I ( 'p', 1, 'intval' ); // 默认显示第一页数据
		
		$isAjax = I ( 'isAjax' );
		$isRadio = I ( 'isRadio' );
		
		// 解析列表规则
		$list_data = $this->_list_grid ( $model );
		$fields = $list_data ['fields'];
		
		// 搜索条件
		$nickname = I ( 'nickname' );
		if ($nickname) {
			$map ['uid'] = array (
					'in',
					D ( 'Common/User' )->searchUser ( $nickname ) 
			);
		}
		$row = empty ( $model ['list_row'] ) ? 20 : $model ['list_row'];
		$order = 'uid desc';
		// 读取模型数据列表
		$px = C ( 'DB_PREFIX' );
		$data = M ( 'manager' )->field ( 'uid' )->where ( $map )->order ( $order )->page ( $page, $row )->select ();
		
		foreach ( $data as $k => $d ) {
			$user = getUserInfo ( $d ['uid'] );
			// dump ( $user );
			$data [$k] = $user;
		}
		/* 查询记录总数 */
		$count = M ( 'manager' )->where ( $map )->count ();
		
		$list_data ['list_data'] = $data;
		
		// 分页
		if ($count > $row) {
			$page = new \Think\Page ( $count, $row );
			$page->setConfig ( 'theme', '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%' );
			$list_data ['_page'] = $page->show ();
		}
		
		$this->assign ( 'syc_wechat', $this->syc_wechat );
		if ($this->syc_wechat) {
			$this->assign ( 'noraml_tips', '请定期手动点击“一键同步微信公众号粉丝”按钮同步微信数据' );
		}
		if ($isAjax) {
			$this->assign ( 'isRadio', $isRadio );
			$this->assign ( $list_data );
			$this->display ( 'admin_lists' );
		} else {
			$this->assign ( $list_data );
			$this->display ( 'lists' );
		}
	}
	function detail() {
		$uid = I ( 'uid' );
		$userInfo = getUserInfo ( $uid );
// 		dump($userInfo);
		$strgroup='';
		foreach ($userInfo['groups'] as $v){
		    $strgroup.=$v['title'].',';
		}
		$len=strlen($strgroup)-1;
		$str=substr($strgroup, 0,$len);
		$userInfo['groupstr']=$str;
		$this->assign ( 'info', $userInfo );
		
		$this->display ();
	}
	function set_login() {
		$model = $this->getModel ( 'user' );
		$map ['uid'] = $id = I ( 'get.uid' );
		
		// 获取数据
		$data = M ( get_table_name ( $model ['id'] ) )->find ( $id );
		$data || $this->error ( '数据不存在！' );
		
		if (IS_POST) {
			if (empty ( $_POST ['login_name'] ) || empty ( $_POST ['login_password'] )) {
				$this->error ( '账号信息不能为空' );
			}
			
			$save ['login_name'] = I ( 'login_name' );
			$old_uid = M ( 'user' )->where ( $save )->getField ( 'uid' );
			if ($old_uid > 0 && $old_uid != $id) {
				$this->error ( '该账号已经存在，请更换后再试' );
			}
			// 手工升级会员时，用户经历值也增加到该会员级别的条件经历值
			$membership_condition = M ( 'shop_membership' )->where ( array (
					'id' => $_POST ['membership'] 
			) )->getField ( 'condition' );
			$user_experience = get_userinfo ( $map ['uid'], 'experience' );
			if ($user_experience < $membership_condition) {
				$save ['experience'] = $membership_condition;
			}
			
			$save ['leven'] = 1;
			$save ['manager_id'] = $this->mid;
			$save ['is_audit'] = 1;
			$save ['is_init'] = 1;
			$save ['status'] = 1;
			$save ['login_password'] = I ( 'login_password' );
			$save ['password'] = think_weiphp_md5 ( $save ['login_password'] );
			$save ['membership'] = $_POST ['membership'];
			// 获取模型的字段信息
			if (M ( 'user' )->where ( $map )->save ( $save ) !== false) {
				D ( 'Common/User' )->getUserInfo ( $id, true );
				
				$this->success ( '保存' . $model ['title'] . '成功！', U ( 'lists' ) );
			} else {
				$this->error ( '保存失败' );
			}
		} else {
			$fields = get_model_attribute ( $model ['id'] );
			
			$extra = $this->getMembershipData ();
			if (! empty ( $extra )) {
				foreach ( $fields as &$vo ) {
					if ($vo ['name'] == 'membership') {
						$vo ['extra'] .= "\r\n" . $extra;
					}
				}
			}
			
			$this->assign ( 'fields', $fields );
			$this->assign ( 'data', $data );
			
			$this->assign ( 'post_url', U ( 'set_login', $map ) );
			
			$this->display ( 'edit' );
		}
	}
	// 获取会员等级
	function getMembershipData() {
		$map ['uid'] = $this->mid;
		$map ['token'] = get_token ();
		$uid = I ( 'uid' );
		$userExperience = get_userinfo ( $uid, 'experience' );
		$list = M ( 'shop_membership' )->where ( $map )->select ();
		foreach ( $list as $v ) {
			if ($v ['condition'] >= $userExperience) {
				$extra .= $v ['id'] . ':' . $v ['membership'] . "\r\n";
			}
		}
		return $extra;
	}
	
	// 用户绑定
	public function edit() {
		$is_admin_edit = false;
		if (! empty ( $_REQUEST ['id'] )) {
			$map ['id'] = intval ( $_REQUEST ['id'] );
			$is_admin_edit = true;
			$msg = '编辑';
			$html = 'edit';
		} else {
			$msg = '绑定';
			$openid = $map ['openid'] = get_openid ();
			$html = 'moblieForm';
		}
		$token = $map ['token'] = get_token ();
		$model = $this->getModel ( 'user' );
		
		if (IS_POST) {
			$is_admin_edit && $_POST ['status'] = 2;
			$Model = D ( parse_name ( get_table_name ( $model ['id'] ), 1 ) );
			// 获取模型的字段信息
			$Model = $this->checkAttr ( $Model, $model ['id'] );
			if ($Model->create () && $Model->where ( $map )->save ()) {
				// lastsql();exit;
				$url = '';
				$bind_backurl = cookie ( '__forward__' );
				$config = getAddonConfig ( 'UserCenter' );
				$jumpurl = $config ['jumpurl'];
				
				if (! empty ( $bind_backurl )) {
					$url = $bind_backurl;
					cookie ( '__forward__', NULL );
				} elseif (! empty ( $jumpurl )) {
					$url = $jumpurl;
				} elseif (! $is_admin_edit) {
					$url = addons_url ( 'WeiSite://WeiSite/index', $map );
				}
				
				$this->success ( '操作成功！', $url );
			} else {
				// lastsql();
				// dump($map);exit;
				$this->error ( $Model->getError () );
			}
		} else {
			$fields = get_model_attribute ( $model ['id'] );
			// dump($fields);
			if (! $is_admin_edit) {
				$fieldArr = array (
						'nickname',
						'sex',
						'mobile',
						'email' 
				); // headimgurl
				foreach ( $fields as $k => $vo ) {
					if (! in_array ( $vo ['name'], $fieldArr ))
						unset ( $fields [$k] );
				}
				
				$this->assign ( 'button_name', '用户绑定' );
			}
			
			// 获取数据
			$data = M ( get_table_name ( $model ['id'] ) )->where ( $map )->find ();
			
			$token = get_token ();
			if (isset ( $data ['token'] ) && $token != $data ['token'] && defined ( 'ADDON_PUBLIC_PATH' )) {
				$this->error ( '非法访问！' );
			}
			
			// 自动从微信接口获取用户信息
			empty ( $openid ) || $info = getWeixinUserInfo ( $openid, $token );
			if (is_array ( $info )) {
				if (empty ( $data ['headimgurl'] ) && ! empty ( $info ['headimgurl'] )) {
					// 把微信头像转到WeiPHP的通用图片ID保存 TODO
					$data ['headimgurl'] = $info ['headimgurl'];
				}
				$data = array_merge ( $info, $data );
			}
			
			$this->assign ( 'fields', $fields );
			$this->assign ( 'data', $data );
			
			$this->assign ( 'post_url', U ( 'edit' ) );
			
			$this->display ( $html );
		}
	}
	public function userCenter() {
		$this->display ();
	}
	function config() {
		// 使用提示
		$normal_tips = '如需用户关注时提示先绑定，请进入‘欢迎语’插件按提示进行配置提示语';
		$this->assign ( 'normal_tips', $normal_tips );
		if (IS_POST) {
			$config = I ( 'config' );
			$credit ['score'] = intval ( $config ['score'] );
			$credit ['experience'] = intval ( $config ['experience'] );
			$token = get_token ();
			D ( 'Common/Credit' )->updateSubscribeCredit ( $token, $credit, 1 );
		}
		
		parent::config ();
	}
	// 设置用户组
	public function changeGroup() {
		$id = array_unique ( ( array ) I ( 'ids', 0 ) );
		
		if (empty ( $id )) {
			$this->error ( '请选择用户!' );
		}
		$group_id = I ( 'group_id', 0 );
		if (empty ( $group_id )) {
			$this->error ( '请选择用户组!' );
		}
		D ( 'Home/AuthGroup' )->move_group ( $id, $group_id );
		echo 1;
	}
	// 预先同步好用户组数据
	function syc_auth_group() {
		redirect ( U ( 'Home/AuthGroup/updateWechatGroup', array (
				'need_return' => 1 
		) ) );
	}
	// 第一步：获取全部用户的ID，并先保存到public_follow表中，新的用户UID暂时为0，后面的步骤补充
	function syc_openid() {
		// 获取openid列表
		$url = 'https://api.weixin.qq.com/cgi-bin/user/get?access_token=' . get_access_token () . '&next_openid=' . I ( 'next_openid' );
		$data = wp_file_get_contents ( $url );
		$data = json_decode ( $data, true );
		
		if (! isset ( $data ['count'] ) || $data ['count'] == 0) {
			// 拉取完毕
			$this->success ( '同步用户数据中，请勿关闭', U ( 'syc_user' ) );
			exit ();
		}
		
		$map ['openid'] = array (
				'in',
				$data ['data'] ['openid'] 
		);
		$map ['token'] = $save ['token'] = get_token ();
		$res = M ( 'public_follow' )->where ( $map )->setField ( 'syc_status', 0 );
		if ($res != $data ['count']) {
			// 更新的数量不一致，可能有增加的用户openid
			$openids = ( array ) M ( 'public_follow' )->where ( $map )->getFields ( 'openid' );
			$diff = array_diff ( $data ['data'] ['openid'], $openids );
			if (! empty ( $diff )) {
				foreach ( $diff as $id ) {
					$save ['openid'] = $id;
					$save ['uid'] = 0;
					$save ['syc_status'] = 0;
					$res = M ( 'public_follow' )->add ( $save );
				}
			}
		}
		
		$param2 ['next_openid'] = $data ['next_openid'];
		$url = U ( 'syc_openid', $param2 );
		$this->success ( '同步用户OpenID中，请勿关闭', $url );
	}
	// 第二步：同步用户信息
	function syc_user() {
		$map ['token'] = $map2 ['token'] = $map5 ['token'] = get_token ();
		$map ['syc_status'] = 0;
		$list = M ( 'public_follow' )->where ( $map )->field ( 'uid,openid' )->limit ( 100 )->select ();
		
		if (empty ( $list )) {
			$this->success ( '用户分组信息同步中', U ( 'syc_user_group' ) );
			exit ();
		}
		
		foreach ( $list as $vo ) {
			$param ['user_list'] [] = array (
					'openid' => $vo ['openid'],
					'lang' => 'zh-CN' 
			);
			$openids [] = $vo ['openid'];
			$uids [$vo ['openid']] = $vo ['uid'];
		}
		// 先把关注状态设置未关注
		$map2 ['openid'] = array (
				'in',
				$openids 
		);
		M ( 'public_follow' )->where ( $map2 )->setField ( 'has_subscribe', 0 );
		
		$url = 'https://api.weixin.qq.com/cgi-bin/user/info/batchget?access_token=' . get_access_token ();
		$data = post_data ( $url, $param );
		
		$userDao = D ( 'Common/User' );
		$config = getAddonConfig ( 'UserCenter' );
		foreach ( $data ['user_info_list'] as $u ) {
			if ($u ['subscribe'] == 0)
				continue;
			
			$uid = intval ( $uids [$u ['openid']] );
			if ($uid == 0) { // 新增加的用户
				$u ['experience'] = intval ( $config ['experience'] );
				$u ['score'] = intval ( $config ['score'] );
				$u ['reg_time'] = $u ['subscribe_time'];
				$u ['status'] = 1;
				$u ['is_init'] = 1;
				$u ['is_audit'] = 1;
				
				$uid = D ( 'Common/User' )->addUser ( $u );
				
				$map5 ['openid'] = $u ['openid'];
				$uid > 0 && M ( 'public_follow' )->where ( $map5 )->setField ( 'uid', $uid );
			} else { // 更新的用户
				$userDao->updateInfo ( $uid, $u );
			}
			
			$openidArr [] = $u ['openid'];
		}
		
		M ( 'public_follow' )->where ( $map2 )->setField ( 'syc_status', 1 );
		
		// 设置关注状态
		if (! empty ( $openidArr )) {
			$map2 ['openid'] = array (
					'in',
					$openidArr 
			);
			M ( 'public_follow' )->where ( $map2 )->setField ( 'has_subscribe', 1 );
		}
		
		$this->success ( '同步用户数据中，请勿关闭', U ( 'syc_user?uid=' . $uid ) );
	}
	// 第三步：同步用户组信息
	function syc_user_group() {
		$map ['token'] = $map2 ['token'] = get_token ();
		$map ['syc_status'] = 1;
		$uids = M ( 'public_follow' )->where ( $map )->limit ( 100 )->getFields ( 'uid' );
		
		if (empty ( $uids )) {
			$this->success ( '用户分组信息同步完毕', U ( 'lists' ) );
			exit ();
		}
		
		$user_map ['uid'] = $map2 ['uid'] = array (
				'in',
				$uids 
		);
		$list = M ( 'user' )->where ( $user_map )->field ( 'uid,groupid' )->select ();
		foreach ( $list as $vo ) {
			$userArr [$vo ['uid']] = $vo ['groupid'];
		}
		
		$auth_map ['manager_id'] = $this->mid;
		$auth_map ['token'] = get_token ();
		$groups = M ( 'auth_group' )->where ( $auth_map )->field ( 'id,wechat_group_id' )->select ();
		foreach ( $groups as $g ) {
			$groupArr [$g ['id']] = $g ['wechat_group_id'];
			$wechatArr [$g ['wechat_group_id']] = $g ['id'];
		}
		
		$list = M ( 'auth_group_access' )->where ( $user_map )->select ();
		foreach ( $list as $vo ) {
			$access [$vo ['uid']] = $vo ['group_id'];
		}
		foreach ( $uids as $uid ) {
			$new_groupid = $userArr [$uid];
			$old_groupid = $groupArr [$access [$uid]];
			
			if (isset ( $access [$uid] ) && $new_groupid == $old_groupid)
				continue;
			
			$save ['group_id'] = $wechatArr [$new_groupid];
			if (isset ( $access ['uid'] )) {
				$amap ['uid'] = $vo ['uid'];
				$res = M ( 'auth_group_access' )->where ( $amap )->save ( $save );
			} else {
				$save ['uid'] = $uid;
				$res = M ( 'auth_group_access' )->add ( $save );
			}
			// dump ( $res );
			// lastsql ();
		}
		// exit ();
		M ( 'public_follow' )->where ( $map2 )->setField ( 'syc_status', 2 );
		
		$this->success ( '用户分组信息同步中，请勿关闭', U ( 'syc_user_group?uid=' . $uid ) );
	}
	function set_remark() {
		$map ['uid'] = I ( 'uid', 0, 'intval' );
		if (empty ( $map ['uid'] )) {
			$this->error ( '用户信息出错' );
		}
		
		$param ['remark'] = I ( 'remark' );
		if (empty ( $param ['remark'] )) {
			$this->error ( '备注不能为空' );
		}
		
		$map ['token'] = get_token ();
		
		$info = M ( 'public_follow' )->where ( $map )->find ();
		if (! $info) {
			$this->error ( '用户信息出错啦' );
		}
		
		$res = M ( 'public_follow' )->where ( $map )->save ( $param );
		if ($res !== false) { // 同步到微信端
			D ( 'Common/User' )->getUserInfo ( $map ['uid'], true );
			if (C ( 'USER_REMARK' )) {
				$url = 'https://api.weixin.qq.com/cgi-bin/user/info/updateremark?access_token=' . get_access_token ();
				$param ['openid'] = $info ['openid'];
				$result = post_data ( $url, $param );
				if ($res ['errcode'] != 0) {
					$this->error ( error_msg ( $res ) );
				}
			}
		} else {
			$this->error ( '保存数据库失败' );
		}
		
		$this->success ( '设置成功' );
	}
}
