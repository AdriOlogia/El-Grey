<?php
/*
	Bibliteca de funciones comunes.
	Todas las funciones que no encajan en ninguna otra biblioteca o que son necesitadas en otras partes.
*/

$reptildes=array(' ','á','é','í','ó','ú','à','è','ì','ò','ù','ñ','ë');
$repplanas=array('','a','e','i','o','u','a','e','i','o','u','n','e');

/* Muestra una variable para debug */
function ShowVar($var, $type = false) {
	echo "<pre>";
	if ($type) { var_dump($var); }
	else { print_r($var); }
	echo "</pre>";
} // ShowVar

/**/
function EchoLog($msg) {
	echo $msg.FDL;
}
function EchoLogP($msg) {
	echo "<p>".nl2br($msg)."</p>";
}

/* Imprime la estructura JSON como mensaje para JavaScript. Errores. */
function EmitJSON($msg, $generr = true, $hidden = false) {
	if ($hidden) { echo '<!-- '; }
	echo '<json>{';
	if (!empty($msg) and is_array($msg) and (count($msg)>0)) {
		echo '"dataerr":'.json_encode($msg);
	} else {
		if ($generr) {
			echo '"generr":"'.$msg.'"';
		} else {
			echo '"goodmsg":"'.$msg.'"';
		}
	}
	echo '}</json>';
	if ($hidden) { echo ' -->'; }
}
/*	Imprime mensaje JSON para JavaScript. Todo bien */
function ResponseOk($msg = null, $hidden = false) {
	if ($hidden) { echo '<!-- '; }
	echo '<json>{';
	if (empty($msg)) {
		echo '"ok":"ok"';
	} else {
		if (is_array($msg)) {
			$s = array('"ok":"ok"');
			foreach($msg as $key => $value) {
				$s[] = '"'.addslashes($key).'":"'.addslashes($value).'"';
			}
			echo implode(',',$s);
		} else {
			echo '"ok":"'.$msg.'"';
		}
	}
	echo ',"time":"'.Date('Y-m-d H:i:s').'"';
	echo '}</json>';
	if ($hidden) { echo ' -->'; }
}

function MakeJSON($arr = array()) {
	$salida = '<json>'.json_encode($arr, JSON_FORCE_OBJECT).'</json>';
	return $salida;
	
}
/*
	Se asegura que el último caracter de la cadena es un separador de directorio. Solo si la cadena no está vacía.
*/
function EnsureTrailingSlash($str) {
	if (!empty($str)) {
		if (substr($str,-1) != DIRECTORY_SEPARATOR) {
			$str .= DIRECTORY_SEPARATOR;
		}
	}
	return $str;
}
/*
	Elimina el separador de directorio del final de la cadena si lo tiene.
*/
function RemoveTrailingSlash($str) {
	if (!empty($str)) {
		if (substr($str,-1) == DIRECTORY_SEPARATOR) {
			$str = substr($str,0,strlen($str)-1);
		}
	}
	return $str;
}

/*
	Se asegura que el primer caracter de la cadena es un separador de directorio. Solo si la cadena no está vacía.
*/
function EnsureLeadingSlash($str) {
	if (!empty($str)) {
		if (substr($str,0,1) != DIRECTORY_SEPARATOR) {
			$str = DIRECTORY_SEPARATOR.$str;
		}
	}
	return $str;
}
/*
	Elimina el separador de directorio del inicio de la cadena si lo tiene.
*/
function RemoveLeadingSlash($sql) {
	if (!empty($str)) {
		if (substr($str,0,1) == DIRECTORY_SEPARATOR) {
			$str = substr($str,1,strlen($str));
		}
	}
	return $str;
}
/*
	Se asegura que el último caracter de la cadena es un separador de URI. Solo si la cadena no está vacía.
*/
function EnsureTrailingURISlash($str) {
	if (!empty($str)) {
		if (substr($str,-1) != '/') {
			$str .= '/';
		}
	}
	return $str;
}
/*
	Elimina el separador de URI al final de la cadena.
*/
function RemoveTrailingURISlash($str) {
	if (!empty($str)) {
		if (substr($str,-1) == '/') {
			$str = substr($str,0,strlen($str)-1);
		}
	}
	return $str;
}
/*
	Dada una ruta de directorios, cambia el separador por el correcto en el SO actual.
*/
function DirSlash($str) {
	return str_replace(array('\\','/'),DS,$str);
}
/*
	Determina si un archivo existe, es un archivo y es legible.
*/
function ExisteArchivo($file) {
	return (file_exists($file) and is_file($file) and is_readable($file));
}

