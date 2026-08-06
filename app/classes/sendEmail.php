<?php
namespace sendEmail;
$filepath = realpath(dirname(__FILE__));

require ($filepath.'/../classes/SMTP.php');
require ($filepath.'/../classes/PHPMailer.php');
require ($filepath.'/../classes/Exception.php');

//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;


class sendEmail {
    public static function sendEmail($name, $email, $subject, $message) {
        $sent = FALSE;
        try {
            // Create an instance; passing `true` enables exceptions
            $mail = new PHPMailer(true);

            // Server settings
            $mail->SMTPDebug = SMTP::DEBUG_OFF;                      //Enable verbose debug output
            $mail->isSMTP();                                            //Send using SMTP
            $mail->Host       = constant('SMTP_HOST');                     //Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
            $mail->Username   = constant('SMTP_UNAME');                     //SMTP username
            $mail->Password   = $_ENV['SMTP_PASS'];                               //SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
            
            // TCP port to connect to; use 587 if you have set:
            // $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS
            $mail->Port       = constant('SMTP_PORT');

            // Recipients
            $mail->setFrom(constant('SMTP_SENDER'), constant('SMTP_SENDER_PRETTY'));
            $mail->addAddress($email);               //Add recipient
            $mail->addReplyTo(constant('SMTP_SENDER'), constant('SMTP_SENDER_PRETTY'));
            //$mail->addBCC(constant('sysadmin_email'));

            // Content
            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject = $subject;
            $mail->Body    = $message;
            $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

            $mail->send();
            echo '<strong>Success!</strong>';
            $sent = TRUE;
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            $sent =   FALSE;
        }
        return $sent;
    }
}