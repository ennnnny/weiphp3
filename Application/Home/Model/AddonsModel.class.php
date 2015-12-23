<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: yangweijie <yangweijiester@gmail.com> <code-tech.diandian.com>
// +----------------------------------------------------------------------
namespace Home\Model;

use Think\Model;

/**
 * 插件模型
 *
 * @author yangweijie <yangweijiester@gmail.com>
 */
class AddonsModel extends Model {
	protected $tableName = 'addons';
	
	/**
	 * 获取微信插件列表
	 *
	 * @param string $addon_dir        	
	 */
	public function getWeixinList($isAll = false, $token_status = array(), $is_admin = false, $is_show = false) {
		$list = $this->getList ();
		
		if ($is_show) {
			foreach ( $list as $n => $vo ) {
				if ($vo ['is_show'] != 1)
					unset ( $list [$n] );
			}
		}
		
		$isAll || $token_status = D ( 'Common/AddonStatus' )->getList ( $is_admin );
		foreach ( $list as $addon ) {
			if (! $isAll && isset ( $token_status [$addon ['name']] ) && $token_status [$addon ['name']] < 1)
				continue;
			
			if ($addon ['has_adminlist']) {
				$addon ['addons_url'] = addons_url ( $addon ['name'] . '://' . $addon ['name'] . '/lists' );
			} elseif (file_exists ( ONETHINK_ADDON_PATH . $addon ['name'] . '/config.php' )) {
				$addon ['addons_url'] = addons_url ( $addon ['name'] . '://' . $addon ['name'] . '/config' );
			} else {
				$addon ['addons_url'] = addons_url ( $addon ['name'] . '://' . $addon ['name'] . '/nulldeal' );
			}
			
			$addons [$addon ['name']] = $addon;
		}
		
		return $addons;
	}
	function getList($update = false) {
		$key = "Home_Addons_getList_" . get_token ();
		$list = S ( $key );
		if ($list === false || $update) {
			$map ['status'] = 1;
			$list_res = $this->where ( $map )->select ();
			foreach ( $list_res as $vo ) {
				$list [$vo ['name']] = $vo;
			}
			S ( $key, $list );
		}
		
		return $list;
	}
	function getInfoByName($name, $update = false) {
		$list = $this->getList ( $update );
		return $list [$name];
	}
}
