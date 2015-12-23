<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------
namespace Home\Controller;

use Think\Controller;

/**
 * 扩展控制器
 * 用于调度各个扩展的URL访问需求
 */
class AddonsController extends Controller {
	protected $addons = null;
	protected $model;
	function _initialize() {
		$token = get_token ();
		$param = array (
				'lists',
				'config',
				'nulldeal' 
		);
		if (in_array ( _ACTION, $param ) && (empty ( $token ) || $token == '-1')) {
			$url = U ( 'Public/step_0?from=2' );
			redirect ( $url );
		}
		
		C ( 'EDITOR_UPLOAD.rootPath', './Uploads/Editor/' . $token . '/' );
		
		if ($GLOBALS ['is_wap']) {
			// 默认错误跳转对应的模板文件
			C ( 'TMPL_ACTION_ERROR', 'Addons:dispatch_jump_mobile' );
			// 默认成功跳转对应的模板文件
			C ( 'TMPL_ACTION_SUCCESS', 'Addons:dispatch_jump_mobile' );
		} else {
			$this->_nav ();
		}
	}
	public function execute($_addons = null, $_controller = null, $_action = null) {
	}
	public function plugin($_addons = null, $_controller = null, $_action = null) {
	}
	function _nav() {
		$addon = D ( 'Home/Addons' )->getInfoByName ( _ADDONS );
		
		$nav = array ();
		if ($addon ['has_adminlist']) {
			$res ['title'] = $addon ['title'];
			$res ['url'] = U ( 'lists' );
			$res ['class'] = _ACTION == 'lists' ? 'current' : '';
			$nav [] = $res;
		}
		if (file_exists ( ONETHINK_ADDON_PATH . _ADDONS . '/config.php' )) {
			$res ['title'] = '功能配置';
			$res ['url'] = U ( 'config' );
			$res ['class'] = _ACTION == 'config' ? 'current' : '';
			$nav [] = $res;
		}
		if (empty ( $nav ) && _ACTION != 'nulldeal') {
			U ( 'nulldeal', '', true );
		}
		$this->assign ( 'nav', $nav );
		
		return $nav;
	}
	/**
	 * 重写模板显示 调用内置的模板引擎显示方法，
	 *
	 * @access protected
	 * @param string $templateFile
	 *        	指定要调用的模板文件
	 *        	默认为空 由系统自动定位模板文件
	 *        	支持格式: 空, index, UserCenter/index 和 完整的地址
	 * @param string $charset
	 *        	输出编码
	 * @param string $contentType
	 *        	输出类型
	 * @param string $content
	 *        	输出内容
	 * @param string $prefix
	 *        	模板缓存前缀
	 * @return void
	 */
	protected function display($templateFile = '', $charset = '', $contentType = '', $content = '', $prefix = '') {
		$templateFile = $this->getAddonTemplate ( $templateFile );
		$this->view->display ( $templateFile, $charset, $contentType, $content, $prefix );
	}
	function getAddonTemplate($templateFile = '') {
		if (file_exists ( $templateFile )) {
			return $templateFile;
		}
		$type = is_dir ( ONETHINK_PLUGIN_PATH . _ADDONS ) ? 'Plugins' : 'Addons';
		// dump ( $templateFile );
		$oldFile = $templateFile;
		if (empty ( $templateFile )) {
			$templateFile = T ( $type . '://' . _ADDONS . '@' . _CONTROLLER . '/' . _ACTION );
		} elseif (stripos ( $templateFile, '/Addons/' ) === false && stripos ( $templateFile, THINK_PATH ) === false) {
			if (stripos ( $templateFile, '/' ) === false) { // 如index
				$templateFile = T ( $type . '://' . _ADDONS . '@' . _CONTROLLER . '/' . $templateFile );
			} elseif (stripos ( $templateFile, '@' ) === false) { // // 如 UserCenter/index
				$templateFile = T ( $type . '://' . _ADDONS . '@' . $templateFile );
			}
		}
		
		if (stripos ( $templateFile, '/Addons/' ) !== false && ! file_exists ( $templateFile )) {
			$templateFile = ! empty ( $oldFile ) && stripos ( $oldFile, '/' ) === false ? $oldFile : _ACTION;
		}
		// dump ( $templateFile );//exit;
		return $templateFile;
	}
	
