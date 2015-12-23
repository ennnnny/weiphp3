<?php

namespace Addons\BusinessCard;
use Common\Controller\Addon;

/**
 * 微名片插件
 * @author 凡星
 */

    class BusinessCardAddon extends Addon{

        public $info = array(
            'name'=>'BusinessCard',
            'title'=>'微名片',
            'description'=>'',
            'status'=>1,
            'author'=>'凡星',
            'version'=>'0.1',
            'has_adminlist'=>1
        );

	public function install() {
		$install_sql = './Addons/BusinessCard/install.sql';
		if (file_exists ( $install_sql )) {
			execute_sql_file ( $install_sql );
		}
		return true;
	}
	public function uninstall() {
		$uninstall_sql = './Addons/BusinessCard/uninstall.sql';
		if (file_exists ( $uninstall_sql )) {
			execute_sql_file ( $uninstall_sql );
		}
		return true;
	}

        //实现的weixin钩子方法
        public function weixin($param){

        }

    }