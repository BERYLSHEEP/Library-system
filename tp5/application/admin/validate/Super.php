<?php
/**
 * Created by PhpStorm.
 * User: 11727
 * Date: 2018/12/23
 * Time: 11:21
 */

namespace app\admin\validate;

use think\Validate;

class Super extends Validate{
    protected $rule=[
        'man_id|用户账号'=>'require|min:3',
        'man_name|用户名'=>'require',
        'man_password|密码'=>'require|min:6|confirm:man_repassword',
        'man_mail|邮箱'=>'require',
        'man_rank|等级'=>'require',
    ];
    protected $message=[
        'man_id.require'=>'用户账号不能为空',
        'man_name.require'=>'用户名不能为空',
        'man_password.require'=>'密码不能为空',
        'man_password.min'=>'密码长度不能少于6位',
        'man_password.confirm'=>'两次密码不一致',
        'man_mail.require'=>'邮箱不能为空',
        'man_rank.require'=>'管理员等级不能为空',
    ];
}