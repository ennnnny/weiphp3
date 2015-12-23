<?php

namespace Common\Model;

use Think\Model;

/**
 * 计数池
 */
class CountModel extends Model {
	protected $tableName = 'user';
	// 写入缓存
	function set($table, $id, $field, $step = 1) {
		$key = 'Comment_Count_set_datas';
		$datas = ( array ) S ( $key );
		$index = $table . '|' . $id;
		if (isset ( $datas [$index] [$field] )) {
			$datas [$index] [$field] += $step;
		} else {
			$datas [$index] [$field] = $step;
		}
		S ( $key, $datas );
		
		// 数据过大时自动写入数据库
		$count = count ( $datas );
		if ($count > 100) {
			$this->write ();
		}
	}
	// 异步写入数据库
	function write() {
		$key = 'Comment_Count_set_datas';
		$datas = ( array ) S ( $key );
		S ( $key, null );
		
		foreach ( $datas as $k => $d ) {
			list ( $table, $id ) = explode ( '|', $k );
			$set = '';
			foreach ( $d as $f => $c ) {
				$set .= "`$f`=`$f`+$c, ";
			}
			$set = rtrim ( $set, ', ' );
			if (empty ( $set ))
				continue;
			
			$sql = "UPDATE wp_{$table} SET $set WHERE id=" . $id . ' limit 1';
			M ()->execute ( $sql );
		}
	}
}
?>
