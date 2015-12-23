<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: huajie <banhuajie@163.com>
// +----------------------------------------------------------------------
namespace Admin\Controller;

use Admin\Model\AuthGroupModel;

/**
 * 模型管理控制器
 *
 * @author huajie <banhuajie@163.com>
 */
class ModelController extends AdminController {
	
	/**
	 * 模型管理首页
	 *
	 * @author huajie <banhuajie@163.com>
	 */
	public function index() {
		$map = array (
				'status' => array (
						'gt',
						- 1 
				) 
		);
		if (isset ( $_GET ['title'] )) {
			$title = I ( 'get.title' );
			$map ['name'] = array (
					'like',
					"%$title%" 
			);
		}
		$list = $this->lists ( 'model', $map );
		
		$addonArr = $this->_get_all_addon ();
		foreach ( $list as &$vo ) {
			empty ( $vo ['addon'] ) || $vo ['addon'] = $addonArr [$vo ['addon']];
		}
		
		int_to_string ( $list );
		// 记录当前列表页的cookie
		Cookie ( '__forward__', $_SERVER ['REQUEST_URI'] );
		
		$this->assign ( '_list', $list );
		$this->meta_title = '模型管理';
		$this->display ();
	}
	
	/**
	 * 新增页面初始化
	 *
	 * @author huajie <banhuajie@163.com>
	 */
	public function add() {
		// 获取所有的模型
		$models = M ( 'model' )->field ( 'id,name,title' )->select ();
		$this->assign ( 'models', $models );
		
		$this->_get_all_addon ();
		
		$this->meta_title = '新增模型';
		$this->display ();
	}
	function _get_all_addon() {
		$list = M ( 'addons' )->field ( 'name,title' )->select ();
		
		$arr ['Core'] = '系统核心模块';
		foreach ( $list as $vo ) {
			$arr [$vo ['name']] = $vo ['title'];
		}
		$this->assign ( 'list', $arr );
		
		return $arr;
	}
	
	/**
	 * 编辑页面初始化
	 *
	 * @author huajie <banhuajie@163.com>
	 */
	public function edit() {
		$id = I ( 'get.id', '' );
		if (empty ( $id )) {
			$this->error ( '参数不能为空！' );
		}
		
		/* 获取一条记录的详细数据 */
		$Model = M ( 'model' );
		$data = $Model->field ( true )->find ( $id );
		if (! $data) {
			$this->error ( $Model->getError () );
		}
		// 更新前台缓存
		S ( 'getModelByName_' . $data ['name'], NULL );
		
		$data ['attribute_list'] = empty ( $data ['attribute_list'] ) ? '' : explode ( ",", $data ['attribute_list'] );
		$fieldArr = M ( 'attribute' )->where ( array (
				'model_id' => $data ['id'] 
		) )->order ( 'id asc' )->getField ( 'id,name,title,is_show', true );
		
		foreach ( $fieldArr as $f ) {
			$fields [$f ['name']] = $f;
		}
		$fields = empty ( $fields ) ? array () : $fields;
		
		// 梳理属性的可见性
		foreach ( $fields as $key => $field ) {
			if (! empty ( $data ['attribute_list'] ) && ! in_array ( $field ['id'], $data ['attribute_list'] )) {
				$fields [$key] ['is_show'] = 0;
			}
		}
		
		// 获取模型排序字段
		$field_sort = json_decode ( $data ['field_sort'], true );
		if (! empty ( $field_sort )) {
			/* 对字段数组重新整理 */
			$sort = array ();
			foreach ( $field_sort as $s ) {
				$sort [$s] = $fields [$s];
				unset ( $fields [$s] );
			}
			
			$fields = array_merge ( $sort, $fields );
		}
		
		$this->_get_all_addon ();
		
		$this->assign ( 'fields', $fields );
		$this->assign ( 'info', $data );
		$this->meta_title = '编辑模型';
		$this->display ();
	}
	
	/**
	 * 删除一条数据
	 *
	 * @author huajie <banhuajie@163.com>
	 */
	public function del() {
		$ids = I ( 'get.ids' );
		empty ( $ids ) && $this->error ( '参数不能为空！' );
		$ids = explode ( ',', $ids );
		foreach ( $ids as $value ) {
			$res = D ( 'Model' )->del ( $value );
			if (! $res) {
				break;
			}
		}
		if (! $res) {
			$this->error ( D ( 'Model' )->getError () );
		} else {
			$this->success ( '删除模型成功！' );
		}
	}
	
	/**
	 * 更新一条数据
	 *
	 * @author huajie <banhuajie@163.com>
	 */
	public function update() {
		$res = D ( 'Model' )->update ();
		
		if (! $res) {
			$this->error ( D ( 'Model' )->getError () );
		} else {
			$this->success ( $res ['id'] ? '更新成功' : '新增成功', Cookie ( '__forward__' ) );
		}
	}
	
