<?php
/*

	Clase para eniar mensajes por correo electrónico usando la plataforma de Infobip

	Created: 2020-03-30
	Author: DriverOp
*/

require_once(DIR_includes."common.inc.php");
require_once(DIR_includes."class.checkinputs.inc.php");
require_once(__DIR__.DS."class.infobip_email_base.php");

class cInfobipMail extends InfoBipEmail_base {

	public $To = null;
	public $From = null;
	public $ReplyTo = null;
	public $Subject = null;
	public $HTMLBody = null;
	public $TextBody = null;
	public $notifyContentType = 'application/json';
	
	private $this_file = null;
	
	function __construct($APIKey = null, $APIUrl = null, $SenderDomain = null) {
		$this->this_file = substr(__FILE__,strlen(DIR_BASE))." ";
		parent::__construct($APIKey, $APIUrl, $SenderDomain);
	}
	
	public function AddAddress($address) {
		$this->To = $this->MakeAddress($address, null);
	}

	public function AddFrom($address, $name = null) {
		$this->From = $this->MakeAddress($address, $name);
	}

	public function AddReplyTo($address) {
		$this->ReplyTo = $this->MakeAddress($address, null);
	}
	
	public function AddSubject($subject) {
		$this->Subject = trim($subject);
		$this->Subject = htmlspecialchars($this->Subject, ENT_QUOTES+ENT_HTML5);
	}
	
	public function Send() {
		$result = false;
		try {
			if (empty($this->To) or empty($this->From)) { throw new Exception('"to" y "from" no pueden estar vacíos.'); }
			if (empty($this->HTMLBody) and empty($this->TextBody)) { throw new Exception('"HTMLBody" y "TextBody" no pueden estar vacíos al mismo tiempo.'); }
			if (empty($this->Subject)) { throw new Exception('"Subject" no puede estar vacío.'); }
			$this->MakeEmailHeader();
			$result = $this->SendEmail();
			
		} catch(Exception $e) {
			$this->SetError($this->this_file." ".__CLASS__,__METHOD__, $e->getMessage());
		}
		return $result;
	}



	private function MakeEmailHeader() {
		$this->BodyForm = array();
		if (!empty($this->To)) {
			$this->BodyForm['to'] = $this->To;
		}
		if (!empty($this->From)) {
			$this->BodyForm['from'] = $this->From;
		}
		if (!empty($this->ReplyTo)) {
			$this->BodyForm['replyto'] = $this->ReplyTo;
		}
		if (!empty($this->Subject)) {
			$this->BodyForm['subject'] = $this->Subject;
		}
		if (!empty($this->TextBody)) {
			$this->BodyForm['text'] = $this->TextBody;
			if (isset($this->BodyForm['html'])) { unset($this->BodyForm['html']); }// No pueden estar los dos seteados al mismo tiempo
		}
		if (!empty($this->HTMLBody)) {
			$this->BodyForm['html'] = $this->HTMLBody;
			if (isset($this->BodyForm['text'])) { unset($this->BodyForm['text']); }// No pueden estar los dos seteados al mismo tiempo
		}
		if (!empty($this->notifyContentType)) {
			$this->BodyForm['notifyContentType'] = $this->notifyContentType;
		}
	}
	
	private function MakeAddress($address, $name) {
		$result = null;
		$address = trim($address);
		$name = trim($name);
		try {
			if (empty($address)) { throw new Exception('No se indicó una dirección de correo electrónico.'); }
			if (cCheckInput::IsEmail($address) == false) { throw new Exception('La dirección de correo electrónico no es válida ('.$address.').'); }
			$result = $address;
			if (!empty($name)) {
				$result = addcslashes($name,'<>').' <'.$address.'>';
			}
		} catch(Exception $e) {
			$this->SetError($this->this_file." ".__CLASS__,__METHOD__, $e->getMessage());
		}
		return $result;
	}
}

?>