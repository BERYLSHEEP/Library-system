<?php

namespace app\admin\controller;

use think\Controller;
use app\admin\model\Book as BookModel;
use app\admin\validate\Book as BookValidate ;
use think\Validate;

use think\Db;


class Check extends Base
{
    public function feedback(){
        $data=Db::table('feedback')->select();
        //'list'为返回前端的名字
        $this->assign('list',$data);
        return $this->fetch();
    }
    public function comment(){
        $data=Db::table('comment')->select();
        //'list'为返回前端的名字
        $this->assign('list',$data);
        return $this->fetch();
    }
    public function fdelete(){
        $id=input("get.fdbId");

    $result=Db::table('feedback')->where('fdb_id',$id)->delete();
    if($result){

        $this->success('删除反馈成功','feedback');
    }
    else{

        $this->error('删除反馈失败');
    }
    }
    public function cdelete(){
        $id=input("get.comId");

        $result=Db::table('comment')->where('com_id',$id)->delete();
        if($result){

            $this->success('删除评论成功','comment');
        }
        else{

            $this->error('删除评论失败');
        }
    }
//批量删除反馈
    public  function  fdel(){



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
        Db::table('feedback')->whereIn('fdb_id',$id)->delete();
        $this->success('删除成功','feedback');


    }
    //批量删除评论
    public  function  cdel(){



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
        Db::table('comment')->whereIn('com_id',$id)->delete();
        $this->success('删除成功','comment');


    }
    public function jou_comment(){
        $data=Db::table('jou_comment')->select();
        //'list'为返回前端的名字
        $this->assign('list',$data);
        return $this->fetch();
    }
    public function jdelete(){
        $id=input("get.comId");

        $result=Db::table('jou_comment')->where('com_id',$id)->delete();
        if($result){

            $this->success('删除评论成功','jou_comment');
        }
        else{

            $this->error('删除评论失败');
        }
    }
    //批量删除评论
    public  function  jdel(){



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
        Db::table('jou_comment')->whereIn('com_id',$id)->delete();
        $this->success('删除成功','jou_comment');


    }
}