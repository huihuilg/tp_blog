<?php
/**
 * Created by Liu.
 * User: Hui
 * Date: 2019/1/16
 * Time: 10:30
 */

namespace app\admin\controller;


class Time  extends Common
{
    /**
     * @return mixed
     * 时间轴
     */
    public function index()
    {
        $time = new \app\admin\model\Time();
//        $learn = $time->alias('t')->field('t.id,t.title,t.create_time,l.pic_url,l.like_num,l.page_view')->join('blog_learn l','t.learn_id=l.id')->order(['t.create_time'=>'desc'])->select()->toArray();
//        $life = $time->alias('t')->field('t.id,t.title,t.create_time,f.pic_url,f.like_num,f.page_view')->join('blog_life f','t.life_id=f.id')->order(['f.create_time'=>'desc'])->select()->toArray();
//        $result = array_merge($learn,$life);

        //每页数据
        if(!empty(input('get.num'))){
            $num = input('get.num');
        }else{
            $num = 5;
        }

        $page = max(1,input('get.page'));
        //关键字
        $search = trim(input('get.search'));
        //搜索方式
        if(!empty($search)){
            $where[] = ['title','like','%'.$search.'%'];
        }else{
            $where = [];
        }
        $limit = ($page-1) * $num;


        $result = $time->where($where)->order(['create_time'=>'desc'])->limit($limit,$num)->select();
        $counts = $time->where($where)->order(['create_time'=>'desc'])->count();
        $count = $time->count();
        $this->assign(['result'=>$result,'count'=>$count,'counts'=>$counts,'page'=>$page,'search'=>$search,'num'=>$num]);
        return $this->fetch();
    }
}