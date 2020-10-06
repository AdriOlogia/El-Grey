<?php
/*
	Validación del formulario de contacto.
	Created: 2020-04-27
	Author: DriverOp.
*/
require_once(DIR_includes."class.checkinputs.inc.php");
require_once(DIR_config."access.config.inc.php");
require_once(DIR_model."class.mensajes.inc.php");
require_once(DIR_vendor.'autoload.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;


$this_file = substr(__FILE__,strlen(DIR_BASE))." ";

$post = CleanArray($_POST);
$msgerr = array();
$data = [];

$data['inputname'] = titleCase(mb_substr($post['nombre'],0,64));
$data['inputsurname'] = titleCase(mb_substr($post['apellido'],0,64));
$data['email'] = mb_substr($post['email'],0,120);


cCheckInput::NomApe($data['inputname'], 'inputname', 'Contact name');
cCheckInput::NomApe($data['inputsurname'], 'inputsurname', 'Contact name');
cCheckInput::Email($data['email'], 'email', 'Email');

$msgerr = array_merge($msgerr, cCheckInput::$msgerr);

if (CanUseArray($msgerr)) {
	EmitJson($msgerr);
	return;
}

$body = " El Nombre del Usuario es: ".$data['inputname']." ".$data['inputsurname']." Su correo es: " . $data['email'];

//Create a new PHPMailer instance
$mail = new PHPMailer;

//Tell PHPMailer to use SMTP
$mail->isSMTP();

//Enable SMTP debugging
// SMTP::DEBUG_OFF = off (for production use)
// SMTP::DEBUG_CLIENT = client messages
// SMTP::DEBUG_SERVER = client and server messages
$mail->SMTPDebug = SMTP::DEBUG_OFF;

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
$mail->Username = CONTACT_MAIL;

//Password to use for SMTP authentication
$mail->Password = CONTACT_PASS;

//Set who the message is to be sent from --> CORREO DEL GREY
$mail->setFrom('info@elgreyid.com', 'Info El Grey ID');

//Set who the message is to be sent to --> CORREO DEL GREY
$mail->addAddress('info@elgreyid.com', 'Desde EL GREY'); 

//Set the subject line
$mail->Subject = <<<EOT
CONTACT - A user {$data['inputname']} {$data['inputsurname']} send you a message to suscribe.
EOT;

//Read an HTML message body from an external file, convert referenced images to embedded,
//convert HTML into a basic plain-text alternative body

$mail->isHTML(false);
$mail->Body = $body;

//send the message, check for errors
$result = $mail->send();

if (!$result) {
	EmitJSON("No se pudo enviar el Mensaje.");
	cLogging::Write($this_file." No se pudo enviar correo de resumen del formulario a <mail de el grey>.",LGEV_WARN);
	return;
}
cLogging::Write($this_file." Se envió correo a mail de el grey.",LGEV_WARN);

ResponseOk();

?>