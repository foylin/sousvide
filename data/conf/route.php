<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

use think\Route;

Route::get('/',function(){
    return '非法访问';
});

Route::resource(':version/users', 'api/:version.Users');   //注册一个资源路由，对应restful各个方法

Route::rule(':version/users/:id/fans', 'api/:version.Users/fans');//restful方法中另外一个方法等。。。

return [
    '__pattern__' => [
        'name' => '\w+',
    ]
];