	/**
	 * 生成一个模型
	 *
	 * @author huajie <banhuajie@163.com>
	 */
	public function generate() {
		if (! IS_POST) {
			// 获取所有的数据表
			$tables = D ( 'Model' )->getTables ();
			
			$this->assign ( 'tables', $tables );
			$this->meta_title = '生成模型';
			$this->display ();
		} else {
			$table = I ( 'post.table' );
			empty ( $table ) && $this->error ( '请选择要生成的数据表！' );
			$res = D ( 'Model' )->generate ( $table, I ( 'post.name' ), I ( 'post.title' ) );
			if ($res) {
				$this->success ( '生成模型成功！', U ( 'index' ) );
			} else {
				$this->error ( D ( 'Model' )->getError () );
			}
		}
	}
	/**
	 * 导出一个模型
	 */
	public function export($is_all = false, $model_id = 0, $export_type = 0, $return_sql = false) {
		$id = empty ( $model_id ) ? I ( 'get.id' ) : $model_id;
		$type = empty ( $export_type ) ? I ( 'get.type', 0, 'intval' ) : $export_type;
		empty ( $id ) && $this->error ( '参数不能为空！' );
		
		// 模型信息
		$map ['id'] = $id;
		$model = D ( 'Model' )->where ( $map )->find ();
		
		// 模型字段
		$map2 ['model_id'] = $id;
		$list = D ( 'Attribute' )->where ( $map2 )->order('id asc')->select ();
		
		// 模型数据表
		$name = get_table_name ( $model ['id'] );
		$return_sql || $data = M ( parse_name ( $name, true ) )->select ();
		$name = strtolower ( $name );
		if ($type == 1) {
			$sql = "DELETE FROM `wp_attribute` WHERE model_id = (SELECT id FROM wp_model WHERE `name`='{$model['name']}' ORDER BY id DESC LIMIT 1);\r\n";
			$sql .= "DELETE FROM `wp_model` WHERE `name`='{$model['name']}' ORDER BY id DESC LIMIT 1;\r\n";
			$sql .= "DROP TABLE IF EXISTS `wp_" . strtolower ( $name ) . "`;";
			$path = $is_all ? RUNTIME_PATH . 'uninstall/' . $model ['name'] . '.sql' : RUNTIME_PATH . 'uninstall.sql';
		} else {
			// 获取索引表
			$index = '';
			$index_list = M ()->query ( "SHOW INDEX FROM wp_{$name}" );
			foreach ( $index_list as $vo ) {
				if ($vo ['Key_name'] == 'PRIMARY')
					continue;
				
				if (isset ( $indexArr [$vo ['Key_name']] )) {
					$indexArr [$vo ['Key_name']] ['field'] [] = $vo ['Column_name'];
				} else {
					$px_type = '';
					if ($vo ['Index_type'] == 'FULLTEXT') {
						$px_type = 'FULLTEXT ';
					} elseif ($vo ['Non_unique'] == 0) {
						$px_type = 'UNIQUE ';
					}
					$indexArr [$vo ['Key_name']] ['text'] = $px_type . 'KEY `' . $vo ['Key_name'] . '`';
					$indexArr [$vo ['Key_name']] ['field'] [] = '`' . $vo ['Column_name'] . '`';
				}
			}
			foreach ( $indexArr as $vv ) {
				$index .= $vv ['text'] . ' (' . implode ( ',', $vv ['field'] ) . "),\r\n";
			}
			$index = trim ( $index, ",\r\n" );
			
			if ($model ['need_pk']) {
				$create_table = "`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',\r\n";
				$key = empty ( $index ) ? "PRIMARY KEY (`id`)" : "PRIMARY KEY (`id`),\r\n";
			}
			
			foreach ( $list as $field ) {
				// 获取默认值
				if ($field ['value'] === '') {
					$default = '';
				} elseif (is_numeric ( $field ['value'] )) {
					$default = ' DEFAULT ' . $field ['value'];
				} elseif (is_string ( $field ['value'] )) {
					$default = ' DEFAULT \'' . $field ['value'] . '\'';
				} else {
					$default = '';
				}
				$create_table .= "`{$field['name']}`  {$field['field']} {$default} COMMENT '{$field['title']}',\r\n";
			}
			
			$sql .= <<<sql
CREATE TABLE IF NOT EXISTS `wp_{$name}` (
{$create_table}{$key}{$index}
) ENGINE={$model['engine_type']} DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci CHECKSUM=0 ROW_FORMAT=DYNAMIC DELAY_KEY_WRITE=0;\r\n
sql;
			$search = array (
					"\r\n",
					"'" 
			);
			$replace = array (
					'\r\n',
					"\\'" 
			);
			unset ( $field );
			foreach ( $data as $d ) {
				$field = '';
				$value = '';
				foreach ( $d as $k => $v ) {
					$field .= "`$k`,";
					$value .= "'" . str_replace ( $search, $replace, $v ) . "',";
				}
				$sql .= "INSERT INTO `wp_{$name}` (" . rtrim ( $field, ',' ) . ') VALUES (' . rtrim ( $value, ',' ) . ");\r\n";
			}
			
			unset ( $model ['id'] );
			$field = '';
			$value = '';
			foreach ( $model as $k => $v ) {
				$field .= "`$k`,";
				$value .= "'" . str_replace ( $search, $replace, $v ) . "',";
			}
			$sql .= 'INSERT INTO `wp_model` (' . rtrim ( $field, ',' ) . ') VALUES (' . rtrim ( $value, ',' ) . ');' . "\r\n";
			
			// dump($list);
			foreach ( $list as $k => $vo ) {
				unset ( $vo ['id'] );
				$vo ['model_id'] = 0;
				$field = '';
				$value = '';
				foreach ( $vo as $k => $v ) {
					$field .= "`$k`,";
					$value .= "'" . str_replace ( $search, $replace, $v ) . "',";
				}
				$sql .= 'INSERT INTO `wp_attribute` (' . rtrim ( $field, ',' ) . ') VALUES (' . rtrim ( $value, ',' ) . ');' . "\r\n";
			}
			$sql .= 'UPDATE `wp_attribute` SET model_id= (SELECT MAX(id) FROM `wp_model`) WHERE model_id=0;';
			
			$path = $is_all ? RUNTIME_PATH . 'install/' . $model ['name'] . '.sql' : RUNTIME_PATH . 'install.sql';
		}
		
		if ($return_sql)
			return $sql;
		
		if ($is_all) {
			mkdirs ( RUNTIME_PATH . 'install' );
			mkdirs ( RUNTIME_PATH . 'uninstall' );
		}
		
		@file_put_contents ( $path, $sql );
		
		if (! $is_all)
			redirect ( SITE_URL . '/' . $path );
	}
	// 一键增加微信插件常用模型
	function add_comon_model() {
		$install_sql = './Application/Admin/Conf/common_model.sql';
		if (file_exists ( $install_sql )) {
			execute_sql_file ( $install_sql );
		}
		$this->success ( '增加成功' );
	}
	// 导出全部模型数据
	function export_all() {
		$id = I ( 'id', 0, 'intval' );
		$type = I ( 'type', 0, 'intval' );
		$map ['id'] = array (
				'gt',
				$id 
		);
		$info = M ( 'model' )->where ( $map )->order ( 'id asc' )->find ();
		if (! $info) {
			echo 'It is over';
			exit ();
		}
		
		$this->export ( true, $info ['id'], 0 );
		$this->export ( true, $info ['id'], 1 );
		
		$param ['id'] = $info ['id'];
		
		echo 'export ' . $info ['name'] . ' now...';
		
		$url = U ( 'export_all', $param );
		echo '<script>window.location.href="' . $url . '"</script> ';
	}
	// 更新插件的安装卸载文件
	function update_sql() {
		set_time_limit ( 0 );
		
		$id = I ( 'id', 0, 'intval' );
		$map ['id'] = array (
				'gt',
				$id 
		);
		$addon = M ( 'addons' )->where ( $map )->order ( 'id asc' )->find ();
		if (! $addon) {
			echo 'It is over';
			exit ();
		}
		$map2 ['addon'] = $addon ['name'];
		$list = M ( 'model' )->where ( $map2 )->order ( 'id asc' )->select ();
		if (! empty ( $list )) {
			$path = realpath ( SITE_PATH . '/Addons/' . $addon ['name'] );
			
			$install_sql = $uninstall_sql = '';
			foreach ( $list as $info ) {
				$install_sql .= $this->export ( true, $info ['id'], 0, true ) . "\r\n" . "\r\n" . "\r\n";
				$uninstall_sql .= $this->export ( true, $info ['id'], 1, true ) . "\r\n" . "\r\n" . "\r\n";
			}
			
			// 更新文件
			@file_put_contents ( $path . '/install.sql', $install_sql );
			@file_put_contents ( $path . '/uninstall.sql', $uninstall_sql );
		}
		$param ['id'] = $addon ['id'];
		echo 'update ' . $addon ['name'] . ' now...';
		
		$url = U ( 'update_sql', $param );
		echo '<script>window.location.href="' . $url . '"</script> ';
	}
}
