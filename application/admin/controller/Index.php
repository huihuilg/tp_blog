<?php
/**
 * Created by Liu.
 * User: Hui
 * Date: 2019/1/15
 * Time: 14:29
 */

namespace app\admin\controller;


use app\admin\model\About;
use app\admin\model\Module;
use app\admin\model\User;
use think\Request;

class Index extends Common
{
    /**
     * @return mixed
     * 后台首页
     */
    public function index()
    {
        $module = new Module();
        $model = $module->where(['pid'=>0,'status'=>1])->select()->toArray();
        $username = session('user_name');
        $this->assign(['model'=>$model, 'username'=>$username]);
        return $this->fetch();
    }

    /**
     * @return mixed
     * 欢迎页
     */
    public function welcome()
    {
        $server_name = $_SERVER['SERVER_NAME'];
        $user = new User();
        $userNum = $user->where(['status'=>1,'root'=>1])->count();
        $learn = new \app\admin\model\Learn();
        $life = new \app\admin\model\Life();
        $learnNum = $learn->where(['status'=>1])->count();
        $lifeNum = $life->where(['status'=>1])->count();
        $count = $learnNum + $lifeNum;
        $phpversion = PHP_VERSION;
        $h = php_sapi_name();
        $time = date('Y-m-d H:i:s');
        $username = session('user_name');
        $this->assign(['username'=>$username,'time'=>$time,'phpversion'=>$phpversion,'h'=>$h,'userNum'=>$userNum,'count'=>$count,'server_name'=>$server_name]);
        return $this->fetch();
    }

}