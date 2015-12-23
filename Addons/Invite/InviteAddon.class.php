<?php

namespace Addons\Invite;
use Common\Controller\Addon;

/**
 * 微邀约插件
 * @author 无名
 */

    class InviteAddon extends Addon{

        public $info = array(
            'name'=>'Invite',
            'title'=>'微邀约',
            'description'=>'微邀约适合各行各业，可用于会议邀约、活动邀约，同时实现微信报名人数自动统计等功能。',
            'status'=>1,
            'author'=>'无名',
            'version'=>'0.1',
            'has_adminlist'=>1,
            'type'=>1         
        );

	public function install() {
		$install_sql = './Addons/Invite/install.sql';
		if (file_exists ( $install_sql )) {
			execute_sql_file ( $install_sql );
		}
		return true;
	}
	public function uninstall() {
		$uninstall_sql = './Addons/Invite/uninstall.sql';
		if (file_exists ( $uninstall_sql )) {
			execute_sql_file ( $uninstall_sql );
		}
		return true;
	}

        //实现的weixin钩子方法
        public function weixin($param){

        }

    }