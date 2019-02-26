<?php

namespace app\index\controller;

use think\Console;
use think\Controller;
use think\Request;
use think\Db;

function object_to_array($obj) {
    $obj = (array)$obj;
    foreach ($obj as $k => $v) {
        if (gettype($v) == 'resource') {
            return;
        }
        if (gettype($v) == 'object' || gettype($v) == 'array') {
            $obj[$k] = (array)object_to_array($v);
        }
    }

    return $obj;
}

class book extends Controller
{
    //

    public function create(Request $request)
    {

        $words = '意林';
        $journal_data = Db::table('journal')
            ->field('jou_name as name,publish_name as publish,journal.Other as content,book_type_name as book_type,jou_cover as picture,
                    location_name as location,jou_point as point,book_id as bookid')
            ->join('location','journal.location_id = location.location_id','LEFT')
            ->join('publish','publish.publish_id = journal.publish_id','LEFT')
            ->join('book_type','journal.book_type_id = book_type.book_type_id','LEFT')
            ->where('publish_name','like','%'.$words.'%')
            ->order(['point'=>'desc'])
            ->select();
        $book_data = Db::table('book')
            ->field('book_name as name,book_author as author,book_intro as content,book_type_name as book_type,book_cover as picture,
                    publish_name as publish,location_name as location,book_point as point')
            ->join('location','book.location_id = location.location_id','LEFT')
            ->join('book_type','book.book_type_id = book_type.book_type_id','LEFT')
            ->join('publish','publish.publish_id = book.publish_id','LEFT')
            ->where('publish_name','like','%'.$words.'%')
            ->order(['point'=>'desc'])
            ->select();
        $row_num= $journal_data->count() + $book_data->count();

        if($row_num == 0){
            $response = array("result"=>0);
        }else{
            $temp =0;
            $type = 1;
            if(!$journal_data->isEmpty())
            {
                $journal_data = $journal_data->toArray();
                foreach($journal_data as $k=>$v){
                    $journal_data[$k]['type'] = 0;
                }
                $temp = $temp + 1;
            }
            if(!$book_data->isEmpty())
            {
                $book_data = $book_data->toArray();
                foreach($book_data as $k=>$v){
                    $book_data[$k]['type'] = 1;
                }
                $temp = $temp + 1;
                $type = 2;
            }

            if($temp == 2)
                $data = array_merge($book_data, $journal_data);
            elseif ($type == 1) {
                $data = $journal_data;
            }elseif($type == 2){
                $data = $book_data;
            }else{
                $data = [];
            }

            $fre = array();
            foreach ($data as $key => $row) {
                $fre[$key] = $row['point'];
            }
            array_multisort($fre, SORT_DESC, $data);

            $response["data"] = $data;
            $response["result"] = 1;
        }
        $response["result_num"] = $row_num;
        
        var_dump($response);

    }


    public function search()
    {
        $words = "白夜行";
        $identify = 1;

        switch ($identify)
        {
            case 1: //默认输出所有数据
                $book_data = Db::table('book_info')
                    ->field('book_name as name,book_author as author,book_intro as content,location,book_frequency as frequency')
                    ->join('location_info','book_info.location_id = location_info.location_id','LEFT')
                    ->where('book_name','like','%'.$words.'%')
                    ->order(['frequency'=>'desc'])
                    ->select();
                $journal_data = Db::table('journal_info')
                    ->field('journal_name as name,publish_name as author,journal.Other as content,location,jou_frequency as frequency')
                    ->join('location_info','journal_info.location_id = location_info.location_id','LEFT')
                    ->join('publish_info','publish_info.publish_id = journal_info.publish_id','LEFT')
                    ->where('journal_name','like','%'.$words.'%')
                    ->order(['frequency'=>'desc'])
                    ->select();
                $row_num = $book_data->count() + $journal_data->count();

                if($book_data->isEmpty() && $journal_data->isEmpty()){
                    $response = array("result"=>0);
                }else {
                    $book_data = $book_data->toArray();
                    $journal_data = $journal_data->toArray();
                    $response = array_merge($book_data, $journal_data);

                    $fre = array();
                    foreach ($response as $key => $row) {
                        $fre[$key] = $row['frequency'];
                    }
                    array_multisort($fre, SORT_DESC, $response);

                    $response["result"] = 1;
                }
                $response["result_num"] = $row_num;
                break;
            case 2: //以书名作为检索框
                $book_data = Db::table('book_info')
                    ->field('book_name as name,book_author as author,book_intro as content,location,book_frequency as frequency')
                    ->join('location_info','book_info.location_id = location_info.location_id','LEFT')
                    ->where('book_name','like','%'.$words.'%')
                    ->order(['frequency'=>'desc'])
                    ->select();

                $row_num = $book_data->count();
                if($book_data->isEmpty()){
                    $response = array("result"=>0);
                }else{
                    $response = $book_data->toArray();
                    $response["result"] = 1;
                }

                $response["result_num"] = $row_num;
                break;
            case 3: //以作者作为检索项
                $book_data = Db::table('book_info')
                    ->field('book_name as name,book_author as author,book_intro as content,location,book_frequency as frequency')
                    ->join('location_info','book_info.location_id = location_info.location_id','LEFT')
                    ->where('book_author',$words)
                    ->order(['frequency'=>'desc'])
                    ->select();
                $row_num = $book_data->count();

                if($book_data->isEmpty()){
                    $response = array("result"=>0);
                }else{
                    $response = $book_data->toArray();
                    $response["result"] = 1;
                }
                $response["result_num"] = $row_num;
                break;

            case 4: //以期刊名作为检索项
                $journal_data = Db::table('journal_info')
                    ->field('journal_name as name,publish_name as author,Other as content,location,jou_frequency as frequency')
                    ->join('location_info','journal_info.location_id = location_info.location_id','LEFT')
                    ->join('publish_info','publish_info.publish_id = journal_info.publish_id','LEFT')
                    ->where('journal_name','like','%'.$words.'%')
                    ->order(['frequency'=>'desc'])
                    ->select();
                $row_num = $journal_data->count();

                if($journal_data->isEmpty()){
                    $response = array("result"=>0);
                }else{
                    $response = $journal_data->toArray();
                    $response["result"] = 1;
                }
                $response["result_num"] = $row_num;
                break;

            case 5: //以出版社名作为检索项
                $journal_data = Db::table('journal_info')
                    ->field('journal_name as name,publish_name as author,Other as content,location,jou_frequency as frequency')
                    ->join('location_info','journal_info.location_id = location_info.location_id','LEFT')
                    ->join('publish_info','publish_info.publish_id = journal_info.publish_id','LEFT')
                    ->where('publish_name','like','%'.$words.'%')
                    ->order(['frequency'=>'desc'])
                    ->select();

                $row_num= $journal_data->count();

                if($journal_data->isEmpty()){
                    $response = array("result"=>0);
                }else{
                    $response = $journal_data->toArray();
                    $response["result"] = 1;
                }
                $response["result_num"] = $row_num;
                break;
        }
        return json_encode($response);
    }
}
