<?php
/**
 * EmailSend.php
 * 文件描述
 * Created on 2023/11/2 16:38
 * Creat by ClearSwitch
 */

namespace ClearSwitch\DragonBallLaravel\Service\SendMessage\Mail;

use ClearSwitch\DragonBallLaravel\Service\SendMessage\MessageFactory;
use PHPMailer\PHPMailer\PHPMailer;

class EmailSend implements MessageFactory
{
    public function sendMessage(...$args)
    {
        $data = func_get_args($args);
        $this->mailSentCC($data[0], $data[1], $data[2]);
    }

    function mailSentCC($title, $text, $cc_list, $sent_mail = '', $sent_name = '')
    {
        $username = 'alert@yicencorp.com';
        $password = '3HfYQqWe3TmyM4Za';
        $sender = 'Alert';
        $mail = new PHPMailer(true);
        $mail->CharSet = "UTF-8";                     //设定邮件编码
        $mail->SMTPDebug = 0;                        // 调试模式输出
        $mail->isSMTP();                             // 使用SMTP
        $mail->Host = 'smtp.exmail.qq.com';                // SMTP服务器
        $mail->SMTPAuth = true;                      // 允许 SMTP 认证
        $mail->Username = $username;                // SMTP 用户名  即邮箱的用户名
        $mail->Password = $password;             // SMTP 密码  部分邮箱是授权码(例如163邮箱)
        $mail->SMTPSecure = 'ssl';                    // 允许 TLS 或者ssl协议
        $mail->Port = 465;                            // 服务器端口 25 或者465 具体要看邮箱服务器支持

        $mail->setFrom($username, $sender);  //发件人
        if (!$sent_mail) {
            $sent_mail = $username;
            $sent_name = $sender;
        }
        $mail->addAddress($sent_mail, $sent_name);  // 可添加多个收件人
        if (!empty($cc_list)) {
            foreach ($cc_list as $key => $value) {
                $mail->addCC($value);
            }
        }
        $mail->isHTML(true);
        $mail->Subject = $title;
        $mail->Body = $text;
        $mail->AltBody = $text;
        $mail->send();
        return 'success';
    }
}
