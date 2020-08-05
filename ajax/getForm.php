<?php
/*
	Validación del formulario de contacto.
	Created: 2020-04-27
	Author: DriverOp.
*/
require_once(DIR_includes."class.checkinputs.inc.php");
require_once(DIR_model."class.mensajes.inc.php");
$this_file = substr(__FILE__,strlen(DIR_BASE))." ";

$post = CleanArray($_POST);
$msgerr = array();
$data = [];

$data['companyName'] = titleCase(mb_substr($post['companyName'],0,64));
$data['industry'] = titleCase(mb_substr($post['industry'],0,64));
$data['country'] = FormatStrUTF8(mb_substr($post['country'],0,64),2);
$data['contactName'] = titleCase(mb_substr($post['contactName'],0,64));
$data['titleName'] = mb_substr($post['titleName'],0,64);
$data['telNumber'] = mb_substr($post['telNumber'],0,20);
$data['email'] = mb_substr($post['email'],0,120);


cCheckInput::NomApe($data['contactName'], 'contactName', 'Contact name');
cCheckInput::Email($data['email'], 'email', 'Email');
if (!empty($data['telNumber'])) {
	cCheckInput::Tel($data['telNumber'], 'telNumber', 'Phone');
}

$msgerr = array_merge($msgerr, cCheckInput::$msgerr);

if (CanUseArray($msgerr)) {
	EmitJson($msgerr);
	return;
}

$mensaje = new cMensaje();

$mensaje_id = $mensaje->Set(array(
	'data'=>json_encode($data, JSON_HACELO_BONITO_CON_ARRAY)
));

$email_recipient = '';


require_once(DIR_model.DS."infobip-email".DS."class.infobip_email.php");

$ibmail = new cInfobipMail();
$ibmail->debug_level = 1;

$ibmail->APIUrl = '';
$ibmail->APIKey = '';
$ibmail->SenderDomain = '';

$plantilla = DIR_plantillas."emails".DS."formulario_contacto.html";

if (!ExisteArchivo($plantilla)) {
	EmitJSON("No se pudo enviar el Mensaje.<br />El sistema no está bien configurado.");
	cLogging::Write($this_file." No se pudo enviar correo de resumen del formulario a <".$email_recipient.">.",LGEV_WARN);
	return;
}
$html = file_get_contents($plantilla);

$ibmail->HTMLBody = str_replace('[fechahoratxt]', cFechas::SQLDate2Str(cFechas::Ahora()), $html);
$ibmail->HTMLBody = str_replace('[fechahora]', cFechas::SQLDate2Str(cFechas::Ahora()), $ibmail->HTMLBody);
$ibmail->HTMLBody = str_replace('[appname]', '', $ibmail->HTMLBody);
$ibmail->HTMLBody = str_replace('[appdescription]', '', $ibmail->HTMLBody);

$ibmail->HTMLBody = str_replace('[companyName]', $data['companyName'], $ibmail->HTMLBody);
$ibmail->HTMLBody = str_replace('[industry]', $data['industry'], $ibmail->HTMLBody);
$ibmail->HTMLBody = str_replace('[country]', $data['country'], $ibmail->HTMLBody);
$ibmail->HTMLBody = str_replace('[contactName]', $data['contactName'], $ibmail->HTMLBody);
$ibmail->HTMLBody = str_replace('[titleName]', $data['titleName'], $ibmail->HTMLBody);
$ibmail->HTMLBody = str_replace('[telNumber]', $data['telNumber'], $ibmail->HTMLBody);
$ibmail->HTMLBody = str_replace('[email]', $data['email'], $ibmail->HTMLBody);

$ibmail->AddAddress($email_recipient);
$ibmail->AddFrom('hi@'.$ibmail->SenderDomain, APP_NAME);
$ibmail->AddReplyTo($data['email']);
$ibmail->Subject =  APP_NAME.' :: Contacto.';

$result = $ibmail->Send();

if (!$result) {
	EmitJSON("No se pudo enviar el Mensaje.");
	cLogging::Write($this_file." No se pudo enviar correo de resumen del formulario a <".$email_recipient.">.",LGEV_WARN);
	return;
}
cLogging::Write($this_file." Se envió correo a ".$email_recipient.".",LGEV_WARN);

ResponseOk();

if ($mensaje_id) { $mensaje->UpdateMsg(array('enviado'=>1),$mensaje_id); }
?>