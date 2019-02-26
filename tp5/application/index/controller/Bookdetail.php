<?php

namespace app\index\controller;

use think\Controller;
use think\Request;
use think\Db;

class bookdetail extends Controller
{
    //
    public function detail()
    {
        return view();
    }

    public function getInfo(Request $request)
    {
        $post = $request->post();
        $id = $post['bookid'];

        $book_data = Db::table('book')
            ->field('book_name as name,book_author as author,book_translator as translator,book_cover as picture,
            publish_name as publish,book_price as price,book_id as bookID,country_short as author_country,book_type.book_type_name as book_type,
            book_point as point,book_quantity as quantity,book_intro as intro,book_publish_date as publish_time,location,location_name')
            ->join('location','book.location_id = location.location_id','LEFT')
            ->join('publish','book.publish_id = publish.publish_id','LEFT')
            ->join('country','country.country_id = book.country_id','LEFT')
            ->join('book_type','book.book_type_id = book_type.book_type_id','LEFT')
            ->where('book.book_id',$id)
            ->select();

        $journal_data = Db::table('journal')
            ->field('jou_name as name,jou_cover as picture,book_type_name as book_type,publish_name as publish,
            jou_price as price,journal.book_id as bookID,jou_point as point,jou_quantity as quantity,journal.Other as intro,
            jou_publish_date as publish_time,location,location_name')
            ->join('location','journal.location_id = location.location_id','LEFT')
            ->join('publish','journal.publish_id = publish.publish_id','LEFT')
            ->join('book_type','journal.book_type_id = book_type.book_type_id','LEFT')
            ->where('journal.book_id',$id)
            ->select();

        if(!$book_data->isEmpty())
        {
            $book_data = $book_data->toArray();
            foreach($book_data as $k=>$v){
                $book_data[$k]['type'] = 1;
            }
            $response["data"] = $book_data;
            $response["result"]=1;
        }elseif (!$journal_data->isEmpty())
        {
            $journal_data = $journal_data->toArray();
            foreach($journal_data as $k=>$v){
                $journal_data[$k]['type'] = 0;
            }
            $response["data"] = $journal_data;
            $response["result"]=1;
        }else
        {
            $response["result"]=0;
        }

        return json_encode($response);
    }

    public function getComment(Request $request)
    {
        $post = $request->post();
        $id = $post['bookid'];
        $type = $post['type'];

        if($type)
        {
            $comment = Db::table('comment')
                ->field('com_content as comment,com_date as time,com_point as point')
                ->where('comment.book_id',$id)
                ->select();
        }else{
            $comment = Db::table('jou_comment')
                ->field('com_content as comment,com_date as time,com_point as point')
                ->where('jou_comment.book_id',$id)
                ->select();
        }

        $com_num = $comment->count();

        $comment = $comment->toArray();

        $response = ["comment"=>$comment, "result_num"=>$com_num];


        return json_encode($response);
    }


    public function isAppointment(Request $request)
    {
        $post = $request->post();
        $reader_id = session('userId');
        $book_id = $post['book_id'];

        if($reader_id == null)
        {
            $response['error'] = 6;
            $response['isSucceed'] = 0;
            return json_encode($response);
        }

        //判断读者的信用值
        $reader_data = Db::table('reader')
            ->where('reader_id',$reader_id)
            ->join('reader_type','reader.reader_type_id = reader_type.reader_type_id','LEFT')
            ->select();
        $reader_data = $reader_data->toArray();
        $credit = $reader_data[0]['reader_credit'];
        if($credit<=0)
        {
            $response['error'] = 1;
            $response['isSucceed'] = 0;
            return json_encode($response);
        }

        //判断读者是否还有罚金没有上交
        $fine = $reader_data[0]['reader_fine'];
        if($fine>0)
        {
            $response['error']=2;
            $response['isSucceed'] = 0;
            return json_encode($response);
        }

        //借阅：判断是否到达借阅上限
        $have_borrowed = $reader_data[0]['reader_bquantity'];
        $limit = $reader_data[0]['upper_limit'];
        if($have_borrowed >= $limit)
        {
            $response['error'] = 3;
            $response['isSucceed'] = 0;
            return json_encode($response);
        }
        //判断之前是否预约过
        if(Db::table('appointment')
                ->where('reader_id',$reader_id)
                ->where('book_id',$book_id)
                ->find() != null)
        {
            $response['error'] = 4;
            $response['isSucceed'] = 0;
            return json_encode($response);
        }

        //判断之前是否借阅
        if(Db::table('borrowed')
                ->where('reader_id',$reader_id)
                ->where('book_id',$book_id)
                ->find() != null)
        {
            $response['error'] = 5;
            $response['isSucceed'] = 0;
            return json_encode($response);
        }

        //预约成功

        $time = date("y-m-d", time());
        $data = ['book_id' => $book_id, 'reader_id' => $reader_id, 'app_date' => $time];
        $isSucceed = Db::table('appointment')->insert($data);
        $response['isSucceed'] = $isSucceed;

        return json_encode($response);
    }

    public function isBorrow(Request $request)
    {
        $post = $request->post();
        $reader_id = session('userId');
        $book_id = $post['book_id'];

        if($reader_id == null)
        {
            $response['error'] = 5;
            $response['isSucceed'] = 0;
            return json_encode($response);
        }

        //判断读者的信用值
        $reader_data = Db::table('reader')
            ->where('reader_id',$reader_id)
            ->join('reader_type','reader.reader_type_id = reader_type.reader_type_id','LEFT')
            ->select();

        $reader_data = $reader_data->toArray();
        $credit = $reader_data[0]['reader_credit'];
        if($credit<=0)
        {
            $response['error'] = 1;
            $response['isSucceed'] = 0;
            return json_encode($response);
        }

        //判断读者是否还有罚金没有上交
        $fine = $reader_data[0]['reader_fine'];
        if($fine>0)
        {
            $response['error']=2;
            $response['isSucceed'] = 0;
            return json_encode($response);
        }

        //借阅：判断是否到达借阅上限
        $have_borrowed = $reader_data[0]['reader_bquantity'];
        $limit = $reader_data[0]['upper_limit'];
        if($have_borrowed >= $limit)
        {
            $response['error'] = 3;
            $response['isSucceed'] = 0;
            return json_encode($response);
        }

        //判断该读者之前是否已经借阅了该书
        if(Db::table('borrowed')
            ->where('reader_id',$reader_id)
            ->where('book_id',$book_id)
            ->find() != null)
        {
            $response['error'] = 4;
            $response['isSucceed'] = 0;
            return json_encode($response);
        }
        //判断该书是否还有可以借的
        $book_data = Db::table('book')
            ->field('book_quantity')
            ->where('book_id',$book_id)
            ->select();
        $book_data = $book_data->toArray();
        $book_quantity = $book_data[0]['book_quantity'];
        if(!$book_quantity)
        {
            $response['error'] = 6;
            $response['isSucceed'] = 0;
            return json_encode($response);
        }


        //借阅成功

        $s_time = date("y-m-d", time());
        $e_time = date('y-m-d',strtotime("$s_time +6 month"));
        $data = ['book_id' => $book_id, 'reader_id' => $reader_id, 'borrowed_sdate' => $s_time,
            'borrowed_edate' =>$e_time,'borrowed_times'=> 0,'is_overed'=> 0];
        $isSucceed = Db::table('borrowed')->insert($data);
        $response['isSucceed'] = $isSucceed;

        //查看该读者是否之前有预约书籍，如果有，则再预约表中删除相关数据
        $app = Db::table('appointment')
            ->field('app_id')
            ->where('reader_id',$reader_id)
            ->where('book_id',$book_id)
            ->select();

        if(!$app->isEmpty())
        {
            $app = $app->toArray();
            foreach ($app as $value)
            {
                Db::table('appointment')->where('app_id',$value['app_id'])->delete();
            }
        }

        //更新图书或期刊的frequency

        Db::table('book')->where('book_id', $book_id)
            ->update(['book_frequency' => Db::raw('book_frequency+1'),'book_quantity' => Db::raw('book_quantity-1')]);
        Db::table('reader')->where('reader_id', $reader_id)
            ->update(['reader_bquantity' => Db::raw('reader_bquantity+1')]);


        return json_encode($response);

    }

    public function  isReturn(Request $request)
    {
        $post= $request->post();
        $book_id = $post['book_id'];
        $reader_id = session('userId');

        //判断这本书是否在借阅表中，然后归还成功
        if(Db::table('borrowed')
                ->where('book_id',$book_id)
                ->where('reader_id',$reader_id)
                ->find() == null)
        {
            $response['error'] = 1;
            $response['isSucceed'] = 0;
            return json_encode($response);
        }else
        {
            $r_time = date("y-m-d", time());
            Db::table('borrowed')
                ->where('book_id', $book_id)
                ->where('reader_id',$reader_id)
                ->update(['borrowed_rdate' => $r_time]);
            $response['isSucceed'] = 2;
        }
        //判断有无读者预约这本书
        $inform = Db::table('appointment')
            ->field('reader_id')
            ->where('book_id',$book_id)
            ->select();

        //将对应的读者和索书号存储在通知表中
        if(!$inform->isEmpty())
        {
            $inform = $inform->toArray();
            foreach ($inform as $value)
            {

                $data = ['book_id'=>$book_id,'reader_id'=>$value['reader_id']];
                Db::table('book_apm_inform')->insert($data);
            }
        }
        Db::table('reader')->where('reader_id', $reader_id)
            ->update(['reader_bquantity' => Db::raw('reader_bquantity-1')]);

        $response['isSucceed'] = 1;
        return json_encode($response);

    }

    public function comment(Request $request)
    {
        $post = $request->post();
        $id = $post['book_id'];
        $reader_id = session('userId');
        $comment = $post['comment'];
        $point = $post['point'];
        $type = $post['type'];

        if($reader_id == null)
        {
            $response['error'] = 1;
            $response['isSucceed'] = 0;
            return json_encode($response);
        }
        //插入comment表中
        $time = date("y-m-d", time());
        $data = ['book_id' => $id, 'reader_id' => $reader_id, 'com_content' => $comment, 'com_point' => $point, 'com_date' => $time];

        if($type)
        {
            //图书
            $isSucceed = Db::table('comment')->insert($data);
            //获取该书的评论分数
            $data = Db::table('comment')
                ->field('com_point')
                ->where('book_id', $id)
                ->select();

            $com_num = $data->count();

        }else{
            //期刊
            $isSucceed = Db::table('jou_comment')->insert($data);
            //获取该期刊的评论分数
            $data = Db::table('jou_comment')
                ->field('com_point')
                ->where('book_id', $id)
                ->select();

            $com_num = $data->count();
        }




        //目前该书本或期刊的分数
        $sum = 0.0;
        $data = $data->toArray();
        foreach ($data as $v) {
            $sum = $sum + $v['com_point'];
        }

        $avg = $sum / ($com_num * 1.0);

        //更新该书目或起开新的评论分数

        if ($type)
            Db::table('book')->where('book_id', $id)->update(['book_point' => $avg]);
        else
            Db::table('journal')->where('book_id', $id)->update(['jou_point' => $avg]);

        $response['isSucceed'] = $isSucceed;

        return json_encode($response);
    }
}
