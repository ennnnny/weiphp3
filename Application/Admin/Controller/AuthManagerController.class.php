<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 朱亚杰 <zhuyajie@topthink.net>
// +----------------------------------------------------------------------
namespace Admin\Controller;

use Admin\Model\AuthRuleModel;
use Admin\Model\AuthGroupModel;

/**
 * 权限管理控制器
 * Class AuthManagerController
 *
 * @author 朱亚杰 <zhuyajie@topthink.net>
 */
class AuthManagerController extends AdminController {
	
	/**
	 * 后台节点配置的url作为规则存入auth_rule
	 * 执行新节点的插入,已有节点的更新,无效规则的删除三项任务
	 *
	 * @author 朱亚杰 <zhuyajie@topthink.net>
	 */
	public function updateRules() {
		// 需要新增的节点必然位于$nodes
		$nodes = $this->returnNodes ( false );
		
		$AuthRule = M ( 'AuthRule' );
		$map = array (
				'module' => 'admin',
				'type' => array (
						'in',
						'1,2' 
				) 
		); // status全部取出,以进行更新
		   // 需要更新和删除的节点必然位于$rules
		$rules = $AuthRule->where ( $map )->order ( 'name' )->select ();
		
		// 构建insert数据
		$data = array (); // 保存需要插入和更新的新节点
		foreach ( $nodes as $value ) {
			$temp ['name'] = $value ['url'];
			$temp ['title'] = $value ['title'];
			$temp ['status'] = 1;
			$data [strtolower ( $temp ['name'] )] = $temp; // 去除重复项
		}
		
		$update = array (); // 保存需要更新的节点
		$ids = array (); // 保存需要删除的节点的id
		foreach ( $rules as $index => $rule ) {
			$key = strtolower ( $rule ['name'] . $rule ['module'] . $rule ['type'] );
			if (isset ( $data [$key] )) { // 如果数据库中的规则与配置的节点匹配,说明是需要更新的节点
				$data [$key] ['id'] = $rule ['id']; // 为需要更新的节点补充id值
				$update [] = $data [$key];
				unset ( $data [$key] );
				unset ( $rules [$index] );
				unset ( $rule ['condition'] );
				$diff [$rule ['id']] = $rule;
			} elseif ($rule ['status'] == 1) {
				$ids [] = $rule ['id'];
			}
		}
		if (count ( $update )) {
			foreach ( $update as $k => $row ) {
				if ($row != $diff [$row ['id']]) {
					$AuthRule->where ( array (
							'id' => $row ['id'] 
					) )->save ( $row );
				}
			}
		}
		if (count ( $ids )) {
			$AuthRule->where ( array (
					'id' => array (
							'IN',
							implode ( ',', $ids ) 
					) 
			) )->save ( array (
					'status' => - 1 
			) );
			// 删除规则是否需要从每个用户组的访问授权表中移除该规则?
		}
		if (count ( $data )) {
			$AuthRule->addAll ( array_values ( $data ) );
		}
		if ($AuthRule->getDbError ()) {
			trace ( '[' . __METHOD__ . ']:' . $AuthRule->getDbError () );
			return false;
		} else {
			return true;
		}
	}
	
	/**
	 * 用户组管理首页
	 */
	public function index() {
		$map ['manager_id'] = 0;
		$map ['type'] = array (
				'exp',
				'!=4' 
		);
		$list = $this->lists ( 'AuthGroup', $map, 'id desc' );
		$list = int_to_string ( $list );
		
		$type_arr = array (
				0 => '普通用户组',
				1 => '微信用户组',
				2 => '等级用户组',
				3 => '认证用户组' 
		);
		// 4=>'公众号分组' 这类程序写死，不可增加，删除等操作
		foreach ( $list as &$v ) {
			$v ['type'] = $type_arr [$v ['type']];
		}
		
		$this->assign ( '_list', $list );
		
		$this->assign ( '_use_tip', true );
		$this->meta_title = '用户组管理';
		$this->display ();
	}
	/**
	 * 公众号组管理首页
	 */
	public function wechat() {
		// 获取微信权限节点
		$list = M ( 'public_auth' )->select ();
		$this->assign ( 'list_data', $list );
		// dump ( $list );
		
		$this->meta_title = '用户组管理';
		
		$this->display ();
	}
	function set_switch() {
		$name = I ( 'name' );
		$val = I ( 'val' );
		$val = $val == 'true' ? 1 : 0;
		
		$arr = explode ( '|', $name );
		
		$map ['name'] = $arr [1];
		$save [$arr [0]] = $val;
		$res = M ( 'public_auth' )->where ( $map )->save ( $save );
		
		$returnData ['status'] = $res;
		if ($res) {
			S ( 'PUBLIC_AUTH_0', NULL );
			S ( 'PUBLIC_AUTH_1', NULL );
			S ( 'PUBLIC_AUTH_2', NULL );
			S ( 'PUBLIC_AUTH_3', NULL );
			$returnData ['info'] = "设置保存成功";
		} else {
			$returnData ['info'] = "设置保存失败";
		}
		$this->ajaxReturn ( $returnData, "JSON" );
	}
	/**
	 * 创建管理员用户组
	 *
	 * @author 朱亚杰 <zhuyajie@topthink.net>
	 */
	public function createGroup() {
		if (empty ( $this->auth_group )) {
			$this->assign ( 'auth_group', array (
					'title' => null,
					'id' => null,
					'description' => null,
					'rules' => null 
			) ); // 排除notice信息
		}
		$this->meta_title = '新增用户组';
		$this->display ( 'editgroup' );
	}
	
