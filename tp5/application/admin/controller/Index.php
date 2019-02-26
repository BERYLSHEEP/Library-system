<?php
/**
 * Created by PhpStorm.
 * User: 11727
 * Date: 2018/11/18
 * Time: 23:31
 */
namespace app\admin\controller;
use app\admin\model\Manager;

use think\Controller;
class Index extends Controller{
    //登录
    public function login(){
        return $this->fetch();
    }
    //验证登录
    public function check(){
        $data=input('post.');

        $manager=new Manager();
        $result=$manager->where('man_id',$data['man_id'])->find();
       if($result){
           $rank=$result['man_rank'];
           $password=$data['man_password'];

            //if($result['man_password']==$data['man_password']){
           if($result['man_password']== md5($password)){
                if(captcha_check($data['code'])){
                    session('man_id',$data['man_id']);
                    if($rank=='1'){
                        $this->success('登录成功','manager/index');
                    }
                    else if($rank=='2'){
                        $this->success('登录成功','manager/index2');
                    }
                    else{
                        $this->success('登录成功','manager/index3');
                    }
                }
                else{
                    $this->error('验证码错误');
                }




            }
            else{
                $this->error('密码错误');
            }
        }
        else{
            $this->error('账号不存在或输入错误');
            exit;
        }



    }
    //用户退出登录
    public function  logout(){
        session(null);
        $this->success('退出登录成功','index/login');

    }
}