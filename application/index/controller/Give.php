<?php
/**
 * Created by Liu.
 * User: Hui
 * Date: 2019/1/28
 * Time: 13:55
 */

namespace app\index\controller;


use think\Controller;

class Give extends Controller
{
    public function index()
    {
        //个人信息
        $about = new \app\admin\model\About();
        $userInfo = $about->find();
        $this->assign(['userInfo'=>$userInfo]);
        return $this->fetch();
    }
}