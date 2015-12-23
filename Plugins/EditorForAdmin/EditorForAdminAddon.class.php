<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: yangweijie <yangweijiester@gmail.com> <code-tech.diandian.com>
// +----------------------------------------------------------------------
namespace Plugins\EditorForAdmin;

use Common\Controller\Plugin;

/**
 * 编辑器插件
 * 
 * @author yangweijie <yangweijiester@gmail.com>
 */
class EditorForAdminAddon extends Plugin {
	public $info = array (
			'name' => 'EditorForAdmin',
			'title' => '后台编辑器',
			'description' => '用于增强整站长文本的输入和显示',
			'status' => 1,
			'author' => 'thinkphp',
			'version' => '0.2' 
	);
	public function install() {
		return true;
	}
	public function uninstall() {
		return true;
	}
	
	/**
	 * 编辑器挂载的后台文档模型文章内容钩子
	 * 
	 * @param
	 *        	array('name'=>'表单name','value'=>'表单对应的值')
	 */
	public function adminArticleEdit($data) {
		$data ['is_mult'] = intval ( $data ['is_mult'] ); // 默认不传时为0
		$this->assign ( 'addons_data', $data );
		$this->assign ( 'addons_config', $this->getConfig () );
		$this->assign('styleUrl',addons_url('EditorForAdmin://Style/get_article_style'));
		$this->display ( 'content' );
	}
	/**
	 * 编辑器挂载的后台文档模型文章内容钩子
	 * 
	 * @param
	 *        	array('name'=>'表单name','value'=>'表单对应的值')
	 */
	public function uploadImg($data) {
		$this->assign ( 'addons_data', $data );
		$this->assign ( 'addons_config', $this->getConfig () );
		$this->display ( 'uploadBtn' );
	}
		
}