	// 通用插件的列表模型
	public function lists($model = null, $page = 0) {
		is_array ( $model ) || $model = $this->getModel ( $model );
		$templateFile = $this->getAddonTemplate ( $model ['template_list'] );
		parent::common_lists ( $model, $page, $templateFile );
	}
	function export($model = null) {
		is_array ( $model ) || $model = $this->getModel ( $model );
		parent::common_export ( $this->model );
	}
	
	// 通用插件的编辑模型
	public function edit($model = null, $id = 0) {
		
		is_array ( $model ) || $model = $this->getModel ( $model );
		$templateFile = $this->getAddonTemplate ( $model ['template_edit'] );
		parent::common_edit ( $model, $id, $templateFile );
		
		
	}
	
	// 通用插件的增加模型
	public function add($model = null) {
		
		is_array ( $model ) || $model = $this->getModel ( $model );
		$templateFile = $this->getAddonTemplate ( $model ['template_add'] );
		
		parent::common_add ( $model, $templateFile );
	
	}
	
	// 通用插件的删除模型
	public function del($model = null, $ids = null) {
		parent::common_del ( $model, $ids );
	}
	
	// 通用设置插件模型
	public function config() {
		$this->getModel ();
		if (IS_POST) {
			$flag = D ( 'Common/AddonConfig' )->set ( _ADDONS, I ( 'config' ) );
			
			if ($flag !== false) {
				$this->success ( '保存成功', Cookie ( '__forward__' ) );
			} else {
				$this->error ( '保存失败' );
			}
		}
		
		$map ['name'] = _ADDONS;
		$addon = M ( 'addons' )->where ( $map )->find ();
		if (! $addon)
			$this->error ( '插件未安装' );
		$addon_class = get_addon_class ( $addon ['name'] );
		if (! class_exists ( $addon_class ))
			trace ( "插件{$addon['name']}无法实例化,", 'ADDONS', 'ERR' );
		$data = new $addon_class ();
		$addon ['addon_path'] = $data->addon_path;
		$addon ['custom_config'] = $data->custom_config;
		$this->meta_title = '设置插件-' . $data->info ['title'];
		$db_config = D ( 'Common/AddonConfig' )->get ( _ADDONS );
		$addon ['config'] = include $data->config_file;
		if ($db_config) {
			foreach ( $addon ['config'] as $key => $value ) {
				if ($value ['type'] != 'group') {
					! isset ( $db_config [$key] ) || $addon ['config'] [$key] ['value'] = $db_config [$key];
				} else {
					foreach ( $value ['options'] as $gourp => $options ) {
						foreach ( $options ['options'] as $gkey => $value ) {
							! isset ( $db_config [$key] ) || $addon ['config'] [$key] ['options'] [$gourp] ['options'] [$gkey] ['value'] = $db_config [$gkey];
						}
					}
				}
			}
		}
		$this->assign ( 'data', $addon );
		// dump($addon);
		if ($addon ['custom_config'])
			$this->assign ( 'custom_config', $this->fetch ( $addon ['addon_path'] . $addon ['custom_config'] ) );
		$this->display ();
	}
	
