<?php
namespace Addons\Xydzp\Controller;

use Home\Controller\AddonsController;

class XydzpController extends AddonsController
{

    protected $model;

    protected $option;

    protected $jplist;

    public function __construct()
    {
        parent::__construct();
        $this->model = getModelByName($_REQUEST['_controller']);
        $this->model || $this->error('模型不存在！');
        
        $this->assign('model', $this->model);
        
        $this->option = getModelByName('xydzp_option');
        $this->assign('option', $this->option);
        
        $this->jplist = getModelByName('xydzp_jplist');
        $this->assign('jplist', $this->jplist);
        $arr = array(
            'lists',
            'add',
            'edit'
        );
        $action = strtolower(_ACTION);
        $res['title'] = '幸运大转盘';
        $res['url'] = addons_url('Xydzp://Xydzp/lists');
        $res['class'] = in_array($action, $arr) ? 'current' : '';
        $nav[] = $res;
        $arr = array(
            'jpoplists',
            'jpopadd',
            'jpopedit'
        );
        $res['title'] = '奖品库管理';
        $res['url'] = addons_url('Xydzp://Xydzp/jpoplists');
        $res['class'] = in_array($action, $arr) ? 'current' : '';
        $nav[] = $res;
        
        $res['title'] = '功能设置';
        $res['url'] = addons_url('Xydzp://Xydzp/config');
        $res['class'] = $action == 'config' ? 'current' : '';
        $nav[] = $res;
        $arr = array(
            'zjloglists',
            'zjlogadd',
            'zjlogedit'
        );
        if (in_array($action, $arr)) {
            $res['title'] = '中奖记录';
            $res['url'] = addons_url('Xydzp://Xydzp/zjloglists');
            $res['class'] = 'current';
            $nav[] = $res;
        }
        $arr = array(
            'jplists',
            'jpadd',
            'jpedit'
        );
        if (in_array($action, $arr)) {
            $res['title'] = '奖品设置';
            $res['url'] = addons_url('Xydzp://Xydzp/jplists');
            $res['class'] = 'current';
            $nav[] = $res;
        }
        
        $this->assign('nav', $nav);
    }

    /**
     * 显示指定模型列表数据
     */
    /*
     * public function lists() {
     * $page = I ( 'p', 1, 'intval' ); // 默认显示第一页数据
     *
     * // 解析列表规则
     * $fields = array ();
     * $grids = preg_split ( '/[;\r\n]+/s', $this->model ['list_grid'] );
     * foreach ( $grids as &$value ) {
     * // 字段:标题:链接
     * $val = explode ( ':', $value );
     * // 支持多个字段显示
     * $field = explode ( ',', $val [0] );
     * $value = array (
     * 'field' => $field,
     * 'title' => $val [1]
     * );
     * if (isset ( $val [2] )) {
     * // 链接信息
     * $value ['href'] = $val [2];
     * // 搜索链接信息中的字段信息
     * preg_replace_callback ( '/\[([a-z_]+)\]/', function ($match) use(&$fields) {
     * $fields [] = $match [1];
     * }, $value ['href'] );
     * }
     * if (strpos ( $val [1], '|' )) {
     * // 显示格式定义
     * list ( $value ['title'], $value ['format'] ) = explode ( '|', $val [1] );
     * }
     * foreach ( $field as $val ) {
     * $array = explode ( '|', $val );
     * $fields [] = $array [0];
     * }
     * }
     * // 过滤重复字段信息
     * $fields = array_unique ( $fields );
     * // 关键字搜索
     * $map ['token'] = get_token ();
     * $key = $this->model ['search_key'] ? $this->model ['search_key'] : 'title';
     * if (isset ( $_REQUEST [$key] )) {
     * $map [$key] = array (
     * 'like',
     * '%' . htmlspecialchars ( $_REQUEST [$key] ) . '%'
     * );
     * unset ( $_REQUEST [$key] );
     * }
     * // 条件搜索
     * foreach ( $_REQUEST as $name => $val ) {
     * if (in_array ( $name, $fields )) {
     * $map [$name] = $val;
     * }
     * }
     * $row = empty ( $this->model ['list_row'] ) ? 20 : $this->model ['list_row'];
     *
     * // 读取模型数据列表
     * empty ( $fields ) || in_array ( 'id', $fields ) || array_push ( $fields, 'id' );
     * $name = parse_name ( get_table_name ( $this->model ['id'] ), true );
     *
     * $data = M ( $name )->field ( empty ( $fields ) ? true : $fields )->where ( $map )->order ( 'id DESC' )->page ( $page, $row )->select ();
     *
     * $count = M ( $name )->where ( $map )->count ();
     *
     * // 分页
     * if ($count > $row) {
     * $page = new \Think\Page ( $count, $row );
     * $page->setConfig ( 'theme', '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%' );
     * $this->assign ( '_page', $page->show () );
     * }
     *
     * $this->assign ( 'list_grids', $grids );
     * $this->assign ( 'list_data', $data );
     * $this->meta_title = $this->model ['title'] . '列表';
     * $this->display ();
     * }
     */
    public function add()
    {
        if (IS_POST) {
            $this->checkPostData();
            // 自动补充token
            $_POST['token'] = get_token();
            $Model = D(parse_name(get_table_name($this->model['id']), 1));
            // 获取模型的字段信息
            $Model = $this->checkAttr($Model, $this->model['id']);
            if ($Model->create() && $xydzp_id = $Model->add()) {
                // 保存关键词
                D('Common/Keyword')->set(I('keyword'), 'Xydzp', $xydzp_id);
                $this->success('添加' . $this->model['title'] . '成功！', U('lists?model=' . $this->model['name']));
            } else {
                $this->error($Model->getError());
            }
        } else {
            
            $xydzp_fields = get_model_attribute($this->model['id']);
            $this->assign('fields', $xydzp_fields);
            // 奖品表
            $option_fields = get_model_attribute($this->option['id']);
            $this->assign('option_fields', $option_fields);
            
            $this->meta_title = '新增' . $this->model['title'];
            $this->display();
        }
    }

