<?php

namespace app\admin\controller;

use think\Controller;
use app\admin\model\Hotjournal as HjModel;


use think\Db;
use think\Loader;

class Hotjournal extends Base
{
    public function update(){

        //按评分合借阅数量选出热门期刊100本
        $list= Db::table('journal')->order('jou_point desc')->limit(5)->select();

        //先将之前的表清空
        $sql='TRUNCATE table hot_journal';
        Db::execute($sql);


        foreach($list as $data){

            $journal=new HjModel($data);
            $journal->allowField(true)->save();

        }
        $this->success('热门期刊更新成功','hot_list');
    }
    public function  hot_list(){
        $hotJournal=new HjModel();

        $data=$hotJournal->join('journal','hot_journal.book_id=journal.book_id')
            ->join('book_type','journal.book_type_id=book_type.book_type_id')
            ->join('publish','journal.publish_id=publish.publish_id')
            ->join('location','journal.location_id=location.location_id')->select();

        $this->assign('list',$data);
        return $this->fetch();
    }
}