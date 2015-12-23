<?php

namespace Addons\RealPrize;
use Common\Controller\Addon;

/**
 * 实物奖励插件
 * @author aManx
 */

    class RealPrizeAddon extends Addon{

        public $info = array(
            'name'=>'RealPrize',
            'title'=>'实物奖励',
            'description'=>'实物奖励设置',
            'status'=>1,
            'author'=>'aManx',
            'version'=>'0.1',
            'has_adminlist'=>1,
            'type'=>1         
        );

	public function install() {
		$install_sql = './Addons/RealPrize/install.sql';
		if (file_exists ( $install_sql )) {
			execute_sql_file ( $install_sql );
		}
		return true;
	}
	public function uninstall() {
		$uninstall_sql = './Addons/RealPrize/uninstall.sql';
		if (file_exists ( $uninstall_sql )) {
			execute_sql_file ( $uninstall_sql );
		}
		return true;
	}

        //实现的weixin钩子方法
        public function weixin($param){

        }

    }