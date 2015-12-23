<?php

namespace Addons\Sms;
use Common\Controller\Addon;

/**
 * 短信服务插件
 * @author jacy
 */

    class SmsAddon extends Addon{

        public $info = array(
            'name'=>'Sms',
            'title'=>'短信服务',
            'description'=>'短信服务，短信验证，短信发送',
            'status'=>1,
            'author'=>'jacy',
            'version'=>'0.1',
            'has_adminlist'=>0
        );

	public function install() {
		$install_sql = './Addons/Sms/install.sql';
		if (file_exists ( $install_sql )) {
			execute_sql_file ( $install_sql );
		}
		return true;
	}
	public function uninstall() {
		$uninstall_sql = './Addons/Sms/uninstall.sql';
		if (file_exists ( $uninstall_sql )) {
			execute_sql_file ( $uninstall_sql );
		}
		return true;
	}

        //实现的weixin钩子方法
        public function weixin($param){

        }

    }