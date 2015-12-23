<?php

namespace Addons\Shop\Controller;

use Addons\Shop\Controller\BaseController;

class TemplateController extends BaseController {
	function _initialize() {
		parent::_initialize ();
		
		// 使用提示
		$param ['shop_id'] = $this->shop_id;
		$param ['publicid'] = get_token_appinfo ( '', 'id' );
		$normal_tips = '点击选中下面模板即可实时切换模板，请慎重点击。选择后可点击<a target="_blank" href="' . addons_url ( 'Shop://Wap/index', $param ) . '">这里</a>进行预览';
		$this->assign ( 'normal_tips', $normal_tips );
	}
	
	// 模板列表
	function lists() {
		$shop = D ( 'Shop' )->getInfo ( $this->shop_id );
		$this->_getTemplateByDir ( $shop ['template'] );
		
		$this->display ( 'index' );
	}
	
	// 保存切换的模板
	function save() {
		$save ['template'] = I ( 'template' );
		D ( 'Shop' )->updateById ( $this->shop_id, $save );
		echo 1;
	}
	
	// 获取目录下的所有模板
	function _getTemplateByDir($default = '') {
		empty ( $default ) && $default = 'jd';
		$dir = ONETHINK_ADDON_PATH . '/Shop/View/default/Wap/Template/';
		
		$dirObj = opendir ( $dir );
		while ( $file = readdir ( $dirObj ) ) {
			if ($file === '.' || $file == '..' || $file == '.svn' || is_file ( $dir . '/' . $file ))
				continue;
			
			$res ['dirName'] = $res ['title'] = $file;
			
			// 获取配置文件
			if (file_exists ( $dir . '/' . $file . '/info.php' )) {
				$info = require_once $dir . '/' . $file . '/info.php';
				$res = array_merge ( $res, $info );
			}
			
			// 获取效果图
			if (file_exists ( $dir . '/' . $file . '/icon.png' )) {
				$res ['icon'] = __ROOT__ . '/Addons/Shop/View/default/Wap/Template/' . $file . '/icon.png';
			} else {
				$res ['icon'] = ADDON_PUBLIC_PATH . '/default.png';
			}
			
			// 默认选中
			if ($default == $file) {
				$res ['class'] = 'selected';
				$res ['checked'] = 'checked="checked"';
			}
			
			$tempList [] = $res;
			unset ( $res );
		}
		closedir ( $dir );
		
		// dump ( $tempList );
		
		$this->assign ( 'tempList', $tempList );
	}
}
