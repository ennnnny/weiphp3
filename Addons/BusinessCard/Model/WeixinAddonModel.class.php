<?php

namespace Addons\BusinessCard\Model;

use Home\Model\WeixinModel;

/**
 * BusinessCard的微信模型
 */
class WeixinAddonModel extends WeixinModel {
	function reply($dataArr, $keywordArr = array()) {
		$config = getAddonConfig ( 'BusinessCard' ); // 获取后台插件的配置参数
			                                             // dump($config);
	}
	/*
	 * 个人中心里的链接配置参数
	 * 只配置一个链接时 personal是一维数组 如 array ( 'url' => '','title' => '我的XX','icon' => '', 'group' => '', 'new_count' => 0);
	 * 如果要配置多个链接是personal是二维数组 如
	 * array(
	 * array ( 'url' => '','title' => '我的XX','icon' => '', 'group' => '', 'new_count' => 0),
	 * array ( 'url' => '','title' => '我的XX','icon' => '', 'group' => '', 'new_count' => 0),
	 * array ( 'url' => '','title' => '我的XX','icon' => '', 'group' => '', 'new_count' => 0)
	 * );
	 */
	function personal() {
		$param ['uid'] = $GLOBALS ['mid'];
		$param ['publicid'] = get_token_appinfo ( '', 'id' );
		
		$links = array (
				array (
						'url' => addons_url ( 'BusinessCard://Wap/detail', $param ), // 链接地址
						'title' => '我的名片', // 链接名称
						'icon' => '', // 图标，选填
						'group' => '我的互动', // 在个人中心里的分组名，选填
						'new_count' => 0 
				),
				array (
						'url' => addons_url ( 'BusinessCard://Wap/collected', $param ), // 链接地址
						'title' => '我收藏的名片', // 链接名称
						'icon' => '', // 图标，选填
						'group' => '我的互动', // 在个人中心里的分组名，选填
						'new_count' => 0 
				),
				array (
						'url' => addons_url ( 'BusinessCard://Wap/collecting', $param ), // 链接地址
						'title' => '收藏我的名片', // 链接名称
						'icon' => '', // 图标，选填
						'group' => '我的互动', // 在个人中心里的分组名，选填
						'new_count' => 0 
				) 
		);
		
		// new_count 为新消息的数目，如果大于0，会在个人空间里的链接旁边显示新消息数目
		// 下面实现获取new_count的功能
		
		return $links;
	}
}
        	