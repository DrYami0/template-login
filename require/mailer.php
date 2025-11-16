<?php
require_once __DIR__ . '/config.php';

function envoyerMailAdmin($to, $subject, $body) {
    if (defined('MAILER') && MAILER === 'smtp') {
        if (class_exists('PHPMailer\\PHPMailer\\PHPMailer')) {
            $mail = new PHPMailer\\PHPMailer\\PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = SMTP_HOST;
                $mail->Port = SMTP_PORT;
                if (SMTP_SECURE === 'ssl') {
                    $mail->SMTPSecure = PHPMailer\\PHPMailer\\PHPMailer::ENCRYPTION_SMTPS;
                } elseif (SMTP_SECURE === 'tls') {
                    $mail->SMTPSecure = PHPMailer\\PHPMailer\\PHPMailer::ENCRYPTION_STARTTLS;
                }
                $mail->SMTPAuth = true;
                $mail->Username = SMTP_USER;
                $mail->Password = SMTP_PASS;
                $mail->setFrom(SMTP_USER, 'Site Admin');
                $mail->addAddress($to);
                $mail->Subject = $subject;
                $mail->Body = $body;
                $mail->send();
                return;
            } catch (Exception $e) {
                mail($to, $subject, $body);
                return;
            }
        } else {
            mail($to, $subject, $body);
            return;
        }
    } else {
        mail($to, $subject, $body);
    }
}