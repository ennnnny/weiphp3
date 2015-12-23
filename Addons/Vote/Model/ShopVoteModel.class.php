<?php

namespace Addons\Vote\Model;

use Think\Model;

/**
 * Vote模型
 */
class ShopVoteModel extends Model {
    function getInfo($id, $update = false, $data = array()) {
        $key = 'ShopVote_getInfo_' . $id;
        $info = S ( $key );
        if ($info === false || $update) {
            $info = ( array ) (empty ( $data ) ? $this->find ( $id ) : $data);
            S ( $key, $info, 86400 );
        }
        return $info;
    }
    
}
