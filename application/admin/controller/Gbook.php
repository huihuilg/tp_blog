<?php
/**
 * Created by Liu.
 * User: Hui
 * Date: 2019/1/16
 * Time: 10:31
 */

namespace app\admin\controller;


use think\Request;

class Gbook extends Common
{
    /**
     * @return mixed
     * 留言
     */
    public function index()
    {
        return $this->fetch();
    }

    /**
     * @param Request $request
     * 绑定
     */
    public function bind(Request $request){
        // 设置GatewayWorker服务的Register服务ip和端口，请根据实际情况改成实际值(ip不能是0.0.0.0)
        \Gateway::$registerAddress = '172.16.42.69:1238';

        // 假设用户已经登录，用户uid和群组id在session中
//        $group_id = $_SESSION['group'];
        // client_id与uid绑定
        $client_id = $request->param('client_id');
        \Gateway::bindUid($client_id, session('uid',''));
        // 加入某个群组（可调用多次加入多个群组）
//        Gateway::joinGroup($client_id, $group_id);
    }

    /**
     * @param Request $request
     * 发送消息
     */
    public function send(Request $request){
        $data = $request->param();

        \Gateway::$registerAddress = '172.16.42.69:1238';
        $message = ['type'=>$data['data']['mine']['username'],'res'=>$data['data']['mine']['content']];
        $message = json_encode($message);
        $uid      = $data['data']['to']['id'];
        // 向任意uid的网站页面发送数据
        \Gateway::sendToUid($uid, $message);
        // 向任意群组的网站页面发送数据
//        Gateway::sendToAll($message);
    }
}