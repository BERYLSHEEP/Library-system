<?php
/**
 * Created by PhpStorm.
 * User: 11727
 * Date: 2018/11/18
 * Time: 23:57
 */

//基类控制器
namespace app\admin\controller;

use think\Controller;
class Base extends Controller{
    public function _initialize()
    {
        if(!session('man_id')){
            $this->error('请先登录！','Index/login');


        }
    }

}