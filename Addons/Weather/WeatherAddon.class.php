<?php

namespace Addons\Weather;
use Common\Controller\Addon;

/**
 * 天气预报插件
 * @author Riek
 */

    class WeatherAddon extends Addon{

        public $info = array(
            'name'=>'Weather',
            'title'=>'天气预报',
            'description'=>'天气预报！',
            'status'=>1,
            'author'=>'Riek',
            'version'=>'0.1',
            'has_adminlist'=>1,
            'type'=>1         
        );

	public function install() {
		$install_sql = './Addons/Weather/install.sql';
		if (file_exists ( $install_sql )) {
			execute_sql_file ( $install_sql );
		}
		return true;
	}
	public function uninstall() {
		$uninstall_sql = './Addons/Weather/uninstall.sql';
		if (file_exists ( $uninstall_sql )) {
			execute_sql_file ( $uninstall_sql );
		}
		return true;
	}

        //实现的weixin钩子方法
        public function weixin($param){

        }

    }