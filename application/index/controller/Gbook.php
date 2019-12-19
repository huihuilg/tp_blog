<?php
/**
 * Created by Liu.
 * User: Hui
 * Date: 2019/1/14
 * Time: 11:15
 */

namespace app\index\controller;


use think\Controller;

class Gbook extends Controller
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