<?php
/**
 * Created by Liu.
 * User: Hui
 * Date: 2019/1/15
 * Time: 17:35
 */

namespace app\admin\controller;


use app\admin\model\User;
use think\Controller;
use think\Request;

class Login extends Controller
{
    /**
     * @return mixed
     * 登录页面
     */
    public function index()
    {
        return $this->fetch();
    }
    /**
     * 登录操作
     */
    public function login(Request $request)
    {
        $info = $request->param();
        $user = new User();
        $map['user_name'] = $info['username'];
        $map['password'] = md5($info['password']);
        $map['root'] = 1;
        $isLogin = $user->where($map)->find();
        if($isLogin){
            cookie('user_name',$isLogin->user_name);
//            cookie('uid',$isLogin->id);
            session('user_name', $isLogin->user_name);
            session('uid', $isLogin->id);
            return 1;
        }
        return 0;
    }
    /**
     * 退出
     */
    public function loginOut(Request $request)
    {
        session(null);
        $this->success('退出成功','/admin/login/index');
    }
}