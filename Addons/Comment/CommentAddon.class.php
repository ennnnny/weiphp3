<?php

namespace Addons\Comment;
use Common\Controller\Addon;

/**
 * 评论互动插件
 * @author 凡星
 */

    class CommentAddon extends Addon{

        public $info = array(
            'name'=>'Comment',
            'title'=>'评论互动',
            'description'=>'可放到手机界面里进行评论，显示支持弹屏方式',
            'status'=>1,
            'author'=>'凡星',
            'version'=>'0.1',
            'has_adminlist'=>1,
            'type'=>1         
        );

	public function install() {
		$install_sql = './Addons/Comment/install.sql';
		if (file_exists ( $install_sql )) {
			execute_sql_file ( $install_sql );
		}
		return true;
	}
	public function uninstall() {
		$uninstall_sql = './Addons/Comment/uninstall.sql';
		if (file_exists ( $uninstall_sql )) {
			execute_sql_file ( $uninstall_sql );
		}
		return true;
	}

        //实现的weixin钩子方法
        public function weixin($param){

        }

    }