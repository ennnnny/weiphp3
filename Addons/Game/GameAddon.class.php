<?php

namespace Addons\Game;
use Common\Controller\Addon;

/**
 * 互动游戏插件
 * @author 凡星
 */

    class GameAddon extends Addon{

        public $info = array(
            'name'=>'Game',
            'title'=>'互动游戏',
            'description'=>'这是一个临时描述',
            'status'=>1,
            'author'=>'凡星',
            'version'=>'0.1',
            'has_adminlist'=>0,
            'type'=>1         
        );

	public function install() {
		$install_sql = './Addons/Game/install.sql';
		if (file_exists ( $install_sql )) {
			execute_sql_file ( $install_sql );
		}
		return true;
	}
	public function uninstall() {
		$uninstall_sql = './Addons/Game/uninstall.sql';
		if (file_exists ( $uninstall_sql )) {
			execute_sql_file ( $uninstall_sql );
		}
		return true;
	}

        //实现的weixin钩子方法
        public function weixin($param){

        }

    }