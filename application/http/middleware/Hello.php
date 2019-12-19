<?php
/**
 * Created by Liu.
 * User: Hui
 * Date: 2019/1/11
 * Time: 16:39
 */

namespace app\http\middleware;


use think\Exception;

class Hello
{
    public function handle($request, \Closure $next)
    {
        if($request->param('name') == 'admin'){
            return $next($request);
        }
        throw new Exception('参数错误');
    }
}