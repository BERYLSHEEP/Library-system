<?php

namespace app\admin\controller;

use think\Controller;

use app\admin\validate\Super as superValidate;
use think\Db;
use think\Validate;
use app\admin\model\Manager ;
class Super extends Controller
{
    public function index(){
        return $this->fetch();
    }
    public function add()
    {
        return $this->fetch();
    }
    public function insert()
    {
        $post = input('post.');
        //用自定义的验证器有问题
        /* $val=Loader::validate('Manager');
         echo'1111';
        if(!$val->check($post)){
             //dump($post);
            echo'2222';
             $this->error($val->getError());
             exit;
         }
         echo'3333';*/

//这样使用验证器没问题
//        $rule = [
//            'man_id|用户账号' => 'require|min:3',
//            'man_name|用户名' => 'require',
//            'man_password|密码' => 'require|min:6|confirm:man_repassword',
//            'man_mail|邮箱' => 'require',
//        ];
//        $message = [
//            'man_id.require' => '用户账号不能为空',
//            'man_name.require' => '用户名不能为空',
//            'man_password.require' => '密码不能为空',
//            'man_password.min' => '密码长度不能少于6位',
//            'man_password.confirm' => '两次密码不一致',
//            'man_mail.require' => '邮箱不能为空',
//
//        ];
        $val = new superValidate();
        if (!$val->check($post)) {
            $this->error($val->getError());
            exit;
        }
        $id = $post['man_id'];
        $ret = Db::table('manager')->where('man_id', $id)->find();

        if ($ret) {
            $this->error('管理员id不能重复，添加失败');
        }
        else{
        $manager = new Manager();
        $post['man_password']=md5($post['man_password']);
        $post['man_repassword']=md5($post['man_repassword']);

        $result = $manager->allowField(true)->save($post);

        if ($result) {
            $this->success('新增管理员成功', 'manlist');
        } else {
            $this->error('新增管理员失败');
        }
        }
    }
    public function manlist(){

        //以表格形式输出书的内容
        //$data=Db::table('book_info')->select();
        //使用模型
        $manager=new Manager();
        $data=$manager->select();
        //$data=Db::table('manager')->select();
        //'list'为返回前端的名字
        $this->assign('list',$data);

        return $this->fetch();


    }
    public function delete(){

        $id=input('get.manId');

        $manager=Manager::get($id);
        $result=$manager->delete();
        //$result=Db::table('')->where('man_id',$id)->delete();
        if($result){

            $this->success('删除管理员成功','manlist');
        }
        else{

            $this->error('删除管理员失败');
        }
    }
    //批量删除
    public  function  del(){



        $post=input('post.');

        $rule=[
            'ids|索引号'=>'require',];
        $message=[
            'ids.require'=>'勾选框不能为空',];
        $val=new Validate($rule,$message);

        if(!$val->check($post)){
            $this->error($val->getError(),'manlist');
            exit;
        }

        //dump($post);
        $id=$post['ids'];

        $manager=new Manager();
        $manager->whereIn('man_id',$id)->delete();
        $this->success('删除成功','manlist');


    }
    public function edit(){
        $id=input('get.manId');
        $data=Manager::get($id);

        //将数据渲染到模板
        $this->assign('data',$data);
        return $this->fetch();
    }

//更新内容的方法
    public function update(){

        $data=input('post.');
        $manId=input('get.manId');
        $data['man_password']=md5($data['man_password']);
        $data['man_repassword']=md5($data['man_repassword']);
        $data['man_id']=$manId;

        $val = new superValidate();
        if (!$val->check($data)) {
            $this->error($val->getError());
            exit;
        }
        $manager=new Manager();

        $ret=$manager->allowField(true)->save($data,['man_id'=>$manId]);
        if($ret){

            $this->success('修改管理员信息成功','manlist');
        }
        else{
            $this->error('修改管理员信息失败');
        }
    }
    public function seeklist(){

        $post=input('post.');

        $type=$post['type'];
        //1表示按索引号查找

        if($type=='1'){
            $manId=$post['content'];

            $manager=new Manager();
            $data=$manager->where('man_id',$manId)->select();
            if($data){

                $this->assign('list',$data);
                return $this->fetch();
            }
            else{
                $this->error('未查找到该管理员');
            }

        }
        //1表示按书名查找
        else if($type=='2'){

            $manName=$post['content'];
            $manager=new Manager();
            $data=$manager->where('man_name',$manName)->select();
            if($data){
                $this->assign('list',$data);
                return $this->fetch();
            }
            else{
                $this->error('未查找到该管理员');
            }
        }
    }


}

