<?php

namespace Addons\YouaskService\Controller;
use Addons\YouaskService\Controller\BaseController;

class GroupController extends BaseController{
	var $model;	
	function _initialize() {
		parent::_initialize();		
		$this->model = $this->getModel ( 'youaskservice_group' );	
		$this->model || $this->error ( '模型不存在！' );
				
		// 子导航
		$action = strtolower ( _ACTION );
		$controller = strtolower ( _CONTROLLER );
		
		$res ['title'] = '工号管理';
		$res ['url'] = addons_url (  'YouaskService://YouaskService/lists'  );
		$res ['class'] = ($controller == "youaskservice" && $action == 'lists')   ? 'cur' : '';
		$nav [] = $res;	

		$res ['title'] = '客服分组';
		$res ['url'] = addons_url (  'YouaskService://Group/lists'  );
		$res ['class'] = ($controller == "group" && $action == 'lists') ? 'cur' : '';
		$nav [] = $res;			
			
		$res ['title'] = '客服在线状态';
		$res ['url'] = addons_url ( 'YouaskService://YouaskService/kfzxstate' );
		$res ['class'] = $action == 'kfzxstate' ? 'cur' : '';
		$nav [] = $res;		
				
		$this->assign ( 'sub_nav', $nav );
	
	}
	
	
	// 通用插件的列表模型
	public function lists() {
		// 使用提示		
		$normal_tips = '当用户发送的消息包含匹配的关键字时,将自动转入人工客服。<br/>
		注意:<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;1、一旦接入人工客服后,该用户发送的消息自动转发到多客服，机器人不在应答。<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;2、当接入的用户和客服交流时间超过2个小时，将自动结束人工客服，切换回机器人自动应答。<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;3、为使更多用户能被客服接待，客服人员应在多客服中配置自动结束会话时间，建议设置为1分钟，到时将切换到机器人自动应答。<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;4、当设置的关键字被停用后，用户发送包含此关键字将不再自动转人工客服。';
		$this->assign ( 'normal_tips', $normal_tips );
				
		$token = get_token();
		$page = I ( 'p', 1, 'intval' ); // 默认显示第一页数据		                                
		// 解析列表规则
		$fields = array ();
		$grids = preg_split ( '/[;\r\n]+/s', $this->model ['list_grid'] );
		foreach ( $grids as &$value ) {
			// 字段:标题:链接
			$val = explode ( ':', $value );
			// 支持多个字段显示
			$field = explode ( ',', $val [0] );
			$value = array (
					'field' => $field,
					'title' => $val [1] 
			);
			if (isset ( $val [2] )) {
				// 链接信息
				$value ['href'] = $val [2];
				// 搜索链接信息中的字段信息
				preg_replace_callback ( '/\[([a-z_]+)\]/', function ($match) use(&$fields) {
					$fields [] = $match [1];
				}, $value ['href'] );
			}
			if (strpos ( $val [1], '|' )) {
				// 显示格式定义
				list ( $value ['title'], $value ['format'] ) = explode ( '|', $val [1] );
			}
			foreach ( $field as $val ) {
				$array = explode ( '|', $val );
				$fields [] = $array [0];
			}
		}
		// 过滤重复字段信息
		$fields = array_unique ( $fields );
		// 关键字搜索
		$map ['token'] = $token;
		$key = $this->model ['search_key'] ? $this->model ['search_key'] : 'title';
		if (isset ( $_REQUEST [$key] )) {
			$map [$key] = array (
					'like',
					'%' . htmlspecialchars ( $_REQUEST [$key] ) . '%' 
			);
			unset ( $_REQUEST [$key] );
		}
		// 条件搜索
		foreach ( $_REQUEST as $name => $val ) {
			if (in_array ( $name, $fields )) {
				$map [$name] = $val;
			}
		}
		$row = empty ( $this->model ['list_row'] ) ? 20 : $this->model ['list_row'];
		
		// 读取模型数据列表		
		empty ( $fields ) || in_array ( 'id', $fields ) || array_push ( $fields, 'id' );
				
		$name = parse_name ( get_table_name ( $this->model ['id'] ), true );
		/* 查询记录总数 */
		$count = M ( $name )->where ( $map )->count ();				
		$data = M ( $name )->field ( empty ( $fields ) ? true : $fields )->where ( $map )->order ( 'id DESC' )->page ( $page, $row )->select ();		
		
		
		// 分页
		if ($count > $row) {
			$page = new \Think\Page ( $count, $row );
			$page->setConfig ( 'theme', '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%' );
			$this->assign ( '_page', $page->show () );
		}
		
		$this->assign ( 'list_grids', $grids );
		$this->assign ( 'list_data', $data );
		$this->meta_title = $this->model ['title'] . '列表';
		
		
		$this->display ();
	}
		