/*
	Verifica si una cadena es un número entero.
*/
function CheckInt($int){
	if(is_numeric($int) === TRUE){
		if((int)$int == $int){
			return TRUE;
		} else {
			return FALSE;
		}
	}
	else {
		return FALSE;
	}
}
/*
	Verifica que $var es un número entero, en caso de no serlo, devuelve el valor $default
*/
function SecureInt($var,$default = NULL) {
	if (!CheckInt($var)) { $var = $default; }
	return $var;
}
/*
	Fltra un array dejando pasar solo los elementos que son enteros
*/
function SecureIntArray($array,$default = NULL) {
	$result = array();
	if (CanUseArray($array)) {
		foreach($array as $var) {
			if (CheckInt($var)) { $result[] = $var; }
		}
	}
	return $result;
}
/*
	Verifica si una cadena es un número real.
*/
function CheckFloat($float) {
	if(is_numeric($float) === TRUE){
		$float = (float)$float;
		if(is_float($float)){
			return TRUE;
		} else {
			return FALSE;
		}
	} else {
		return FALSE;
	}
}
/*
	Verifica que $var es un número real, en caso de no serlo, devuelve el valor $default
*/
function SecureFloat($var,$default = NULL) {
	if (!CheckFloat($var)) { $var = $default; }
	return $var;
}
/*
	Muestra una salida, pero como si fuera un comentario. Apto para incrustar en JavaScript o CSS.
*/
function ShowOutput($str, $debug = false) {
	echo "\r\n/*\r\n";
	if ($debug) {
		ShowVar($str);
	} else {
		echo $str;
	}
	echo "\r\n*/\r\n";
}
/*
	Determina si un array no está vacío, es realmente un array y contiene al menos un elemento.
*/
function CanUseArray($array) {
	return ((!empty($array)) and (is_array($array)) and (count($array)>0));
}
/*
	Agrega apostrofes alrededor de las cadenas. Para ser usada en el armado de sentencias SQL.
*/
function SQLQuote($cad,$pre = null) {
	$result = '';
	if ($pre != null) {
		$result = "`".$pre."`.`".$cad."`";
	} else {
		$result = "`".$cad."`";
	}
	return $result;
}
/*
	Muestra el mensaje de error al parsear un JSON.
*/
function ShowLastJSONError($num, $silent = false) {
	$msg = '';
	if ($num != 0) {
		switch ($num) {
			case JSON_ERROR_DEPTH:
				$msg = 'Se excedió la profundidad máxima de la pila.';
			break;
			case JSON_ERROR_STATE_MISMATCH:
				$msg = 'Subdesbordamiento o el modo no coincide.';
			break;
			case JSON_ERROR_CTRL_CHAR:
				$msg = 'Se encontró un caracter de control inesperado.';
			break;
			case JSON_ERROR_SYNTAX:
				$msg = 'Error de sintaxis. JSON mal formado.';
			break;
			case JSON_ERROR_UTF8:
				$msg = 'Caracter UTF-8 mal formado, posiblemente mal codificado.';
			break;
			default:
				$msg = 'Error desconocido.';
			break;
		}
		if (!$silent) {
			EchoLog($msg);
		}
	}
	return $msg;
}

function ValidIP($ip) {
	if (!empty($ip) && ip2long($ip)!=-1) {
		$reserved_ips = array (
			array('0.0.0.0','2.255.255.255'),
			array('10.0.0.0','10.255.255.255'),
			array('127.0.0.0','127.255.255.255'),
			array('169.254.0.0','169.254.255.255'),
			array('172.16.0.0','172.31.255.255'),
			array('192.0.2.0','192.0.2.255'),
			array('192.168.0.0','192.168.255.255'),
			array('255.255.255.0','255.255.255.255')
		);
		foreach ($reserved_ips as $r) {
			$min = ip2long($r[0]);
			$max = ip2long($r[1]);
			if ((ip2long($ip) >= $min) and (ip2long($ip) <= $max)) { return false; }
		}
		return true;
	} else { return false; }
}

