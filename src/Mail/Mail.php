<?php

namespace App\Mail;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once '../Config.php';
use Config;

class Mail {

    public static function sendMail($mail_user, $name_user, $firstname_user, $message__user) {

        $flag = null;

        try {

            $mail = new PHPMailer(true);

            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = Config::getMailUser();
            $mail->Password = Config::getMailUserPassword();
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = 465;

            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allo_self_signed' => true
                ]
            ];

            $mail->addAddress(Config::getMailUser());
            $mail->addReplyTo($mail_user, $name_user . ' ' . $firstname_user);

            $mail->isHTML(true);
            $mail->Subject = 'New contact message from speakfluent';
            $mail->Body = 'Firstname: ' . $firstname_user . '<br>' .
                          'Name: ' . $name_user . '<br>' .
                          'Email: ' . $mail_user . '<br>' . 
                          'Message: ' . '<br>' . $message__user;

            $mail->send();

            $flag = true;
        }

        catch (\Exception $e) {

            $flag = false;
        }

        return $flag;
    }



    public static function sendMailResetPassword($mail_user, $token) {

        $flag = null;

        try {

            $mail = new PHPMailer(true);

            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = Config::getMailUser();
            $mail->Password = Config::getMailUserPassword();
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = 465;

            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allo_self_signed' => true
                ]
            ];

            $mail->addAddress($mail_user);
            $mail->addReplyTo($mail_user);

            $mail->isHTML(true);
            $mail->Subject = 'Reset your password (Speakfluent)';
            $mail->Body = 'Click on this link to reset your password (available 1 hour): ' . 'http://localhost:3000/set-new-password?key=' . $token . '&mail=' . $mail_user;

            $mail->send();

            $flag = true;
        }

        catch (\Exception $e) {

            $flag = false;
        }

        return $flag;
    }
}