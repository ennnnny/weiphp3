<?php

namespace Addons\AutoReply;
use Common\Controller\Addon;

/**
 * 自动回复插件
 * @author 凡星
 */

    class AutoReplyAddon extends Addon{

        public $info = array(
            'name'=>'AutoReply',
            'title'=>'自动回复',
            'description'=>'WeiPHP基础功能，能实现配置关键词，用户回复此关键词后自动回复对应的文件，图文，图片信息',
            'status'=>1,
            'author'=>'凡星',
            'version'=>'0.1',
            'has_adminlist'=>1
        );

	public function install() {
		$install_sql = './Addons/AutoReply/install.sql';
		if (file_exists ( $install_sql )) {
			execute_sql_file ( $install_sql );
		}
		return true;
	}
	public function uninstall() {
		$uninstall_sql = './Addons/AutoReply/uninstall.sql';
		if (file_exists ( $uninstall_sql )) {
			execute_sql_file ( $uninstall_sql );
		}
		return true;
	}

        //实现的weixin钩子方法
        public function weixin($param){

        }

    }