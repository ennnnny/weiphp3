<?php

namespace Addons\Ask;

use Common\Controller\Addon;

/**
 * 抢答插件
 *
 * @author 凡星
 */
class AskAddon extends Addon {
	public $info = array (
			'name' => 'Ask',
			'title' => '抢答',
			'description' => '用于电视互动答题',
			'status' => 1,
			'author' => '凡星',
			'version' => '0.1',
			'has_adminlist' => 1,
			'type' => 1 
	);
	public $personal = array (
			'url' => 'Ask://Ask/personal',
			'title' => '我的抢答',
			'icon' => '',
			'group' => '我的互动' 
	);
	public function install() {
		$install_sql = './Addons/Ask/install.sql';
		if (file_exists ( $install_sql )) {
			execute_sql_file ( $install_sql );
		}
		return true;
	}
	public function uninstall() {
		$uninstall_sql = './Addons/Ask/uninstall.sql';
		if (file_exists ( $uninstall_sql )) {
			execute_sql_file ( $uninstall_sql );
		}
		return true;
	}
	
	// 实现的weixin钩子方法
	public function weixin($param) {
	}
}