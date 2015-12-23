<?php

namespace Addons\CardVouchers;

use Common\Controller\Addon;

/**
 * 卡券插件
 *
 * @author 凡星
 */
class CardVouchersAddon extends Addon {
	public $info = array (
			'name' => 'CardVouchers',
			'title' => '微信卡券',
			'description' => '在微信平台创建卡券后，可配置到这里生成素材提供用户领取，它既支持电视台自己公众号发布的卡券，也支持由商家公众号发布的卡券',
			'status' => 1,
			'author' => '凡星',
			'version' => '0.1',
			'has_adminlist' => 1,
			'type' => 1 
	);
	public function install() {
		$install_sql = './Addons/CardVouchers/install.sql';
		if (file_exists ( $install_sql )) {
			execute_sql_file ( $install_sql );
		}
		return true;
	}
	public function uninstall() {
		$uninstall_sql = './Addons/CardVouchers/uninstall.sql';
		if (file_exists ( $uninstall_sql )) {
			execute_sql_file ( $uninstall_sql );
		}
		return true;
	}
	
	// 实现的weixin钩子方法
	public function weixin($param) {
	}
}