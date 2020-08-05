<?php
/*
	Clase base que encapsula el uso de la API de Infobip para el envío de mensajes de correo electrónico.
	
	Created: 2020-03-28
	Author: DriverOp
	Rebrit SRL - San Martín 987 - Gualeguaychú - Entre Ríos.
	email: info@rebrit.com.ar
*/
define("INFOBIP_TIMEOUT",30); // Cuántos segundos esperar hasta morir la conexión.
define("INFOBIP_LOGFILE","infobip_email.log"); // Cómo se llama el archivo que guardará el log total de la conexión.

require_once(DIR_includes."common.inc.php");
require_once(DIR_model."class.apirest_base.inc.php");

class InfoBipEmail_base extends cAPIRestBase {
	
	public $debug_level = 0;
	public $APIKey = '';
	public $APIUrl = '';
	public $SenderDomain = 'fakesite.tld';
	
	public $BodyForm = array();
	
	public $ResponseMessage = '';
	
	public $Final_URL = null;
	public $curl_options = array();
	private $curl_defaultoptions = array(
		CURLOPT_USERAGENT => 'OmbuFintech cURL 1.2',
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_REFERER => 'http://fakesite.tld/',
		CURLOPT_HEADER => false,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_TIMEOUT => INFOBIP_TIMEOUT,
		CURLOPT_SSL_VERIFYPEER => false,
		CURLOPT_SSL_VERIFYHOST => false
	);
	private $link;


	function __construct($APIKey = null, $APIUrl = null, $SenderDomain = null) {
		$this->log_file_name = INFOBIP_LOGFILE;
		parent::__construct();
		if (version_compare(PHP_VERSION, '5.4.0','<')) {
			$this->SetINTERNALError(1);
			return;
		}
		if (!function_exists('curl_init')) {
			$this->SetINTERNALError(2);
			return;
		}
		if (!empty($APIKey)) { $this->APIKey = $APIKey; }
		if (!empty($APIUrl)) { $this->APIUrl = $APIUrl; }
		if (!empty($SenderDomain)) { $this->SenderDomain = $SenderDomain; }
	} // __constructor
	
	public function SendEmail() {
		$result = true;
		$this->Final_URL = EnsureTrailingURISlash($this->APIUrl).'email/2/send';
		$this->ResponseMessage = '';
		$this->curl_options = array(
			CURLOPT_HTTPHEADER => array(
				'Accept: application/json', 
				'Content-type: multipart/form-data',
				'Authorization: App '.$this->APIKey
			),
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_POST =>true,
			CURLOPT_POSTFIELDS => $this->BodyForm,
			CURLOPT_URL => $this->Final_URL
		);
		$result = $this->ExecCall();
			if ($result) {
				$a = json_decode($this->response,true);
				$result = !$this->SetJSONError(json_last_error());
				$this->parsed_response = $a;
				if (($this->debug_level > 0) and ($result)) {
					$this->SetLog(__METHOD__.PHP_EOL.print_r($this->parsed_response,true)); 
				}
				if ($result) {
					if (isset($this->parsed_response['messages']) and CanUseArray($this->parsed_response['messages'])) {
						if (CanUseArray($this->parsed_response['messages'][0]['status']))
						$this->ResponseMessage = @$this->parsed_response['messages'][0]['status']['description'];
						
					}
				}
			}
		return $result;
	}

	private function ExecCall() {
		$result = true;
		try {
			$this->parsed_response = array();
			$this->errores = '';
			$this->link = curl_init();
			curl_setopt_array($this->link, ($this->curl_options + $this->curl_defaultoptions));

			$this->SetLog(__METHOD__." URL: ".$this->Final_URL);
			$this->response = curl_exec($this->link); // Exec!
			if ($this->debug_level > 0) {
				if (mb_strlen($this->response) <= 2048) {
					$this->SetLog(__METHOD__.PHP_EOL.print_r($this->response,true)); 
				} else {
					$this->SetLog(__METHOD__.PHP_EOL.$this->response);
				}
			}
			if ($this->SetCURLError($this->link) == false) { // Si no hubo error en cURL...
				$this->SetHTTPError($this->link);
				if ($this->http_nroerr >= 400) {
					$result = false;
				}
			} else {
				$result = false;
			}
		} catch (Exception $e) {
			$this->SetError(__FILE__,__METHOD__,$e->getMessage());
		}
		@curl_close($this->link);
		return $result;
	}
}

?>