	/**
	 * 编辑管理员用户组
	 *
	 * @author 朱亚杰 <zhuyajie@topthink.net>
	 */
	public function editGroup() {
		$auth_group = M ( 'AuthGroup' )->find ( ( int ) $_GET ['id'] );
		$this->assign ( 'auth_group', $auth_group );
		$this->meta_title = '编辑用户组';
		$this->display ();
	}
	
	/**
	 * 访问授权页面
	 */
	public function access() {
		$map ['id'] = I ( 'group_id', 0, 'intval' );
		$auth_group = M ( 'AuthGroup' )->where ( $map )->getfield ( 'id,title,rules' );
		
		$map2 ['status'] = 1;
		$rules = M ( 'AuthRule' )->where ( $map2 )->field ( true )->select ();
		foreach ( $rules as $vo ) {
			$node_list [$vo ['group']] [] = $vo;
		}
		
		$this->assign ( 'node_list', $node_list );
		$this->assign ( 'auth_group', $auth_group );
		$this->assign ( 'this_group', $auth_group [( int ) $_GET ['group_id']] );
		$this->meta_title = '访问授权';
		$this->display ( 'managergroup' );
	}
	
	/**
	 * 管理员用户组数据写入/更新
	 *
	 * @author 朱亚杰 <zhuyajie@topthink.net>
	 */
	public function writeGroup() {
		if (isset ( $_POST ['rules'] )) {
			sort ( $_POST ['rules'] );
			$_POST ['rules'] = implode ( ',', array_unique ( $_POST ['rules'] ) );
		}
		// $_POST['module'] = 'admin';
		// $_POST['type'] = AuthGroupModel::TYPE_ADMIN;
		$AuthGroup = D ( 'AuthGroup' );
		$data = $AuthGroup->create ();
		if ($data) {
			if (empty ( $data ['id'] )) {
				$r = $AuthGroup->add ();
			} else {
				$r = $AuthGroup->save ();
			}
			if ($r === false) {
				$this->error ( '操作失败' . $AuthGroup->getError () );
			} else {
				$this->success ( '操作成功!', U ( 'index' ) );
			}
		} else {
			$this->error ( '操作失败' . $AuthGroup->getError () );
		}
	}
	
	/**
	 * 状态修改
	 *
	 * @author 朱亚杰 <zhuyajie@topthink.net>
	 */
	public function changeStatus($method = null) {
		if (empty ( $_REQUEST ['id'] )) {
			$this->error ( '请选择要操作的数据!' );
		}
		switch (strtolower ( $method )) {
			case 'forbidgroup' :
				$this->forbid ( 'AuthGroup' );
				break;
			case 'resumegroup' :
				$this->resume ( 'AuthGroup' );
				break;
			case 'deletegroup' :
				$this->delete ( 'AuthGroup' );
				break;
			default :
				$this->error ( $method . '参数非法' );
		}
	}
	
	/**
	 * 用户组授权用户列表
	 *
	 * @author 朱亚杰 <zhuyajie@topthink.net>
	 */
	public function user($group_id) {
		if (empty ( $group_id )) {
			$this->error ( '参数错误' );
		}
		
		$auth_group = M ( 'AuthGroup' )->field ( 'id,title,rules' )->select ();
		$prefix = C ( 'DB_PREFIX' );
		$l_table = $prefix . (AuthGroupModel::MEMBER);
		$r_table = $prefix . (AuthGroupModel::AUTH_GROUP_ACCESS);
		$model = M ()->table ( $l_table . ' m' )->join ( $r_table . ' a ON m.uid=a.uid' );
		$_REQUEST = array ();
		$list = $this->lists ( $model, array (
				'a.group_id' => $group_id,
				'm.status' => array (
						'egt',
						0 
				) 
		), 'm.uid asc', 'm.uid,m.nickname,m.last_login_time,m.last_login_ip,m.status' );
		int_to_string ( $list );
		$this->assign ( '_list', $list );
		$this->assign ( 'auth_group', $auth_group );
		$this->assign ( 'this_group', $auth_group [( int ) $_GET ['group_id']] );
		$this->meta_title = '成员授权';
		$this->display ();
	}
	public function tree($tree = null) {
		$this->assign ( 'tree', $tree );
		$this->display ( 'tree' );
	}
	