    public function del()
    {
        $ids = I('id', 0);
        if (empty($ids)) {
            $ids = array_unique((array) I('ids', 0));
        }
        if (empty($ids)) {
            $this->error('请选择要操作的数据!');
        }
        
        $Model = M(get_table_name($this->model['id']));
        $map = array(
            'id' => array(
                'in',
                $ids
            )
        );
        if ($Model->where($map)->delete()) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }

    public function edit()
    {
        // 获取模型信息
        $id = I('id', 0, 'intval');
        
        if (IS_POST) {
            $this->checkPostData();
            $Model = D(parse_name(get_table_name($this->model['id']), 1));
            // 获取模型的字段信息
            $Model = $this->checkAttr($Model, $this->model['id']);
            if ($Model->create() && $Model->save()) {
                // 保存关键词
                D('Common/Keyword')->set(I('keyword'), 'Xydzp', $id);
                D('Xydzp')->getXydzpInfo($id, true);
                $this->success('保存' . $this->model['title'] . '成功！', U('lists'));
            } else {
                $this->error($Model->getError());
            }
        } else {
            $fields = get_model_attribute($this->model['id']);
            
            // 获取数据
            $data = M(get_table_name($this->model['id']))->find($id);
            $data || $this->error('数据不存在！');
            
            // $option_list = M ( 'xydzp_option' )->where ( 'xydzp_id=' . $id )->select ();
            // $this->assign ( 'option_list', $option_list );
            
            $this->assign('fields', $fields);
            $this->assign('data', $data);
            $this->meta_title = '编辑' . $this->model['title'];
            $this->display(T('Addons://Xydzp@Xydzp/edit'));
        }
    }
    function lists() {
        $isAjax = I ( 'isAjax' );
        $isRadio = I ( 'isRadio' );
        $model = $this->getModel ( 'xydzp' );
        $list_data = $this->_get_model_list ( $model, 0, 'id desc', true );
        // 		判断该活动是否已经设置投票调查
        if ($isAjax) {
            $this->assign('isRadio',$isRadio);
            $this->assign ( $list_data );
            $this->display ( 'ajax_lists_data' );
        } else {
            $this->assign ( $list_data );
            $this->display ();
        }
    }
    function checkPostData()
    {
        if (! I('post.keyword')) {
            $this->error('关键词不能为空');
        }
        if (! I('post.title')) {
            $this->error('活动标题不能为空');
        }
        if (! I('post.picurl')) {
            $this->error('请选择封面图片');
        }
        if (! I('post.des_jj')) {
            $this->error('活动简介不能为空');
        }
        if (! I('post.guiz')) {
            $this->error('活动规则不能为空');
        }
        
        if (I('post.choujnum') < 0) {
            $this->error('每日抽奖总次数不能低于0次');
        }
        if (! I('post.start_date')) {
            $this->error('请选择开始时间');
        } else 
            if (! I('post.end_date')) {
                $this->error('请选择结束时间');
            } else 
                if (strtotime(I('post.start_date')) > strtotime(I('post.end_date'))) {
                    $this->error('开始时间不能大于结束时间');
                }
    }

