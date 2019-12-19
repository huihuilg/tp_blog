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

class Socket extends Controller
{
    public function index(Request $request){

        return $this->fetch();
    }

    /**
     * @param Request $request
     * 绑定
     */
    public function bind(Request $request){
        // 设置GatewayWorker服务的Register服务ip和端口，请根据实际情况改成实际值(ip不能是0.0.0.0)
        Gateway::$registerAddress = '172.16.42.69:1238';
//        (new Session())->set('group',1);

        // 假设用户已经登录，用户uid和群组id在session中
//        $group_id = $_SESSION['group'];
        // client_id与uid绑定
        $client_id = $request->param('client_id');
        Gateway::bindUid($client_id, session('uid','','socket'));
        // 加入某个群组（可调用多次加入多个群组）
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
  	public function sends()
    {
      Gateway::$registerAddress = '172.16.42.69:1238';
      $message = ['type'=>'hui','res'=>'22323232'];
        $message = json_encode($message);
    	Gateway::sendToUid('3',$message);
    }

    public function init(){
        $data = [
            'code'=>0,
            'msg'=>'',
            'data'=>[
                'mine'=>[
                    'username'=>'纸飞机',
                    'id'=>3,
                    'status'=>'online',
                    'avatar'=>"//res.layui.com/images/fly/avatar/00.jpg",
                    'sign'=> "在深邃的编码世界，做一枚轻盈的纸飞机"
                ],
                'friend'=>[
                    [
                        'groupname'=>'知名人物',
                        'id'=>1,
                        'list'=>[
                            [
                                'username'=>'客服',
                                'id'=>1,
                                'status'=>'',
                                'avatar'=>"//tva3.sinaimg.cn/crop.0.0.180.180.180/7f5f6861jw1e8qgp5bmzyj2050050aa8.jpg",
                                'sign'=>'新的一天',
                            ]
                        ]
                    ]
                ]
            ]
        ];

        echo json_encode($data);
    }

    public function admin(){
        $data = [
            'code'=>0,
            'msg'=>'',
            'data'=>[
                'mine'=>[
                    'username'=>'客服',
                    'id'=>1,
                    'status'=>'online',
                    'avatar'=>"//tva3.sinaimg.cn/crop.0.0.180.180.180/7f5f6861jw1e8qgp5bmzyj2050050aa8.jpg",
                    'sign'=> "新的一天"
                ],
                'friend'=>[
                    [
                        'groupname'=>'知名人物',
                        'id'=>1,
                        'list'=>[
                            [
                                'username'=>'纸飞机',
                                'id'=>3,
                                'status'=>'online',
                                'avatar'=>"//res.layui.com/images/fly/avatar/00.jpg",
                                'sign'=>'在深邃的编码世界，做一枚轻盈的纸飞机',
                            ]
                        ]
                    ]
                ]
            ]
        ];
        $online = \session('uid','','hui');
        if(empty($online)){
            $data['data']['friend'][0]['list'][0]['status'] = '';
        }
        echo json_encode($data);
    }
}