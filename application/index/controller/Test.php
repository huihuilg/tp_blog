<?php
/**
 * Created by PhpStorm.
 * User: ch
 * Date: 19/3/13
 * Time: 上午11:55
 */

namespace app\index\controller;


use app\common\command\Sockett;
use PHPMailer\PHPMailer\PHPMailer;
use think\Controller;

class Test extends Controller
{
    public function printTest()
    {
        dump($this->getRandomMoney(1,5));
    }



    public function socket()
    {
        return $this->fetch();
    }


    /**
     * 配置youji
     */
    public function sendMailSetting($address,$title,$message){
        $mail = new PHPMailer();
        // 设置PHPMailer使用SMTP服务器发送Email
        $mail->IsSMTP();
        // 设置邮件的字符编码，若不指定，则为'UTF-8'
        $mail->CharSet='UTF-8';
        // 添加收件人地址，可以多次使用来添加多个收件人
        $mail->AddAddress($address);
        // 设置邮件正文
        $mail->Body=$message;
        //设置发件人邮箱地址 这里填入上述提到的“发件人邮箱”
        $mail->From='18500254733@163.com';
        //设置发件人姓名（昵称） 任意内容，显示在收件人邮件的发件人邮箱地址前的发件人姓名
        $mail->FromName='皮特张';
        // 设置邮件标题
        $mail->Subject=$title;
        // 设置SMTP服务器。
        $mail->Host='smtp.163.com';
        // 设置为"需要验证"
        $mail->SMTPAuth=true;
        //smtp登录的账号 这里填入字符串格式的qq号即可
        $mail->Username='18500254733@163.com';
        //smtp登录的密码 使用生成的授权码 你的最新的授权码
        $mail->Password='18500254733LGH';
        // 发送邮件。    成功返回true或false
        return($mail->Send());
    }


    /**
     * 发送邮箱
     */
    public function sendMail()
    {
        $res = $this->sendMailSetting('615946628@qq.com','发送标题','发送成功了耶');
        if(!$res){
//            $this->error('发送邮件失败');
            echo '失败';
        }
        echo '成功';
//       $this->success('发送邮件成功','/');
    }



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