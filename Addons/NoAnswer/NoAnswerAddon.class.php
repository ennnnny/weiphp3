<?php

namespace Addons\NoAnswer;

use Common\Controller\Addon;

/**
 * 没回答的回复插件
 *
 * @author 凡星
 */
class NoAnswerAddon extends Addon {
	public $custom_config = 'config.html';
	public $info = array (
			'name' => 'NoAnswer',
			'title' => '没回答的回复',
			'description' => '当用户提供的内容或者关键词系统无关识别回复时，自动把当前配置的内容回复给用户',
			'status' => 1,
			'author' => '凡星',
			'version' => '0.1',
			'has_adminlist' => 0 
	);
	public function install() {
		return true;
	}
	public function uninstall() {
		return true;
	}
	
	// 实现的weixin钩子方法
	public function weixin($param) {
	}
}