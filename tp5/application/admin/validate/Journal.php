<?php
/**
 * Created by PhpStorm.
 * User: 11727
 * Date: 2018/12/23
 * Time: 11:37
 */

namespace app\admin\validate;

use think\Validate;

class Journal extends Validate
{
    protected $rule=[
        'book_id|期刊索引号' => 'require',
        'jou_name|期刊名' => 'require',
        'book_type_id|期刊类别' => 'require',
        'jou_price|期刊价格' => 'require',

        'jou_quantity|期刊数量' => 'require',
        'publish_id|期刊出版社编号' => 'require',
        'jou_publish_date|期刊出版日期' => 'require',
        'location_id|期刊馆藏位置编号' => 'require',
    ];
    protected $message=[
        'book_id.require' => '期刊索引号不能为空',
        'jou_name.require' => '期刊名不能为空',
        'book_type_id_require' => '期刊类别不能为空',
        'jou_price.require' => '价格不能为空',
        'jou_quantity.require' => '数量不能为空',
        'jou_publish_date.require' => '出版日期不能为空',
        'publish_id.require' => '出版社编号不能为空',
        'location_id.require' => '馆藏位置编号不能为空',
    ];
}