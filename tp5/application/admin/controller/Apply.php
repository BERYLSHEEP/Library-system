<?php

namespace app\admin\controller;

use think\Controller;
use app\admin\model\Apply as ApplyModel;
use app\admin\model\Reader;
use think\Validate;

use think\Db;


class Apply extends Base
{
    public function apply_list(){

        $apply=new ApplyModel();

        $data=$apply->join('reader_type','apply.reader_type_id=reader_type.reader_type_id')->select();


        //'list'为返回前端的名字
        $this->assign('list',$data);

        return $this->fetch();
    }
    public function agree(){
        $id1=input('get.applyId');
        $data= Db::table('apply')->where('apply_id',$id1)->find();
        //$id=array_column($data,'reader_id');
        //$id=$data['apply_id'];
        //先判断是否有该学生
        //$ret=Db::table('reader')->where('reader_id',$id[0])->find();
        $id=$data['reader_id'];
        $ret=Db::table('reader')->where('reader_id',$id)->find();
        if($ret){
            $this->error('该读者已存在，审核不同意');
        }
        else{
        //同意申请，状态改为1
        $apply=new ApplyModel();
        $apply->where('apply_id',$id1)->update(['apply_result'=>1]);

//$data是一个二维数组
//      $password=array_column($data,'reader_password');
////      $name=array_column($data,'reader_name');
////      $type=array_column($data,'reader_type_id');
////      $date=array_column($data,'apply_date');
////      $tel=array_column($data,'reader_tel');
////      $mail=array_column($data,'reader_mail');
            //同意申请，将读者的信息存入读者信息表中

            $password=$data['reader_password'];
            $name=$data['reader_name'];
            $type=$data['reader_type_id'];
            $date=$data['apply_date'];
            $tel=$data['reader_tel'];
            $mail=$data['reader_mail'];
//            $newData=['reader_id'=>$id[0],'reader_password'=>$password[0],
//            'reader_name'=>$name[0],'reader_type_id'=>$type[0],'reader_date'=>$date[0],
//            'reader_tel'=>$tel[0],'reader_mail'=>$mail[0]];
            $newData=['reader_id'=>$id,'reader_password'=>$password,
            'reader_name'=>$name,'reader_type_id'=>$type,'reader_date'=>$date,
            'reader_tel'=>$tel,'reader_mail'=>$mail];
        $result=Db::table('reader')->insert($newData);
     if($result) {

          $this->success('审核成功','apply_list');

      }
     else{
         $this->error('审核失败');
     }
      }
    }


    public function  delete(){
        $id=input('get.applyId');
        $apply=new ApplyModel();
        $rel=$apply->where('apply_id',$id)->delete();
        if($rel){
            $this->success('删除成功','apply_list');
        }
        else{
            $this->error('删除失败');
        }

    }
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

        $apply=new ApplyModel();
        $apply->whereIn('apply_id',$id)->delete();
        $this->success('删除成功','apply_list');


    }
}