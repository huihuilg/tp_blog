<?php


namespace app\index\controller;


use Gateway;
use think\Controller;

class SocketBase extends Controller
{
    public function initialize()
    {
        // 设置GatewayWorker服务的Register服务ip和端口，请根据实际情况改成实际值(ip不能是0.0.0.0)
        Gateway::$registerAddress = '172.16.42.69:1238';
    }

}