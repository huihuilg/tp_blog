<?php
/**
 * Created by PhpStorm.
 * User: hui
 * Date: 2019/2/22
 * Time: 18:48
 */

namespace app\index\controller;

use app\admin\model\User;
use \Gateway;
use think\cache\driver\Redis;
use think\Request;
use think\Session;

class Socket extends SocketBase
{
    public function index(Request $request)
    {
        $uid = session('uid');
        $this->assign('uid', $uid);
        return $this->fetch();
    }

    /**
     * @param Request $request
     * 是否在校
     */
    public function isOnline(Request $request)
    {
        $uid = $request->param('uid');
        $is_online = User::find($uid)->is_online;
        echo json_encode(['is_online'=>$is_online]);
    }


    /**
     * @param Request $request
     * 获取在校数据
     */
    public function getList(Request $request)
    {
        $uid = $request->param('uid');
        $name = User::find($uid)->user_name;
        $redis = new Redis();
        $count = $redis->has('im_count');
        if (!$count) {
            $count = 0;
        } else {
            $count = $redis->get('im_count');
        }
//        $list = $redis->keys('im_uid:*');
//        if ($list) {
//            $userList = [];
//            foreach ($list as $key => $val) {
//                $userList[$key]['id'] = explode(':', $val)[1];
//                $userList[$key]['username'] = User::find($userList[$key]['id'])->user_name;
//                $userList[$key]['is_online'] = User::find($userList[$key]['id'])->is_online;
//                $userList[$key]['sign'] = "我是客服测试";
//                $userList[$key]['avatar'] = "http://img.mp.sohu.com/q_mini,c_zoom,w_640/upload/20170731/4c79a1758a3a4c0c92c26f8e21dbd888_th.jpg";
//            }
//        } else {
//            $userList = [];
//        }
        $list = User::where(['is_online'=>1])->select();
        $userList = [];
        foreach($list as $key=>$value){
            $userList[$key]['id'] = $value['id'];
            $userList[$key]['username'] = $value['user_name'];
            $userList[$key]['is_online'] = $value['is_online'];
            $userList[$key]['sign'] = '个性签名';
            $userList[$key]['avatar'] = "http://img.mp.sohu.com/q_mini,c_zoom,w_640/upload/20170731/4c79a1758a3a4c0c92c26f8e21dbd888_th.jpg";
        }
        echo json_encode([
            'code' => 0,
            'count' => $count,
            'msg' => '',
            'data' => [
                'mine' => [
                    'username' => $name,
                    'id' => $uid,
                    'status' => "online",
                    'sign' => '开心每一天',
                    'avatar' => "//res.layui.com/images/fly/avatar/00.jpg"
                ],
                'friend' => [
                    [
                        'groupname' => '在线人员',
                        'id' => 0,
                        'list' => $userList
                    ]
                ]
            ]
        ]);
    }

    public function aa(Request $request)
    {
        file_put_contents('./1.txt','uid');
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
        Gateway::bindUid($client_id, session('uid','','liu_'));
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