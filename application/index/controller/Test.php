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

    }

    public function socket()
    {
        return $this->fetch();
    }


    /**
     * 配置邮箱
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


}