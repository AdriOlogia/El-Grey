<?php
/*
	Clase para la manipulación de mensajes del formulario de contacto.
	Created: 2020-04-28
	Author: DriverOp
	
*/
require_once(DIR_includes."class.fechas.inc.php");
require_once(DIR_model."class.fundation.inc.php");


class cMensaje extends cModels {
	
	public $tabla_mensajes = TBL_mensajes;
	
	public function __construct() {
		parent::__construct();
		$this->actual_file = __FILE__;
	}

	
	public function Get($id) {
		$result = false;
		$this->Reset();
		if (SecureInt($id,null) == null) { throw new Exception(__LINE__." ID debe ser un número."); }
		$this->sql = "SELECT * FROM ".SQLQuote($this->tabla_mensajes)." WHERE `id` = ".$id;
		if (parent::Get($id)) {
			$this->ParseRecord();
			$result = true;
			if (!empty($this->data) and (is_object($this->data) or is_array($this->data))) {
				foreach($this->data as $key => $value) {
					$this->$key = $value;
				}
			}
		}
		return $result;
	}
/*
	Guarda un nuevo mensaje
	OJO: NO valida los datos de entrada.
*/
	public function Set($data) {
		$result = false;
		try {
			$reg = [];
			if (CanUseArray($data)) {
				$reg = $this->RealEscapeArray($data);
				if (isset($reg['id'])) { unset($reg['id']); }
			} else {
				$reg['data'] = json_encode($data);
			}
			$reg['fechahora'] = cFechas::Ahora();
			$this->Insert($this->tabla_mensajes, $reg);
			$result = $this->last_id;
		} catch (Exception $e) {
			$this->SetError(__METHOD__,$e->GetMessage());
		}
		return $result;
	}
/*
	Actualizar registro
*/
	public function UpdateMsg($data, $id) {
		$result = false;
		try {
			if (!is_numeric($id)) { throw new Exception("ID debe ser un número."); }
			$reg = $this->RealEscapeArray($data);
			if (isset($reg['id'])) { unset($reg['id']); }
			$result = $this->Update($this->tabla_mensajes, $reg, "`id` = ".$id);
		} catch (Exception $e) {
			$this->SetError(__METHOD__,$e->GetMessage());
		}
		return $result;
	}
}



?>