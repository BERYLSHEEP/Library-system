<?php

namespace app\admin\model;
use think\Model;

class Super extends Model{
    //表示该模型使用这个表
    protected $table = 'manager';
    protected  $auto=['man_password','man_repassword'];
//给密码加密
    public function setManPasswordAttr($value){
        return md5($value);
    }
    public function setManRepasswordAttr($value){
        return md5($value);
    }
}
