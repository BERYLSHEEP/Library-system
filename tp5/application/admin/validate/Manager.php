<?php
namespace app\admin\validate;
use think\Validate;

//自定义的验证器
class Manager extends Validate{
	protected $rule=[
        'man_name|用户名'=>'require',
         'man_password|密码'=>'require|min:6|confirm:man_repassword',
       'man_mail|邮箱'=>'require',
	];
		protected $message=[
            'man_name.require'=>'用户名不能为空',
            'man_password.require'=>'密码不能为空',
            'man_password.min'=>'密码长度不能少于6位',
            'man_password.confirm'=>'两次密码不一致',
            'man_mail.require'=>'邮箱不能为空',
		];
}
