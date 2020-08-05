<?php
/*

	- SetError. Controla los erroes. Pasar __METHOD__ y el mensaje siempre.
	- FilterFloat: Convierte la entrada en un tipo flotante PHP, o false en caso de no poder hacerlo.
	- FilterInt: Convierte la entrada en un tipo int PHP, o false en caso de no poder hacerlo.
	- FilterNumber: Verifica si $value está compuesto de solo números.
	- FilterVar: Recorta la entrada a una cantidad arbitraria de caracteres y opcionalmente pasa a minúsculas.
	- BuildEstadoCond: Uso interno, resuelve la lógica para armar la consulta SQL en base al campo `estado`.
	- GenerateAlias: Con un string, generar un alias apto para URL.
	- DescomponerRango: Descompone las fechas enviadas por el plugin datetimepicker en las dos fechas límites, devolviéndolas en un array en formato ISO.
*/

include_once(DIR_includes."class.checkinputs.inc.php");
include_once(DIR_includes."class.logging.inc.php");

$Estados_Validos = array(
	'HAB'=>'Habilitado',
	'DES'=>'Deshabilitado',
	'ELI'=>'Eliminado'
);
$Estados_Validos_Colores = array(
	'HAB'=>'<span class="font-weight-bold">%s</span>',
	'DES'=>'<span class="font-weight-bold text-warning">%s</span>',
	'ELI'=>'<span class="font-weight-bold text-danger">%s</span>'
);

class cSidekick{
	
	public static $reg_param = null;

	static function SetError($method, $msg) {
		$line = substr(__FILE__,strlen(DIR_BASE))." -> ".$method.". ".$msg;
		if (DEVELOPE) { EchoLogP(htmlentities($line)); }
		cLogging::Write($line);
	}
/*
	Convierte la entrada $value en un tipo flotante PHP, o false en caso de no poder hacerlo.
*/
	static function FilterFloat($value) {
		$value = trim($value);
		$value = substr($value,0,11);
		$value = trim($value);
		if (empty($value)) { return false; }
		$p = strrpos($value,',');
		if (($p !== false) and ($p > 0)) {
			$value = str_replace('.','',$value);
			$value = str_replace(',','.',$value);
		} else {
			$p = strrpos($value,'.');
			if (($p !== false) and ($p > 0)) {
				$value = str_replace(',','',$value);
			}
		}
		$value = trim($value);
		if (empty($value)) { return false; }
		if (!preg_match('/[0-9\.]+/',$value)) { return false; }
		if (!is_numeric($value)) { return false; }
		return (float)$value;
	}
/*
	Convierte la entrada $value en un tipo int PHP, o false en caso de no poder hacerlo.
*/
	static function FilterInt($value) {
		$value = trim($value);
		$value = substr($value,0,11);
		$value = trim($value);
		if (empty($value)) { return false; }
		if (!preg_match('/^[0-9]+$/',$value)) { return false; }
		return (int)$value;
	}
/*
	Verifica si $value está compuesto de solo números.
*/
	static function FilterNumber($value) {
		$value = trim($value);
		if (empty($value)) { return false; }
		if (!preg_match('/[0-9]+/',$value)) { return false; }
		return $value;
	}
/*
	Recorta la entrada a una cantidad arbitraria de caracteres y opcionalmente pasa a minúsculas.
	$var: la variable.
	$length: a qué largo cortarla.
	$lower: convertir a minúsculas.
*/
	static function FilterVar($var, $length = 11, $lower = false) {
		$var = trim($var);
		$var = mb_substr($var,0, $length);
		$var = trim($var);
		if ($lower) {
			$var = mb_strtolower($var);
		}
		return $var;
	}
/*
	Resuelve la lógica para armar la consulta SQL en base al campo `estado`.
*/
	static function BuildEstadoCond($db, $estado, $field = 'estado', $tabla = NULL) {
		$salida = '';
		if (!empty($estado)) {
			if (!empty($tabla)) {
				$campo = SQLQuote($tabla).".".SQLQuote($field);
			} else {
				$campo = SQLQuote($field);
			}
			if (is_array($estado) and (count($estado)>0)) {
				foreach($estado as $k => $e) { $estado[$k] = $db->RealEscape($e); }
				$salida = $campo." IN ('".implode("','",$estado)."')";
			}
			if (is_string($estado)) {
				$salida = "UPPER(".$campo.") = UPPER('".$db->RealEscape($estado)."')";
			}
		}
		if (!empty($salida)) {
			$salida = "AND (".$salida.") ";
		}
		return $salida;
	}

