<?php

namespace Common\Model;

use Think\Model;

/**
 * 插件配置操作集成
 */
class AddonStatusModel extends Model {
	protected $tableName = 'addons';
	/**
	 * 保存配置
	 */
	function set($addon, $status) {
		$map ['token'] = get_token ();
		if (empty ( $map ['token'] )) {
			return false;
		}
		$info = get_token_appinfo ( $map ['token'] );
		if (! $info) {
			$map ['uid'] = session ( 'mid' );
			$addon_status [$addon] = intval ( $status );
			$map ['addon_status'] = json_encode ( $addon_status );
			$info ['id'] = M ( 'public' )->add ( $map );
		} else {
			$addon_status = json_decode ( $info ['addon_status'], true );
			$addon_status [$addon] = intval ( $status );
			M ( 'public' )->where ( $map )->setField ( 'addon_status', json_encode ( $addon_status ) );
		}
		D ( 'Common/Public' )->clear ( $info ['id'] );
		// dump(M ( 'public' )->getLastSql());exit;
		return $info ['id'];
	}
	/**
	 * 获取插件配置
	 * 获取的优先级：当前用户插件权限》当前公众号设置》后台默认配置》安装文件上的配置
	 */
	function getList($is_admin = false) {
		// 当前公众号的设置
		$map ['token'] = get_token ();
		if (empty ( $map ['token'] )) {
			return array ();
		}
		
		$info = get_token_appinfo ( $map ['token'] );
		$token_status = json_decode ( $info ['addon_status'], true );
		
		// 等级权限
		if ($info ['group_id']) {
			$map2 ['id'] = $info ['group_id'];
			$addon_ids = M ( 'public_group' )->where ( $map2 )->getField ( 'addon_status' );
			if ($addon_ids) {
				$map3 ['id'] = array (
						'in',
						$addon_ids 
				);
				$addons = M ( 'addons' )->where ( $map3 )->field ( '`name`' )->select ();
				foreach ( $addons as $a ) {
					$token_status [$a ['name']] = '-1';
				}
			}
		}
		
		// 对当前用户的权限进行判断
		if ($is_admin) {
			unset ( $map );
			$map ['uid'] = get_mid ();
			$map ['mp_id'] = $info ['id'];
			
			$addon_ids = M ( 'public_link' )->where ( $map )->getField ( 'addon_status' );
			if ($addon_ids) {
				$map3 ['id'] = array (
						'in',
						$addon_ids 
				);
				$addons = M ( 'addons' )->where ( $map3 )->field ( '`name`' )->select ();
				foreach ( $addons as $a ) {
					$token_status [$a ['name']] = '-1';
				}
			}
		}
		// dump ( $token_status );
		// dump(M ( 'public' )->getLastSql());exit;
		return $token_status;
	}
	
	// 获取当前公众号已授权的插件列表
	function getPublicAddons($mp_id) {
		$info = D ( 'Common/Public' )->getInfo ( $mp_id );
		// 等级权限
		if ($info ['group_id']) {
			$map2 ['id'] = $info ['group_id'];
			$addon_ids = M ( 'public_group' )->where ( $map2 )->getField ( 'addon_status' );
			
			if ($addon_ids) {
				$map ['id'] = array (
						'not in',
						$addon_ids 
				);
			}
		}
		
		$data = M ( 'Addons' )->where ( $map )->order ( 'id DESC' )->select ();
		
		return $data;
	}
}
?>
