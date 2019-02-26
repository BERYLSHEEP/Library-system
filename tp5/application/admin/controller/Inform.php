<?php

namespace app\admin\controller;
use app\admin\model\Inform as InformModel;

use think\Db;


class Inform extends Base
{
    public function informlist(){

        $app=new InformModel();
        //$data=$app->select();
        //'list'为返回前端的名字

        $data=$app->join('book','book_apm_inform.book_id=book.book_id')->join('reader','book_apm_inform.reader_id=reader.reader_id')->select();
        $this->assign('list',$data);
        return $this->fetch();

    }

    public function inform(){
        //管理员看到信息后，通知读者可以借书（以邮件的方式），通知完后，将该记录删除

        $id=input('get.informId');
        /*$inform=new InformModel();
        $data=$inform->where('inform_id',$id)->join('reader','book_apm_inform.reader_id=reader.reader_id')->find();
        $mail=$data['reader_mail'];
        sendMail($mail,'图书预约通知','您预约的图书，有人归还，您可以前来借阅！');*/

        $result=Db::table('book_apm_inform')->where('inform_id',$id)->delete();

       if($result){

            $this->success('通知成功','informlist');
        }
        else{

            $this->error('通知失败');
        }
    }

}