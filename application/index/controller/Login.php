<?php
/**
 * Created by PhpStorm.
 * User: hui
 * Date: 2019/2/23
 * Time: 22:56
 */

namespace app\index\controller;


use app\admin\model\User;
use think\cache\driver\Redis;
use think\Controller;
use think\Request;
use think\Session;

class Login extends Controller
{
    /**
     * @return mixed
     * 登录
     */
    public function login()
    {
        return $this->fetch();
    }

    public function doLogin(Request $request)
    {
        $info = $request->param();
        $user = new User();
        $map['user_name'] = $info['username'];
        $map['password'] = md5($info['password']);
        $isLogin = $user->where($map)->find();
        if($isLogin){
            cookie('user_name',$isLogin->user_name);
//            cookie('uid',$isLogin->id);
            session('uid',$isLogin->id);
            return 1;
        }
        return 0;
    }

    public function doRegister(Request $request)
    {
        $info = $request->param();
        $user = new User();
        if(strlen($info['username'])<6){
            return '用户名不能小于6位';
        }
        if(strlen($info['password'])<6){
            return '密码不能小于6位';
        }
        $isReg = $user->where(['user_name'=>$info['username']])->find();
        if($isReg){
            return '该用户名已被注册';
        }
        $user->user_name = trim($info['username']);
        $user->password = md5(trim($info['password']));
        $user->save();
        cookie('user_name',$user->user_name);
//            cookie('uid',$isLogin->id);
        session('uid',$user->id);
        return 1;
    }
    /**
     * @return mixed
     * 注册
     */
    public function register(){
        return $this->fetch();
    }

    public function loginOut()
    {
        \think\facade\Session::delete('uid');
        $this->success('退出成功','/index/index/index');
    }
}