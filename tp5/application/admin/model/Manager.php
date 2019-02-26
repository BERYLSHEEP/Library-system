<?php
namespace app\admin\model;
use think\Model;

class Manager extends Model{
    protected $table = 'manager';

	//对什么字段进行自动完成,这里对密码和重复输入的密码进行加密
	protected $auto=['man_password','man_repassword'];

	//对密码进行加密
//	protected function setManPasswordAttr($value){
//		return md5($value);
//
//
//	}
//	protected function setManRepasswordAttr($value){
//		return md5($value);
//	}
}
//不实现软删除了