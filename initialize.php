<?php
/* La hora y fecha es la de Argentina. */
date_default_timezone_set('America/Argentina/Buenos_Aires');

/* Acortar el nombre de la constante separador de directorio del sistema operativo. */
define("DS", DIRECTORY_SEPARATOR);

/* Asegurar que PHP trabaje en UTF-8. */
mb_internal_encoding('UTF-8');
mb_regex_encoding('UTF-8');

/* Estirar el tiempo de vida de las cookies y de la sesión PHP a una hora */
$maxlifetime = ini_get('session.gc_maxlifetime');
if ($maxlifetime < '3600') {
	ini_set('session.gc_maxlifetime', '3600');
}
$maxlifetime = ini_get('session.cookie_lifetime');
if ($maxlifetime < '3600') {
	ini_set("session.cookie_lifetime", "3600");
}

/* Estas constantes no existían en versiones viejas de PHP */
if (!defined('JSON_PRETTY_PRINT')) {
	define('JSON_PRETTY_PRINT', 0);
}
if (!defined('JSON_UNESCAPED_UNICODE')) {
	define('JSON_UNESCAPED_UNICODE', 0);
}

/* Normalizar el Fin De Línea 'FDL' */
$FDL = (isset($_SERVER['SERVER_NAME'])) ? '<br />' . PHP_EOL : PHP_EOL;
define("FDL", $FDL);



// Path al directorio donde está el sitio web en el sistema de archivo.
if (phpversion() >= '5.3.0') {
	define("DIR_BASE", __DIR__ . DS);
} else {
	define("DIR_BASE", dirname(__FILE__) . DS);
}

// Directorio virtual al que fue llamado el sitio.
$base = dirname($_SERVER["SCRIPT_NAME"]);
$base = str_replace('\\', '/', trim($base));


/*
 Determina si se está en la raiz virtual del servidor web o en uno de sus directorios.
 En cualquier caso, se usa esa información para establecer la raiz desde la cual llamar al resto de los contenidos.
*/
$intercec = array_intersect(explode("/", str_replace('\\', '/', trim(DIR_BASE))), explode("/", $base)); // ¿Qué tienen en común DIR_BASE y $base?
$base = '/';
foreach ($intercec as $value) { // Volver a intercalar la / en cada uno de los directorios para formar así la URL.
	if (!empty($value)) {
		$base .= $value . '/';
	}
}

if (isset($base[0]) and ($base[0] != "/")) {
	$base = "/" . $base;
} // Asegurarse que $base tenga la primera / para no tener problemas después.

$scheme = (isHttps()) ? "https" : "http"; // ¿El servidor puede gestionar una conexión segura?, ¿el contenido se pidió de tal forma?.

/* 		La URL base del sitio	 */
define('BASE_URL', $scheme . '://' . @$_SERVER['HTTP_HOST'] . $base);  // Establecer cuál es la URL base del sitio.

/* Establecer los directorios de trabajo del sitio */
require_once(DIR_BASE . "directories.php");


/*
	Determina si la petición HTTP se hizo en una conexión segura. Incluso si fue a través de un PROXY.
*/
function isHttps()
{
	$result = false;
	if (array_key_exists("HTTPS", $_SERVER) and (strtolower($_SERVER["HTTPS"]) === 'on')) {
		$result = true;
	}
	if (array_key_exists("SERVER_PORT", $_SERVER) and ((int) $_SERVER["SERVER_PORT"] === 443)) {
		$result = true;
	}
	if (array_key_exists("HTTP_X_FORWARDED_SSL", $_SERVER) and (strtolower($_SERVER["HTTP_X_FORWARDED_SSL"]) === 'on')) {
		$result = true;
	}
	if (array_key_exists("HTTP_X_FORWARDED_PROTO", $_SERVER) and (strtolower($_SERVER["HTTP_X_FORWARDED_PROTO"]) === 'https')) {
		$result = true;
	}
	return $result;
}
