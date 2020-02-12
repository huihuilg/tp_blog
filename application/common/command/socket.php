<?php
//namespace app\common\command;

class Socket
{
    public $server;

    public function __construct()
    {
        $this->server = new Swoole\WebSocket\Server("0.0.0.0", 9501);
        $this->server->set(['worker_num'=>2,'dispatch_mode'=>5]);
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
//        var_export($request);
        foreach ($this->server->connections as $fd) {
            // 需要先判断是否是正确的websocket连接，否则有可能会push失败
            $server->push($fd, json_encode(['name'=>'','msg'=>'有新人加入聊天室']));
        }
    }

    /**
     * 接收到信息的回调函数
     * @param $server
     * @param $frame
     */
    public function onMessage($server, $frame)
    {
        echo $frame->fd . '来了，说：' . $frame->data . PHP_EOL;//打印到我们终端
//        var_export($this->server->getClientList(0,5));
        $data = json_decode($frame->data);
        switch ($data->type) {
            case 'bind':
//                $server->bind($frame->fd, $frame->fd);
//                var_export($server->getClientInfo($frame->fd));
                break;
            case 'say':
                foreach ($this->server->connections as $fd) {
                    // 需要先判断是否是正确的websocket连接，否则有可能会push失败
                    if ($server->isEstablished($fd)) {
                        $server->push($fd, json_encode(['name'=>$data->name,'msg'=>$data->msg]));
                    }
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
    }
}
new Socket();