function GetIP() {
	$result = "0.0.0.0";
	if (Validip(@$_SERVER["HTTP_CLIENT_IP"])) {
		$result = $_SERVER["HTTP_CLIENT_IP"];
	}
	if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
		foreach (explode(",",$_SERVER["HTTP_X_FORWARDED_FOR"]) as $ip) {
			if (ValidIP(trim($ip))) { $result = $ip; break; }
		}
	}
	if (!ValidIP($result)) {
		if (ValidIP(@$_SERVER["HTTP_X_FORWARDED"])) { $result =  $_SERVER["HTTP_X_FORWARDED"]; }
		else {
			if (ValidIP(@$_SERVER["HTTP_FORWARDED_FOR"])) { $result = $_SERVER["HTTP_FORWARDED_FOR"]; } 
			else {
				if (ValidIP(@$_SERVER["HTTP_FORWARDED"])) { $result = $_SERVER["HTTP_FORWARDED"]; } 
				else { 
					if (ValidIP(@$_SERVER["HTTP_X_FORWARDED"])) { $result = $_SERVER["HTTP_X_FORWARDED"]; }
					else { $result = $_SERVER["REMOTE_ADDR"]; }
				}
			}
		}
	}
	return $result;
} // GetIP


function AplanarStr($str) {
	global $reptildes;
	global $repplanas;
	$str = str_replace($reptildes,$repplanas,FormatStrUTF8($str));
	return $str;
}

function SameStr($str1,$str2) {
	return (AplanarStr($str1) == AplanarStr($str2));
}
function mb_ucfirst($string) {
	mb_internal_encoding('UTF-8');
	$string = mb_strtoupper(mb_substr($string, 0, 1)) . mb_substr($string, 1);
	return $string;
}

/*
	Formatea una cadena en UTF8.
	$string: la cadena UTF8.
	$option:
	  = 0 o null: convertir a minúsculas.
	  = 1 o true: convertir a minúsculas, luego poner el primer caracter en mayúscula.
	  = 2: convertir a minúsculas, luego poner el primer caracter de todas las palabras a mayúsculas.
*/
function FormatStrUTF8($string, $option = null) {
	$string = mb_convert_case($string,MB_CASE_LOWER,"UTF-8");
	if ($option != null) {
		if ($option === 2) {
			$string = mb_convert_case($string,MB_CASE_TITLE,"UTF-8");
		} else {
			if (($option == 1) or ($option == true)) {
				$string = mb_ucfirst($string);
			}
		}
	}
	return $string;
}
/*
	Formatea una cadena en UTF8 con excepciones.
*/
function titleCase($string, $delimiters = array(" ", "-", ".", "'", "O'", "Mc", "Mac"), $exceptions = array("de", "del", "al", "con", "y", "SRL", "SA", "I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX", "X", "XI", "XII", "XIII", "XIV", "XV", "XVI", "XVII", "XVIII", "XIX", "XX", "XXI", "XXII", "XXIII", "XXIV", "XXV", "XXVI", "XXVII", "XXVIII", "XXIX", "XXX" )) {
/*
 Excepciones en minúscula son las palabras que no se quieren convertir.
 Excepciones en mayúsuclas son cualquier palabra que no se quieren convertir a título
 pero deberían convertirse a mayúscula, por ejemplo:
   king henry viii o king henry Viii debería ser King Henry VIII
*/
	$string = mb_convert_case($string, MB_CASE_TITLE, "UTF-8");
	
	foreach ($delimiters as $dlnr => $delimiter){
			$words = explode($delimiter, $string);
			$newwords = array();
			foreach ($words as $wordnr => $word){
			
					if (in_array(mb_strtoupper($word, "UTF-8"), $exceptions)){
							// check exceptions list for any words that should be in upper case
							$word = mb_strtoupper($word, "UTF-8");
					}
					elseif (in_array(mb_strtolower($word, "UTF-8"), $exceptions)){
							// check exceptions list for any words that should be in upper case
							$word = mb_strtolower($word, "UTF-8");
					}
					
					elseif (!in_array($word, $exceptions) ){
							// convert to uppercase (non-utf8 only)
						
							//$word = ucfirst($word);
							$word = mb_strtoupper(mb_substr($word, 0, 1)) . mb_substr($word, 1);
							
					}
					array_push($newwords, $word);
			}
			$string = join($delimiter, $newwords);
	}//foreach
	return $string;
 }

