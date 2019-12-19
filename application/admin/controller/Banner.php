<?php
/**
 * Created by PhpStorm.
 * User: hui
 * Date: 2019/1/25
 * Time: 20:47
 */

namespace app\admin\controller;


use think\Request;

class Banner extends Common
{
    /**
     * 首页
     */
    public function index()
    {
        $banner = new \app\admin\model\Banner();
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
            $where[] = ['content','like','%'.$search.'%'];
        }else{
            $where = [];
        }
        $limit = ($page-1) * $num;


        $result = $banner->where($where)->order(['create_time'=>'desc'])->limit($limit,$num)->select();
        $counts = $banner->where($where)->order(['create_time'=>'desc'])->count();
        $count = $banner->order(['create_time'=>'desc'])->count();
        $this->assign(['result'=>$result,'count'=>$count,'counts'=>$counts,'page'=>$page,'search'=>$search,'num'=>$num]);
        return $this->fetch();
    }


    /**
     * 删除
     */
    public function del(Request $request)
    {
        $banner = new \app\admin\model\Banner();
        $id = $request->param();
        if(empty($id)){
            return 0;
        }
        $del = $banner->destroy($id);
        return $del;
    }

    /**
     * 批量删除
     */
    public function delAll(Request $request)
    {
        $banner = new \app\admin\model\Banner();
        $id = $request->param();
        if(empty($id)){
            return 0;
        }
        $del = $banner->destroy($id);
        return $del;
    }

    /**
     * 添加
     */
    public function adminAdd(Request $request)
    {
        return $this->fetch();
    }

    /**
     * 添加
     */
    public function add(Request $request)
    {
        $content = $request->param('data');
        $banner = new \app\admin\model\Banner();
        $res = $banner->allowField(true)->save($content);
        return $res;
    }

    /**
     * 编辑
     */
    public function adminEdit(Request $request)
    {
        $id = $request->param();
        $banner = new \app\admin\model\Banner();
        $result = $banner->find($id);
        $this->assign(['result'=>$result]);
        return $this->fetch();
    }


    /**
     * 保存
     */
    public function save(Request $request)
    {
        $content = $request->param('data');
        $banner = new \app\admin\model\Banner();
        //保存
        $res = $banner->allowField(true)->save($content,['id'=>$content['id']]);
        return $res;
    }
}