<?php


namespace app\common\command;

use Swoole\Redis\Server;
use Swoole\WebSocket\Server as WebsocketServer;
use Swoole\Coroutine\Redis;
use Swoole\Coroutine;

class SwooleIm
{
    public $server;

    public function __construct()
    {
        $this->server = new WebsocketServer("0.0.0.0", 9502);
        $this->server->set(['worker_num'=>2,'dispatch_mode'=>5]);
        Coroutine::set(['max_coroutine'=>4]);
        $this->server->on('open', [$this, 'onOpen']);
        //监听接收消息事件
        $this->server->on('message', [$this, 'onMessage']);
        //监听关闭事件
        $this->server->on('close', [$this, 'onClose']);
        $this->server->start();
    }

    /**
     * 连接
     */
    public function onOpen($server, $request)
    {
        echo $request->fd . '连接了' . PHP_EOL;//打印到我们终端
    }

    /**
     * 接收到信息的回调函数
     * @param $server
     * @param $frame
     */
    public function onMessage($server, $frame)
    {
        $redis = new Redis();
        $redis->connect('139.224.9.252',6379);
//        $redis->connect('192.168.33.10',6379);
        $redis->auth('123456');
        echo $frame->fd . '来了，说：' . $frame->data . PHP_EOL;//打印到我们终端
//        var_export($this->server->getClientList(0,5));
        $data = json_decode($frame->data,true);
        switch ($data['type']) {
            case 'bind':
                if(!empty($data['uid'])){
                    $redis->set('im_uid_'.$data['uid'],$frame->fd);
                    $redis->set('im_fd_'.$frame->fd,$data['uid']);
                }
                break;
            case 'say':
                $res = ['username'=>$data['data']['mine']['username'],'avatar'=>'http://img.mp.sohu.com/q_mini,c_zoom,w_640/upload/20170731/4c79a1758a3a4c0c92c26f8e21dbd888_th.jpg',
                    'id'=>$data['data']['mine']['id'],'type'=>'friend','content'=>$data['data']['mine']['content'],
                    'timestamp'=> 1467475443306];
                $fd = $redis->get('im_uid_'.$data['data']['to']['id']);

                $server->push($fd,json_encode($res));
                break;
            case 'close':
                if(!empty($data['uid'])){
                    $redis->del('im_uid_'.$data['uid'],$frame->fd);
                }
                break;
        }
    }

    /**
     * @param $server
     * @param $fd
     */
    public function onClose($server, $fd)
    {
        echo $fd . '走了' . PHP_EOL;//打印到我们终端
        $redis = new Redis();
        $redis->connect('139.224.9.252',6379);
//        $redis->connect('192.168.33.10',6379);
        $redis->auth('123456');
        $uid = $redis->get('im_fd_'.$fd);
        $redis->del('im_fd_'.$fd);
        $redis->del('im_uid_'.$uid);
    }
}
new SwooleIm();