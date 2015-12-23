<?php

namespace Addons\Guess;
use Common\Controller\Addon;

/**
 * 竞猜插件
 * @author 无名
 */

    class GuessAddon extends Addon{

        public $info = array(
            'name'=>'Guess',
            'title'=>'竞猜',
            'description'=>'节目竞猜 有奖竞猜 竞猜项目配置',
            'status'=>1,
            'author'=>'无名',
            'version'=>'0.1',
            'has_adminlist'=>1,
            'type'=>1         
        );

	public function install() {
		$install_sql = './Addons/Guess/install.sql';
		if (file_exists ( $install_sql )) {
			execute_sql_file ( $install_sql );
		}
		return true;
	}
	public function uninstall() {
		$uninstall_sql = './Addons/Guess/uninstall.sql';
		if (file_exists ( $uninstall_sql )) {
			execute_sql_file ( $uninstall_sql );
		}
		return true;
	}

        //实现的weixin钩子方法
        public function weixin($param){

        }

    }