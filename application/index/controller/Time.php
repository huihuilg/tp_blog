<?php
/**
 * Created by Liu.
 * User: Hui
 * Date: 2019/1/14
 * Time: 11:16
 */

namespace app\index\controller;


use think\Controller;

class Time extends Controller
{
    public function index()
    {
        $time = new \app\admin\model\Time();
        //每页数据
        if(!empty(input('get.num'))){
            $num = input('get.num');
        }else{
            $num = 20;
        }
        $page = max(1,input('get.page'));
        $limit = ($page-1) * $num;


        $timeInfo = $time->where(['status'=>1])->order('create_time desc')->limit($limit,$num)->select()->toArray();
        $counts = $time->where(['status'=>1])->order(['create_time'=>'desc'])->count();
        foreach($timeInfo as &$val){
            $val['create_time'] = date('Y-m-d',strtotime($val['create_time']));
        }
        $this->assign(['timeInfo'=>$timeInfo,'counts'=>$counts,'page'=>$page,'num'=>$num]);
        return $this->fetch();
    }
}