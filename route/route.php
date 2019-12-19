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

Route::get('think', function () {
    return 'hello,ThinkPHP5!';
});

//Route::rule('路由表达式','路由地址','请求类型','路由参数(数组)','变量规则(数组)');
//GET PUT POST DELETE *
//Route::rule('hello','simple/test/hello', 'GET|POST', ['https'=>false]);
//Route::get('hello/:id','simple/test/hello');
//Route::post('hello/:id','simple/Test/hello');
//Route::get('hello/:name', 'index/hello');

return [

];
