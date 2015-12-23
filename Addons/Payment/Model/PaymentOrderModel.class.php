<?php

namespace Addons\Payment\Model;
use Think\Model;

/**
 * Payment模型
 */
class PaymentOrderModel extends Model{
    function getInfo($id, $update = false, $data = array()) {
        $key = 'PaymentOrder_getInfo_' . $id;
        $info = S ( $key );
        if ($info === false || $update) {
            $info = ( array ) (count ( $data )==0 ? $this->find ( $id ) : $data);
            S ( $key, $info );
        }
        return $info;
    }
}
