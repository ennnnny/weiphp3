<?php

namespace Addons\DeveloperTool;
use Common\Controller\Addon;

/**
 * 开发者工具箱插件
 * @author 凡星
 */

    class DeveloperToolAddon extends Addon{

        public $info = array(
            'name'=>'DeveloperTool',
            'title'=>'开发者工具箱',
            'description'=>'开发者可以用来调试，监控运营系统的参数',
            'status'=>1,
            'author'=>'凡星',
            'version'=>'0.1',
            'has_adminlist'=>1
        );

        public $admin_list = array(
            'model'=>'Example',		//要查的表
			'fields'=>'*',			//要查的字段
			'map'=>'',				//查询条件, 如果需要可以再插件类的构造方法里动态重置这个属性
			'order'=>'id desc',		//排序,
			'list_grid'=>array( 		//这里定义的是除了id序号外的表格里字段显示的表头名和模型一样支持函数和链接
                'cover_id|preview_pic:封面',
                'title:书名',
                'description:描述',
                'update_time|time_format:更新时间',
                'id:操作:[EDIT]|编辑,[DELETE]|删除'
            ),
        );

	public function install() {
		$install_sql = './Addons/DeveloperTool/install.sql';
		if (file_exists ( $install_sql )) {
			execute_sql_file ( $install_sql );
		}
		return true;
	}
	public function uninstall() {
		$uninstall_sql = './Addons/DeveloperTool/uninstall.sql';
		if (file_exists ( $uninstall_sql )) {
			execute_sql_file ( $uninstall_sql );
		}
		return true;
	}


    }