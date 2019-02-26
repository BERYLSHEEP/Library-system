<?php

namespace app\admin\controller;

use think\Controller;
use app\admin\model\Book as BookModel;
use app\admin\validate\Book as BookValidate ;
use think\Validate;

use think\Db;


class Book extends Base
{
	public function index(){

  return $this->fetch('index');

}
public function add(){

	return $this->fetch();

}
//添加图书
public function insert(){

    $post=input('post.');

    $val=new BookValidate();
    if(!$val->check($post)){
        $this->error($val->getError());
        exit;
    }

    $id=$post['book_id'];
    $rel=Db::table('book')->where('book_id',$id)->find();
    if($rel){
        $this->error('该索引号的图书已存在，添加失败');
    }
    else{
    $book=new BookModel();

    $result=$book->allowField(true)->save($post);


    if($result){
        $this->success('添加图书成功','booklist');

    }
    else{
        $this->error('添加图书失败');

    }}



}


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

        $book=new BookModel();
        $data=$book->where('book_id',$bookId)->join('book_type','book.book_type_id=book_type.book_type_id')
            ->join('country','book.country_id=country.country_id')
            ->join('publish','book.publish_id=publish.publish_id')
            ->join('location','book.location_id=location.location_id')->select();
        if($data){

            $this->assign('list',$data);
            return $this->fetch();
        }
        else{
            $this->error('未查找到该图书');
        }

    }
    //1表示按书名查找
    else if($type=='2'){

        $bookName=$post['content'];
        $book=new BookModel();
        $data=$book->where('book_name',$bookName)->join('book_type','book.book_type_id=book_type.book_type_id')
            ->join('country','book.country_id=country.country_id')
            ->join('publish','book.publish_id=publish.publish_id')
            ->join('location','book.location_id=location.location_id')->select();
        if($data){
            $this->assign('list',$data);
            return $this->fetch();
        }
        else{
            $this->error('未查找到该图书');
        }
    }
}


    public function delete(){

    $id=input('get.bookid');
    $result=Db::table('book')->where('book_id',$id)->delete();
    if($result){

        $this->success('删除图书成功','booklist');
    }
    else{

        $this->error('删除图书失败');
    }
}
public function booklist(){

	//以表格形式输出书的内容
	//$data=Db::table('book_info')->select();
	//使用模型
	$book=new BookModel();
	//$data=$book->select();
    $data=$book->join('book_type','book.book_type_id=book_type.book_type_id')
        ->join('country','book.country_id=country.country_id')
        ->join('publish','book.publish_id=publish.publish_id')
        ->join('location','book.location_id=location.location_id')->select();
	//'list'为返回前端的名字
	$this->assign('list',$data);
	return $this->fetch();

}
public function edit(){
	$id=input('get.bookid');
	$data=BookModel::get($id);
	//将数据渲染到模板
	$this->assign('data',$data);
	return $this->fetch();
}

//更新内容的方法
public function update(){

	$data=input('post.');

    $bookId=input('get.bookId');
$data['book_id']=$bookId;

	//验证输入的信息，是不是满足要求(使用自己构造的验证器)


    $val=new BookValidate();
	if(!$val->check($data)){
        $this->error($val->getError());
		exit;
	}
	$book=new BookModel();
	$ret=$book->allowField(true)->save($data,['book_id'=>$bookId]);
	if($ret){
		$this->success('修改图书信息成功','booklist');
	}
	else{
		$this->error('修改图书信息失败');
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
        $this->error($val->getError(),'booklist');
        exit;
    }


    $id=$post['ids'];

    $book=new BookModel();
    $book->whereIn('book_id',$id)->delete();
    $this->success('删除成功','booklist');


}

}
