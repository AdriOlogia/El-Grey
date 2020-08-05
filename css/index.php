<?php
/*
	Author: DriverOp.
	Created: 2018-11-23

	Esto es el controller para cargar los CSS según el alias del contenido.
	Cuando el alias es "f" significa que se está cargando un archivo suelto y no los CSS de un contenido.
*/
	if (!isset($asset)) { // Si $asset no está creada, quiere decir que se intenta acceder a este archivo directamente. Hay que fallar.
		$this_file = __FILE__;
		include("404.htm");
		exit;
	}
	
	$this_file = substr(__FILE__,strlen(DIR_BASE))." ";

$alias = (!empty($handler[0]))?$handler[0]:DEFAULT_CONTENT;

if (strtolower($alias) == 'f') {
	if (count($handler) > 1) {
		array_shift($handler);
		LoadFiles($handler);
		exit;
	}
}
echo '/*';
$objeto_contenido->GetContent($handler);
echo '*/';

// Esto es por si el contenido no fue encontrado, se cambia $alias por el alias de la página de error 404.
$alias = $objeto_contenido->alias;

$files = array();

// Los archivos que se cargan siempre.
if (defined('DEFAULT_CSS')) {
	$default_css = DEFAULT_CSS;
	if (!empty($default_css)) {
		$aux = @json_decode($default_css, true);
		$files = @$aux['css'];
	}
}

$aux = $objeto_contenido->CssList();
$files = array_merge($files, $aux);

if (array_search($alias, $files) === false) {
	$files[] = $alias;
}

LoadFiles($files);
exit;

function LoadFiles($files) {

	global $objeto_usuario;
	global $objeto_contenido;

	if (phpversion() >= '5.2.9') {
		$files = array_unique($files, SORT_REGULAR);
	} else {
		$files = array_unique($files);
	}
	
	reset($files);

	if (DEVELOPE) {
		header("Cache-Control: no-cache, must-revalidate");
	}
	header("Content-type: text/css; charset=UTF-8");

	if (!DEVELOPE) {
		ob_start("compressCss");
	}

	foreach ($files as $value) {
		$value = $value.".css";
		$ruta = DIR_css.$value;
		if ((file_exists($ruta)) AND (is_file($ruta))) {
			echo "\r\n/* ".$value." */\r\n";
			include($ruta);
		}else{
			echo "\r\n/* El archivo ".$value." no pudo ser encontrado */\r\n";
		}
	}

	if (!DEVELOPE) {
		ob_end_flush();
	}
} // LoadFiles.


	function compressCss($buffer) {
		/* remove comments */
		$buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer);
		/* remove tabs, spaces, newlines, etc. */
		$buffer = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $buffer);
		/* remove irrelevant white spaces */
		$buffer = str_replace(
			array(', ',': ',' {','{ ',' ;','; '),
			array(',',':','{','{',';',';'),
			$buffer);
		return $buffer;
	}
?>