	/**
	 * 将用户添加到用户组的编辑页面
	 *
	 * @author 朱亚杰 <zhuyajie@topthink.net>
	 */
	public function group() {
		$uid = I ( 'uid' );
		$auth_groups = D ( 'AuthGroup' )->getGroups ();
		$user_groups = AuthGroupModel::getUserGroup ( $uid );
		$ids = array ();
		foreach ( $user_groups as $value ) {
			$ids [] = $value ['group_id'];
		}
		$nickname = D ( 'Common/User' )->getNickName ( $uid );
		$this->assign ( 'nickname', $nickname );
		$this->assign ( 'auth_groups', $auth_groups );
		$this->assign ( 'user_groups', implode ( ',', $ids ) );
		$this->meta_title = '用户组授权';
		$this->display ();
	}
	
	/**
	 * 将用户添加到用户组,入参uid,group_id
	 *
	 * @author 朱亚杰 <zhuyajie@topthink.net>
	 */
	public function addToGroup() {
		$uid = I ( 'uid' );
		$gid = I ( 'group_id' );
		if (empty ( $uid )) {
			$this->error ( '参数有误' );
		}
		$AuthGroup = D ( 'AuthGroup' );
		if (is_numeric ( $uid )) {
			if (is_administrator ( $uid )) {
				$this->error ( '该用户为超级管理员' );
			}
			if (! M ( 'User' )->where ( array (
					'uid' => $uid 
			) )->find ()) {
				$this->error ( '用户不存在' );
			}
		}
		
		if ($gid && ! $AuthGroup->checkGroupId ( $gid )) {
			$this->error ( $AuthGroup->error );
		}
		if ($AuthGroup->addToGroup ( $uid, $gid )) {
			$this->success ( '操作成功' );
		} else {
			$this->error ( $AuthGroup->getError () );
		}
	}
	
	/**
	 * 将用户从用户组中移除 入参:uid,group_id
	 *
	 * @author 朱亚杰 <zhuyajie@topthink.net>
	 */
	public function removeFromGroup() {
		$uid = I ( 'uid' );
		$gid = I ( 'group_id' );
		if ($uid == UID) {
			$this->error ( '不允许解除自身授权' );
		}
		if (empty ( $uid ) || empty ( $gid )) {
			$this->error ( '参数有误' );
		}
		$AuthGroup = D ( 'AuthGroup' );
		if (! $AuthGroup->find ( $gid )) {
			$this->error ( '用户组不存在' );
		}
		if ($AuthGroup->removeFromGroup ( $uid, $gid )) {
			$this->success ( '操作成功' );
		} else {
			$this->error ( '操作失败' );
		}
	}
	
	/**
	 * 将分类添加到用户组 入参:cid,group_id
	 *
	 * @author 朱亚杰 <zhuyajie@topthink.net>
	 */
	public function addToCategory() {
		$cid = I ( 'cid' );
		$gid = I ( 'group_id' );
		if (empty ( $gid )) {
			$this->error ( '参数有误' );
		}
		$AuthGroup = D ( 'AuthGroup' );
		if (! $AuthGroup->find ( $gid )) {
			$this->error ( '用户组不存在' );
		}
		if ($cid && ! $AuthGroup->checkCategoryId ( $cid )) {
			$this->error ( $AuthGroup->error );
		}
		if ($AuthGroup->addToCategory ( $gid, $cid )) {
			$this->success ( '操作成功' );
		} else {
			$this->error ( '操作失败' );
		}
	}
	
	/**
	 * 将模型添加到用户组 入参:mid,group_id
	 *
	 * @author 朱亚杰 <xcoolcc@gmail.com>
	 */
	public function addToModel() {
		$mid = I ( 'id' );
		$gid = I ( 'get.group_id' );
		if (empty ( $gid )) {
			$this->error ( '参数有误' );
		}
		$AuthGroup = D ( 'AuthGroup' );
		if (! $AuthGroup->find ( $gid )) {
			$this->error ( '用户组不存在' );
		}
		if ($mid && ! $AuthGroup->checkModelId ( $mid )) {
			$this->error ( $AuthGroup->error );
		}
		if ($AuthGroup->addToModel ( $gid, $mid )) {
			$this->success ( '操作成功' );
		} else {
			$this->error ( '操作失败' );
		}
	}
}