/*
	Toma un párrafo y pone mayúsculas después de un punto y aparte.
*/
	function SetCase($texto, &$i) { // Se usa en la siguiente función.
		$arr = array(".",":"," ","\r","\n","\t");
		$fin = false;
		$char = mb_substr($texto,$i,1);
		$x = $i;
		$strlen = mb_strlen($texto);
		while (!$fin and ($x < $strlen)) {
			if (!in_array($char,$arr)) {
				$fin = true;
				$texto = mb_substr($texto,0,$x).mb_strtoupper($char).mb_substr($texto,$x+1);
				$i = $x;
			} else {
				$x++;
				$char = mb_substr($texto,$x,1);
			}
		}
		return $texto;
	} // SetCase
	
function ArreglarMayusculas($texto) {
	mb_internal_encoding('UTF-8');
	$texto = mb_strtoupper(mb_substr($texto, 0, 1)) . mb_substr($texto, 1);
	$strlen = mb_strlen($texto);
	$arr = array(".",":","\r","\n");
	for($i = 0; $i<$strlen;$i++) {
		$char = mb_substr($texto,$i,1);
		if (in_array($char,$arr)) {
			$texto = SetCase($texto,$i);
		}
	}
	return $texto;
}

/*
	Funciones Varias de validación.
*/
function CheckTelFijo(&$datos, &$msgerr) {
	$datos['tel_part'] = NULL;
	if (empty($datos['pretel1']) and !empty($datos['numtel1'])) {
		$msgerr['pretel1'] = 'Debes indicar el<br />prefijo de área.';
	} else {
		if (!empty($datos['pretel1']) and empty($datos['numtel1'])) {
			$msgerr['numtel1'] = 'Debes indicar el<br />número de abonado.';
		} else {
			if (!empty($datos['pretel1']) and !empty($datos['numtel1'])) {
				$datos['tel_part'] = $datos['pretel1'].'-'.$datos['numtel1'];
			}
		}
	}
	unset($datos['pretel1']);
	unset($datos['numtel1']);
}

function CheckTelCel(&$datos, &$msgerr) {
	$datos['tel_movil'] = NULL;
	if (empty($datos['pretel2']) and !empty($datos['numtel2'])) {
		$msgerr['pretel2'] = 'Debes indicar el<br />prefijo de área.';
	} else {
		if (!empty($datos['pretel2']) and empty($datos['numtel2'])) {
			$msgerr['numtel2'] = 'Debes indicar el<br />número de abonado.';
		} else {
			if (!empty($datos['pretel2']) and !empty($datos['numtel2'])) {
				$datos['tel_movil'] = (substr($datos['pretel2'],0,1) == '0')?'':'0';
				$datos['tel_movil'] .= $datos['pretel2'];
				$datos['tel_movil'] .= '-15'.$datos['numtel2'];
			}
		}
	}
	unset($datos['pretel2']);
	unset($datos['numtel2']);
}

function ParseTel($tel) {
	$result = NULL;
	if (!empty($tel)) {
		$c = mb_stripos($tel,'-');
		if ($c > 0) {
			$result = array(
				'raw'=>$tel,
				'prefijo'=>mb_substr($tel,0,$c),
				'abonado'=>mb_substr($tel,$c+1,mb_strlen($tel))
			);
		} else {
			$result = array(
				'raw'=>$tel,
				'prefijo'=>null,
				'abonado'=>null
			);
		}
		foreach ($result as $key => $value) {
			$result[$key] = trim($value);
		}
	}
	return $result;
}

