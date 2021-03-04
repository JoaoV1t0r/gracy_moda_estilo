<?php

namespace App\Classes;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class EnviarEmail
{
    //==========================================================================================
    public function EnviarEmailConfirmacaoNovoCLiente($emailCliente, $purl)
    {
        //Criando o purl
        $link = BASE_URL . 'confirmar_email?purl=' . $purl;

        //Envia e-mail de confirmação da conta do cliente
        $mail = new PHPMailer(true);

        try {
            //Server settings
            $mail->SMTPDebug = SMTP::DEBUG_OFF;
            $mail->isSMTP();
            $mail->Host       = EMAIL_HOST;
            $mail->SMTPAuth   = true;
            $mail->Username   = EMAIL_FROM;
            $mail->Password   = EMAIL_SENHA;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = EMAIL_PORT;
            $mail->CharSet    = 'UTF-8';

            //Recipients
            $mail->setFrom(EMAIL_FROM, APP_NAME);
            $mail->addAddress($emailCliente);

            //Content
            $mail->isHTML(true);
            $mail->Subject = APP_NAME . ' - Confirmação de E-mail';
            //Mensagem do E-mail
            $html = "<p>Seja bem-vindo a nossa loja " . APP_NAME . ".</p>";
            $html .= "<p>Para poder entrar em nossa loja é necessario a confirmação de e-mail.</p>";
            $html .= "<p>Para confirmar o e-mail clique no link abaixo:</p>";
            $html .= '<p><a href="' . $link . '">Confirmar E-mail</a></p>';
            $html .= '<p><i><smail>' . APP_NAME . '</smail></i></p>';
            $mail->Body = $html;

            $mail->send();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
