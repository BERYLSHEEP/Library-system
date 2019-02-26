<?php
namespace app\admin\validate;
use think\Validate;

//自定义的验证器
class Book extends Validate{
    protected $table = 'book_info';

    protected $rule=[
        'book_id|图书索引号'=>'require',
        'book_name|图书名'=>'require',

        'book_type_id|图书类别'=>'require',
        'book_intro|图书简介'=>'require|min:10',//不少于10个字
        'book_price|图书价格'=>'require',
        'book_quantity|图书数量'=>'require',
        'book_author|作者'=>'require',
        'publish_id|图书出版社编号'=>'require',
        'book_publish_date|图书出版日期'=>'require',
        'country_id|国家编号'=>'require',


        //一些要求
	];
		protected $message=[
            'book_id.require'=>'图书索引号不能为空',
            'book_name.require'=>'书名不能为空',
            'book_type_id_require'=>'图书类别不能为空',
            'book_price.require'=>'价格不能为空',
            'book_quantity.require'=>'数量不能为空',

            'book_intro.require'=>'简介不能为空',
            'book_intro.min'=>'简介内容不能少于10个字',
            'book_author.require'=>'作者不能为空',
            'book_publish_date.require'=>'出版日期不能为空',
            'publish_id.require'=>'出版社编号不能为空',
            'country_id.require'=>'国家编号不能为空',
		];




}