function DeterminarSexo($nombre) {
	$result = 'N';
	$aux = explode(" ",$nombre);
	$nombre = AplanarStr($aux[0]);
	$aux = file_get_contents(DIR_includes."nombresvaron.txt");
	$varones = explode("/",$aux);
	if (in_array($nombre,$varones)) {
		$result = "M";
	} else {
		$aux = file_get_contents(DIR_includes."nombresmujer.txt");
		$mujeres = explode("/",$aux);
		if (in_array($nombre,$mujeres)) {
			$result = "F";
		}
	}
	return $result;
} // DeterminarSexo


function FileUploadErrorMsg($error_code) {
    switch ($error_code) { 
        case UPLOAD_ERR_INI_SIZE: 
            return $error_code." El archivo es m&aacute;s grande que lo permitido por el Servidor."; 
        case UPLOAD_ERR_FORM_SIZE: 
            return $error_code." El archivo subido es demasiado grande."; 
        case UPLOAD_ERR_PARTIAL: 
            return $error_code." El archivo subido no se termin&oacute; de cargar (probablemente cancelado por el usuario)."; 
        case UPLOAD_ERR_NO_FILE: 
            return $error_code." No se subi&oacute; ning&uacute;n archivo"; 
        case UPLOAD_ERR_NO_TMP_DIR: 
            return $error_code." Error del servidor: Falta el directorio temporal."; 
        case UPLOAD_ERR_CANT_WRITE: 
            return $error_code." Error del servidor: Error de escritura en disco"; 
        case UPLOAD_ERR_EXTENSION: 
            return $error_code." Error del servidor: Subida detenida por la extenci&oacute;n";
		default: 
            return "Error del servidor: ".$error_code; 
    } 
}
/*
    Dado un nombre de archivo, devuelve su extensión
*/
function ExtraerExtension($archivo) {
	$aux = pathinfo($archivo);
	if (isset($aux['extension'])) {
		return $aux['extension'];
	} else {
		return "";
	}
}
function ConvertSize($tamano) {
	$result = "";
	if ($tamano < 1024) {
		$result = $tamano." bytes";
	} else {
		if (($tamano >= 1024) and ($tamano < 1048576)) {
			$result = round(($tamano / 1024),2)." KB.";
		} else {
			if (($tamano >= 1048576) and ($tamano < 1073741824)) {
				$result = round(($tamano / 1048576),2)." MB.";
			} else {
				if ($tamano >= 1073741824) {
					$result = round(($tamano / 1073741824),2)." GB.";
				}	
			}
		}
	}
	return $result;
}
/*
	Corta $cad por el espacio en blanco siguente a la posición $len y agrega $elipse al final.
	Pero si $strict es true, entonces pone el elipse exactamente en la posición $len y elimina el resto.
*/
function CortarElipse($cad, $len, $elipse = "&hellip;", $strict = false) {
	$result = $cad;
	if (mb_strlen($cad) > $len) {
		if (!$strict) {
			$posSpace = mb_strpos($cad," ",$len);
			if (is_bool($posSpace) and ($posSpace == false)) {
				$posSpace = mb_strlen($cad);
			}
			$posHyphen = mb_strpos($cad,"-",$len);
			if (!is_bool($posHyphen) and ($posHyphen < $posSpace)) {
				$posSpace = $posHyphen;
			}
			if ($posSpace > 0) {
				$result = mb_substr($cad,0,$posSpace).$elipse;
			} else { $result = $cad; }
		} else {
			$result = mb_substr($cad,0,($len-1)).$elipse;
		}
	}
	return $result;
}

