<?php

namespace app\admin\controller;

use think\Controller;
use app\admin\model\Manager as ManModel;
use app\admin\validate\Manager as ManValidate;
use think\Validate;
class Manager extends Base
{
	public function index(){
  return $this->fetch();
}
    public function index2(){
        return $this->fetch();
    }
    public function index3(){
        return $this->fetch();
    }
    public function info(){

        $id=session('man_id');
        $data=ManModel::get($id);

    	$this->assign('data', $data);

        return $this->fetch();
    }
    public function insert(){
    	$data=input('post.');
    	$val=new ManValidate();
    	if(!$val->check($data)){
    		
    	}
    }
    public function edit(){
        $id=session('man_id');
        $data=ManModel::get($id);
        //将数据渲染到模板
        $this->assign('data',$data);
        return $this->fetch();
    }

//更新内容的方法
    public function update(){

        $data=input('post.');

        $managerId=session('man_id');
        $data['man_id']= $managerId;

        $val=new ManValidate();
        if(!$val->check($data)){
            $this->error($val->getError());
            exit;
        }
        $data['man_password']=md5($data['man_password']);
        $data['man_repassword']=md5($data['man_repassword']);
        $manager=new ManModel();
        $ret=$manager->allowField(true)->save($data,['man_id'=>$managerId]);
        if($ret){
            $this->success('修改个人信息成功','info');
        }
        else{
            $this->error('修改个人信息失败');
        }
    }
    public function home(){
        return $this->fetch();
    }
}
