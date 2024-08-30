<?php

namespace App\Tools;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class Mail
{
    private $mail;

    public function __construct()
    {
        $this->mail = new PHPMailer(true);
        $this->mail->SMTPDebug = MAIL_DEBUG ? SMTP::DEBUG_SERVER : 0; //Enable verbose debug output
        $this->mail->isSMTP(); //Send using SMTP
        $this->mail->Host = MAIL_HOST; //Set the SMTP server to send through
        $this->mail->SMTPAuth = true; //Enable SMTP authentication
        $this->mail->Username = MAIL_USERNAME; //SMTP username
        $this->mail->Password = MAIL_PASSWORD; //SMTP password
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; //Enable implicit TLS encryption
        $this->mail->Port = 465; //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
        $this->mail->Encoding = "base64";
        $this->mail->CharSet = "UTF-8";
        $this->mail->setFrom(MAIL_USERNAME, APP_NAME);
    }

    public function send($to, $email_confirm_token)
    {
        try {
            $this->mail->addAddress($to);
            $this->mail->isHTML(true);
            $this->mail->Subject = 'Подтверждение аккаунта.';
            $this->mail->Body = "Для активации перейдите по ссылке http://webchat.local/registration/activate?token=$email_confirm_token";
            $this->mail->send();
            if (MAIL_DEBUG) {
                echo 'Message has been sent';
            }
        } catch (Exception $e) {
            if (MAIL_DEBUG) {
                echo "Message could not be sent. Mailer Error: {$this->mail->ErrorInfo}";
            }
        }
    }
}
