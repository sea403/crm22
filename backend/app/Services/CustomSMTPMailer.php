<?php

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class CustomSMTPMailer
{
    public function send($user, $toEmail, $subject, $body)
    {
        $smtp = $user->config['email']['smtp'] ?? null;

        if (!$smtp || !$smtp['host']) {
            throw new \Exception("SMTP config not found for the user.");
        }

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = $smtp['host'];
            $mail->SMTPAuth   = true;
            $mail->Username   = $smtp['username'];
            $mail->Password   = $smtp['password'];
            $mail->SMTPSecure = $smtp['encryption'];
            $mail->Port       = $smtp['port'];

            $mail->setFrom($smtp['username'], $user->name);
            $mail->addAddress($toEmail);

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $body;

            $mail->send();

            return ['success' => true, 'message' => 'Email sent successfully.'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => "SMTP error: {$mail->ErrorInfo}"];
        }
    }
}
