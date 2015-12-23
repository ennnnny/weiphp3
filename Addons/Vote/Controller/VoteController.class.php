<?php

namespace Addons\Vote\Controller;

use Home\Controller\AddonsController;

class VoteController extends AddonsController {
	protected $model;
	protected $option;
	protected $vlog;
	public function __construct() {
		if (_ACTION == 'show') {
			$GLOBALS ['is_wap'] = true;
		}
		
		parent::__construct ();
		$this->model = getModelByName ( $_REQUEST ['_controller'] );
		$this->model || $this->error ( '模型不存在！' );
		
		$this->assign ( 'model', $this->model );
		
		$this->option = getModelByName ( 'vote_option' );
		$this->assign ( 'option', $this->option );
		
		$this->vlog = getModelByName ( 'vote_log' );
		$this->assign ( 'vlog', $this->vlog );
	}
	/**
	 * 显示指定模型列表数据
	 */
	public function lists() {
		$page = I ( 'p', 1, 'intval' ); // 默认显示第一页数据
		$isAjax = I ( 'isAjax' );
		$isRadio = I ( 'isRadio' );
		 
		// 解析列表规则
		$list_data = $this->_list_grid ( $this->model );
		$grids = $list_data ['list_grids'];
		$fields = $list_data ['fields'];
		
		// 关键字搜索
		$map ['token'] = get_token ();
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
		$data = M ( $name )->field ( empty ( $fields ) ? true : $fields )->where ( $map )->order ( 'id DESC' )->page ( $page, $row )->select ();
		//var_dump($data);
		/* 查询记录总数 */
		$count = M ( 'vote' )->where ( $map )->count ();
		
		// 分页
		if ($count > $row) {
			$page = new \Think\Page ( $count, $row );
			$page->setConfig ( 'theme', '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%' );
			$this->assign ( '_page', $page->show () );
		}
		$this->assign ( 'list_grids', $grids );
		$this->assign ( 'list_data', $data );
		$this->meta_title = $this->model ['title'] . '列表';
		if ($isAjax) {
		    $this->assign('isRadio',$isRadio);
		    $this->display ( 'ajax_lists_data' );
		} else {
		    $this->display ( T ( 'Addons://Vote@Vote/lists' ) );
		}
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
		
		if (IS_POST) {
			
			$this->checkPostData ();
			$Model = D ( parse_name ( get_table_name ( $this->model ['id'] ), 1 ) );
			// 获取模型的字段信息
			$Model = $this->checkAttr ( $Model, $this->model ['id'] );
			if ($Model->create () && $Model->save ()) {
				D ( 'Vote' )->clear ( $id );
				// 增加选项
				D ( 'Addons://Vote/VoteOption' )->set ( I ( 'post.id' ), I ( 'post.' ) );
				
				// 保存关键词
				D ( 'Common/Keyword' )->set ( I ( 'post.keyword' ), 'Vote', I ( 'post.id' ) );
				
				$this->success ( '保存' . $this->model ['title'] . '成功！', U ( 'lists?model=' . $this->model ['name'] ) );
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
			
			$option_list = M ( 'vote_option' )->where ( 'vote_id=' . $id )->order ( '`order` asc' )->select ();
			$this->assign ( 'option_list', $option_list );
			
			$this->assign ( 'fields', $fields );
			$this->assign ( 'data', $data );
			$this->meta_title = '编辑' . $this->model ['title'];
			$this->display ( T ( 'Addons://Vote@Vote/edit' ) );
		}
	}
	function checkPostData() {
		if (! I ( 'post.keyword' )) {
			$this->error ( '关键词不能为空' );
		}
		if (strlen ( I ( 'post.keyword' ) ) > 50) {
			$this->error ( '关键词应在50个字符内' );
		}
		if (! I ( 'post.title' )) {
			$this->error ( '投票标题不能为空' );
		}
		
		if (strlen ( I ( 'post.title' ) ) > 100) {
			$this->error ( '投票标题应在100个字符内' );
		}
		if (! I ( 'post.description' )) {
			$this->error ( '投票描述不能为空' );
		}
		if (! I ( 'post.start_date' )) {
			$this->error ( '请选择开始时间' );
		} else if (! I ( 'post.end_date' )) {
			$this->error ( '请选择结束时间' );
		} else if (strtotime ( I ( 'post.start_date' ) ) > strtotime ( I ( 'post.end_date' ) )) {
			$this->error ( '开始时间不能大于结束时间' );
		}
		
		// $_POST ['mTime'] = time ();
		// dump($_POST['mTime']);die();
		if (! I ( 'post.name' ) || count ( I ( 'post.name' ) ) < 2) {
			$this->error ( '请添加至少两个投票选项！' );
		} else {
			$optionName = I ( 'post.name' );
			foreach ( $optionName as $k => $v ) {
				if (empty ( $v )) {
					$this->error ( '选项标题不能为空' );
				}
			}
		}
	}
	public function add() {
		if (IS_POST) {
			// 自动补充token
			
			$_POST ['token'] = get_token ();
			$this->checkPostData ();
			$Model = D ( parse_name ( get_table_name ( $this->model ['id'] ), 1 ) );
			// 获取模型的字段信息
			$Model = $this->checkAttr ( $Model, $this->model ['id'] );
			if ($Model->create () && $vote_id = $Model->add ()) {
				
				// 增加选项
				D ( 'Addons://Vote/VoteOption' )->set ( $vote_id, I ( 'post.' ) );
				
				// 保存关键词
				D ( 'Common/Keyword' )->set ( I ( 'keyword' ), 'Vote', $vote_id );
				
				$this->success ( '添加' . $this->model ['title'] . '成功！', U ( 'lists?model=' . $this->model ['name'] ) );
			} else {
				$this->error ( $Model->getError () );
			}
		} else {
			
			$vote_fields = get_model_attribute ( $this->model ['id'] );
			$this->assign ( 'fields', $vote_fields );
			// 选项表
			$option_fields = get_model_attribute ( $this->option ['id'] );
			$this->assign ( 'option_fields', $option_fields );
			
			$this->meta_title = '新增' . $this->model ['title'];
			$this->display ( $this->model ['template_add'] ? $this->model ['template_add'] : T ( 'Addons://Vote@Vote/add' ) );
		}
	}
	protected function checkAttr($Model, $model_id) {
		$fields = get_model_attribute ( $model_id );
		$validate = $auto = array ();
		foreach ( $fields as $key => $attr ) {
			if ($attr ['is_must']) { // 必填字段
				$validate [] = array (
						$attr ['name'],
						'require',
						$attr ['title'] . '必须!' 
				);
			}
			// 自动验证规则
			if (! empty ( $attr ['validate_rule'] ) || $attr ['validate_type'] == 'unique') {
				$validate [] = array (
						$attr ['name'],
						$attr ['validate_rule'],
						$attr ['error_info'] ? $attr ['error_info'] : $attr ['title'] . '验证错误',
						0,
						$attr ['validate_type'],
						$attr ['validate_time'] 
				);
			}
			// 自动完成规则
			if (! empty ( $attr ['auto_rule'] )) {
				$auto [] = array (
						$attr ['name'],
						$attr ['auto_rule'],
						$attr ['auto_time'],
						$attr ['auto_type'] 
				);
			} elseif ('checkbox' == $attr ['type']) { // 多选型
				$auto [] = array (
						$attr ['name'],
						'arr2str',
						3,
						'function' 
				);
			} elseif ('datetime' == $attr ['type']) { // 日期型
				$auto [] = array (
						$attr ['name'],
						'strtotime',
						3,
						'function' 
				);
			}
		}
		return $Model->validate ( $validate )->auto ( $auto );
	}
	
	// 显示投票记录
	function showLog() {
		$nav [0] ['title'] = "投票列表";
		$nav [0] ['class'] = "";
		$nav [0] ['url'] = U ( "lists" );
		$nav [1] ['title'] = "投票记录";
		$nav [1] ['class'] = "current";
		$this->assign ( 'nav', $nav );
		
		$btn['url']=U('lists',array('mdm'=>$_GET['mdm']));
		$btn['title']='返回';
		$returnbtn[]=$btn;
		$this->assign('top_more_button',$returnbtn);
		$this->assign ( 'add_button', false );
		$this->assign ( 'search_button', false );
		$this->assign ( 'del_button', false );
		$this->assign ( 'check_all', false );
		
		$model = $this->vlog;
		
		$page = I ( 'p', 1, 'intval' ); // 默认显示第一页数据
		                                
		// 解析列表规则
		$list_data = $this->_list_grid ( $model );
		
		unset ( $list_data ['list_grids'] [4] );
		
		$grids = $list_data ['list_grids'];
		$fields = $list_data ['fields'];
		
		// 搜索条件
		// $map ['addon'] = $this->addon;
		$map ['vote_id'] = I ( 'id' );
		// $map ['token'] = get_token ();
		session ( 'common_condition', $map );
		// $map = $this->_search_map ( $model, $fields );
		
		$row = empty ( $model ['list_row'] ) ? 20 : $model ['list_row'];
		
		empty ( $fields ) || in_array ( 'id', $fields ) || array_push ( $fields, 'id' );
		
		$name = parse_name ( get_table_name ( $model ['id'] ), true );
		$data = M ( $name )->field ( empty ( $fields ) ? true : $fields )->where ( $map )->order ( 'id DESC' )->page ( $page, $row )->select ();
		//var_dump($data);
		// 获取投票标题
// 		$map2 ['id'] = I ( 'id' );
// 		$vname = M ( 'vote' )->where ( $map2 )->getField ( 'title' );
		
		foreach ( $data as $v ) {
			$option_ids [$v ['options']] = $v ['options'];
		}
		
		// 代码须优化
		// 获取投票选项名称
		if (! empty ( $option_ids )) {
			$map3 ['id'] = array (
					'in',
					$option_ids 
			);
			$list = M ( 'vote_option' )->where ( $map3 )->field ( 'id,name' )->select ();
			
			foreach ( $list as $vo ) {
				$option_names [$vo ['id']] = $vo ['name'];
			}
		}
		foreach ( $data as &$v ) {
// 			$v ['vote_id'] = $vname;
			$v ['options'] = $option_names [$v ['options']];
			$user=get_userinfo($v['user_id']);
			$v['vote_id']=url_img_html($user['headimgurl']);
			
			$v ['user_id'] = get_nickname ( $v ['user_id'] );
			
		}
		
		$count = M ( $name )->where ( $map )->count ();
		$list_data ['list_data'] = $data;
		
		// 分页
		if ($count > $row) {
			$page = new \Think\Page ( $count, $row );
			$page->setConfig ( 'theme', '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%' );
			$list_data ['_page'] = $page->show ();
		}
		unset($list_data["list_grids"][""]);
		$this->assign ( $list_data );
		//dump($list_data);
		$this->display ( './Application/Home/View/default/Addons/lists.html' );
	}
	// 统计各选项的投票次数
	function showCount() {
		$vote_id = I ( 'id' );
		
		$nav [0] ['title'] = "投票列表";
		$nav [0] ['class'] = "";
		$nav [0] ['url'] = U ( "lists" );
		$nav [1] ['title'] = "投票记录";
		$nav [1] ['class'] = "";
		$nav [1] ['url'] = addons_url ( 'Vote://Vote/showLog?id=' . $vote_id );
		$nav [2] ['title'] = "选项票数";
		$nav [2] ['class'] = "current";
		$this->assign ( 'nav', $nav );
		
		$this->assign ( 'add_button', false );
		$this->assign ( 'del_button', false );
		$this->assign ( 'check_all', false );
		$this->assign ( 'search_button', false );
		
		// 将缓存的数据，写入数据库
		D ( 'VoteOption' )->updateOptCount ( $vote_id, null );
		
		$page = I ( 'p', 1, 'intval' );
		$model = $this->option;
		
		$list_data = $this->_list_grid ( $model );
		$fields = $list_data ['fields'];
		$grids = $list_data ['list_grids'];
		// 查询条件
		$map ['vote_id'] = $vote_id;
		// $map['token']=get_token();
		session ( 'common_condition', $map );
		// $map = $this->_search_map ( $model, $fields );
		
		$row = empty ( $model ['list_row'] ) ? 20 : $model ['list_row'];
		empty ( $fields ) || in_array ( 'id', $fields ) || array_push ( $fields, 'id' );
		$name = parse_name ( get_table_name ( $model ['id'] ), true );
		$data = M ( $name )->field ( empty ( $fields ) ? true : $fields )->where ( $map )->order ( 'id ASC' )->page ( $page, $row )->select ();
		$count = M ( $name )->where ( $map )->count ();
		$list_data ['list_data'] = $data;
		
		// 分页
		if ($count > $row) {
			$page = new \Think\Page ( $count, $row );
			$page->setConfig ( 'theme', '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%' );
			$list_data ['_page'] = $page->show ();
		}
		
		$this->assign ( $list_data );
		//dump($list_data);
		//var_dump($list_grids);
		$this->display ( './Application/Home/View/default/Addons/lists.html' );
	}
	/**
	 * **************************微信上的操作功能************************************
	 */
	function index() {
		$vote_id = I ( 'id', 0, 'intval' );
		$openid = get_openid ();
		
		// $openid = 'orgF0t7i-s4xXa2ucIVrm5BMca-Y';
		// echo 'openid: '.$openid;die();
		$token = get_token ();
		// dump($openid);die();
		$info = $this->_getVoteInfo ( $vote_id );
		// echo $this->_is_overtime ( $vote_id )?'a':'b';die();
		$overtime = $this->_is_overtime ( $vote_id );
		$overtime = $overtime ? '1' : '0';
		$this->assign ( 'overtime', $overtime );
		$canJoin = ! empty ( $openid ) && ! empty ( $token ) && ! $overtime && ! ($this->_is_join ( $vote_id, $this->mid, $token ));
		$this->assign ( 'canJoin', $canJoin );
		$test_id = intval ( $_REQUEST ['test_id'] );
		$this->display ();
	}
	function preview(){
		$vote_id = I ( 'id', 0, 'intval' );
		$url = U('index',array('id'=>$vote_id));
		$this -> assign('url',$url);
		$this->display ( SITE_PATH . '/Application/Home/View/default/Addons/preview.html' );
	}
	function _getVoteInfo($id) {
		// 检查ID是否合法
		if (empty ( $id ) || 0 == $id) {
			$this->error ( "错误的投票ID" );
		}
		
		$map ['id'] = $map2 ['vote_id'] = $id = intval ( $id );
		$info = D ( 'Vote' )->getInfo ( $id );
		// dump(M ( 'vote' )->getLastSql());
		$this->assign ( 'info', $info );
		
		// dump($info);
		$opts = D ( 'VoteOption' )->getList ( $id );
		foreach ( $opts as $p ) {
			$total += $p ['opt_count'];
		}
		foreach ( $opts as &$vo ) {
			$vo ['percent'] = round ( $vo ['opt_count'] * 100 / $total, 1 );
		}
		// dump($opts);
		$this->assign ( 'opts', $opts );
		$this->assign ( 'num_total', $total );
		return $info;
	}
	// 用户投票信息
	function join() {
		$token = get_token ();
		$opts_ids = array_filter ( I ( 'post.optArr' ) );
		
		$vote_id = intval ( $_POST ["vote_id"] );
		// 检查ID是否合法
		if (empty ( $vote_id ) || 0 == $vote_id) {
			$this->error ( "错误的投票ID" );
		}
		if ($this->_is_overtime ( $vote_id )) {
			$this->error ( "请在指定的时间内投票" );
		}
		if ($this->_is_join ( $vote_id, $this->mid, $token )) {
			$this->error ( "您已经投过,请不要重复投" );
		}
		if (empty ( $_POST ['optArr'] )) {
			$this->error ( "请先选择投票项" );
		}
		// 如果没投过，就添加
		$data ["user_id"] = $this->mid;
		$data ["vote_id"] = $vote_id;
		$data ["token"] = $token;
		$data ["options"] = implode ( ',', $opts_ids );
		$data ["cTime"] = time ();
		$addid = M ( "vote_log" )->add ( $data );
		D ( 'VoteLog' )->getFollowLog ( $this->mid, $vote_id, true );
		// 更新投票数
		D ( 'VoteOption' )->updateOptCount ( $vote_id, $opts_ids );
		// 投票信息的vote_count+1
		// $res = M ( "vote" )->where ( 'id=' . $vote_id )->setInc ( "vote_count" );
		$vote = D ( 'Vote' );
		$voteinfo = $vote->getInfo ( $vote_id );
		$up ['vote_count'] = $voteinfo ['vote_count'] + 1;
		$vote->update ( $vote_id, $up );
		
		// 增加积分
		add_credit ( 'vote' );
		
		// 连续投票（投票表里没有next_id字段）
		// $next_id = M ( "vote" )->where ( 'id=' . $vote_id )->getField ( "next_id" );
		// if (! empty ( $next_id )) {
		// $vote_id = $next_id;
		// }
		
		redirect ( U ( 'show', 'id=' . $vote_id ) );
	}
	// 已过期返回 true ,否则返回 false
	private function _is_overtime($vote_id) {
		// 先看看投票期限过期与否
		$the_vote = D ( "Vote" )->getInfo ( $vote_id );
		
		if (! empty ( $the_vote ['start_date'] ) && $the_vote ['start_date'] > NOW_TIME)
			return ture;
		
		$deadline = $the_vote ['end_date'] + 86400;
		if (! empty ( $the_vote ['end_date'] ) && $deadline <= NOW_TIME)
			return ture;
		
		return false;
	}
	private function _is_join($vote_id, $user_id, $token) {
		// $vote_limit = M ( 'vote' )->where ( 'id=' . $vote_id )->getField ( 'vote_limit' );
		$vote_limit = 1;
		$list = D ( 'VoteLog' )->getFollowLog ( $user_id, $vote_id );
		
		$count = count ( $list );
		$info = array_pop ( $list );
		if ($info) {
			$joinData = explode ( ',', $info ['options'] );
			$this->assign ( 'joinData', $joinData );
		}
		if ($count >= $vote_limit) {
			return true;
		}
		return false;
	}
	function show(){
	    $id=I('id');
	    $url=addons_url('Vote://Vote/index',array('id'=>$id));
	    redirect($url);
	    
	}
}
