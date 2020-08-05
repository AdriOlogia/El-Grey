<?php
/*
	Clase fundacional para todas las clases que implementan conexió a una API externa.
	Establece los métodos comunes a todas las clases que necesitan comunicarse al exterior.
	
	Created: 2020-03-04
	Author: DriverOp.

*/


require_once("apirest.msgs.php"); // Aquí están los mensajes de error.

define("API_referer","https://mail.ombutech.net/");

class cAPIRestBase {
	
	public $parsed_response = array();
	public $data;
	public $response = '';
	public $errores = '';
	public $intl_nroerr = 0;
	public $curl_nroerr = 0;
	public $http_nroerr = 0;
	public $json_nroerr = 0;
	public $api_nroerr = 0;
	private $internal_errors = null;
	private $curl_errors = null;
	private $http_codes = null;
	private $json_errors = null;
	public $log_file_name = 'apirest.log';
	
	function __construct() {
		global $internal_errors;
		global $curl_errors;
		global $http_codes;
		global $json_errors;
		$this->internal_errors = $internal_errors;
		$this->curl_errors = $curl_errors;
		$this->http_codes = $http_codes;
		$this->json_errors = $json_errors;
	}
	
	public function SetLog($msg) {
		if ($this->debug_level > 1) {
			echo $msg.'<br />';
		}
		umask(0);
	
		$mes = Date('Y-m');
		$dia = Date('Y-m-d');
		
		$basename = basename($this->log_file_name);
		if (empty($basename)) { $basename = 'apirest.log'; }
		
		$dirname = dirname($this->log_file_name);
		if (empty($dirname) or ($dirname == '.')) { $dirname = DIR_logging; }
		
		if (!file_exists($dirname)) {
			mkdir($dirname,0777);
		}
		$dir = $dirname.DIRECTORY_SEPARATOR.$mes;
	
		if (!file_exists($dir)) {
			mkdir($dir,0777);
		}
		$archivo = $dir.DIRECTORY_SEPARATOR.$dia.'-'.$basename;
	
		$linea = '['.Date('Y-m-d H:i:s').'] '.basename(__FILE__).' '.$msg.PHP_EOL;
		file_put_contents($archivo, $linea, FILE_APPEND);
	}
/*
	Establece el error interno propio de esta clase.
*/
	protected function SetINTERNALError($nro = 0) {
		
		$this->intl_nroerr = $nro;
		if (isset($this->internal_errors[$nro])) {
			$this->errores = $this->internal_errors[$nro];
		}
		$msg = "Internal Error: ".$this->intl_nroerr." ".$this->errores;
		if ($this->debug_level > 0) {
			$this->SetLog($msg);
		}
		if ($this->debug_level > 1) {
			echo $msg.'<br />';
		}
		return $msg;
	} // SetINTERNALError
/*
	Interpreta el error devuelto por cURL.
*/
	protected function SetCURLError($link) {
		$result = false; // Asumir ningún error.
		$this->errores = '';
		$this->curl_nroerr = curl_errno($link);
		if ($this->curl_nroerr != 0) { // Hubo un error?
			$result = true; // Sí!
			if (isset($this->curl_errors[$this->curl_nroerr])) {
				$this->errores = $this->curl_errors[$this->curl_nroerr];
			}
			$msg = "cRUL Error: ".$this->curl_nroerr." ".$this->errores;
			if ($this->debug_level > 0) {
				$this->SetLog($msg);
			}
			if ($this->debug_level > 1) {
				echo $msg.'<br />';
			}
		}
		return $result;
	} // SetCURLError
/*
	Interpretar código de Status HTTP como un error... o no.
*/	
	protected function SetHTTPError($link) {
		$msg = null;
		$this->errores = NULL;
		$this->http_nroerr = curl_getinfo($link,CURLINFO_HTTP_CODE);
		if (isset($this->http_codes[$this->http_nroerr])) {
			$this->errores = $this->http_codes[$this->http_nroerr];
		} else {
			$this->errores = 'HTTP code: '.$this->http_nroerr;
		}
		$msg = "HTTP Status: ".$this->http_nroerr." ".$this->errores;
		if ($this->debug_level > 0) { 
			$this->SetLog($msg);
		}
		if ($this->debug_level > 1) {
			echo $msg.'<br />';
		}
		return ($this->http_nroerr >= 400); // Cualquier código por encima de 400 (inclusive) es un error.
	} // SetHTTPError
/*
	La respuesta del servidor es siempre JSON.
	Esta función interpreta el error del parser de JSON.
*/	
	protected function SetJSONError($error_code) {
		$result = false; // Asumir ningún error.
		$linea = null;
		$this->errores = NULL;
		$this->json_nroerr = $error_code;
		if ($this->json_nroerr != 0) { // Hubo un error?
			$result = true; // Sí!
			if (isset($this->json_errors[$this->json_nroerr])) { // Cuál?
				$this->errores = $this->json_errors[$this->json_nroerr];
			} else {
				$this->errores = 'Error de JSON no esperado'; // No sé cuál es el error.
			} // else
		} // if
		$msg = "JSON Error: ".$this->json_nroerr." ".$this->errores;
		if ($this->debug_level > 0) { 
			$this->SetLog($msg);
		}
		if ($this->debug_level > 1) {
			echo $msg.'<br />';
		}
		return $result;
	} // SetJSONError()
	
	protected function SetError($file, $method, $msg) {
		$this->error = true;
		$this->errores = $msg;
		$line = basename($file)." -> ".$method.". ".$msg;
		if ($this->debug_level > 0) { 
			$this->SetLog($msg);
		}
		if ($this->debug_level > 1) {
			echo $msg.'<br />';
		}
	} // SetError

}

?>