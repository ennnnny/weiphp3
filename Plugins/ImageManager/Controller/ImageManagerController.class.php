<?php
namespace Plugins\ImageManager\Controller;

use Home\Controller\AddonsController;

class ImageManagerController extends AddonsController {
    public function _initialize(){
        parent::_initialize();
        $this->config=get_addon_config("ImageManager");
    }
	public function ImageManager(){
            $name          = I("name");
            $id            = I("id");
            $times         = I("times", 0, "intval");
            $where         = array();
            if (!empty($times)) {
                $where = $this->getTimeMap($times);
            }
            /*wzh2015年8月13日15:24:46增加token筛选，避免不同账号图片错乱*/
            $where['token']=get_token();
            $strTimes      = $this->getPictureTimes(array('token'=>$where['token']));
            $PictureResult = $this->getAllPictureData($where);
            $config = $this->config;
            //var_dump($PictureResult);die;
            
            $this->assign("addon_path", "./Plugins/ImageManager/");
            $this->assign("curtime",    $times);
            $this->assign("strTimes",   $strTimes);
			$arrname=(strpos($name,'[')==false)?$name:explode('[',trim($name,']'));
			is_array($arrname)&&$name=$arrname[1];
            $this->assign("name",       $name);
            $this->assign("id",         $id);
            $this->assign("config", $config);
            $this->assign($PictureResult);
	    $this->display('index');
	}
        
        public function deleteImage() {
            $config = get_addon_config("ImageManager");
            if ($config['delete_switch'] != 1) {
                $this->error('没有开启删除选项！');
            }
            $id = I("id", 0, "intval");
            if ($config['delete_mode'] == 1) {
                $pic = M("Picture")->find($id);
                if (file_exists($_SERVER['DOCUMENT_ROOT'] . $pic['path'])) {
                    @unlink($_SERVER['DOCUMENT_ROOT'] . $pic['path']);
                }
                M("Picture")->delete($id);
            }else {
                M("Picture")->where(array('id' => $id))->setField("status", 0);
            }
            $this->success('ok');
        }
        /**
         * 
         * @return type
         */
        private function getAllPictureData (array $where = array()) {
            $config = $this->config;
            $listrow = 18;
            $p = I("p", 1, "intval");
            if (is_numeric($config['page_size']) && $config['page_size'] > 0) {
                $listrow = intval($config['page_size']);
            }
            $result['count'] = $count = M("Picture")->where($where)->count('id');
            $page = new \Think\Page($count, $listrow);
            $result['_page'] = $page->show();
            $result['_page'] =str_replace('addon/', 'plugin/', $result['_page'] );
            $result['_list'] = M("Picture")->where($where)->where("status=1")->order('id desc')->page($p, $listrow)->select();
            return $result;
        }
        
        /**
         * 获取所有图片上传时间去重复
         * @param string $format    时间格式
         * @return array
         */
        private function getPictureTimes ($where = array(),$format = "Y年m月") {
            $times = M("Picture")->where($where)->field("create_time")->select();
            $strTimes = array();
            foreach ($times as $time) {
               $strTimes[$time['create_time']] = date($format, $time['create_time']); 
            }
            return array_unique($strTimes);
        }
        
        private function getTimeMap ($time) {
            $start_time = strtotime(date("Y-m-1 00:00:00", $time));
            $end_time   = strtotime(date("Y-m-t 23:59:59", $time));
            $where['create_time'] = array("BETWEEN", array($start_time, $end_time));
            return $where;
        }
}