function GetTrueImageSize($path) {
    $mime = getimagesize($path);

    if($mime['mime']=='image/png') { 
        $src_img = imagecreatefrompng($path);
    }
    if($mime['mime']=='image/jpg' || $mime['mime']=='image/jpeg' || $mime['mime']=='image/pjpeg') {
        $src_img = imagecreatefromjpeg($path);
    }   
    if($mime['mime']=='image/gif') { 
        $src_img = imagecreatefromgif($path);
    }
    if($mime['mime']=='image/webp') { 
        $src_img = imagecreatefromwebp($path);
    }
	return array(
		'width' => imageSX($src_img),
		'height'  => imageSY($src_img)
	);
} // GetTrueImageSize
/*
	Arma el navegador de páginas con elipses.
	$ItemsTotales: cuántos registros hay en la consulta.
	$PaginaActual: el índice de la página actual.
	$ItemsPorPagina: cuántos items hay que mostrar en cada página.
	$Rango: cuántas páginas mostrar alrededor de la actual (debe ser un número impar)
	$CantExtremos: cuántas páginas mostrar al inicio y al final.
	
	Devuelve un array donde cada elemento es:
	array(
		"p"=>Número de página o bién null cuando se trata de un elipse.
		"act"=>true cuando es la página actual, false para las otras.
	)
	
*/
function Paginar($ItemsTotales, $PaginaActual=1, $ItemsPorPagina=9, $Rango=5, $CantExtremos=5) {
	$result = array();
	
	
	// $MedioRango = ($Rango-1)/2;
	$MedioRango = $Rango;
    $TotalDePagina = ceil($ItemsTotales / $ItemsPorPagina);
    $ContPuntos = false;
    for ($p = 1; $p <= $TotalDePagina; $p++) {
        // do we display a link for this page or not?
        if (
			($p <= $CantExtremos) or
            ($p > ($TotalDePagina - $CantExtremos)) or
            (($p >= $PaginaActual - $MedioRango) and ($p <= $PaginaActual + $MedioRango)) or
            ($p == $CantExtremos + 1 and $p == $PaginaActual - $MedioRango - 1) or
            ($p == $TotalDePagina - $CantExtremos and $p == $PaginaActual + $MedioRango + 1 )
			)
			{
            $ContPuntos = false;
            if ($p == $PaginaActual) {
					$result[] = array("p"=>$p, "act"=>true);
            } else {
				$result[] = array("p"=>$p, "act"=>false);
			}
			echo "\n";
        // if not, have we already shown the elipses? 
        } elseif ($ContPuntos == false) { 
			$result[] = array("p"=>null, "arc"=>false);
            $ContPuntos=true; // make sure we only show it once
        }
    }
	return $result;
} 

/*
	Eliminar items vacíos de un array.
*/

function CleanArray(&$arr) {
	$result = null;
	foreach($arr as $key => $value) {
		// $key = strtolower($key); // No!. Hay scripts que sí les importa que la clave tenga mayúsculas.
		if (is_array($value)) {
			$result[$key] = CleanArray($value);
		} else {
			$result[$key] = trim($value);
		}
	}
	return $result;
}

function FormatPrecio($precio) {
	if (empty($precio) or is_null($precio)) { $precio = '0'; }
	return "$".number_format($precio, 2, ',', '.');
}

/*
	Reemplaza el caracter pleca por el tag <br /> forzando un salto de línea.
*/
function ParsePleca($texto) {
	return str_replace('|','<br />',$texto);
}

/*
	Ecscribe una entrada en el registro de logs y además emite un mensaje por pantalla cuando el sitio está en modo developer.
*/
function WriteLog($msg) {
	cLogging::Write($msg);
	if (DEVELOPE) { EchoLogP($msg); }
}

/*
	Fltra un array dejando pasar solo los elementos varchar que cumplen el criterio
*/
function SecureCharArray($array, $len = 11, $empty = false) {
	$result = array();
	if (CanUseArray($array)) {
		foreach($array as $var) {
			$var = trim($var);
			$pass = (mb_strlen($var) <= $len);
			if (!$empty) {
				$pass = (!empty($var)) and $pass;
			}
			if ($pass) { $result[] = $var; }
		}
	}
	return $result;
}
/*
	Funciones para satinizar valores de un array.
*/
function RealEscapeValues($ele, $key, $db) {
	$ele = $db->RealEscape($ele);
}
function RealEscapeKeys($key, $ele, $db) {
	$key = $db->RealEscape($key);
}
function ToUppercase($key, $ele) {
	mb_internal_encoding('UTF-8');
	$ele = mb_strtoupper($ele);
}
function RealEscapeArray($db, $arr) {
	foreach ($arr as $key => $value) {
		$arr[$key] = $db->RealEscape($value);
	}
	return $arr;
}

