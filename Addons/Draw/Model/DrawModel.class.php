<?php

namespace Addons\Draw\Model;
use Think\Model;

/**
 * Draw模型
 */
class DrawModel extends Model{
    protected $tableName = 'lzwg_activities';
    
    function getInfo($id, $update = false, $data = array()) {
        $key = 'Draw_getInfo_' . $id;
        $info = S ( $key );
        if ($info === false || $update) {
            $info = ( array ) (empty ( $data ) ? $this->find ( $id ) : $data);
        }
        S ( $key, $info, 86400 );
        return $info;
    }
}
