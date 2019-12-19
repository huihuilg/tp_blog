<?php
/**
 * Created by PhpStorm.
 * User: ch
 * Date: 19/3/13
 * Time: 上午11:55
 */

namespace app\index\controller;


use think\Controller;

class Test extends Controller
{
    public function common()
    {

    }


    /**
     * 加密方法
     */
//    public function encryption($data)
//    {
//        //初始化加密类
//        $encryption = new \Home\Service\Aes2();
//        //加密
//        $data = json_encode($data);
//        $data = $encryption->encrypt($data);
//        return $data;
//    }
    /**
     * 解密方法
     */
//    public function decryption($data)
//    {
//        //初始化加密类
//        $encryption = new \Home\Service\Aes2();
//        //解密
//        $data = $encryption->decrypt($data);
//        $data = json_decode($data,true);
//        return $data;
//    }

    /**
     * 魔蝎运营商回调
     * 创建任务通知回调
     */
    public function scorpionTechCreate()
    {
//        $d = ['vid'=>1,'task_id'=>'fdsafdsafdsafdsd'];
//        $a = $this->encryption($d);
        header("HTTP/1.1 201 Created");
        $data = input('post.');
        file_put_contents("./test.txt",var_export($data,true));
        exit();
        //解密
//        $get=$this->decryption($data);
        //用户id
        $vid = $get['vid'];
        //创建的任务task_id号
        $task_id = strtoupper($get['task_id']);

        if(empty($vid)||empty($task_id)){
            $errData = ['ret' => '1002','code'=>'error'];
            $errData = $this->encryption($errData);
            $this->return = ['data'=>$errData];
            exit;
        }
        $addyysdata = array(
            'vid'=>$vid,
            'task_id'=>$task_id,
            'ctime'=>time(),
        );
        $reportModel = M('tdyyshd_report','',C('ZHENGXIN'));
        //运营商数据查询是否存在 存在修改状态，不存在添加一条
        $res = M('tdyyshd_report','',C('ZHENGXIN'))->where(array('vid'=>$vid))->getField('id');
        if($res){
            $reportModel->where(array('vid'=>$vid))->save(array('task_id'=>$task_id,'ctime'=>time()));
        }else{
            $reportModel->add($addyysdata);
        }
        //修改认证状态为认证中
        M('user','',C('DB'))->where(array('vid'=>$vid,'mobile_verify'=>'1'))->save(array('mobile_verify'=>'2'));
        $result['ret']='1001';
        $result['code']='success';
//        $result = $this->encryption($result);
        $this->return = ['data'=>$result];
        exit;
    }

    /**
     * 登录结果回调
     */
    public function scorpionTechLogin()
    {
      header("HTTP/1.1 201 Created");
        $data = input('post.');
        file_put_contents("./test1.txt",var_export($data,true));
        exit();
      
      
        header("HTTP/1.1 201 Created");
        file_put_contents("./test.txt",var_export(I('post.'),true));
        $data = I('post.');
        $data = json_decode($data);
        if(empty($data)){
            $data = file_get_contents('php://input');
            $data = json_decode($data);
        }
        //修改认证状态为未认证
        M('user','',C('DB'))->where(array('vid'=>$data['user_id'],'mobile_verify'=>'1'))->save(array('mobile_verify'=>'2'));
        exit();
    }

    /**
     * 采集失败回调
     */
    public function scorpionTechCollect()
    {
      header("HTTP/1.1 201 Created");
        $data = input('post.');
        file_put_contents("./test2.txt",var_export($data,true));
        exit();
      
      
        header("HTTP/1.1 201 Created");
        file_put_contents("./fail.txt",var_export(I('post.'),true));
        $data = I('post.');
        $data = json_decode($data);
        if(empty($data)){
            $data = file_get_contents('php://input');
            $data = json_decode($data);
        }
        //修改认证状态为未认证
        M('user','',C('DB'))->where(array('vid'=>$data['user_id'],'mobile_verify'=>'1'))->save(array('mobile_verify'=>'2'));
        exit();
    }

    /**
     * 账单通知回调
     */
    public function scorpionTechBill()
    {
      header("HTTP/1.1 201 Created");
        $data = input('post.');
        file_put_contents("./test3.txt",var_export($data,true));
        exit();
      
      
      
        header("HTTP/1.1 201 Created");
        file_put_contents("./bill.txt",var_export(I('post.'),true));
        $data = I('post.');
        $data = json_decode($data);
        if(empty($data)){
            $data = file_get_contents('php://input');
            $data = json_decode($data);
        }
        if($data['result'] == false){
            //修改认证状态为未认证
            M('user','',C('DB'))->where(array('vid'=>$data['user_id'],'mobile_verify'=>'1'))->save(array('mobile_verify'=>'2'));
            exit();
        }
        //
        exit;
    }


    /**
     * 用户资信报告通知回调
     */
    public function scorpionTechReport()
    {
      header("HTTP/1.1 201 Created");
        $data = input('post.');
        file_put_contents("./test4.txt",var_export($data,true));
        exit();
      
      
        header("HTTP/1.1 201 Created");
        file_put_contents("./bill.txt",var_export(I('post.'),true));
        $data = I('post.');
        $data = json_decode($data);
        if(empty($data)){
            $data = file_get_contents('php://input');
            $data = json_decode($data);
        }
        if($data['result'] == false){
            //修改认证状态为未认证
            M('user','',C('DB'))->where(array('vid'=>$data['user_id'],'mobile_verify'=>'1'))->save(array('mobile_verify'=>'2'));
            exit();
        }
        //


    }

    /**
     *
     */


}