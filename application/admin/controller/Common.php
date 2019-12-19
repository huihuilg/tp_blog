<?php
/**
 * Created by Liu.
 * User: Hui
 * Date: 2019/1/15
 * Time: 14:28
 */

namespace app\admin\controller;


use think\App;
use think\Controller;

class Common extends Controller
{
    //检查是否登录
    public function initialize()
    {
        if(!session('user_name') || !session('uid')){
            $this->error('请先登录！',url('/admin/login/index'));
        }
    }
}