<?php

namespace Addons\WishCard\Controller;
use Home\Controller\AddonsController;

class BaseController extends AddonsController{
	function _initialize() {
		parent::_initialize();
		
		$controller = strtolower ( _CONTROLLER );
		$action = strtolower ( _ACTION );
		
		$res ['title'] = '微贺卡';
		$res ['url'] = addons_url ( 'WishCard://WishCard/lists' );
		$res ['class'] = $controller == 'wishcard' ? 'current' : '';
		$nav [] = $res;

		$res ['title'] = '祝福语管理';
		$res ['url'] = addons_url ( 'WishCard://WishCardContent/lists' );
		$res ['class'] = ($controller == 'wishcardcontent' || $controller == 'wishcardcontentcate') ? 'current' : '';
		$nav [] = $res;

		$this->assign ( 'nav', $nav );
		$normal_tips = "分享制作网址：".addons_url ( 'WishCard://Wap/card_type',array('token'=>get_token()));	
		$this->assign ( 'normal_tips', $normal_tips );
		// 定义贺卡模板路径常量
		define ( 'CUSTOM_TEMPLATE_PATH', ONETHINK_ADDON_PATH . 'WishCard/View/default/Template');
	}
	// 获取目录下的所有模板
	//cateDirName 为空代表获取所有模板 不分类别
	public function _getTemplateByDir($cateDirName = "") {
		$dir = ONETHINK_ADDON_PATH . _ADDONS . '/View/default/Template';
		$url = SITE_URL . '/Addons/' . _ADDONS . '/View/default/Template';
		
		$dirObj = opendir ( $dir );
		if($cateDirName==""){
			while ( $file = readdir ( $dirObj ) ) {
				if ($file === '.' || $file == '..' || $file == '.svn' || is_file ( $dir . '/' . $file ))
					continue;
				$cateRes['file'] = $file;
				if (file_exists ( $dir .'/' . $file . '/info.php' )) {
					$info = require_once $dir . '/' . $file . '/info.php';
					$cateRes = array_merge ( $cateRes, $info );
				}
				$cateFile[] = $cateRes;
			}
		}else{
			$cateRes['file'] = $cateDirName;
			if (file_exists ( $dir .'/' . $cateDirName . '/info.php' )) {
					$info = require_once $dir . '/' . $cateDirName . '/info.php';
					$cateRes = array_merge ( $cateRes, $info );
				}
			$cateFile[] = $cateRes;
		}
		foreach($cateFile as $cate){
			$dirObj = opendir ( $dir.'/'.$cate['file'] );
			while ( $file = readdir ( $dirObj ) ) {
				if ($file === '.' || $file == '..' || $file == '.svn' || is_file ( $dir . '/'.$cate['file']. '/' . $file ))
					continue;
				
				$tempList [] = $this -> _getTemplateInfo($cate['file'],$file);
				
			}
		}
		closedir ( $dir );
		//dump ( $cateFile );
		//dump ( $tempList );
		$data['tempListCate'] = $cateFile;
		$data['tempList'] = $tempList;
		return $data;
	}
	//获取指定贺卡模板的信息
	public function _getTemplateInfo($cate,$file){
		$dir = ONETHINK_ADDON_PATH . _ADDONS . '/View/default/Template';
		$url = SITE_URL . '/Addons/' . _ADDONS . '/View/default/Template';
		$res ['dirName'] = $res ['title'] = $file;
		// 获取配置文件
		if (file_exists ( $dir . '/'.$cate.'/' . $file . '/info.php' )) {
			$info = require_once $dir . '/'.$cate.'/' . $file . '/info.php';
			$res = array_merge ( $res, $info );
		}
		
		// 获取效果图
		if (file_exists ( $dir . '/'.$cate.'/' . $file . '/info.php' )) {
			$res ['icon'] = $url.'/'.$cate.'/'. $file . '/icon.png';
		} else {
			$res['icon'] = ADDON_PUBLIC_PATH . '/default.png';
		}
		//分类目录
		$res['cate'] = $cate;
		return $res;
	}
}
