<?php
/**
 * Created by PhpStorm.
 * User: 11727
 * Date: 2018/12/17
 * Time: 17:48
 */

namespace app\admin\controller;

use think\Controller;
use app\admin\model\Journal as JourModel;
use app\admin\validate\Journal as JournalValidate ;

use think\Db;
use think\Validate;

class Journal extends Base
{
    public function index(){




        return $this->fetch();

    }
    public function add(){

        return $this->fetch();


    }
//添加图书
    public function insert()
    {

        $post = input('post.');
        //验证器有问题
        /*$val=new BookValidate();
        if(!$val->check($post)){
            dump($post);
            $this->error($val->getError());
            exit;
        }*/

        //这样验证还是有问题，但是管理员的是对的

//        $rule = [
//            'book_id|期刊索引号' => 'require',
//            'jou_name|期刊名' => 'require',
//            'book_type_id|期刊类别' => 'require',
//            'jou_price|期刊价格' => 'require',
//
//            'jou_quantity|期刊数量' => 'require',
//            'publish_id|期刊出版社编号' => 'require',
//            'jou_publish_date|期刊出版日期' => 'require',
//            'location_id|期刊馆藏位置编号' => 'require',
//        ];
//        $message = [
//            'book_id.require' => '期刊索引号不能为空',
//            'jou_name.require' => '期刊名不能为空',
//            'book_type_id_require' => '期刊类别不能为空',
//            'jou_price.require' => '价格不能为空',
//            'jou_quantity.require' => '数量不能为空',
//            'jou_publish_date.require' => '出版日期不能为空',
//            'publish_id.require' => '出版社编号不能为空',
//            'location_id.require' => '馆藏位置编号不能为空',
//
//        ];
        $val = new JournalValidate();
        if (!$val->check($post)) {
            $this->error($val->getError());
            exit;
        }

//添加书的时候，出版日期必须时20XX-XX-XX格式，不然会报错
        $id = $post['book_id'];
        $ret = DB::table('journal')->where('book_id', $id)->find();
        if ($ret) {
            $this->error('该期刊已存在，添加失败');
        } else {
            $journal = new JourModel($post);

            $result = $journal->allowField(true)->save();
            if ($result) {
                $this->success('添加期刊成功', 'jourlist');
            } else {
                $this->error('添加期刊失败');
            }
        }
    }

    //后面再实现吧
    public function seek()
    {

        return $this->fetch();

    }
    public function seeklist(){

        $post=input('post.');

        $type=$post['type'];
        //1表示按索引号查找

        if($type=='1'){
            $bookId=$post['content'];

            $journal=new JourModel();
            $data=$journal->where('book_id',$bookId)->join('book_type','journal.book_type_id=book_type.book_type_id')
                ->join('publish','journal.publish_id=publish.publish_id')
                ->join('location','journal.location_id=location.location_id')->select();
        //'list'为返回前端的名字
        $this->assign('list',$data);

            if($data){
                $this->assign('list',$data);
                return $this->fetch();
            }
            else{
                $this->error('未查找到该期刊');
            }

        }
        //1表示按书名查找
        else if($type=='2'){

            $jourName=$post['content'];
            $journal=new JourModel();
            $data=$journal->where('jou_name',$jourName)->join('book_type','journal.book_type_id=book_type.book_type_id')
                ->join('publish','journal.publish_id=publish.publish_id')
                ->join('location','journal.location_id=location.location_id')->select();
            if($data){
                $this->assign('list',$data);
                return $this->fetch();
            }
            else{
                $this->error('未查找到该期刊');
            }
        }
    }


    public function delete(){

        $id=input('get.bookId');

        $result=Db::table('journal')->where('book_id',$id)->delete();
        if($result){

            $this->success('删除期刊成功','jourlist');
        }
        else{

            $this->error('删除期刊失败');
        }
    }
    public function jourlist(){

        //以表格形式输出书的内容

        //使用模型
        $journal=new JourModel();
        $data=$journal->join('book_type','journal.book_type_id=book_type.book_type_id')
            ->join('publish','journal.publish_id=publish.publish_id')
            ->join('location','journal.location_id=location.location_id')->select();
        //'list'为返回前端的名字
        $this->assign('list',$data);
        return $this->fetch();
    }
    public function edit(){
        $id=input('get.bookId');

        $data=JourModel::get($id);

        //将数据渲染到模板
        $this->assign('data',$data);

        return $this->fetch();
    }

//更新内容的方法
    public function update(){
        //echo'修改';
        $data=input('post.');
        //dump($data);
        $bookId=input('get.bookId');
        $data['book_id']=$bookId;
        $val = new JournalValidate();
        if (!$val->check($data)) {
            $this->error($val->getError());
            exit;
        }
        $journal=new JourModel();
        $ret=$journal->allowField(true)->save($data,['book_id'=>$bookId]);
        if($ret){
            $this->success('修改期刊信息成功','jourlist');
        }
        else{
            $this->error('修改期刊信息失败');
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
            $this->error($val->getError(),'jourlist');
            exit;
        }


        $id=$post['ids'];
        $journal=new JourModel();
        $journal->whereIn('book_id',$id)->delete();
        $this->success('删除成功','jourlist');


    }
}
