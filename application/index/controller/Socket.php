<?php
/**
 * Created by PhpStorm.
 * User: hui
 * Date: 2019/2/22
 * Time: 18:48
 */

namespace app\index\controller;

use \Gateway;
use think\Controller;
use think\Request;
use think\Session;

class Socket extends SocketBase
{
    public function index(Request $request){

        return $this->fetch();
    }

    /**
     * @param Request $request
     * 绑定
     */
    public function bind(Request $request){
//        假设用户已经登录，用户uid和群组id在session中
//        $group_id = $_SESSION['group'];
//        client_id与uid绑定
        $client_id = $request->param('client_id');
        Gateway::bindUid($client_id, session('uid','','socket'));
//        加入某个群组（可调用多次加入多个群组）
//        Gateway::joinGroup($client_id, $group_id);
    }

    /**
     * @param Request $request
     * 发送消息
     */
    public function send(Request $request){
       $data = $request->param();

        Gateway::$registerAddress = '172.16.42.69:1238';
        $message = ['type'=>$data['data']['mine']['username'],'res'=>$data['data']['mine']['content']];
        $message = json_encode($message);
        $uid      = $data['data']['to']['id'];
        // 向任意uid的网站页面发送数据
        Gateway::sendToUid($uid, $message);
        // 向任意群组的网站页面发送数据
//        Gateway::sendToAll($message);
    }

}