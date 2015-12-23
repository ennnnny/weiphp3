<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 凡星
// +----------------------------------------------------------------------
namespace Admin\Controller;

/**
 * 在线应用商店
 */
class SucaiController extends AdminController {
	
	function chooseTemplate(){
		$Model = D('SucaiTemplate');
		$templateList = $this->_getSucaiTemplate();
		$uid = I('uid');
		$userTemplateList = $Model->where('uid='.$uid)->select();
		if(!empty($userTemplateList)){
			foreach($templateList as &$vo){
				$addons = $vo['addons'];
				$template = $vo['template'];
				foreach($userTemplateList as $vou){
					if($vou['addons']==$addons && $vou['template']==$template){
						$vo['isUse'] = true;
						//dump('aaaaaaaaaaaaaaaaaaa');
					}
				}
			}
		}
		//dump($templateList);
		$this -> assign('templateList',$templateList);
		$this->display();
	}
	function _getSucaiTemplate($addons=''){
		if(empty($addons)){
			$dir = SITE_PATH . '/SucaiTemplate';
			$dirObj = opendir ( $dir );
			while ( $file = readdir ( $dirObj ) ) {
				if ($file === '.' || $file == '..' || $file == '.svn' || is_file ( $dir . '/' . $file ))
					continue;
				
				$subDir = $dir.'/'.$file;
				$subDirObj = opendir ( $subDir );
				while ( $subFile = readdir ( $subDirObj ) ) {
					if ($subFile === '.' || $subFile == '..' || $subFile == '.svn' || is_file ( $subDir . '/' . $subFile ))
						continue;
					// 获取配置文件
					$res['addons'] = $file;
					$res['template'] = $subFile;
					if (file_exists ( $subDir . '/' . $subFile . '/info.php' )) {
						$info = require_once $subDir . '/' . $subFile . '/info.php';
						$res = array_merge ( $res, $info );
					}
					
					// 获取效果图
					if (file_exists ( $subDir . '/' . $subFile . '.png' )) {
						$res ['icon'] = __ROOT__ . '/SucaiTemplate/'.$file.'/'.$subFile.'.png';
					} else {
						$res ['icon'] = __ROOT__ . '/Public/Home/images/no_template_icon.png';
					}
					$templateList [] = $res; 
					unset ( $res );
				}
			}
			closedir ( $dir );
			//dump($templateList);
			return $templateList;
		}else{
			$dir = SITE_PATH . '/SucaiTemplate/'.$addons;
			$dirObj = opendir ( $dir );
			
			while ( $subFile = readdir ( $dirObj ) ) {
				if ($subFile === '.' || $subFile == '..' || $subFile == '.svn' || is_file ( $dir . '/' . $subFile ))
					continue;
				// 获取配置文件
				$res['addons'] = $addons;
				$res['template'] = $subFile;
				if (file_exists ( $dir . '/' . $subFile . '/info.php' )) {
					$info = require_once $dir . '/' . $subFile . '/info.php';
					$res = array_merge ( $res, $info );
				}
				
				// 获取效果图
				if (file_exists ( $dir . '/' . $subFile . '.png' )) {
					$res ['icon'] = __ROOT__ . '/SucaiTemplate/'.$file.'/'.$subFile.'.png';
				} else {
					$res ['icon'] = __ROOT__ . '/Public/Home/images/no_template_icon.png';
				}
				$templateList [] = $res; 
				unset ( $res );
			}
			
			closedir ( $dir );
			//dump(count($templateList));
			return $templateList;
		}
		
	}
	
	//授权操作
	function useTemplate(){
		$data['addons'] = I('addons');
		$data['template'] = I('template');
		$data['uid'] = I('uid');
		$mInfo = D('Public')->where('uid='.$data['uid'])->find();
		$data['token'] = $mInfo['token'];
		if(D('SucaiTemplate')->add($data)){
			$this->success ( '授权成功' );
		}else{
			$this->error ( '授权失败！' );
		}
	}
	//取消授权
	function cancelTemplate(){
		$data['addons'] = I('addons');
		$data['template'] = I('template');
		$data['uid'] = I('uid');
		$mInfo = D('Public')->where('uid='.$data['uid'])->find();
		$data['token'] = $mInfo['token'];
		
		if(D('SucaiTemplate')->where($data)->delete()){
			$this->success ( '取消授权成功' );
		}else{
			$this->error ( '取消授权失败！' );
		}
	}
	
}
