<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------
namespace Home\Controller;

/**
 * 后台用户控制器
 *
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
class AdminController extends HomeController {
	protected $addon, $model;
	public function _initialize() {
		parent::_initialize ();
		
		$this->assign ( 'check_all', false );
		$this->assign ( 'search_url', U ( 'lists' ) );
		
		define ( 'ADDON_PUBLIC_PATH', '' );
		defined ( '_ADDONS' ) or define ( '_ADDONS', MODULE_NAME );
		defined ( '_CONTROLLER' ) or define ( '_CONTROLLER', CONTROLLER_NAME );
		defined ( '_ACTION' ) or define ( '_ACTION', ACTION_NAME );
		
		$this->model = M('model')->getByName ( 'user' );
		$this->assign ( 'model', $this->model );
		// dump ( $this->model );
		
		$res ['title'] = '公众号管理';
		$res ['url'] = U ( 'Home/Public/lists' );
		$res ['class'] = '';
		$nav [] = $res;
		
		$res ['title'] = '管理员配置';
		$res ['url'] = U ( 'Home/Admin/lists' );
		$res ['class'] = 'current';
		$nav [] = $res;
		
		$this->assign ( 'nav', $nav );
	}
	protected function _display() {
		$this->view->display ( 'Addons:' . ACTION_NAME );
	}
	
	/**
	 * 显示指定模型列表数据
	 */
	public function lists() {
		// 获取模型信息
		$model = $this->model;
		
		$page = I ( 'p', 1, 'intval' );
		// 解析列表规则
		$list_data = $this->_list_grid ( $model );
		$grids = $list_data ['list_grids'];
		$fields = $list_data ['fields'];
		
		$map = $this->_search_map ( $model, $fields );
		$map ['uid'] = array (
				'not in',
				array (
						$this->mid 
				) 
		);
		$row = empty ( $model ['list_row'] ) ? 20 : $model ['list_row'];
		
		// 读取模型数据列表
		$name = parse_name ( get_table_name ( $model ['id'] ), true );
		$data = M ( $name )->where ( $map )->order ( 'uid DESC' )->page ( $page, $row )->select ();
		foreach ( $data as &$v ) {
			$v ['public_ids'] = $this->_get_public_name ( $v ['public_ids'] );
		}
		
		/* 查询记录总数 */
		$count = M ( $name )->where ( $map )->count ();
		
		// 分页
		if ($count > $row) {
			$page = new \Think\Page ( $count, $row );
			$page->setConfig ( 'theme', '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%' );
			$_page = $page->show ();
			$this->assign ( '_page', $_page );
		}
		
		$this->assign ( 'list_grids', $grids );
		$this->assign ( 'list_data', $data );
		
		$this->_display ();
	}
	public function del($model = null, $ids = null) {
		$model = $this->model;
		! empty ( $ids ) || $ids = I ( 'id' );
		! empty ( $ids ) || $ids = array_filter ( array_unique ( ( array ) I ( 'ids', 0 ) ) );
		! empty ( $ids ) || $this->error ( '请选择要操作的数据!' );
		
		$Model = M ( get_table_name ( $model ['id'] ) );
		$map ['uid'] = $maps ['id'] = array (
				'in',
				$ids 
		);
		
		// 插件里的操作自动加上Token限制
		$token = get_token ();
		if (defined ( 'ADDON_PUBLIC_PATH' ) && ! empty ( $token )) {
			$map ['token'] = $token;
		}
		
		if ($Model->where ( $map )->delete ()) {
			M ( 'public_link' )->where ( $map )->delete ();
			$this->success ( '删除成功' );
		} else {
			$this->error ( '删除失败！' );
		}
	}
	public function edit($model = null, $id = 0) {
		$model = $this->model;
		$id || $id = I ( 'id' );
		
		// 获取数据
		$data = M ( get_table_name ( $model ['id'] ) )->find ( $id );
		$data || $this->error ( '数据不存在！' );
		$data ['id'] = $data ['uid'];
		
		if (IS_POST) {
			$_POST ['uid'] = $_POST ['id'];
			$Model = D ( parse_name ( get_table_name ( $model ['id'] ), 1 ) );
			// 获取模型的字段信息
			$Model = $this->checkAttr ( $Model, $model ['id'] );
			if ($Model->create () && $Model->save ()) {
				$this->editPublicLink ( $_POST ['uid'], $_POST ['public_ids'] );
				$this->success ( '保存' . $model ['title'] . '成功！', U ( 'lists?model=' . $model ['name'], $this->get_param ) );
			} else {
				$this->error ( $Model->getError () );
			}
		} else {
			$this->_getField ( $model );
			
			$this->assign ( 'data', $data );
			
			$this->_display ();
		}
	}
	public function add() {
		$model = $this->model;
		if (IS_POST) {
			$_POST ['status'] = 1;
			/* 调用注册接口注册用户 */
			$uid = D('Common/User')->register ( $_POST ['nickname'], $_POST ['password'], $_POST ['nickname'] . NOW_TIME . '@weiphp.cn' );
			if (0 < $uid) { // 注册成功
				$_POST ['uid'] = $uid;
				$Model = D ( parse_name ( get_table_name ( $model ['id'] ), 1 ) );
				// 获取模型的字段信息
				$Model = $this->checkAttr ( $Model, $model ['id'] );
				if ($Model->create () && $id = $Model->add ()) {
					$this->editPublicLink ( $uid, $_POST ['public_ids'] );
					$this->success ( '添加' . $model ['title'] . '成功！', U ( 'lists?model=' . $model ['name'], $this->get_param ) );
				} else {
					// lastsql();
					$this->error ( $Model->getError () );
				}
			} else { // 注册失败，显示错误信息
				$this->error ( $this->showRegError ( $uid ) );
			}
		} else {
			$this->_getField ( $model );
			$this->_display ( $templateFile );
		}
	}
	function editPublicLink($uid, $ids) {
		$map ['uid'] = $uid;
		M ( 'public_link' )->where ( $map )->delete ();
		foreach ( $ids as $id ) {
			$map ['mp_id'] = $id;
			$res = M ( 'public_link' )->add ( $map );
		}
	}
	function _getField($model) {
		$fields = get_model_attribute ( $model ['id'] );
		
		$list = M ( 'public' )->select ();
		$extra = '';
		foreach ( $list as $vo ) {
			$extra .= $vo ['id'] . ":" . $vo ['public_name'] . "\r\n";
		}
		$extra = rtrim ( $extra, "\r\n" );
		$fields ['public_ids'] ['extra'] = $extra;
		
		$this->assign ( 'fields', $fields );
	}
	function _get_public_name($ids) {
		if (empty ( $ids ))
			return '';
		
		static $_public_list;
		if (empty ( $_public_list )) {
			$list = M ( 'public' )->select ();
			foreach ( $list as $v ) {
				$_public_list [$v ['id']] = $v ['public_name'];
			}
		}
		
		$ids = explode ( ',', $ids );
		foreach ( $ids as $id ) {
			$res [$id] = $_public_list [$id];
		}
		
		return implode ( ', ', $res );
	}
	/**
	 * 获取用户注册错误信息
	 *
	 * @param integer $code
	 *        	错误编码
	 * @return string 错误信息
	 */
	private function showRegError($code = 0) {
		switch ($code) {
			case - 1 :
				$error = '用户名长度必须在16个字符以内！';
				break;
			case - 2 :
				$error = '用户名被禁止注册！';
				break;
			case - 3 :
				$error = '用户名被占用！';
				break;
			case - 4 :
				$error = '密码长度必须在6-30个字符之间！';
				break;
			case - 5 :
				$error = '邮箱格式不正确！';
				break;
			case - 6 :
				$error = '邮箱长度必须在1-32个字符之间！';
				break;
			case - 7 :
				$error = '邮箱被禁止注册！';
				break;
			case - 8 :
				$error = '邮箱被占用！';
				break;
			case - 9 :
				$error = '手机格式不正确！';
				break;
			case - 10 :
				$error = '手机被禁止注册！';
				break;
			case - 11 :
				$error = '手机号被占用！';
				break;
			default :
				$error = '未知错误';
		}
		return $error;
	}
}
