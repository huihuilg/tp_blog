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
        $swoole_mysql = new \Swoole\Coroutine\MySQL();
        $swoole_mysql->connect([
            'host'     => '139.224.9.252',
            'port'     => 3306,
            'user'     => 'blog',
            'password' => '18500254733',
            'database' => 'blog',
        ]);
        $redis = new Redis(['host'       => 'localhost',
            'port'       => 6379,
            'password'   => 18500254733,]);
//        $redis->connect('localhost',6379);
//        $redis->connect('192.168.33.10',6379);
//        $redis->auth('18500254733');
        echo $frame->fd . '来了，说：' . $frame->data . PHP_EOL;//打印到我们终端
//        var_export($this->server->getClientList(0,5));
        $data = json_decode($frame->data,true);
        switch ($data['type']) {
            case 'bind':
                if(!empty($data['uid'])){
                    $redis->set('im_uid:'.$data['uid'],$frame->fd);
                    $redis->set('im_fd:'.$frame->fd,$data['uid']);
                }
                $res = $swoole_mysql->query("update blog_user set is_online=1 where id={$data['uid']}");
                $res = [
                    'type'=>'bind'
                ];
                $server->push($frame->fd,json_encode($res));
//                foreach ($this->server->connections as $fds) {
//                    // 需要先判断是否是正确的websocket连接，否则有可能会push失败
//                    if ($server->isEstablished($fds)) {
//                        $server->push($fds, json_encode([
//                            'content'=>'用户上线了',
//                            'uid'=>$data['uid'],
//                            'type'=>'bind',
//                            'fd'=>$fds,
//                            'status'=>'1',
//                        ]));
//                    }
//                }
                break;
            case 'say':
                $result = $swoole_mysql->query("select * from blog_user where id={$data['data']['mine']['id']} limit 1");
                $res = [
                        'username'=>$data['data']['mine']['username'],
                        'avatar'=>'http://img.mp.sohu.com/q_mini,c_zoom,w_640/upload/20170731/4c79a1758a3a4c0c92c26f8e21dbd888_th.jpg',
                        'id'=>$data['data']['mine']['id'],
                        'type'=>'friend',
                        'content'=>$data['data']['mine']['content'],
                        'is_online'=>$result[0]['is_online'],
                    ];

                $to_line = $swoole_mysql->query("select * from blog_user where id={$data['data']['to']['id']}");
                if($to_line[0]['is_online'] == 0){

                }else{
                    $fd = $redis->get('im_uid:'.$data['data']['to']['id']);
                    $server->push($fd,json_encode($res));
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
        $redis = new Redis(['host'       => 'localhost',
            'port'       => 6379,
            'password'   => 18500254733]);
//        $redis->connect('localhost',6379);
//        $redis->connect('192.168.33.10',6379);
//        $redis->auth('123456');
        $uid = $redis->get('im_fd:'.$fd);
        $redis->del('im_fd:'.$fd);
        $redis->del('im_uid:'.$uid);

        $swoole_mysql = new \Swoole\Coroutine\MySQL();
        $swoole_mysql->connect([
            'host'     => '139.224.9.252',
            'port'     => 3306,
            'user'     => 'blog',
            'password' => '18500254733',
            'database' => 'blog',
        ]);
        $res = $swoole_mysql->query("update blog_user set is_online=0 where id=$uid");
    }
}
new SwooleIm();