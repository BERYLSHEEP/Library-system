<?php
/**
 * Created by PhpStorm.
 * User: 11727
 * Date: 2018/11/28
 * Time: 20:35
 */
namespace app\admin\controller;

use app\admin\model\Reader as ReaderModel;
use think\Db;
use think\Validate;
class Reader extends Base
{
    public function readerlist(){

        //以表格形式输出书的内容
        //$data=Db::table('book_info')->select();
        //使用模型
        $reader=new ReaderModel();
        $data=$reader->select();
        //$data=Db::table('manager')->select();
        //'list'为返回前端的名字
        $this->assign('list',$data);

        return $this->fetch();


    }
    public function edit(){
        $id=input('get.readerId');
        $data=ReaderModel::get($id);
        //将数据渲染到模板
        $this->assign('data',$data);
        return $this->fetch();
    }

//更新内容的方法
    public function update(){

        $data=input('post.');

        $readerId=input('get.readerId');
        $reader=new ReaderModel();
        $ret=$reader->allowField(true)->save($data,['reader_id'=>$readerId]);
        if($ret){
            $this->success('读者罚款信息修改成功','readerlist');
        }
        else{
            $this->error('读者罚款信息修改失败');
        }
    }
    public function delete(){

        $id=input('get.readerId');

        $result=Db::table('reader')->where('reader_id',$id)->delete();
        if($result){

            $this->success('删除读者成功','readerlist');
        }
        else{

            $this->error('删除读者失败');
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
            $this->error($val->getError(),'readerlist');
            exit;
        }


        $id=$post['ids'];

        $reader=new ReaderModel();
        $reader->whereIn('reader_id',$id)->delete();
        $this->success('删除成功','readerlist');


    }

    //查找
    public function seeklist(){

        $post=input('post.');

        $type=$post['type'];
        //1表示按索引号查找

        if($type=='1'){
            $readerId=$post['content'];

            $reader=new ReaderModel();
            $data=$reader->where('reader_id',$readerId)->select();
            if($data){

                $this->assign('list',$data);
                return $this->fetch();
            }
            else{
                $this->error('未查找到该读者');
            }

        }
        //1表示按书名查找
        else if($type=='2'){

            $readerName=$post['content'];
            $reader=new ReaderModel();
            $data=$reader->where('reader_name',$readerName)->select();
            if($data){
                $this->assign('list',$data);
                return $this->fetch();
            }
            else{
                $this->error('未查找到该读者');
            }
        }
    }


}