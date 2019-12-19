<?php
/**
 * Created by Liu.
 * User: Hui
 * Date: 2019/1/16
 * Time: 10:30
 */

namespace app\admin\controller;


use think\Request;

class Learn extends Common
{
    /**
     * @return mixed
     * 学无止境
     */
    public function index()
    {
        $learn = new \app\admin\model\Learn();
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


        $result = $learn->where($where)->order(['especially_recommend'=>'desc','create_time'=>'desc'])->limit($limit,$num)->select();
        $counts = $learn->where($where)->order(['especially_recommend'=>'desc','create_time'=>'desc'])->count();
        $count = $learn->order(['especially_recommend'=>'desc','create_time'=>'desc'])->count();
        $this->assign(['result'=>$result,'count'=>$count,'counts'=>$counts,'page'=>$page,'search'=>$search,'num'=>$num]);
        return $this->fetch();
    }

    /**
     * 删除
     */
    public function del(Request $request)
    {
        $learn = new \app\admin\model\Learn();
        $id = $request->param();
        if(empty($id)){
            return 0;
        }
        $del = $learn->destroy($id);
        $time = \app\admin\model\Time::where(['learn_id'=>$id])->find();
        $timeDel = $time->delete();
        return $del;
    }

    /**
     * 批量删除
     */
    public function delAll(Request $request)
    {
        $learn = new \app\admin\model\Learn();
        $id = $request->param();
        if(empty($id)){
            return 0;
        }
        $del = $learn->destroy($id);
        $T = new \app\admin\model\Time();
        $time = $T->where('learn_id', 'in', $id['id'])->select()->toArray();
        $time_id = array_column($time,'id');
        $timeDel = $T->destroy($time_id);
        return $timeDel;
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
        $learn = new \app\admin\model\Learn();
        $content['uid'] = session('uid');
        $res = $learn->allowField(true)->save($content);
        $time = new \app\admin\model\Time();
        $content['learn_id'] = $learn->id;
        $timeSave = $time->allowField(true)->save($content);
        return $res;
    }

    /**
     * 编辑
     */
    public function adminEdit(Request $request)
    {
        $id = $request->param();
        $learn = new \app\admin\model\Learn();
        $result = $learn->find($id);
        $this->assign(['result'=>$result]);
        return $this->fetch();
    }


    /**
     * 保存
     */
    public function save(Request $request)
    {
        $content = $request->param('data');
        $learn = new \app\admin\model\Learn();
        $life = new \app\admin\model\Life();
        //置顶是否超过上限
        if($content['is_top'] == 1){
            $learn_count = $learn->where(['is_top'=>1,'status'=>1])->count();
            $life_count = $life->where(['is_top'=>1,'status'=>1])->count();
            $c = $learn_count + $life_count;
            if($c>=2){
                return -1;
            }
        }
        //保存
        $res = $learn->allowField(true)->save($content,['id'=>$content['id']]);
        $time = \app\admin\model\Time::where(['learn_id'=>$content['id']])->find();
        $time->title = $content['title'];
        $time->status = $content['status'];
        $timeSave = $time->allowField(true)->save();
        return $res;
    }
}