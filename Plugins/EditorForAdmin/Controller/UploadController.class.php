<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: huajie <banhuajie@163.com>
// +----------------------------------------------------------------------
namespace Plugins\EditorForAdmin\Controller;

use Home\Controller\AddonsController;
use Think\Upload;

class UploadController extends AddonsController {
	public $uploader = null;
	
	/* 上传图片 */
	public function upload() {
		session('upload_error', null);
		/* 上传配置 */
		$setting = C ( 'EDITOR_UPLOAD' );
		$setting ['callback'] = array (
				$this,
				'isFile' 
		);

		/* 调用文件上传组件上传文件 */
		$this->uploader = new Upload ( $setting, C ( 'PICTURE_UPLOAD_DRIVER' ) );
		$info = $this->uploader->upload ( $_FILES );

		if ($info) {
			$dao = D ( 'Home/Picture' );
			foreach ( $info as &$file ) {
				$file ['rootpath'] = __ROOT__ . ltrim ( $setting ['rootPath'], "." );
				
				/* 已经存在文件记录 */
				if (isset ( $file ['id'] ) && is_numeric ( $file ['id'] )) {
					$file ['path'] = __ROOT__ . ltrim ( $file ['path'], "." );
					continue;
				}
				
				/* 记录文件信息 */
				$file ['path'] = __ROOT__ . ltrim ( $setting ['rootPath'], "." ) . $file ['savepath'] . $file ['savename'];
				$file ['status'] = 1;
				$file ['create_time'] = NOW_TIME;

				if ($dao->create ( $file ) && ($id = $dao->add ())) {
					$file ['id'] = $id;
				}
			}
		}
		session('upload_error', $this->uploader->getError());
		return $info;
	}
	
	// keditor编辑器上传图片处理
	public function ke_upimg() {
		/* 返回标准数据 */
		$return = array (
				'error' => 0,
				'info' => '上传成功',
				'data' => '' 
		);
		$info = $this->upload ();
		$img = $info ['imgFile'] ['path'];
		/* 记录附件信息 */
		if ($img) {
			$return ['id'] = $info ['imgFile'] ['id'];
			$return ['url'] = $img;
			unset ( $return ['info'], $return ['data'] );
		} else {
			$return ['error'] = 1;
			$return['message']   = session('upload_error');
		}
		
		/* 返回JSON数据 */
		exit ( json_encode ( $return ) );
	}
	
	// ueditor编辑器上传图片处理
	public function ue_upimg() {
		$info = $this->upload ();
		$img = $info ['imgFile'] ['path'];
		
		$return = array ();
		$return ['id'] = $info ['imgFile'] ['id'];
		$return ['url'] = $img;
		$title = htmlspecialchars ( $_POST ['pictitle'], ENT_QUOTES );
		$return ['title'] = $title;
		$return ['original'] = $info ['imgFile'] ['name'];
		$return['state'] = ($img)? 'SUCCESS' : session('upload_error');
		/* 返回JSON数据 */
		$this->ajaxReturn ( $return );
	}
	// ueditor编辑器在线管理处理
	// 扫描目录下（包括子文件夹）的图片并返回
	public function ue_mgimg() {
		$setting = C ( 'EDITOR_UPLOAD' );
		$imgRootPath = $setting ['rootPath'];
		$paths = array (
				'' 
		);
		$files = array ();
		$files = $this->getfiles ( $imgRootPath );
		if (! count ( $files ))
			return;
		rsort ( $files, SORT_STRING );
		$str = implode ( '|', $files );
		echo $str;
	}
	
	/**
	 * 遍历获取目录下的指定类型的文件
	 *
	 * @param
	 *        	$path
	 * @param array $files        	
	 * @return array
	 */
	function getfiles($path, &$files = array()) {
		if (! is_dir ( $path ))
			return null;
		$handle = opendir ( $path );
		while ( false !== ($file = readdir ( $handle )) ) {
			if ($file != '.' && $file != '..') {
				$path2 = $path . '/' . $file;
				if (is_dir ( $path2 )) {
					$this->getfiles ( $path2, $files );
				} else {
					if (preg_match ( "/\.(gif|jpeg|jpg|png|bmp)$/i", $file )) {
						// $files[] = '/dev/'.$path2;
						$files [] = __ROOT__ . '/' . ltrim ( ltrim ( $path2, '.' ), '/' );
					}
				}
			}
		}
		return $files;
	}
	
	/**
	 * 检测当前上传的文件是否已经存在
	 *
	 * @param array $file
	 *        	文件上传数组
	 * @return boolean 文件信息， false - 不存在该文件
	 */
	public function isFile($file) {
		if (empty ( $file ['md5'] )) {
			throw new \Exception ( '缺少参数:md5' );
		}
		/* 查找文件 */
		$map = array (
				'md5' => $file ['md5'],
				'sha1' => $file ['sha1'] 
		);
		return M ( 'Picture' )->field ( true )->where ( $map )->find ();
	}
}
