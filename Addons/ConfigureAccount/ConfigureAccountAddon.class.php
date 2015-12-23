<?php

namespace Addons\ConfigureAccount;
use Common\Controller\Addon;

/**
 * 帐号配置插件
 * @author manx
 */

    class ConfigureAccountAddon extends Addon{

        public $info = array(
            'name'=>'ConfigureAccount',
            'title'=>'帐号配置',
            'description'=>'配置共众帐号的信息',
            'status'=>0,
            'author'=>'manx',
            'version'=>'0.1',
            'has_adminlist'=>1,
            'type'=>1         
        );

	public function install() {
		$install_sql = './Addons/ConfigureAccount/install.sql';
		if (file_exists ( $install_sql )) {
			execute_sql_file ( $install_sql );
		}
		return true;
	}
	public function uninstall() {
		$uninstall_sql = './Addons/ConfigureAccount/uninstall.sql';
		if (file_exists ( $uninstall_sql )) {
			execute_sql_file ( $uninstall_sql );
		}
		return true;
	}

        //实现的weixin钩子方法
        public function weixin($param){

        }

    }