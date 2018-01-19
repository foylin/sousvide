<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2017 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 小夏 < 449134904@qq.com>
// +----------------------------------------------------------------------
namespace app\food\validate;

use app\admin\model\RouteModel;
use think\Validate;
use think\Db;

class FoodCategoryValidate extends Validate
{
    protected $rule = [
        'name'  => 'require|checkName',
    ];
    protected $message = [
        'name.require' => '分类名称不能为空',
    ];

    protected $scene = [
//        'add'  => ['user_login,user_pass,user_email'],
//        'edit' => ['user_login,user_email'],
    ];


    // 自定义验证规则
    protected function checkName($value, $rule, $data)
    {   
        $where = [
            'name' => $value,
            'delete_time' => '0',
        ];
        if(isset($data['id'])){
            $where = array_merge(
                [
                    'id' => ['neq',$data['id']]
                ]
            ,$where);
        }
        $exist = Db::name('food_category')->where($where)->value('id');
        if($exist)
            return '分类名称已存在';
        
        return true;
    }


    // 自定义验证规则
    protected function checkAlias($value, $rule, $data)
    {
        if (empty($value)) {
            return true;
        }

        $routeModel = new RouteModel();
        if (isset($data['id']) && $data['id'] > 0){
            $fullUrl    = $routeModel->buildFullUrl('portal/List/index', ['id' => $data['id']]);
        }else{
            $fullUrl    = $routeModel->getFullUrlByUrl($data['alias']);
        }
        if (!$routeModel->exists($value, $fullUrl)) {
            return true;
        } else {
            return "别名已经存在!";
        }

    }
}