	static function CheckField($type, $value, $field) {
		cCheckInput::$checkempty = false; // Que esté o no vacío ya se controló antes.
		switch($type) {
			case 'name': cCheckInput::NomApe($value,null,$field); break;
			case 'email': cCheckInput::Email($value,null,$field); break;
			case 'DNI': cCheckInput::DNI($value,null,$field); break;
			case 'CUIT': cCheckInput::CUIT($value,null,$field); break;
			case 'nro_doc': cCheckInput::nro_doc($value,null,$field); break;
		}
		if (CanUseArray(cCheckInput::$msgerr)) {
			throw new Exception(__LINE__." ".array_shift(cCheckInput::$msgerr));
		}
	} // CheckField

/*
	Con un string, generar un alias apto para URL.
*/
	static function GenerateAlias($titulo) {
$lista = array(
'/\ba\b/i',
'/\bal\b/i',
'/\baquel\b/i',
'/\baquella\b/i',
'/\bante\b/i',
'/\bbajo\b/i',
'/\bcabe\b/i',
'/\bcon\b/i',
'/\bcontra\b/i',
'/\bde\b/i',
'/\bdel\b/i',
'/\bdesde\b/i',
'/\bdurante\b/i',
'/\bel\b/i',
'/\ben\b/i',
'/\bentre\b/i',
'/\besa\b/i',
'/\bese\b/i',
'/\besta\b/i',
'/\beste\b/i',
'/\bhacia\b/i',
'/\bhasta\b/i',
'/\bla\b/i',
'/\blas\b/i',
'/\blo\b/i',
'/\blos\b/i',
'/\bmediante\b/i',
'/\bnos\b/i',
'/\bpara\b/i',
'/\bpor\b/i',
'/\bse\b/i',
'/\bsegun\b/i',
'/\bsin\b/i',
'/\bsu\b/i',
'/\bsus\b/i',
'/\bso\b/i',
'/\bsobre\b/i',
'/\btras\b/i',
'/\btu\b/i',
'/\btus\b/i',
'/\bun\b/i',
'/\buna\b/i',
'/\bunas\b/i',
'/\buno\b/i',
'/\bunos\b/i',
'/\bversus\b/i',
'/\bvia\b/i'
);
		$titulo = self::StripTildes($titulo);
		$titulo = mb_convert_encoding($titulo, 'ASCII'); 
		$titulo = self::StripPunctuation($titulo);
		$titulo = preg_replace($lista, "", $titulo);
		$titulo = preg_replace(array("/(-)\\1{1,}/is","/(\s)\\1{1,}/is","/(_)\\1{1,}/is"),array("-"," ","-"), $titulo); // Elimina las repeticiones de guiones y espacios del preg_replace anterior.
		$titulo = preg_replace("/\s+/", "-", $titulo);
		$titulo = str_replace(" ","", $titulo);
		$titulo = substr($titulo,0,255);
		while ($titulo[0] == '-') {
			$titulo = substr($titulo,1,strlen($titulo)-1);
		}
		while ($titulo[strlen($titulo)-1] == '-') {
			$titulo = substr($titulo,0,strlen($titulo)-1);
		}
		return $titulo;
	} // GenerateAlias

	static function StripTildes($str) {
		$reptildes=array('á','é','í','ó','ú','à','è','ì','ò','ù','ñ','ë','ü','_');
		$repplanas=array('a','e','i','o','u','a','e','i','o','u','n','e','u','-');
		$str = mb_strtolower($str);
		$str = str_replace($reptildes,$repplanas,FormatStrUTF8($str));
		return $str;
	}

