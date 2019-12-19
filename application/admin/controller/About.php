<?php
/**
 * Created by Liu.
 * User: Hui
 * Date: 2019/1/16
 * Time: 10:30
 */

namespace app\admin\controller;


use think\Request;

class About extends Common
{
    /**
     * @return mixed
     * 关于我
     */
    public function index()
    {
        $about = new \app\admin\model\About();
        $about = $about->where(['status'=>1])->whereNull('delete_time')->select();
        $this->assign(['about'=>$about]);
        return $this->fetch();
    }
    /**
     * 编辑
     */
    public function adminEdit(Request $request)
    {
        $id = $request->param();
        $about = new \app\admin\model\About();
        $result = $about->find($id);
        $this->assign(['result'=>$result]);
        return $this->fetch();
    }

    /**
     * 保存
     */
    public function save(Request $request)
    {
        $content = $request->param('data');
        $about = new \app\admin\model\About();
        $res = $about->allowField(true)->save($content,['id'=>$content['id']]);
        return $res;
    }

    /**
     * 删除
     */
    public function del(Request $request)
    {
        $about = new \app\admin\model\About();
        $id = $request->param();
        $del = $about->destroy($id);
        return $del;
    }
}