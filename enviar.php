<?php
/**
 * This example shows settings to use when sending via Google's Gmail servers.
 * This uses traditional id & password authentication - look at the gmail_xoauth.phps
 * example to see how to use XOAUTH2.
 * The IMAP section shows how to save this message to the 'Sent Mail' folder using IMAP commands.
 */

//Import PHPMailer classes into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

require 'vendor\autoload.php';


$usuarioname = $_POST['nombre'];
$usuariosurname = $_POST['apellido'];
$useremail = $_POST['email'];

$body = "El Nombre del Usuario es:   " . $usuarioname . "     
Su correo es: " . 
$useremail;

//Create a new PHPMailer instance
$mail = new PHPMailer;

//Tell PHPMailer to use SMTP
$mail->isSMTP();

//Enable SMTP debugging
// SMTP::DEBUG_OFF = off (for production use)
// SMTP::DEBUG_CLIENT = client messages
// SMTP::DEBUG_SERVER = client and server messages
$mail->SMTPDebug = 0;

$mail->SMTPOptions = array(
'ssl' => array(
'verify_peer' => false,
'verify_peer_name' => false,
'allow_self_signed' => true
)
);
//Set the hostname of the mail server
$mail->Host = gethostbyname('smtp.gmail.com');
// use
// $mail->Host = gethostbyname('smtp.gmail.com');
// if your network does not support SMTP over IPv6

//Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
$mail->Port = 587;

//Set the encryption mechanism to use - STARTTLS or SMTPS
$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;

//Whether to use SMTP authentication
$mail->SMTPAuth = true;

//Username to use for SMTP authentication - use full email address for gmail
$mail->Username = 'aabreu@ombutech.net';

//Password to use for SMTP authentication
$mail->Password = '96033527a';

//Set who the message is to be sent from --> CORREO DEL GREY
$mail->setFrom('aabreu@ombutech.net', $usuarioname);

//Set who the message is to be sent to --> CORREO DEL GREY
$mail->addAddress('aabreu@ombutech.net', 'Desde EL GREY'); 

//Set the subject line
$mail->Subject = <<<EOT
A user "$usuarioname $usuariosurname" send you a message to suscribe.
EOT;

//Read an HTML message body from an external file, convert referenced images to embedded,
//convert HTML into a basic plain-text alternative body

$mail->isHTML(false);
$mail->Body = $body;
//$mail->msgHTML(file_get_contents('contents.html'), _DIR_);

//Replace the plain text body with one created manually
//$mail->AltBody = 'This is a plain-text message body';

//send the message, check for errors
if (!$mail->send()) {
    echo 'Mailer Error: '. $mail->ErrorInfo;
} else {
    echo '<script>
    alert("El Mensaje a sido enviado con exito!");
    window.history.go(-1);
    </script>';
    
}