	static function StripPunctuation($str) {
		$aux = null;
		if (mb_strlen($str) > 0) {
			for ($x=0; $x<mb_strlen($str); $x++) {
				if (ord($str[$x]) == 32) { // Space
					$aux .= $str[$x];
				} else {
					if (ord($str[$x]) < 124) { // No Ansi
						if ((ord($str[$x]) == 45) or (ord($str[$x]) >= 48) and (ord($str[$x]) <= 57)) { // Is a number
							$aux .= $str[$x];
						} else {
							if ((ord($str[$x]) >= 65) and (ord($str[$x]) <= 90)) { // Uppercases
								$aux .= $str[$x];
							} else {
								if ((ord($str[$x]) == 95) or (ord($str[$x]) >= 97)) { // Lowercases
									$aux .= $str[$x];
								}
							}
						}
					}
				}
			} // for
		}
		return $aux;
	}

/*
	Descompone las fechas enviadas por el plugin datetimepicker en las dos fechas límites, devolviéndolas en un array en formato ISO.
*/
	static function DescomponerRango($rango) {
		$result = null;
		$work = explode(' - ',$rango);
		if (count($work)>1) {
			$result = array('desde'=>null, 'hasta'=> null);
			$result['desde'] = substr(trim($work[0]),0,10);
			$result['hasta'] = substr(trim($work[1]),0,10);
			foreach ($result as $key => $value) {
				if (cFechas::LooksLikeDate($value)) {
					$result[$key] = cFechas::FechaToISO($value);
				}
			}
			if ($result['desde'] > $result['hasta']) {
				$aux = $result['desde'];
				$result['desde'] = $result['hasta'];
				$result['hasta'] = $aux;
			}
		}
		return $result;
	}
/*
	Traer un valor de la tabla de parámetros. O null en caso de no encontrarlo.
	$byid indica que en vez de buscar $nombre en el campo `nombre`, lo haga en el campo `id`.
*/
	static function GetParam($db, $nombre, $byid = false) {
		$result = null;
			try {
				$sql = "SELECT * FROM ".SQLQuote(TBL_parametros)." WHERE ";
				if ($byid) {
					if (!is_numeric($nombre)) { throw new Exception(__LINE__." Nombre debe ser un número."); }
					$sql .= "(`id` = ".$nombre.") ";
				} else {
					$sql .= "LOWER(`nombre`) = LOWER('".$db->RealEscape(mb_substr($nombre,0,64))."') ";
				}
				
				$sql .= "AND `estado` = 'HAB' LIMIT 1;";
				$db->Query($sql);
				if ($db->error) { throw new Exception(__LINE__." DBErr: ".$db->errmsg); }
				if ($fila = $db->First()) {
					$result = $fila['valor'];
					self::$reg_param = $fila;
				}
			} catch(Exception $e) {
				self::SetError(__METHOD__,$e->getMessage());
			}
		return $result;
	}
/*
	Poner un valor de la tabla de parámetros. Si ya existe, actualiza el valor.
	No olvides pasar el id del usuario actual.
*/
	static function SetParam($db, $nombre, $valor = null, $tipo='STRING', $usuario_id = null) {
		$result = false;
			try {
				$reg = array(
					'valor'=>$db->RealEscape(mb_substr($valor,0,64)),
					'usuario_id'=>$db->RealEscape(mb_substr($usuario_id,0,11)),
					'fechahora_modif'=>cFechas::Ahora()
				);
				if (!is_null(self::GetParam($db, $nombre))) {
					switch (self::$reg_param['tipo']) {
						case 'STRING': if (!is_string($valor)) { throw new Exception(__LINE__." Valor: '".$valor."' no es STRING."); } break;
						case 'INT': if (self::FilterInt($valor) === false) { throw new Exception(__LINE__." Valor: '".$valor."' no es INT."); } break;
						case 'FLOAT': if (self::FilterFloat($valor) === false) { throw new Exception(__LINE__." Valor: '".$valor."' no es FLOAT."); } break;
						case 'BOOL': if (!is_bool($valor)) { throw new Exception(__LINE__." Valor: '".$valor."' no es BOOL."); } else { $reg['valor'] = ($valor)?1:0; } break;
					}
					$db->Update(TBL_parametros, $reg, "LOWER(`nombre`) = LOWER('".$db->RealEscape(mb_substr($nombre,0,64))."')");
				} else {
					$reg['fechahora_alta']=cFechas::Ahora();
					$reg['nombre'] = $db->RealEscape(mb_substr($nombre,0,64));
					$reg['tipo'] = $tipo;
					$db->Insert(TBL_parametros, $reg);
				}
				$result = true;
			} catch(Exception $e) {
				self::SetError(__METHOD__,$e->getMessage());
			}
		return $result;
	}
/*
	Trae la lista de grupos de parámetros.
*/
	static function GetParamGrupos($db, $estado = null, $id_as_index = false) {
		$result = false;
		try {
			$sql = "SELECT * FROM ".SQLQuote(TBL_parametros_grupos)." WHERE 1 = 1 ";
			if (!is_null($estado)) {
				$sql .= self::BuildEstadoCond($db, $estado);
			}
			$sql .= "ORDER BY `nombre`;";
			$db->Query($sql);
			if ($db->error) { throw new Exception(__LINE__." DBErr: ".$db->errmsg); }
			if ($fila = $db->First()) {
				$result = array();
				do {
					if ($id_as_index) {
						$result[$fila['id']] = $fila;
					} else {
						$result[] = $fila;
					}
				} while($fila = $db->Next());
			}
		} catch(Exception $e) {
			self::SetError(__METHOD__,$e->getMessage());
		}
		return $result;
	}
/*
	Traer un grupo de los grupos de parámetros.
*/
	static function GetParamGrupo($db, $id) {
		$result = false;
			try {
				if (!is_numeric($id)) { throw new Exception("ID debe ser un número."); }
				$sql = "SELECT * FROM ".SQLQuote(TBL_parametros_grupos)." WHERE `id` = ".$id;
				$db->Query($sql);
				if ($db->error) { throw new Exception(__LINE__." DBErr: ".$db->errmsg); }
				if ($fila = $db->First()) {
					if (!empty($fila['data'])) {
						$fila['data'] = json_decode($fila['data']);
					}
					$result = $fila;
				}
			} catch(Exception $e) {
				self::SetError(__METHOD__,$e->getMessage());
			}
		return $result;
	}


} // class cSideKick
/*
		$result = null;
		try {
			$sql = "SELECT * FROM ".SQLQuote(TBL_)." WHERE 1=1 ";
			$db->Query($sql);
			if ($db->error) { throw new Exception(__LINE__." DBErr: ".$db->errmsg); }
			if ($fila = $db->First()) {
				$result = array();
				do {
					
				} while($fila = $db->Next());
			}
		} catch(Exception $e) {
			self::SetError(__METHOD__,$e->getMessage());
		}
		return $result;
*/



?>