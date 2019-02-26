<?php
namespace app\index\controller;

use think\Controller;
use think\Request;
use think\Db;

class Index extends Controller
{
    public function index()
    {
        return view();
    }
    public function signup(){
        return view();
    }
    public function sresult(){
        return view();
    }

    public function booklist()
    {
        return view();
    }
    public function login(){
        return view();
    }
    public function leave(Request $request){
        session('userId',null);
        session('password',null);
        return 1;
    }

    public function search(Request $request)
    {

        $post = $request->post();
        $words = $post['words'];
        $identify = $post['identify'];

        switch ($identify)
        {
            case 1: //默认输出所有数据
                $book_data = Db::table('book')
                    ->field('book_name as name,book_author as author,book_intro as content,publish_name as publish,book_cover as picture,
                    location_name as location,book_frequency as frequency,book_point as point,book_id as bookid,book_type_name as book_type')
                    ->join('location','book.location_id = location.location_id','LEFT')
                    ->join('publish','publish.publish_id = book.publish_id','LEFT')
                    ->join('book_type','book.book_type_id = book_type.book_type_id','LEFT')
                    ->where('book_name','like','%'.$words.'%')
                    ->order(['frequency'=>'desc'])
                    ->select();
                $journal_data = Db::table('journal')
                    ->field('jou_name as name,publish_name as publish,journal.Other as content,book_type_name as book_type,
                    jou_cover as picture,book_id as bookid,location_name as location,jou_point as point')
                    ->join('location','journal.location_id = location.location_id','LEFT')
                    ->join('publish','publish.publish_id = journal.publish_id','LEFT')
                    ->join('book_type','journal.book_type_id = book_type.book_type_id','LEFT')
                    ->where('jou_name','like','%'.$words.'%')
                    ->order(['point'=>'desc'])
                    ->select();
                $row_num = $book_data->count() + $journal_data->count();

                if($book_data->isEmpty() && $journal_data->isEmpty()){
                    $response = array("result"=>0);
                }else {
                    $book_data = $book_data->toArray();
                    foreach($book_data as $k=>$v){
                        $book_data[$k]['type'] = 1;
                    }
                    $journal_data = $journal_data->toArray();
                    foreach($journal_data as $k=>$v){
                        $journal_data[$k]['type'] = 0;
                        $journal_data[$k]['author'] = "";
                    }
                    $data = array_merge($book_data, $journal_data);

                    $fre = array();
                    foreach ($data as $key => $row) {
                        $fre[$key] = $row['point'];
                    }
                    array_multisort($fre, SORT_DESC, $data);

                    $response["data"] = $data;

                    $response["result"] = 1;
                }
                $response["result_num"] = $row_num;
                break;
            case 2: //以书名作为检索框
                $book_data = Db::table('book')
                    ->field('book_name as name,book_author as author,book_intro as content,publish_name as publish,book_cover as picture,
                    location_name as location,book_frequency as frequency,book_type_name as book_type,book_id as bookid')
                    ->join('location','book.location_id = location.location_id','LEFT')
                    ->join('publish','book.publish_id = publish.publish_id','LEFT')
                    ->join('book_type','book.book_type_id = book_type.book_type_id','LEFT')
                    ->where('book_name','like','%'.$words.'%')
                    ->order(['frequency'=>'desc'])
                    ->select();

                $row_num = $book_data->count();
                if($book_data->isEmpty()){
                    $response = array("result"=>0);
                }else{
                    $book_data = $book_data->toArray();
                    foreach($book_data as $k=>$v){
                        $book_data[$k]['type'] = 1;
                    }
                    $response["data"] = $book_data;
                    $response["result"] = 1;
                }

                $response["result_num"] = $row_num;
                break;
            case 3: //以作者作为检索项
                $book_data = Db::table('book')
                    ->field('book_name as name,book_author as author,book_intro as content,publish_name as publish,book_cover as picture,
                    location_name as location,book_frequency as frequency,book_type_name as book_type,book_id as bookid')
                    ->join('location','book.location_id = location.location_id','LEFT')
                    ->join('publish','publish.publish_id = book.publish_id','LEFT')
                    ->join('book_type','book.book_type_id = book_type.book_type_id','LEFT')
                    ->where('book_author','like','%'.$words.'%')
                    ->order(['frequency'=>'desc'])
                    ->select();
                $row_num = $book_data->count();

                if($book_data->isEmpty()){
                    $response = array("result"=>0);
                }else{
                    $data = $book_data->toArray();
                    foreach($data as $k=>$v){
                        $data[$k]['type'] = 1;
                    }
                    $response["data"] = $data;
                    $response["result"] = 1;
                }
                $response["result_num"] = $row_num;
                break;

            case 4: //以期刊名作为检索项
                $journal_data = Db::table('journal')
                    ->field('jou_name as name,publish_name as publish,journal.Other as content,book_type_name as book_type,jou_cover as picture,
                    location_name as location,jou_point as point,book_id as bookid')
                    ->join('location','journal.location_id = location.location_id','LEFT')
                    ->join('publish','publish.publish_id = journal.publish_id','LEFT')
                    ->join('book_type','journal.book_type_id = book_type.book_type_id','LEFT')
                    ->where('jou_name','like','%'.$words.'%')
                    ->order(['point'=>'desc'])
                    ->select();
                $row_num = $journal_data->count();

                if($journal_data->isEmpty()){
                    $response = array("result"=>0);
                }else{
                    $data = $journal_data->toArray();
                    foreach($data as $k=>$v){
                        $data[$k]['type'] = 0;
                        $data[$k]['author'] = "";
                    }
                    $response["data"] = $data;
                    $response["result"] = 1;
                }
                $response["result_num"] = $row_num;
                break;

            case 5: //以出版社名作为检索项
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
                    publish_name as publish,location_name as location,book_point as point,book_id as bookid')
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
                            $journal_data[$k]['author'] = "";
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
                break;
        }

        return json_encode($response);

    }

    public function typeSearch(Request $request)
    {

        $post = $request->post();
        $type = $post['type'];


        $book_data = Db::table('book')
            ->field('book_name as name,book_author as author,book_intro as content,publish_name as publish,
            book_type.book_type_name as book_type,book_cover as picture,book_point as point,
                    location_name as location,book_frequency as frequency,book_id as bookid')
            ->join('location','book.location_id = location.location_id','LEFT')
            ->join('publish','publish.publish_id = book.publish_id','LEFT')
            ->join('book_type','book.book_type_id = book_type.book_type_id','LEFT')
            ->where('book.book_type_id',$type)
            ->order(['frequency'=>'desc'])
            ->select();
        $journal_data = Db::table('journal')
            ->field('jou_name as name,publish_name as publish,journal.Other as content,book_type_name as book_type,
                    jou_point as point,jou_cover as picture,location_name as location,book_id as bookid')
            ->join('location','journal.location_id = location.location_id','LEFT')
            ->join('publish','publish.publish_id = journal.publish_id','LEFT')
            ->join('book_type','journal.book_type_id = book_type.book_type_id','LEFT')
            ->where('journal.book_type_id',$type)
            ->select();
        $row_num = $book_data->count() + $journal_data->count();

        if($book_data->isEmpty() && $journal_data->isEmpty()){
            $response = array("result"=>0);
        }else {
            $book_data = $book_data->toArray();
            foreach($book_data as $k=>$v){
                $book_data[$k]['type'] = 1;
            }
            $journal_data = $journal_data->toArray();
            foreach($journal_data as $k=>$v){
                $journal_data[$k]['type'] = 0;
                $journal_data[$k]['author'] = "";
            }
            $data = array_merge($book_data, $journal_data);

            $fre = array();
            foreach ($data as $key => $row) {
                $fre[$key] = $row['point'];
            }
            array_multisort($fre, SORT_DESC, $data);

            $response["data"] = $data;

            $response["result"] = 1;
        }
        $response["result_num"] = $row_num;

        return json_encode($response);
    }

    public function hotBook()
    {
        $book_data = Db::table('hot_book')
            ->field('book_name as name,hot_book.book_point as point,hot_book.book_frequency as frequency,book_cover as picture,hot_book.book_id as bookid')
            ->join('book','hot_book.book_id = book.book_id','LEFT')
            ->order(['frequency'=>'desc','point'=>'desc'])
            ->limit(5)
            ->select();

        $book_data = $book_data->toArray();
        $response['book_data'] = $book_data;
        return json_encode($response);
    }

    public function hotJournal()
    {
        $journal_data = Db::table('hot_journal')
            ->field('jou_name as name,hot_journal.jou_point as point,jou_cover as picture,hot_journal.book_id as bookid')
            ->join('journal','hot_journal.book_id = journal.book_id','LEFT')
            ->order(['point'=>'desc'])
            ->limit(5)
            ->select();

        $journal_data = $journal_data->toArray();
        $response['journal_data'] = $journal_data;
        return json_encode($response);

    }

    public function register(Request $request)
    {
        $post = $request->post();
        $id = $post['userId'];
        $password = $post['password'];
        $type = $post['type'];
        $name = $post['userName'];
        $mail = $post['mail'];
        $tel = $post['tel'];
        $college = $post['college'];

        if(Db::table('reader')->where('reader_id',$id)->find() == null)
        {
            $time = date("y-m-d", time());
            $reader = ['reader_id' => $id, 'reader_password' => $password, 'reader_type_id' => $type,
                'reader_name'=>$name,'reader_mail'=>$mail,'reader_tel'=>$tel,'apply_date'=>$time,
                'college_id'=>$college];
            $isSucceed = Db::table('apply')->insert($reader);
            $response['isSucceed']=$isSucceed;
        }else
        {
            $response['isSucceed'] = 0;
            $response['error'] = 1;
        }
        return json_encode($response);
    }

    public function entry(Request $request)
    {
        $post = $request->post();
        $id = $post['userId'];
        $password = $post['password'];

        $data = Db::table('reader')
            ->field('reader_password as password')
            ->where('reader_id',$id)
            ->select();

        if($data->isEmpty())
            $response['error'] = 1;
        else{
            $data = $data->toArray();
            if($password == $data[0]['password'])
            {
                $response['isSucceed'] = 1;
                session('userId',$id);
                session('password',$password);
            }
            else
                $response['error'] = 2;
        }

        return json_encode($response);

    }

    public function feedback(Request $request)
    {
        $post = $request->post();
        $content = $post['content'];
        $reader_id = session('userId');
        if($reader_id == null)
        {
            $response['error'] = 1;
            $response['isSucceed'] = 0;
            return json_encode($response);
        }
        $date = date("y-m-d", time());
        $data = ['reader_id'=>$reader_id,'fdb_content'=>$content,'fdb_date'=>$date];
        $isSucceed = Db::table('feedback')->insert($data);
        $response['isSucceed'] = $isSucceed;
        return json_encode($response);

    }
}
