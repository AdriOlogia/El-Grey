<?php
/*
	Constantes para establecer el tipo de evento en los logs del sistema.
*/

require_once(DIR_model."class.dbutili.2.inc.php");

define("LGEV_ALL", 0); // Todos los eventos.
define("LGEV_DEBUG", 1); // Evento de debug.
define("LGEV_INFO", 2); // Información
define("LGEV_WARN", 3); // Aviso de que algo pudo salir mal
define("LGEV_ERROR", 4); // Algo salió mal pero se puede seguir.
define("LGEV_FATAL", 5); // Todo se fue al carajo.
define("LGEV_OFF", 6); // Se apaga el log.
define("LGEV_TRACE", 7); // El log incluye más detalles en la descripción de este evento.

define("LGEV_TARGET_FILE",1); // Se loguea a un archivo.
define("LGEV_TARGET_DB",2); // Se loguea a la base de datos.

define("LGEV_DEFAULT_TARGET",LGEV_TARGET_FILE); // A donde se escribe el log por omisión.

if (!defined("LGEV_LEVEL")) {
	define("LGEV_LEVEL",0);
}

class cLogging {

	
	private static $default_target = LGEV_DEFAULT_TARGET;
	private static $dbtabla = 'sys_logging';
	
	static function SetTarget($target) {
		self::$default_target = $target;
	}


	static function Write($linea = NULL, $event = LGEV_DEBUG, $target = NULL) {
		if ($event < LGEV_LEVEL) { return; }
		if ((is_null($target)) and (self::$default_target != 0)) {
			$target = self::$default_target;
		}
		if ($target & LGEV_TARGET_FILE) {
			self::LogToFile($linea, NULL, $event);
		}
		if ($target & LGEV_TARGET_DB) {
			self::LogToDB($linea);
		}
	}
	
	static function LogToFile($text, $archivo = null, $event = LGEV_DEBUG) {
		umask(0);
	
		$mes = Date('Y-m');
		$dia = Date('Y-m-d');
		
		$dir = DIR_logging.$mes;
		
		if (empty($archivo)) {
			$archivo = $dir.'/'.$dia.'.log';
		}
		
		
		if (!file_exists(DIR_logging)) {
			mkdir(DIR_logging,0777);
		}
	
		if (!file_exists($dir)) {
			mkdir($dir,0777);
		}
		
		$linea = '['.Date('Y-m-d H:i:s').'] ';
		if (($event > -1)) {
			$linea .= self::EventToText($event)." - ";
		}
		$linea .= $text.PHP_EOL;
		
		return file_put_contents($archivo, $linea, FILE_APPEND);
	}

	static function LogToDB($text, $event = LGEV_DEBUG, $trace = null) {
		try {
			$db = new cDB();
			$db->Connect(DBHOST, DBNAME, DBUSER, DBPASS);
			if ($db->IsConnected()) {
				$reg = array();
				$reg['sys_fechahora'] = Date('Y-m-d H:i:s');
				$reg['tipo_evento'] = self::EventToText($event);
				$reg['descripcion'] = $db->RealEscape(substr($text,0,255));
				if (!empty($trace)) {
					if (is_object($trace) or is_array($trace)) {
						$reg['data'] = $db->RealEscape(json_encode($trace, JSON_HACELO_BONITO_CON_ARRAY));
					} else {
						$reg['data'] = $db->RealEscape($trace);
					}
				}
				if (isset($objeto_usuario) and isset($objeto_usuario->id)) {
					$reg['sys_usuario_id'] = $objeto_usuario->id;
				}
				$db->Insert(self::$dbtabla, $reg);
				if ($db->error) { throw new Exception('DBErr: '.$db->errmsg); }
			} else {
				throw new Exception('DBErr: No se pudo conectar a la base de datos: '.$db->errmsg);
			}
		} catch(Exception $e) {
			self::LogToFile(__FILE__.$e->GetMessage(), null, LGEV_ERROR);
		}
	}
	
	static function EventToText($event) {
		$result = NULL;
		switch ($event) {
			case 0: $result = 'ALL'; break;
			case 1: $result = 'DEBUG'; break;
			case 2: $result = 'INFO'; break;
			case 3: $result = 'WARN'; break;
			case 4: $result = 'ERROR'; break;
			case 5: $result = 'FATAL'; break;
			case 6: $result = 'OFF'; break;
			case 7: $result = 'TRACE'; break;
		}
		return $result;
	}
}