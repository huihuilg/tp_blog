<?php
/**
 * Created by Liu.
 * User: Hui
 * Date: 2019/1/14
 * Time: 11:22
 */

namespace app\index\controller;


use think\Controller;

class Life extends Controller
{
    public function index()
    {
        $life = new \app\admin\model\Life();
        //每页数据
        if(!empty(input('get.num'))){
            $num = input('get.num');
        }else{
            $num = 20;
        }
        $page = max(1,input('get.page'));
        $limit = ($page-1) * $num;

        $where['l.status'] = 1;

        $lifeInfo = $life->alias('l')->field('l.id,l.title,l.m_id,l.content,l.create_time,u.nickname,l.pic_url,l.like_num,l.page_view')->join('blog_user u','l.uid=u.id')->where($where)->order('l.create_time desc')->limit($limit,$num)->select()->toArray();
        $counts = $life->where(['status'=>1])->count();
        foreach($lifeInfo as &$val){
            $val['create_time'] = date('Y-m-d',strtotime($val['create_time']));
        }

        //特别推荐
        $learn = new \app\admin\model\Learn();
        $learnEsp = $learn->field('id,pic_url,title,m_id')->where(['especially_recommend'=>1,'status'=>1])->order('create_time desc')->select()->toArray();
        $lifeEsp = $life->field('id,pic_url,title,m_id')->where(['especially_recommend'=>1,'status'=>1])->order('create_time desc')->select()->toArray();
        $result = array_merge($learnEsp,$lifeEsp);
        //推荐
        $learnRec = $learn->field('id,pic_url,title,create_time,m_id')->where(['is_recommend'=>1,'status'=>1])->order('create_time desc')->select()->toArray();
        $lifeRec = $life->field('id,pic_url,title,create_time,m_id')->where(['is_recommend'=>1,'status'=>1])->order('create_time desc')->select()->toArray();
        $recommend = array_merge($learnRec,$lifeRec);
        //点击排行
        $learnClick= $learn->field('id,pic_url,title,create_time,page_view,m_id')->where(['status'=>1])->order(['page_view'=>'desc'])->limit(1,3)->select()->toArray();
        $lifeClick = $life->field('id,pic_url,title,create_time,page_view,m_id')->where(['status'=>1])->order(['page_view'=>'desc'])->limit(1,3)->select()->toArray();
        $clickTop = array_merge($learnClick,$lifeClick);
        array_multisort(array_column($clickTop,'page_view'),SORT_DESC,$clickTop);
        $this->assign(['lifeInfo'=>$lifeInfo,'counts'=>$counts,'page'=>$page,'num'=>$num,'result'=>$result,'recommend'=>$recommend,'clickTop'=>$clickTop]);
        return $this->fetch();
    }
}