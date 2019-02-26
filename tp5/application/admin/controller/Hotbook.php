<?php

namespace app\admin\controller;

use think\Controller;
use app\admin\model\Hotbook as HbModel;
use app\admin\model\Book as BookModel;


use think\Db;


class Hotbook extends Base
{
    public function update(){
       //按评分合借阅数量选出热门图书5本
        $list= Db::table('book')->order('book_point desc,book_frequency desc')->limit(5)->select();

        //先将之前的表清空
        $sql='TRUNCATE table hot_book';
        Db::execute($sql);
        foreach($list as $data){

            $book=new HbModel($data);
            $book->allowField(true)->save();

        }
        $this->success('更新成功','hot_list');
    }
    public function  hot_list(){
        $hotBook=new HbModel();

        $data=$hotBook->join('book','hot_book.book_id=book.book_id')
            ->join('book_type','book.book_type_id=book_type.book_type_id')
            ->join('country','book.country_id=country.country_id')
            ->join('publish','book.publish_id=publish.publish_id')
            ->join('location','book.location_id=location.location_id')->select();

        $this->assign('list',$data);
        return $this->fetch();
    }
}