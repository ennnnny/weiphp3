<?php

namespace Addons\HelloWorld\Controller;
use Home\Controller\AddonsController;

class HelloWorldController extends AddonsController{
    public function see()
    {
        $str  = I('get.str') || 'tmep';
        dump(S('$str'));
    }

}
