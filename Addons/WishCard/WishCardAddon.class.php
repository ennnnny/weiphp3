<?php

namespace Addons\WishCard;
use Common\Controller\Addon;

/**
 * 微贺卡插件
 * @author 凡星
 */

    class WishCardAddon extends Addon{

        public $info = array(
            'name'=>'WishCard',
            'title'=>'微贺卡',
            'description'=>'Diy贺卡 自定贺卡内容 发给好友 后台编辑',
            'status'=>1,
            'author'=>'凡星',
            'version'=>'0.1',
            'has_adminlist'=>1,
            'type'=>1         
        );

	public function install() {
		$install_sql = './Addons/WishCard/install.sql';
		if (file_exists ( $install_sql )) {
			execute_sql_file ( $install_sql );
		}
		return true;
	}
	public function uninstall() {
		$uninstall_sql = './Addons/WishCard/uninstall.sql';
		if (file_exists ( $uninstall_sql )) {
			execute_sql_file ( $uninstall_sql );
		}
		return true;
	}

        //实现的weixin钩子方法
        public function weixin($param){

        }

    }