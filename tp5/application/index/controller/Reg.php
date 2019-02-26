<?php

namespace app\index\controller;


use think\Controller;
use think\Request;
use think\Validate;
use think\Db;

class Reg extends Controller
{
    //
    public function create()
    {
        return view();
    }

    public function post_ajax(Request $request){
        
        $post = $request->post();

        //对post数据进行验证，创建验证规则
        $validate = Validate::make([
            //键名：需要验证的字段名
            //键值：规则
            "id" => "require|min:12|max:12",
            "mail" => "require",
            "Password" => "require|min:6|max:15|confirm",
        ]);
        $status = $validate->check($post);
        if($status){
            $data = ['reader_id' => $post['id'],
                'reader_password' => md5($post['Password']),
                'reader_mail' => $post['mail']];
            $result = Db::table('reader_info')->insert($data);
            if($result){
                 $response = array("responseStatus"=>0);
            }else{
                 $response = array("responseStatus"=>1,"errorMessage" => "学号或职工号重复");
            }
        }else{
             $response = array("responseStatus" => 2,"errorMessage" => $validate->getError());
        }
        return json_encode($response);
    }
}