	// 没有管理页面和配置页面的插件的通用提示页面
	function nulldeal() {
		$this->display ( T ( 'home/Addons/nulldeal' ) );
	}
	function mobileForm() {
		defined ( '_ACTION' ) or define ( '_ACTION', 'mobileForm' );
		
		$model = $this->getModel ( $model );
		
		if (IS_POST) {
			$Model = D ( parse_name ( get_table_name ( $model ['id'] ), 1 ) );
			// 获取模型的字段信息
			$Model = $this->checkAttr ( $Model, $model ['id'] );
			if ($Model->create () && $Model->save ()) {
				$this->success ( '保存' . $model ['title'] . '成功！', U ( 'lists?model=' . $model ['name'] ) );
			} else {
				$this->error ( $Model->getError () );
			}
		} else {
			$fields = get_model_attribute ( $model ['id'] );
			// 获取数据
			$id = I ( 'id' );
			$data = M ( get_table_name ( $model ['id'] ) )->find ( $id );
			
			$this->assign ( 'fields', $fields );
			$this->assign ( 'data', $data );
			
			$this->display ( './Application/Home/View/default/Addons/mobileForm.html' );
		}
	}
	// WAP页面的通用分页HTML
	function _wapPage($count, $row) {
		if ($count <= $row)
			return '';
		
		$page = new \Think\Page ( $count, $row );
		$page->setConfig ( 'theme', '%UP_PAGE% %NOW_PAGE%/%TOTAL_PAGE% %DOWN_PAGE%' );
		$page->setConfig ( 'prev', '上一页<span class="arrow_left"></span>' );
		$page->setConfig ( 'next', '下一页<span class="arrow_right"></span>' );
		return $page->show ();
	}
	function get_package_template() {
		$addons = I ( 'addons' );
		/*
		 * $dir = ONETHINK_ADDON_PATH . $addons . '/View/default/Package';
		 * $url = SITE_URL . '/Addons/' . $addons . '/View/default/Package';
		 *
		 * $dirObj = opendir ( $dir );
		 * while ( $file = readdir ( $dirObj ) ) {
		 * if ($file === '.' || $file == '..' || $file == '.svn' || is_file ( $dir . '/' . $file ))
		 * continue;
		 *
		 * $res ['dirName'] = $res ['title'] = $file;
		 *
		 * // 获取配置文件
		 * if (file_exists ( $dir . '/' . $file . '/info.php' )) {
		 * $info = require_once $dir . '/' . $file . '/info.php';
		 * $res = array_merge ( $res, $info );
		 * }
		 *
		 * // 获取效果图
		 * if (file_exists ( $dir . '/' . $file . '/info.php' )) {
		 * $res ['icon'] = __ROOT__ . '/Addons/'.$addons.'/View/default/Package/' . $file . '/icon.png';
		 * } else {
		 * $res ['icon'] = __IMG__ . '/default.png';
		 * }
		 *
		 * $tempList [] = $res;
		 * unset ( $res );
		 * }
		 * closedir ( $dir );
		 */
		$map ['uid'] = get_mid ();
		$map ['addons'] = $addons;
		
		$Model = D ( 'SucaiTemplate' );
		$tempList = $Model->where ( $map )->select ();
		// dump($tempList);
		if (! $tempList) {
			$default ['addons'] = $addons;
			$default ['template'] = 'default';
			$tempList [] = $default;
		} else {
			$hasDefault = false;
			foreach ( $tempList as $vo ) {
				if ($vo ['template'] == 'default') {
					$hasDefault = true;
					break;
				}
			}
			if ($hasDefault == false) {
				$default ['addons'] = $addons;
				$default ['template'] = 'default';
				$tempList [] = $default;
			}
		}
		// dump($tempList);
		foreach ( $tempList as &$vo ) {
			$info = $this->_readSucaiTemplateInfo ( $vo ['addons'], $vo ['template'] );
			// dump($info);
			$vo ['title'] = $info ['title'];
			$vo ['icon'] = $info ['icon'];
		}
		// dump($tempList);
		$this->ajaxReturn ( $tempList, 'JSON' );
	}
	function getSucaiTemplateInfo() {
		$addons = I ( 'addons' );
		$template = I ( 'template' );
		$res = $this->_readSucaiTemplateInfo ( $addons, $template );
		$this->ajaxReturn ( $res, 'JSON' );
	}
	function _readSucaiTemplateInfo($addons = 'Coupon', $template = 'default') {
		$dir = SITE_PATH . '/SucaiTemplate';
		$infoPath = $dir . '/' . $addons . '/' . $template . '/info.php';
		// dump($infoPath);
		$res ['dirName'] = $template;
		if (file_exists ( $infoPath )) {
			$info = require_once $infoPath;
			$res = array_merge ( $res, $info );
		}
		// 获取效果图
		if (file_exists ( $dir . '/' . $addons . '/' . $template . '.png' )) {
			$res ['icon'] = __ROOT__ . '/SucaiTemplate/' . $addons . '/' . $template . '.png';
		} else {
			$res ['icon'] = __ROOT__ . '/Public/Home/images/no_template_icon.png';
		}
		
		return $res;
	}
	
}
