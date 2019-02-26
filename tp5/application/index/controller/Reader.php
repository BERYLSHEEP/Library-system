<?php

namespace app\index\controller;


use think\Controller;
use think\Db;
use think\Session;
use think\Request;

class Reader extends Controller
{
    public function reader(){
        return view();
    }
    public function mybook(){
        return view();
    }
    public function mycomment(){
        return view();
    }
    public function mypassword(){
        return view();
    }

    //获取个人基本信息
    //传入username
    public function info(){
        //读取用户，不建议前端暴露session值
        $use=session('userId');

        $readers=Db::table('reader')
            ->field('reader_id as readerid,reader_name as readername,read_type as readertype
                ,reader_date as readerdate,reader_tel as readertel,reader_mail as readermail,
                reader_bquantity as borrowedquantity,reader_credit as readercredit,reader_fine as arrear')
            ->join('reader_type','reader.reader_type_id=reader_type.reader_type_id','LEFT')
            ->where('reader_id','=',$use)
            ->select();

        $reajson['readers']=$readers;
        return json_encode($reajson,JSON_UNESCAPED_UNICODE);

    }

    //当前借阅
    //
    public function borrow(){

        $use=session('userId');

        //借阅记录
        $borrow_data=Db::table('borrowed')
            ->field('borrowed.book_id as bookid,borrowed_id as borrowed_id,book_name as bookname,book_author as author,location,
                    borrowed_sdate as sdate,borrowed_edate as edate,borrowed_rdate as rdate,borrowed_times as times
                    ,is_overed as overed')
            ->join('book','borrowed.book_id=book.book_id','LEFT')
            ->join('location','book.location_id=location.location_id','LEFT')
            ->where('reader_id',$use)
            ->select();

        $borrjson['borrow_data']=$borrow_data;

        $borrow_num=count($borrow_data);
        $borrjson["borrow_num"]=$borrow_num;

        return json_encode($borrjson,JSON_UNESCAPED_UNICODE);
    }

    //我的预约,根据reader_id
    //appointment表中app_num,book_id,app_date，book表中的book_name
    public function bappointment(){

        $use=session('userId');

        $myapp=Db::table('appointment')
            ->field('app_date as appdate,book_name as bookname,appointment.book_id as bookid')
            ->join('book','appointment.book_id=book.book_id','LEFT')
            ->where('reader_id','=',$use)
            ->order(['app_date'=>'desc'])
            ->select();

        $appjson['myapp']=$myapp;

        $app_num=count($myapp);
        $appjson["app_num"]=$app_num;

        return json_encode($appjson,JSON_UNESCAPED_UNICODE);

    }

    //我的评论
    public function bcomment(){

        $user=session('userId');

        $mycom=Db::table('comment')
            ->field('book_name as bookname,com_date as comdate,
            com_point as compoint,com_content as content')
            ->join('book','comment.book_id=book.book_id','LEFT')
            ->where('reader_id','=',$user)
            ->order(['com_date'=>'desc'])
            ->select();
        $mycomjou=Db::table('jou_comment')
            ->field('jou_name as bookname,com_date as comdate,
                com_point as compoint,com_content as content')
            ->join('journal','jou_comment.book_id=journal.book_id','LEFT')
            ->where('reader_id','=',$user)
            ->order(['com_date'=>'desc'])
            ->select();


        //总数
        $com_num=count($mycom)+count($mycomjou);

        $mycom=$mycom->toArray();
        foreach ($mycom as $k=>$v){
            $mycom[$k]['type']=1;
        }
        $mycomjou=$mycomjou->toArray();
        foreach ($mycomjou as $k=>$v){
            $mycomjou[$k]['type']=0;
        }
        $data=array_merge($mycom,$mycomjou);

        $fre=array();
        foreach ($data as $key=>$row){
            $fre[$key]=$row['comdate'];
        }
        array_multisort($fre,SORT_DESC,$data);

        $comjson['mycom']=$data;
        $comjson["com_num"]=$com_num;
        return json_encode($comjson,JSON_UNESCAPED_UNICODE);
    }

    //修改个人信息
    //修改邮箱
    public function afmodifymail(Request $request){

       $user=session('userId');

       $post = $request->post();
       $youxiang=$post['newmail'];

        Db::name('reader')
            ->where('reader_id',$user)
            ->data(['reader_mail'=>$youxiang])
            ->update();

        $isSucceed=0;
        $response['isSucceed']=$isSucceed;
        return json_encode($response);

    }
    //修改电话
    public function afmodifyte(Request $request){

        $user=session('userId');
        $post = $request->post();
        $dianhua=$post["newtele"];

        Db::name('reader')
            ->where('reader_id',$user)
            ->data(['reader_tel'=>$dianhua])
            ->update();

        $isSucceed=0;
        $response['isSucceed']=$isSucceed;

        return json_encode($response);
    }

    //修改密码
    //旧密码+新密码；验证旧密码，update新密码
    public function afmodifypass(){
        $user=session('userId');

        //获取输入的新旧密码
        $oldpass=$_POST["oldpass"];
        $newpass=$_POST["newpass"];
        $renewpass=$_POST["renewpass"];

        $condition['reader_id'] = $user;
        $condition['reader_password'] = $oldpass;

        $passes = Db::table('reader')
            ->field('reader_password')
            ->where($condition)
            ->find();
        //如果查询到该用户
        if ($passes) {
            //修改密码
            Db::table('reader')
                ->where('reader_id', '=', $user)
                ->data(['reader_password' => $newpass])
                ->update();
            $isSucceed = 0;
            $response['isSucceed']=$isSucceed;
            return json_encode($response);
        }
        //密码输入错误
        else {
            $error = 0;
            $response['error']=$error;
            return json_encode($response);
        }
    }

    //续借图书
    public function renew(){
        //获取bookid
        $bookid=$_POST["bookid"];
        //获取当前用户
        $user=session('userId');

        $condition['reader_id']=$user;
        $condition['book_id']=$bookid;

        //查看该书是否有人预约
        //在预约表中查bookid
        $appinfo=Db::table('appointment')
            ->where('book_id','=',$bookid)
            ->find();

        //如果有人预约
        if($appinfo){
            $error=1;
            $response['error']=$error;
            return json_encode($response);
        }

        //没人预约
        else {
            //查数据表中的归还日期，续借次数
            $data = Db::table('borrowed')
                ->where($condition)
                ->find();

            $borrowda = $data['borrowed_edate'];
            $borrowti = $data['borrowed_times'];

            //查看该书是否过期
            $isover = 0;

            $condition["is_overed"] = $isover;
            $guoqi = Db::table('borrowed')
                ->field('is_overed')
                ->where($condition)
                ->find();

            //如果该书没有过期
            if ($guoqi) {
                //续借次数+1
                $borrowti = $borrowti + 1;
                //日期+30天
                $reborrowda = date('Y-m-d', strtotime("{$borrowda}+30 days"));
                Db::table('borrowed')
                    ->where($condition)
                    ->data(['borrowed_edate' => $reborrowda, 'borrowed_times' => $borrowti])
                    ->update();
                $isSucceed = 0;
                $response['isSucceed']=$isSucceed;
                return json_encode($response);
            } else {
                $error = 0;
                $response['error']=$error;
                return json_encode($response);
            }
        }
    }

    //归还图书,借阅表中删除记录，
    public function returnbook(){
        //获取bookid
        $bookid=$_POST['bookid'];
        //$bookid='4';
        //获取readerid
        $user=session('userId');

        $condition['reader_id']=$user;
        $condition['book_id']=$bookid;

        //查看该书是否过期
        $isover = 0;

        $condition["is_overed"] = $isover;
        $guoqi = Db::table('borrowed')
            ->field('is_overed')
            ->where($condition)
            ->find();

        //如果没有过期
        if($guoqi) {
            //更新归还时间
            $rtime=date("y-m-d",time());
            Db::table('borrowed')
                ->where($condition)
                ->update(['borrowed_rdate'=>$rtime]);

            //book表中book数量+1
            Db::table('book')
                ->where('book_id','=',$bookid)
                ->update(['book_quantity'=>Db::raw('book_quantity+1')]);
            $isSucceed = 1;
            $response['isSucceed']=$isSucceed;

            //判断有无读者预约这本书
            $inform = Db::table('appointment')
                ->field('reader_id')
                ->where('book_id',$bookid)
                ->select();

            //将对应的读者和索书号存储在通知表中
            if(!$inform->isEmpty())
            {
                $inform = $inform->toArray();
                foreach ($inform as $value)
                {
                    $data = ['book_id'=>$bookid,'reader_id'=>$value['reader_id']];
                    Db::table('book_apm_inform')->insert($data);
                }
            }

            return json_encode($response);
        }
        //过期了
        else{
            $isSucceed=0;
            $response['isSucceed']=$isSucceed;
            return json_encode($response);
        }
    }

    //取消预约
    public function cancelapply(){
        //获取bookid
        $bookid=$_POST["bookid"];
        //$bookid='222';
        //获取readerid
        $user=session('userId');

        $condition['book_id']=$bookid;
        $condition['reader_id']=$user;
        //删除记录
        Db::table('appointment')
            ->where($condition)
            ->delete();

        $isSucceed=1;
        $response['isSucceed']=$isSucceed;
        return json_encode($response);
    }
}
