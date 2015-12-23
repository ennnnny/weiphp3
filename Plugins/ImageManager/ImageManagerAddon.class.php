<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: yangweijie <yangweijiester@gmail.com> <code-tech.diandian.com>
// +----------------------------------------------------------------------
namespace Plugins\ImageManager;

use Common\Controller\Plugin;

/**
 * 图片管理插件
 * @author wzh
 */

    class ImageManagerAddon extends Plugin{

        public $info = array(
            'name'=>'ImageManager',
            'title'=>'图片管理',
            'description'=>'图片管理，快速选择已上传图片到封面',
            'status'=>1,
            'author'=>'wzh',
            'version'=>'0.1',
        );

        public function install(){
            return true;
        }

        public function uninstall(){
            return true;
        }

        //实现的pageFooter钩子方法
        public function pageFooter(){
            $this->assign("addon_path", $this->addon_path);
            $this->display("widget");
        }
		
       
    }