/*
	Ordena el array $arr según el campo $campo. ¡No controla que $campo sea un campo de $arr!.
	Pasale el inicio de la ordenación y el final. O sea, cero y el largo del array.
*/
function ASortByField($arr, $campo, $inicio = 0, $final = 0) {
	if($inicio >= $final){
		return $arr;
	}
	$i = $inicio;
	$d = $final;
	if ($inicio != $final) {
		$piv=0;
		$aux=0;
		$piv = $inicio;
		
		while ($inicio!=$final) {
			while (($arr[$final][$campo] >= $arr[$piv][$campo]) && ($inicio<$final)) {
				$final--;
			}
			while (($arr[$inicio][$campo] < $arr[$piv][$campo]) && ($inicio<$final)) {
				$inicio++;
			}
			if ($final!=$inicio) {
				$aux = $arr[$final];
				$arr[$final] = $arr[$inicio];
				$arr[$inicio] = $aux; 
			} else {
				ASortByField($arr, $campo, $i, $inicio -1);
				ASortByField($arr, $campo, $inicio+1, $d);
			}
		}
	}
	return $arr;
}

/*
	Pone la ordenación en la cabecera de las columnas de los listados.
	Debe existir un array llamado $campos_orden y otro llamado $ordenes;
	¡Ojo con el orden de los parámetros!
*/
	function Ordenar($idx, $title = null, $content = null, $archivo = null) {
		global $camposorden;
		global $ordenes;
		if (isset($camposorden[$idx])) {
			echo sprintf("onClick=\"Ordenar(this, '%s', '%s'%s)\" ",$camposorden[$idx],$content,(($archivo != null)?",'".$archivo."'":null));
			
			echo 'class="pointer';
			if (isset($ordenes[$camposorden[$idx]])) {
				echo ' '.strtolower($ordenes[$camposorden[$idx]]);
			}
			echo '" ';
		}
		echo 'title="';
		if (!empty($title)) {
			echo addslashes($title)." - ";
		}
		echo 'Clic para ordenar" ';
		
	}

function F($value) {
	return number_format($value,2,',','.');
}

/*
	La cadena ingresada $str se limpia para dejar solamente los caracteres que son válidos para un número de tipo float.
*/
function CleanFloat($str) {
	$result = null;
	$validos = array('0','1','2','3','4','5','6','7','8','9','+','-','.');
	$str = preg_replace("/,/",".",$str);
	for($i=0;$i<strlen($str);$i++) {
		if (in_array($str[$i], $validos)) {
			$result .= $str[$i];
		}
	}
	if (is_numeric($result)) {
		$result = $result+0;
		
	}
	return $result;
}

/*
	Redondea hacia arriba un flotante hasta el número especificado de decimales.
	Como ceil() pero permite indicar cuál es la posición de rendondeo.
*/
function RoundUp($value, $places=0) {
  if ($places < 0) { $places = 0; }
  $mult = pow(10, $places);
  return ceil($value * $mult) / $mult;
}

/*
	Redondea un flotante alejándose de cero hasta el número especificado de decimales.
*/
function RoundOut($value, $places=0) {
  if ($places < 0) { $places = 0; }
  $mult = pow(10, $places);
  return ($value >= 0 ? ceil($value * $mult):floor($value * $mult)) / $mult;
}

/*
	Mostrar un monto con separador de miles pero además si tiene decimales, mostrar solo $dec lugares, pero solo si los decimales son distintos de cero.
*/
function Format_Precio($valor, $dec = 0, $simbolo = '$') {
	$result = null;
	if (!is_numeric($valor)) { return null; }
	$valor = number_format($valor, $dec, ',', '.');
	$aux = explode(',',$valor);
	if (count($aux)>1) {
		$decimales = $aux[1];
		$ceros = str_repeat('0',$dec);
		if ($decimales == $ceros) {
			$result = $simbolo.$aux[0];
		} else {
			$result = $simbolo.implode(',',$aux);
		}
	} else {
		$result = $simbolo.$valor;
	}
	return $result;
}

/*
	Calcula un porcentaje pero no salta error cuando el divisor es cero.
*/
function CalcPorc($dividendo, $divisor) {
	if (empty($dividendo) or !is_numeric($dividendo)) { $dividendo = 0; }
	if (empty($divisor) or !is_numeric($divisor)) { $divisor = 0; }
	
	if ($dividendo == 0) { return 0; }
	return (100/$dividendo*$divisor);
}
?>