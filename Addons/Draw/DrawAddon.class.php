<?php

namespace Addons\Draw;
use Common\Controller\Addon;

/**
 * 比赛抽奖插件
 * @author 凡星
 */

    class DrawAddon extends Addon{

        public $info = array(
            'name'=>'Draw',
            'title'=>'比赛抽奖',
            'description'=>'功能主要有奖品设置，抽奖配置和抽奖统计',
            'status'=>1,
            'author'=>'凡星',
            'version'=>'0.1',
            'has_adminlist'=>1,
            'type'=>1         
        );

	public function install() {
		$install_sql = './Addons/Draw/install.sql';
		if (file_exists ( $install_sql )) {
			execute_sql_file ( $install_sql );
		}
		return true;
	}
	public function uninstall() {
		$uninstall_sql = './Addons/Draw/uninstall.sql';
		if (file_exists ( $uninstall_sql )) {
			execute_sql_file ( $uninstall_sql );
		}
		return true;
	}

        //实现的weixin钩子方法
        public function weixin($param){

        }

    }