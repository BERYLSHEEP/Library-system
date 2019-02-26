<?php

namespace app\admin\controller;
use app\admin\model\Appointment as AppModel;

use think\Db;
use think\Validate;


class Appointment extends Base
{
    public function applist(){

        $app=new appModel();
        $data=$app->select();
        //'list'为返回前端的名字
        $this->assign('list',$data);
        return $this->fetch();

    }
    public function delete(){
        $id=input('get.appId');
        $result=Db::table('appointment')->where('app_id',$id)->delete();
        if($result){

            $this->success('删除记录成功','applist');
        }
        else{

            $this->error('删除记录失败');
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
            $this->error($val->getError());
            exit;
        }


        $id=$post['ids'];

        $app=new AppModel();
        $app->whereIn('app_id',$id)->delete();
        $this->success('删除成功','applist');


    }

}