	public function del() {
		$ids = I ( 'id', 0 );
		if (empty ( $ids )) {
			$ids = array_unique ( ( array ) I ( 'ids', 0 ) );
		}
		if (empty ( $ids )) {
			$this->error ( '请选择要操作的数据!' );
		}
		
		$Model = M ( get_table_name ( $this->model ['id'] ) );
		$map = array (
				'id' => array (
						'in',
						$ids 
				) 
		);
		$map ['token'] = get_token ();
		if ($Model->where ( $map )->delete ()) {
			$this->success ( '删除成功' );
		} else {
			$this->error ( '删除失败！' );
		}
	}
	
	
	public function edit() {
		// 获取模型信息
		$id = I ( 'id', 0, 'intval' );
		$token =  get_token ();
		if (IS_POST) {	
			$_POST ['groupdata'] = serialize(explode(",",$_POST["groupdatastr"]));
			$Model = D ( parse_name ( get_table_name ( $this->model ['id'] ), 1 ) );
			// 获取模型的字段信息
			$Model = $this->checkAttr ( $Model, $this->model ['id'] );
			if ($Model->create () && $Model->save ()) {								
				$this->success ( '保存' . $this->model ['title'] . '成功！', U ( 'lists') );
			} else {
				$this->error ( $Model->getError () );
			}
		} else {
			$fields = get_model_attribute ( $this->model ['id'] );
			
			// 获取数据
			$data = M ( get_table_name ( $this->model ['id'] ) )->find ( $id );
			$data || $this->error ( '数据不存在！' );
			
		$token = get_token ();
		if (isset ( $data ['token'] ) && $token != $data ['token'] && defined ( 'ADDON_PUBLIC_PATH' )) {
			$this->error ( '非法访问！' );
		}			
			
			// 工号人员表
			$option_users = M("youaskservice_user")->where(array("token"=>$token))->order("id desc")->select();
			$this->assign ( 'option_users', $option_users );
			
			$this->assign ( 'groupdatastr', unserialize($data["groupdata"]) );
			
			$this->assign ( 'fields', $fields );
			$this->assign ( 'data', $data );
			$this->meta_title = '编辑' . $this->model ['title'];
			$this->display ('edit');
		}
	}
	public function add() {
		$token =  get_token ();
		
		if (IS_POST) {
			// 自动补充token
			$_POST ['token'] =$token;
			$_POST ['groupdata'] = serialize(explode(",",$_POST["groupdatastr"]));
			$Model = D ( parse_name ( get_table_name ( $this->model ['id'] ), 1 ) );
			// 获取模型的字段信息
			$Model = $this->checkAttr ( $Model, $this->model ['id'] );
			if ($Model->create () && $vote_id = $Model->add ()) {								
				$this->success ( '添加' . $this->model ['title'] . '成功！', U ( 'lists') );
			} else {
				$this->error ( $Model->getError () );
			}
		} else {
			
			$fields = get_model_attribute ( $this->model ['id'] );
			$this->assign ( 'fields', $fields );
						
			// 工号人员表
			$option_users = M("youaskservice_user")->where(array("token"=>$token))->order("id desc")->select();
			$this->assign ( 'option_users', $option_users );
			
			$this->meta_title = '新增' . $this->model ['title'];
			$this->display ("add");
		}
	}	
}
