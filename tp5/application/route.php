<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\Route;

Route::rule('/register', 'index/Reg/post_ajax');
Route::rule('/search', 'index/index/search');
Route::rule('/typeSearch', 'index/Index/typeSearch');

Route::rule('/sresult', 'index/Index/sresult');
Route::rule('/booklist', 'index/Index/booklist');

Route::rule('/getInfo', 'index/bookdetail/getInfo');
Route::rule('/getComment', 'index/bookdetail/getComment');
Route::rule('/comment', 'index/bookdetail/comment');

Route::rule('/bookdetail/isAppointment', 'index/bookdetail/isAppointment');
Route::rule('/bookdetail/isBorrow', 'index/bookdetail/isBorrow');
Route::rule('/Index/hotBook', 'index/Index/hotBook');
Route::rule('/Index/hotJournal', 'index/Index/hotJournal');

Route::rule('/index/register', 'index/index/register');
Route::rule('login', 'index/index/login');
Route::rule('signup','index/index/signup');
Route::rule('entry', 'index/index/entry');
Route::rule('/leave','index/Index/leave');

Route::rule('/feedback','index/index/feedback');
///////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////
Route::rule('reader','index/Reader/reader');
Route::rule('mybook','index/Reader/mybook');
Route::rule('mycomment','index/Reader/mycomment');
Route::rule('mypassword','index/Reader/mypassword');

Route::rule('/info', 'index/Reader/info');
Route::rule('/borrow','index/Reader/borrow');
Route::rule('/bappointment','index/Reader/bappointment');
Route::rule('/bcomment','index/Reader/bcomment');
Route::rule('/afmodifymail','index/Reader/afmodifymail');
Route::rule('/afmodifyte','index/Reader/afmodifyte');
Route::rule('/afmodifypass','index/Reader/afmodifypass');
Route::rule('/renew','index/Reader/renew');
Route::rule('/returnbook','index/Reader/returnbook');
Route::rule('/cancelapply','index/Reader/cancelapply');

