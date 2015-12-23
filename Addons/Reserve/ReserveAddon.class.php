<?php

namespace Addons\Reserve;
use Common\Controller\Addon;

/**
 * 微预约插件
 * @author 凡星
 */

    class ReserveAddon extends Addon{

        public $info = array(
            'name'=>'Reserve',
            'title'=>'微预约',
            'description'=>'微预约是商家利用微营销平台实现在线预约的一种服务，可以运用于汽车、房产、酒店、医疗、餐饮等一系列行业，给用户的出行办事、购物、消费带来了极大的便利！且操作简单， 响应速度非常快，受到业界的一致好评！',
            'status'=>1,
            'author'=>'凡星',
            'version'=>'0.1',
            'has_adminlist'=>1,
            'type'=>0         
        );

	public function install() {
		$install_sql = './Addons/Reserve/install.sql';
		if (file_exists ( $install_sql )) {
			execute_sql_file ( $install_sql );
		}
		return true;
	}
	public function uninstall() {
		$uninstall_sql = './Addons/Reserve/uninstall.sql';
		if (file_exists ( $uninstall_sql )) {
			execute_sql_file ( $uninstall_sql );
		}
		return true;
	}

        //实现的weixin钩子方法
        public function weixin($param){

        }

    }