<?php
/**
 * Created by PhpStorm.
 * User: 11727
 * Date: 2018/11/27
 * Time: 21:05
 */
namespace app\admin\controller;
use app\admin\model\Lend as LendModel;
use app\admin\model\Reader ;
use think\Controller;
use think\Db;
use think\Validate;

class Lend extends Base
{
    public function recordlist(){

        //以表格形式输出书的内容
        //$data=Db::table('book_info')->select();
        //使用模型
//将记录更新
        $time=date("y-m-d");
        // dump($time);
        $record1=new LendModel();
        $data=$record1->select();


        foreach($data as $list){

            if($list['borrowed_rdate']==NULL&&(strtotime($time)>strtotime($list['borrowed_edate']))){
                $lend=new LendModel();
                $lend->where('borrowed_id',$list['borrowed_id'])->update(['is_overed'=>'1']);



            }
            else if($list['borrowed_rdate']!=NULL&&(strtotime($list['borrowed_edate'])<strtotime($list['borrowed_rdate']))){
                $lend=new LendModel();
                $lend->where('borrowed_id',$list['borrowed_id'])->update(['is_overed'=>'1']);

            }
        }


        $record=new LendModel();
        $data=$record->select();

        //'list'为返回前端的名字
        $this->assign('list',$data);

        return $this->fetch();



    }
    //判断书是否到期，更新借阅记录表的状态
    public function update(){
        $time=date("y-m-d");

        $record=new LendModel();
        $data=$record->select();

        foreach($data as $list){

            echo $list['borrowed_sdate'];
            if($list['borrowed_rdate']==NULL&&(strtotime($time)>strtotime($list['borrowed_edate']))){
                $lend=new LendModel();
                $lend->where('borrowed_id',$list['borrowed_id'])->update(['is_overed'=>'1']);



                }
            else if($list['borrowed_rdate']!=NULL&&(strtotime($list['borrowed_edate'])>strtotime($list['borrowed_rdate']))){
                $lend=new LendModel();
                $lend->where('borrowed_id',$list['borrowed_id'])->update(['is_overed'=>'1']);

            }
        }


    }
    //对延期还书的读者进行扣费
    public function  fine(){

        $record=new LendModel();
        $time=date("y-m-d");
        $data=$record->where('is_overed','1')->select();
        foreach($data as $list){
            if($list['borrowed_rdate']==NULL){
                $end_date=strtotime($time);
                $begin_date =strtotime($list['borrowed_edate']);
                $days = round(($end_date - $begin_date) / 3600 / 24);
                $reader=new Reader();
                $origin_fine=$reader->where('reader_id',$list['reader_id'])->value('reader_fine');

                $fine=$origin_fine+$days;
                $origin_credit=$reader->where('reader_id',$list['reader_id'])->value('reader_credit');
                //每逾期5天，信用值扣1
                $credit=$origin_credit-$days/5;
                $reader->where('reader_id',$list['reader_id'])->update(['reader_fine'=>$fine,'reader_credit'=>$credit]);

            }
            else {
                $end_date=strtotime($list['borrowed_rdate']);
                $begin_date =strtotime($list['borrowed_edate']);
                $days = round(($end_date - $begin_date) / 3600 / 24);
                $reader=new Reader();
                $origin_fine=$reader->where('reader_id',$list['reader_id'])->value('reader_fine');

                $fine=$origin_fine+$days;
                $origin_credit=$reader->where('reader_id',$list['reader_id'])->value('reader_credit');
                //每逾期5天，信用值扣1
                $credit=$origin_credit-$days/5;
                $reader->where('reader_id',$list['reader_id'])->update(['reader_fine'=>$fine,'reader_credit'=>$credit]);


            }

    }
        $this->success('需要罚款和扣信用值的读者成功设置','screen');
    }
    //撤销上一步的操作.即回退一步。防止重复罚款和扣除信用值
    public function  reset(){
        $record=new LendModel();
        $time=date("y-m-d");
        $data=$record->where('is_overed','1')->select();
        foreach($data as $list){
            if($list['borrowed_rdate']==NULL){
                $end_date=strtotime($time);
                $begin_date =strtotime($list['borrowed_edate']);
                $days = round(($end_date - $begin_date) / 3600 / 24);
                $reader=new Reader();
                $origin_fine=$reader->where('reader_id',$list['reader_id'])->value('reader_fine');

                $fine=$origin_fine-$days;
                $origin_credit=$reader->where('reader_id',$list['reader_id'])->value('reader_credit');

                $credit=$origin_credit+$days/5;
                $reader->where('reader_id',$list['reader_id'])->update(['reader_fine'=>$fine,'reader_credit'=>$credit]);
            }
            else {
                $end_date=strtotime($list['borrowed_rdate']);
                $begin_date =strtotime($list['borrowed_edate']);
                $days = round(($end_date - $begin_date) / 3600 / 24);
                $reader=new Reader();
                $origin_fine=$reader->where('reader_id',$list['reader_id'])->value('reader_fine');

                $fine=$origin_fine-$days;
                $origin_credit=$reader->where('reader_id',$list['reader_id'])->value('reader_credit');

                $credit=$origin_credit+$days/5;
                $reader->where('reader_id',$list['reader_id'])->update(['reader_fine'=>$fine,'reader_credit'=>$credit]);

            }

        }
        $this->success('成功回退','screen');
    }
    //筛选出延期还书的读者
public function screen(){
    $record=new LendModel();

    $data=$record->where('is_overed','1')->select();
    $this->assign('list',$data);
    return $this->fetch();
}
    public function delete(){

        $id=input('get.borrowedId');
        $result=Db::table('borrowed')->where('borrowed_id',$id)->delete();
        if($result){

            $this->success('删除记录成功','recordlist');
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

        $lend=new LendModel();
        $lend->whereIn('borrowed_id',$id)->delete();
        $this->success('删除成功','recordlist');


    }

}