    /**
     * **************************中奖记录************************************
     */
    public function zjloglists()
    {
        $page = I('p', 1, 'intval'); // 默认显示第一页数据
        $xydzp_id = I('get.id', 0, "intval");
        $zjlog = M('model')->getByName('xydzp_log');
//         $list_data=$this->_get_model_list($zjlog);
        $grids = preg_split('/[;\r\n]+/s', $zjlog['list_grid']);
        // 解析列表规则
        $fields = array();
        foreach ($grids as &$value) {
            // 字段:标题:链接
            $val = explode(':', $value);
            // 支持多个字段显示
            $field = explode(',', $val[0]);
            $value = array(
                'field' => $field[0],
                'title' => $val[1]
            );
            if (isset($val[2])) {
                // 链接信息
                $value['href'] = $val[2];
                // 搜索链接信息中的字段信息
                preg_replace_callback('/\[([a-z_]+)\]/', function ($match) use(&$fields)
                {
                    $fields[] = $match[1];
                }, $value['href']);
            }
            if (strpos($val[1], '|')) {
                // 显示格式定义
                list ($value['title'], $value['format']) = explode('|', $val[1]);
            }
            foreach ($field as $val) {
                $array = explode('|', $val);
                $fields[] = $array[0];
            }
        }
        $fix = C("DB_PREFIX");
        // 过滤重复字段信息
        $fields = array_unique($fields);
        $row = empty($zjlog['list_row']) ? 20 : $zjlog['list_row'];
        
        // 中奖列表
        $map['xydzp_id'] = $xydzp_id;
        $data = M('xydzp_log')->where($map)
            ->page($page, $row)
            ->order('zjdate desc')
            ->select();
        foreach ($data as $v) {
            $op[$v['xydzp_option_id']] = $v['xydzp_option_id'];
            $v['uid'] != '-1' && $xy[$v['uid']] = $v['uid'];
        }
        $arr = $this->_getOpionArr($op);
        
        $follow = $this->_getFollowArr($xy);
        
        foreach ($data as &$v) {
            $v['title'] = $arr[$v['xydzp_option_id']];
            $v['truename'] = $follow[$v['uid']]['truename'];
            $v['mobile'] = $follow[$v['uid']]['mobile'];
            $v['openid'] = $v['uid'];
        }
        
        // lastsql ();
        // exit ();
        /* 查询记录总数 */
        $count = M('xydzp_log')->where($map)->count();
        
        // 分页
        if ($count > $row) {
            $page = new \Think\Page($count, $row);
            $page->setConfig('theme', '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
            $this->assign('_page', $page->show());
        }
        $this->assign('xydzp_id', $xydzp_id);
        $list_data['list_data']=$data;
        $this->assign('list_grids', $grids);
        $this->assign('list_data', $data);
        $this->display(T('Addons:lists'));
    }

    function ylingqu()
    {
        // 获取模型信息
        $id = I('id', 0, 'intval');
        M("xydzp_log")->where(array(
            'id' => $id
        ))
            ->data(array(
            "state" => 1
        ))
            ->save();
        $xydzp_log = M("xydzp_log")->where(array(
            'id' => $id
        ))->find();
        $this->success('已标记为已领取状态！', U('zjloglists?id=' . $xydzp_log["xydzp_id"]));
    }

    function wlingqu()
    {
        // 获取模型信息
        $id = I('id', 0, 'intval');
        M("xydzp_log")->where(array(
            'id' => $id
        ))
            ->data(array(
            "state" => 0
        ))
            ->save();
        $xydzp_log = M("xydzp_log")->where(array(
            'id' => $id
        ))->find();
        $this->success('已标记为未领取状态！', U('zjloglists?id=' . $xydzp_log["xydzp_id"]));
    }

    /**
     * **************************奖品库设置功能************************************
     */
    public function jpoplists()
    {
        $this->assign('add_url', U('jpopadd', $this->get_param));
        $this->assign('del_url', U('jpopdel', $this->get_param));
        // $this->assign ( 'search_button', false );
        
        parent::common_lists('xydzp_option', 0, T('Addons:lists'));
    }

    public function jpopadd()
    {
        if (IS_POST) {
            // 自动补充token
            $_POST['token'] = get_token();
            $Model = D(parse_name(get_table_name($this->option['id']), 1));
            // 获取模型的字段信息
            $Model = $this->checkAttr($Model, $this->option['id']);
            if ($Model->create() && $xydzp_option_id = $Model->add()) {
                D('XydzpOption')->getOptionTitles(true);
                if ($_POST['isjx'] == "1") {
                    $this->success('添加' . $this->option['title'] . '成功！将继续进行新增奖品', U('jpopadd'));
                } else {
                    $this->success('添加' . $this->option['title'] . '成功！', U('jpoplists'));
                }
            } else {
                $this->error($Model->getError());
            }
        } else {
            $xydzp_option_fields = get_model_attribute($this->option['id']);
            $this->assign('fields', $xydzp_option_fields);
            $this->assign('post_url', U('jpopadd'));
            
            $this->display(T('Addons:add'));
        }
    }

    public function jpopedit()
    {
        // 获取模型信息
        $id = I('id', 0, 'intval');
        
        if (IS_POST) {
            $Model = D(parse_name(get_table_name($this->option['id']), 1));
            // 获取模型的字段信息
            $Model = $this->checkAttr($Model, $this->option['id']);
            if ($Model->create() && $Model->save()) {
                D('XydzpOption')->getXydzpOptionInfo($id,true);
                D('XydzpOption')->getOptionTitles(true);
                $this->success('保存' . $this->option['title'] . '成功！', U('jpoplists'));
            } else {
                $this->error($Model->getError());
            }
        } else {
            $fields = get_model_attribute($this->option['id']);
            
            // 获取数据
            $data = M(get_table_name($this->option['id']))->find($id);
            $data || $this->error('数据不存在！');
            
            $this->assign('fields', $fields);
            $this->assign('data', $data);
            $this->assign('post_url', U('jpopedit'));
            
            $this->display(T('Addons:edit'));
        }
    }

    public function jpopdel()
    {
        $ids = I('id', 0);
        if (empty($ids)) {
            $ids = array_unique((array) I('ids', 0));
        }
        if (empty($ids)) {
            $this->error('请选择要操作的数据!');
        }
        
        $Model = M(get_table_name($this->option['id']));
        $map = array(
            'id' => array(
                'in',
                $ids
            )
        );
        if ($Model->where($map)->delete()) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }

    /**
     * **************************活动奖品设置************************************
     */
    public function jplists()
    {
        $this->assign('search_button', false);
        $this->assign('add_url', U('jpadd', $this->get_param));
        $this->assign('del_url', U('jpdel', $this->get_param));
        
        $page = I('p', 1, 'intval'); // 默认显示第一页数据
        $model = $this->getModel('xydzp_jplist');
        // 解析列表规则
        $list_data = $this->_list_grid($model);
        $grids = $list_data['list_grids'];
        $fields = $list_data['fields'];
        
        // 搜索条件
        $map = $this->_search_map($model, $fields);
        $map['xydzp_id'] = $xydzp_id = I('xydzp_id');
        
        $row = empty($model['list_row']) ? 20 : $model['list_row'];
        
        // 读取模型数据列表
        $name = parse_name(get_table_name($model['id']), true);
        $data = M($name)->field(true)
            ->where($map)
            ->order('id desc')
            ->page($page, $row)
            ->select();
        if (! empty($data)) {
            $ids = array_filter(getSubByKey($data, 'xydzp_option_id'));
            $map2['id'] = array(
                'in',
                $ids
            );
            
            $list = M('xydzp_option')->where($map2)
                ->field('id,title')
                ->select();
            foreach ($list as $v) {
                $arr[$v['id']] = $v['title'];
            }
            foreach ($data as &$vo) {
                $vo['xydzp_option_id'] = $arr[$vo['xydzp_option_id']];
            }
        }
        /* 查询记录总数 */
        
        $count = M($name)->where($map)->count();
        
        $list_data['list_data'] = $data;
        
        // 分页
        if ($count > $row) {
            $page = new \Think\Page($count, $row);
            $page->setConfig('theme', '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
            $list_data['_page'] = $page->show();
        }
        
        $this->assign($list_data);
        $this->assign('xydzp_id', $xydzp_id);
        $this->assign('normal_tips', "奖品必须设置3个以上，否则会导致大转盘不可用");
        $this->display(T('Addons:lists'));
    }

    public function jpadd()
    {
        $xydzp_id = I('get.xydzp_id', 0, "intval");
        if (IS_POST) {
            if (intval(I('post.gailv')) > 100 || intval(I('post.gailv')) < 0) {
                $this->error('中奖概率必须是0-100之间的整数');
            }
            $Model = D(parse_name(get_table_name($this->jplist['id']), 1));
            // 获取模型的字段信息
            $Model = $this->checkAttr($Model, $this->jplist['id']);
            if ($Model->create() && $xydzp_jplist_id = $Model->add()) {
                D('XydzpJplist')->getXydzpJplists($xydzp_id, true);
                if ($_POST['isjx'] == "1") {
                    $this->success('添加成功！将继续进行新增奖品', U('jpadd?xydzp_id=' . $xydzp_id));
                } else {
                    $this->success('添加成功！', U('jplists?xydzp_id=' . $xydzp_id));
                }
            } else {
                $this->error($Model->getError());
            }
        } else {
            $jplist_fields = get_model_attribute($this->jplist['id']);
            $this->assign('fields', $jplist_fields);
            // 过滤重复字段信息
            $map['token'] = get_token();
            $jpdata = M("xydzp_option")->field(array(
                "title",
                "id"
            ))
                ->where($map)
                ->order('id DESC')
                ->select();
            $this->assign('jpdata', $jpdata);
            $this->assign('xydzp_id', $xydzp_id);
            $this->meta_title = '新增奖品';
            $this->display($this->model['template_add'] ? $this->model['template_add'] : T('Addons://Xydzp@Xydzp/jpadd'));
        }
    }

    public function jpedit()
    {
        // 获取模型信息
        $id = I('id', 0, 'intval');
        $xydzp_id = I('get.xydzp_id', 0, "intval");
        
        if (IS_POST) {
            if (intval(I('post.gailv')) > 100 || intval(I('post.gailv')) < 0) {
                $this->error('中奖概率必须是0-100之间的整数');
            }
            $Model = D(parse_name(get_table_name($this->jplist['id']), 1));
            // 获取模型的字段信息
            $Model = $this->checkAttr($Model, $this->jplist['id']);
            if ($Model->create() && $Model->save()) {
                D('XydzpJplist')->getXydzpJplists($xydzp_id, true);
                D('XydzpJplist')->getXydzpJplistInfo($id, true);
                $this->success('保存成功！', U('jplists?xydzp_id=' . $xydzp_id));
            } else {
                $this->error($Model->getError());
            }
        } else {
            $fields = get_model_attribute($this->jplist['id']);
            
            // 获取数据
            $data = M(get_table_name($this->jplist['id']))->find($id);
            $data || $this->error('数据不存在！');
            $xydzp_id = $data["xydzp_id"];
            $map['token'] = get_token();
            $jpdata = M("xydzp_option")->field(array(
                "title",
                "id"
            ))
                ->where($map)
                ->order('id DESC')
                ->select();
            $this->assign('jpdata', $jpdata);
            $this->assign('seljpdata', $data["xydzp_option_id"]);
            $this->assign('fields', $fields);
            $this->assign('data', $data);
            $this->assign('xydzp_id', $xydzp_id);
            $this->meta_title = '编辑';
            $this->display(T('Addons://Xydzp@Xydzp/jpedit'));
        }
    }

    public function jpdel()
    {
        $ids = I('id', 0);
        if (empty($ids)) {
            $ids = array_unique((array) I('ids', 0));
        }
        if (empty($ids)) {
            $this->error('请选择要操作的数据!');
        }
        
        $Model = M(get_table_name($this->jplist['id']));
        $map = array(
            'id' => array(
                'in',
                $ids
            )
        );
        if ($Model->where($map)->delete()) {
            D('XydzpJplist')->getXydzpJplists($xydzp_id, true);
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }

    /**
     * **************************微信上的操作功能************************************
     */
    function show()
    {
        $xydzp_id = I('id', 0, 'intval');
        $openid = get_openid();
        $token = get_token();
        
        $this->assign('openid', $openid);
        $this->assign('token', $token);
        $this->assign('xydzp_id', $xydzp_id);
        
        // $xydzp_detail = M ( "xydzp" )->where ( "id=$xydzp_id" )->find ();
        $xydzp_detail = D('Xydzp')->getXydzpInfo($xydzp_id);
        $this->assign('xydzp_detail', $xydzp_detail);
        // dump($xydzp_detail);
        // exit;
        
        // 是否已经结束
        $isend = $this->_is_overtime($xydzp_id, $xydzp_detail);
        if ($isend) {
            $this->assign('isend', $isend);
        } else {
            
            // 减少经历值
            $user = get_mult_userinfo($this->mid);
            
            if ($xydzp_detail['experience'] > 0 && $xydzp_detail['experience'] > $user['experience']) {
                $url = addons_url('UserCenter://Wap/userCenter');
                $this->error('你的经历值不足', $url);
            }
            
            // 查询是否绑定了信息
            $user = D('Xydzp')->getUserInfo($openid, $token);
            
            $param['token'] = $token;
            $param['openid'] = $openid;
            $param['id'] = $xydzp_id;
            $url = "";
            if (($user["username"] == "" || $user["mobile"] == "") && $openid != "" && $openid != "-1") {
                $url = addons_url('Xydzp://Xydzp/info', $param);
            }
            $this->assign('isinfo_url', $url);
            $joinurl = addons_url('Xydzp://Xydzp/join', $param);
            $info = $this->_getXydzpInfo($xydzp_id);
            $cjnum = intval($this->is_Maxjoin($xydzp_id, $openid, $token));
            $canJoin = ! empty($openid) && ! empty($token) && $cjnum != 0;
            // 查询奖品列表
            // $fix = C ( "DB_PREFIX" );
            // $jplist = M ( 'xydzp_jplist' )->join ( 'left join ' . $fix . 'xydzp_option on ' . $fix . 'xydzp_jplist.xydzp_option_id=' . $fix . 'xydzp_option.id' )->field ( $fix . "xydzp_option.title,pic," . $fix . "xydzp_option.miaoshu," . $fix . "xydzp_option.isdf" )->where ( array (
            // 'xydzp_id' => $xydzp_id
            // ) )->order ( "type asc,gailv asc" )->select ();
            $jplist = D('XydzpJplist')->getJplistdetail($xydzp_id);
            foreach ($jplist as &$v) {
                $v['picUrl'] = get_cover_url($v['pic']);
            }
            
            $this->assign('jplist', urldecode(json_encode($jplist)));
            $this->assign('jplists', $jplist);
            // 活动数据
            // $data = M ( get_table_name ( $this->model ['id'] ) )->find ( $xydzp_id );
            // dump($data);
            $data = D('Xydzp')->getXydzpInfo($xydzp_id);
            $data["start_date"] = date('Y-m-d H:i:s', $data["start_date"]);
            $data["end_date"] = date('Y-m-d H:i:s', $data["end_date"]);
            $this->assign('hddata', $data);
            // 中奖列表
            // $zjuserlist = M ( 'xydzp_log as lo' )->join ( 'left join ' . $fix . 'xydzp_option op on lo.xydzp_option_id=op.id' )->field ( "lo.id,lo.uid,op.title,zjdate" )->where ( array (
            // 'xydzp_id' => $xydzp_id
            // ) )->order ( "zjdate desc" )->limit ( 10 )->select ();
            // dump($zjuserlist);
            $zjuserlist = D('XydzpLog')->getXydzpLogs($xydzp_id);
            foreach ($zjuserlist as $z) {
                $zjoptids[$z['xydzp_option_id']] = $z['xydzp_option_id'];
            }
            // 获取指定字段（`id`,`title`,`pic`,`miaoshu`,`isdf`）的所有的奖品列表
            $option = D('XydzpOption')->getOptionTitles();
            foreach ($option as $o) {
                if ($o['id'] == $zjoptids[$o['id']]) {
                    $zjoptname[$o['id']] = $o['title'];
                }
            }
            foreach ($zjuserlist as &$v) {
                $v['title'] = $zjoptname[$v['xydzp_option_id']];
                if ($v['uid'] && $v['uid'] != '-1') {
                    $openids[] = $v['uid'];
                }
            }
            if (! empty($openids)) {
                $omap['openid'] = array(
                    'in',
                    $openids
                );
                $uids = M('public_follow')->where($omap)
                    ->field('uid,openid')
                    ->select();
                foreach ($uids as $v) {
                    $uidArr[$v['openid']] = get_followinfo($v['uid'], 'nickname');
                }
            }
            // lastsql ();
            foreach ($zjuserlist as &$v) {
                $v['truename'] = empty($uidArr[$v['uid']]) ? '无名氏' : $uidArr[$v['uid']];
            }
            $this->assign('zjuserlist', $zjuserlist);
            $this->assign('canJoin', $canJoin);
            $this->assign('cjnum', $cjnum);
            $this->assign('joinurl', $joinurl);
        }
        $this->display(T('Addons://Xydzp@Xydzp/show'));
    }

    function index()
    {
        $xydzp_id = I('id', 0, 'intval');
        $openid = get_openid();
        $token = get_token();
        
        $this->assign('openid', $openid);
        $this->assign('token', $token);
        $this->assign('xydzp_id', $xydzp_id);
        $xydzp_detail = D('Xydzp')->getXydzpInfo($xydzp_id);
        $this->assign('data', $xydzp_detail);
        
        $public_info = $info = get_token_appinfo();
        $param['publicid'] = $info['id'];
        $param['id'] = $xydzp_id;
        $jumpUrl = addons_url('Xydzp://Xydzp/show', $param);
        $this->assign('jumpUrl', $jumpUrl);
        $this->display();
    }

    function preview()
    {
        $id = I('id', 0, 'intval');
        $url = U('index', array(
            'id' => $id
        ));
        $this->assign('url', $url);
        $this->display(SITE_PATH . '/Application/Home/View/default/Addons/preview.html');
    }

    function _getXydzpInfo($id)
    {
        // 检查ID是否合法
        if (empty($id) || 0 == $id) {
            $this->error("错误的幸运大转盘ID");
        }
        
        $map['id'] = $map2['xydzp_id'] = intval($id);
        // $info = M ( 'xydzp' )->where ( $map )->find ();
        $info = D('Xydzp')->getXydzpInfo($map['id']);
        // dump(M ( 'xydzp' )->getLastSql());
        $this->assign('info', $info);
        
        // dump($info);
        // $opts = M ( 'xydzp_jplist' )->where ( $map2 )->select ();
        $opts = D('XydzpJplist')->getXydzpJplists($map['id']);
        // dump($opts);
        $this->assign('opts', $opts);
        // $this->assign ( 'num_total', $total );
        return $info;
    }
    
    // 我的中奖信息
    function zjinfo()
    {
        $xydzp_id = I('id', 0, 'intval');
        $openid = get_openid();
        $token = get_token();
        
        $zjuserlist = D('XydzpLog')->getZjUserList($xydzp_id, $openid);
        $this->assign('zjuserlist', $zjuserlist);
        $this->display(T('Addons://Xydzp@Xydzp/zjinfo'));
    }

    function zjlists()
    {
        $map['uid'] = $this->mid;
        
        $list = M('xydzp_log')->where($map)->select();
        
        foreach ($list as &$v) {
            $op[$v['xydzp_option_id']] = $v['xydzp_option_id'];
            $xy[$v['xydzp_id']] = $v['xydzp_id'];
        }
        
        $arr = $this->_getOpionArr($op);
        foreach ($list as &$v) {
            $v['title'] = $arr[$v['xydzp_option_id']];
        }
        
        $map2['id'] = array(
            'in',
            $xy
        );
        $opList = M('xydzp')->where($map2)
            ->field('id,title')
            ->select();
        $arr = makeKeyVal($opList);
        foreach ($list as &$v) {
            $v['event'] = $arr[$v['xydzp_id']];
        }
        
        $this->assign('zjuserlist', $list);
        $this->display(T('Addons://Xydzp@Xydzp/zjinfo'));
    }

    function _getOpionArr($op)
    {
        if (empty($op))
            return array();
        
        $map2['id'] = array(
            'in',
            $op
        );
        $opList = M('xydzp_option')->where($map2)
            ->field('id,title')
            ->select();
        $arr = makeKeyVal($opList);
        
        return $arr;
    }

    function _getFollowArr($openids, $field = 'openid')
    {
        if (empty($openids))
            return array();
        
        $omap['openid'] = array(
            'in',
            $openids
        );
        $uids = M('public_follow')->where($omap)
            ->field('uid,openid')
            ->select();
        foreach ($uids as $v) {
            $uidArr[$v['openid']] = $v['uid'];
            $userList[] = get_followinfo($v['uid']);
        }
        
        foreach ($userList as $v) {
            $userArr[$v[$field]]['truename'] = empty($v['truename']) ? $v['nickname'] : $v['truename'];
            $userArr[$v[$field]]['mobile'] = $v['mobile'];
        }
        
        return $userArr;
    }
    
    // [Ajax]保存用户信息
    function info()
    {
        $data['openid'] = get_openid();
        $data['token'] = get_token();
        
        // 保存用户信息
        $username = I('truename');
        if (! empty($username)) {
            $user['truename'] = $username;
        }
        $mobile = I('mobile');
        if (! empty($mobile)) {
            $user['mobile'] = $mobile;
        }
        if (! empty($user)) {
            D('Common/User')->updateInfo($this->mid, $user);
        }
        $array = array(
            "result" => 0
        );
        $json = json_encode($array);
        echo urldecode($json);
    }
    
    // [Ajax]开始转
    function join()
    {
        $xydzp_id = I('get.id', 0, 'intval');
        $xydzp_detail = D('Xydzp')->getXydzpInfo($xydzp_id);
        
        if ($this->_is_starttime($xydzp_id, $xydzp_detail)) {
            if ($this->_is_overtime($xydzp_id, $xydzp_detail)) {
                $json = json_encode(array(
                    "type" => 4
                ));
                echo urldecode($json);
            } else {
                // 减少经历值
                $user = get_mult_userinfo($this->mid);
                if ($xydzp_detail['experience'] > $user['experience']) {
                    $json = json_encode(array(
                        "type" => 5
                    ));
                    echo urldecode($json);
                    exit();
                }
                
                $credit['experience'] = 0 - $xydzp_detail['experience'];
                $credit['score'] = 0;
                add_credit('xydzp', 5, $credit);
                
                $openid = get_openid();
                $token = get_token();
                $fix = C("DB_PREFIX");
                // 查询奖品列表
                // $jplist = M ( 'xydzp_jplist' )->join ( 'left join ' . $fix . 'xydzp_option as op on ' . $fix . 'xydzp_jplist.xydzp_option_id=op.id' )->field ( "op.card_url,op.experience,op.coupon_id,op.title,gailv,xydzp_id,xydzp_option_id,op.num as kcnum,op.miaoshu,op.jptype" )->where ( "xydzp_id=$xydzp_id" )->order ( 'type asc,gailv asc' )->select ();
                $jplist = D('XydzpJplist')->getJplistdetail($xydzp_id);
                // 按奖品等级计算概率
                echo $this->getResult($jplist, $xydzp_id, $openid, $token);
            }
        } else {
            $json = json_encode(array(
                "type" => 3
            ));
            echo urldecode($json);
        }
    }
    
    // 获得中奖结果
    /*
     * $result['type'] 奖品回调类型
     * 0: 抽奖次数用完
     * 1：奖品库存为0
     * 2：成功
     * 3: 活动时间未到
     * 4: 活动已经结束
     */
    private function getResult($jplist, $xydzp_id, $openid, $token)
    {
        $row = $this->is_Maxjoin($xydzp_id, $openid, $token);
        // dump($row);
        if ($row != 0) {
            $arr = array();
            // 按概率计算
            foreach ($jplist as $key => $val) {
                // 每个奖品的概率
                $arr[] = $val['gailv'] * 10;
            }
            shuffle($arr);
            // dump ( $arr );
            $rid = $this->getRand($arr); // 根据概率获取奖项id
                                            // dump ( $rid );
            $res = $jplist[$rid]; // 中奖项
            $num = $row - 1;
            // 用户抽奖次数减1
            // 是否第一次参加
            // $list = M ( "xydzp_userlog" )->where ( "xydzp_id=$xydzp_id AND uid='$openid' " )->find ();
            $list = D('XydzpUserlog')->getUserlogInfo($openid, $xydzp_id);
            if ($list["id"] > 0) {
                // M ( "xydzp_userlog" )->where ( array (
                // 'xydzp_id' => $res ['xydzp_id'],
                // 'uid' => $openid
                // ) )->data ( array (
                // 'num' => $list ["num"] + 1,
                // 'cjdate' => time ()
                // ) )->save ();
                
                $data['num'] = $list["num"] + 1;
                $data['cjdate'] = time();
                D('XydzpUserlog')->updateLog($openid, $xydzp_id, $data);
            } else {
                M("xydzp_userlog")->add(array(
                    'uid' => $openid,
                    'xydzp_id' => $xydzp_id,
                    'cjdate' => time(),
                    'num' => 1
                ));
                D('XydzpUserlog')->getUserlogInfo($openid, $xydzp_id, true);
            }
            
            $result['type'] = 2;
            $result['jptype'] = $res['jptype'];
            // dump ( $res );
            // exit ();
            if ($res['jptype'] != 2) {
                if ($res['kcnum'] == 0) {
                    $result['type'] = 1;
                } else {
                    // 保存用户的中奖信息(排除谢谢惠顾)
                    // 用户抽取的那个奖项库存减1
                    // M ( "xydzp_option" )->where ( 'id=' . $res ['xydzp_option_id'] )->setField ( 'num', $res ['kcnum'] - 1 );
                    $optnum['num'] = $res['kcnum'] - 1;
                    D('XydzpOption')->updateOptionNum($res['xydzp_option_id'], $optnum);
                    M("xydzp_log")->add(array(
                        'xydzp_id' => $xydzp_id,
                        'uid' => $openid,
                        'xydzp_option_id' => $res['xydzp_option_id'],
                        'zjdate' => time(),
                        'state' => 0
                    ));
                    D('XydzpLog')->getXydzpLogs($xydzp_id, true);
                }
            } else {
                $result['type'] = 6;
            }
            
            $result['num'] = $num;
            // 计算中奖角度的位置
            $result['angle'] = 360 - (360 / sizeof($jplist) / 2) - (360 / sizeof($jplist) * $rid) - 90;
            $result['angle'] == 0 && $result['angle'] = 360;
            $result['card_url'] = '';
            if ($res['jptype'] == 3) { // 谢谢参与
                $result['praisename'] = '恭喜获得卡券一份';
                $result['card_url'] = $res['card_url'];
            } elseif ($res['jptype'] == 2) { // 谢谢参与
                $result['praisename'] = '很抱歉，没有抽中，也许抽的姿势不对，换个姿势再试一次吧！';
            } elseif ($res['jptype'] == 1) { // 优惠券奖励
                $result['praisename'] = '恭喜获得' . $res["title"] . '奖品，已加入你的空间';
                
                // 发放优惠券
                $data['sn'] = uniqid();
                $data['uid'] = $this->mid;
                $data['cTime'] = time();
                $data['addon'] = 'Coupon';
                $data['target_id'] = $res['coupon_id'];
                $data['can_use'] = 1;
                // dump ( $data );
                M('sn_code')->add($data);
                // M ( "coupon" )->where ( 'id=' . $res ['coupon_id'] )->setInc ( "collect_count" );
                D('Addons://Coupon/Coupon')->updateCollectCount($res['coupon_id']);
            } elseif ($res['jptype'] == 0) { // 经历值奖励
                $result['praisename'] = '恭喜获得' . $res["title"] . '奖品，经历值增加' . $res['experience'] . '分';
                
                $credit['experience'] = $res['experience'];
                $credit['score'] = 0;
                add_credit('xydzp_prize', 5, $credit);
            }
        } else {
            $result['type'] = 0;
            $result['num'] = $num;
            // 计算中奖角度的位置
            $result['angle'] = - 90;
            $result['praisename'] = "";
        }
        return $this->json($result);
    }

    private function getRand($proArr)
    {
        $result = '';
        
        // 概率数组的总概率精度
        $proSum = array_sum($proArr);
        
        // 概率数组循环
        foreach ($proArr as $key => $proCur) {
            $randNum = mt_rand(1, $proSum);
            if ($randNum <= $proCur) {
                $result = $key;
                break;
            } else {
                $proSum -= $proCur;
            }
        }
        
        unset($proArr);
        return $result;
    }

    private function json($array)
    {
        $this->arrayRecursive($array, 'urlencode', true);
        $json = json_encode($array);
        return urldecode($json);
    }
    // 对数组中所有元素做处理
    private function arrayRecursive(&$array, $function, $apply_to_keys_also = false)
    {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                arrayRecursive($array[$key], $function, $apply_to_keys_also);
            } else {
                $array[$key] = $function($value);
            }
            if ($apply_to_keys_also && is_string($key)) {
                $new_key = $function($key);
                if ($new_key != $key) {
                    $array[$new_key] = $array[$key];
                    unset($array[$key]);
                }
            }
        }
    }

    /**
     * **************************公用功能************************************
     */
    // 查询参加次数是否超标 反回剩余次数
    private function is_Maxjoin($xydzp_id, $user_id, $token)
    {
        // dump($this->mid);
        // $huodong = M ( "xydzp" )->where ( "id=$xydzp_id And token='$token'" )->find ();
        // $list = M ( "xydzp_userlog" )->where ( "xydzp_id=$xydzp_id AND uid='$user_id' And FROM_UNIXTIME(cjdate, '%Y/%m/%d')=DATE_FORMAT(CURRENT_DATE,'%Y/%m/%d') " )->find ();
        // lastsql();dump($list);
        $huodong = D('Xydzp')->getXydzpInfo($xydzp_id);
        $list = D('XydzpUserlog')->getUserlogInfo($user_id, $xydzp_id);
        if ($list["id"] > 0) {
            if ($list["num"] > $huodong["choujnum"]) {
                return 0;
            } else {
                return ($huodong["choujnum"] - $list["num"]);
            }
        }
        return $huodong["choujnum"];
    }
    // 活动期限过期与否
    private function _is_overtime($xydzp_id, $the_vote = array())
    {
        empty($the_vote) && $the_vote = D('Xydzp')->getXydzpInfo($xydzp_id);
        if ($the_vote['end_date'] <= time()) {
            return true;
        }
        return false;
    }
    
    // 活动是否开始
    private function _is_starttime($xydzp_id, $the_vote = array())
    {
        empty($the_vote) && $the_vote = D('Xydzp')->getXydzpInfo($xydzp_id);
        if ($the_vote['start_date'] <= time()) {
            return true;
        }
        return false;
    }

    protected function checkAttr($Model, $model_id)
    {
        $fields = get_model_attribute($model_id);
        $validate = $auto = array();
        foreach ($fields as $key => $attr) {
            if ($attr['is_must']) { // 必填字段
                $validate[] = array(
                    $attr['name'],
                    'require',
                    $attr['title'] . '必须!'
                );
            }
            // 自动验证规则
            if (! empty($attr['validate_rule'])) {
                $validate[] = array(
                    $attr['name'],
                    $attr['validate_rule'],
                    $attr['error_info'] ? $attr['error_info'] : $attr['title'] . '验证错误',
                    0,
                    $attr['validate_type'],
                    $attr['validate_time']
                );
            }
            // 自动完成规则
            if (! empty($attr['auto_rule'])) {
                $auto[] = array(
                    $attr['name'],
                    $attr['auto_rule'],
                    $attr['auto_time'],
                    $attr['auto_type']
                );
            } elseif ('checkbox' == $attr['type']) { // 多选型
                $auto[] = array(
                    $attr['name'],
                    'arr2str',
                    3,
                    'function'
                );
            } elseif ('datetime' == $attr['type']) { // 日期型
                $auto[] = array(
                    $attr['name'],
                    'strtotime',
                    3,
                    'function'
                );
            }
        }
        return $Model->validate($validate)->auto($auto);
    }
}
