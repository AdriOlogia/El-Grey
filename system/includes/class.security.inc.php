<?php
/*
	Class cSecurity
	Created: 2013-03-18
	Authors:
		Dario Martinelli
		Driverop
	Desc: Clase para validar todo tipo de datos.
	Version: 1.2
	
	Modif: 2018-10-23
	Desc: Eliminados métodos nunca usados.
	
	Modif: 2018-11-24
	Desc: Agregado NeutralizeDT().
	
	Modif: 2020-03-27
	Desc: Agregado método RemoveTrailingSlash().
		Ahora Demilitarized() permite que si se establece en la configuración un content precedido por un *, indica que todo archivo en ese content es de libre entrada.
*/

class cSecurity{

/*
	Esta función limpia una cadena para ser usada en la URL. Se usa para normalizar los parámetros virtuales de la URL en el index.php
*/
	static function StringToUrl($str, $replace=array("'"), $delimiter='-') {
		$str = trim($str);
		if( !empty($replace) ) {
			$str = str_replace((array)$replace, ' ', $str);
		}
		
		$preps = array("/(^|\b)al\s/im","/(^|\b)a\s/im","/(^|\b)ante\s/im","/(^|\b)bajo\s/im","/(^|\b)cabe\s/im","/(^|\b)con\s/im","/(^|\b)contra\s/im","/(^|\b)del\s/im","/(^|\b)de\s/im","/(^|\b)desde\s/im","/(^|\b)en\s/im","/(^|\b)el\s/im","/(^|\b)entre\s/im","/(^|\b)hacia\s/im","/(^|\b)hasta\s/im","/(^|\b)la\s/im","/(^|\b)las\s/im","/(^|\b)lo\s/im","/(^|\b)los\s/im","/(^|\b)para\s/im","/(^|\b)por\s/im","/(^|\b)sin\s/im","/(^|\b)so\s/im","/(^|\b)tras/im");

		$clean = iconv('UTF-8', 'ASCII//TRANSLIT', $str); // Se necesita la biblioteca mb_strings para usar iconv. Esto convierte $str de UTF-8 a ASCII plano para limpiar mejor la URL.

		
		
		$clean = preg_replace($preps, "-", $clean); // Se reemplazan todas las preposiciones.
		$clean = preg_replace("/[^a-zA-Z0-9\/_|+ -\.]/", '', $clean); // Todo lo que no sea letra o número y algunos caracteres seguros, se elimina.
		$clean = trim($clean, '-'); // Se eliminan los - que pudieron quedar delante y detrás de la cadena luego de la función anterior.
		//$clean = strtolower($clean); // Se convierte todo a minúscula.
		$clean = preg_replace("/[\/_|+ -]+/", $delimiter, $clean); // Separadores de directorios, barras, guiones no permitidos se convierten a -

	return $clean;
} // StringToUrl

/*
	El host de una URL es el mismo que el del servidor? 
	$nonstrict permite una comparación laxa.

*/
	static function IsAllowedHost($thishost, $nonstrict = false) {
		global $urlbase;
		if ($nonstrict == false) {
		return trim(parse_url($urlbase, PHP_URL_HOST)) == trim(parse_url($thishost, PHP_URL_HOST)); // PHP Ver. >= 5.1
		} else {
			$haystack = trim(parse_url($thishost, PHP_URL_HOST));
			$needle = trim(parse_url($urlbase, PHP_URL_HOST));
			if (strpos($haystack, $needle) === FALSE) { return false; }
			else { return true; }
		}
	} // IsAllowedHost


/*
   Sustituye caracteres especiales para evitar ataque por directoy traversal
   Quita los caracteres '\', '/' y los dos puntos seguidos '..'
 */
	static function SatinizePathParam($param) {
		if (preg_match("/%2F|%2E|%5C%5C|\.\.|\\\\|\//i",$param)) {
			$patrones = Array("/%2F/i","/%2E/i","/%5C%5C/i","/\.\./i","/\\\/i","/\//i");
			$param = preg_replace($patrones, "-", $param);
		}
		return $param;
	} // function SatinizePathParam
	
/*
	Elimina los caracteres que sirven como ataque por directory traversal, pero deja los separadores de directorio para poder ir más profundo en el árbol de directorios.
	Reemplaza el separador de directorios por el separador correcto para el sistema actual.
	Quita "../", "..\", ".\" y "./" incluyendo codificado como Unicode.
*/
	static function NeutralizeDT($param) {
		if (preg_match("/%5C%5C|%5C%5C%2E|\.\.\/|\.\.%2F|\.\.%5C|\.\/|\.\\\/i",$param)) {
			$patrones = Array("/%5C%5C/i","/%5C%5C%2E/i","/\.\.\//i","/\.\.%2F/i","/\.\.%5C/i","/\.\//i","/\.\\\/i");
			$param = preg_replace($patrones, "", $param);
		}
		$param = str_replace(array('\\','/'),DIRECTORY_SEPARATOR,$param);
		return $param;
} // NeutralizeDT

/*
	Limpia la variable $var de entidades HTML.
	$utf interpreta $var con UTF-8
	Si $utf8 es == true, $var es UTF-8
*/
	static function ClearVar($var, $utf8 = false) {
		// Quita HTML y PHP tags
		$var = ($utf8)?htmlspecialchars($var, ENT_QUOTES):htmlspecialchars($var, ENT_QUOTES, "UTF-8");
		// Quita las barras
		$var = (get_magic_quotes_gpc())?stripslashes($var):$var;
		return trim($var);
	} // function ClearVar

/*
	Genera una contraseña aleatoria a partir del archivo 'reservoreo.txt'.
*/
	static function GenerateRandomPassword() {
	$numeros = array('0','1','2','3','4','5','6','7','8','9');

	$txt = file_get_contents(DIR_includes."reservoreo.txt");

	$a = explode(" ",$txt);

	$numero = "";
	$h = rand(1,4);
	for ($i=0;$i<$h;$i++) {
		$numero .= $numeros[rand(0,count($numeros)-1)];
	}

	return $a[rand(0,count($a)-1)].$numero;
}

/*
	Determina si un contenido AJAX está desmilitarizado, es decir, no se requiere que el usuario esté logeado para poder usarlo
*/
	static function Demilitarized($content, $archivo) {
		$result_content = false;
		$result_archivo = false;
		$content = self::RemoveTrailingSlash($content);
		
		$dmz_contents = str_replace(array(";","|"),",",DMZ_CONTENTS);
		$dmz_archivos = str_replace(array(";","|"),",",DMZ_ARCHIVOS);
		
		$dmz_contents = explode(',',$dmz_contents);
		$dmz_archivos = explode(',',$dmz_archivos);

		if (!empty($content)) {
			if (!empty($dmz_contents)) {
				if (in_array($content,$dmz_contents)) { $result_content = true; }
				else {
					if (in_array('*'.$content,$dmz_contents)) { return true; } // Si el contenido está abierto, dejar pasar todo.
				}
			}
		}
		if (!empty($archivo)) {
			if (!empty($archivo)) {
				if (in_array($archivo,$dmz_archivos)) { $result_archivo = true; }
			}
		}
		return ($result_content || $result_archivo);
	}
/*
	Elimina el separador de URI al final de la cadena.
*/
	static function RemoveTrailingSlash($str) {
		if (!empty($str)) {
			if (in_array(substr($str,-1), array('\\','/'))) {
				$str = substr($str,0,strlen($str)-1);
			}
		}
		return $str;
	} // RemoveTrailingURISlash
/***********************************************************************
							FIN DE LA CLASE
***********************************************************************/
} // class cSecurity


?>