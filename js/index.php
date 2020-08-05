<?php
/*
	Author: DriverOp.
	Created: 2018-11-23

	Esto es el controller para cargar los archivos JavaScript según el alias del contenido.
	Cuando el alias es "f" significa que se está cargando un archivo suelto y no los JS de un contenido.
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

if (in_array("jquery.autocomplete",$handler)) {
	unset($handler[array_search("jquery.autocomplete",$handler)]);
}

echo '/*';
$objeto_contenido->GetContent($handler);
echo '*/';
// Esto es por si el contenido no fue encontrado, se cambia $alias por el alias de la página de error 404.
$alias = $objeto_contenido->alias;

$files = array();

// Los archivos que se cargan siempre.
if (defined('DEFAULT_JS')) {
	$default_js = DEFAULT_JS;
	if (!empty($default_js)) {
		$aux = @json_decode(DEFAULT_JS, true);
		$files = @$aux['js'];
	}
}

$aux = $objeto_contenido->JsList();
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
	header("Content-type: application/javascript; charset=UTF-8");
	
	if (INTERFACE_TYPE == 'backend') {
		$objeto_usuario->CheckLogin();
	}

	foreach ($files as $value) {
		$value = $value.".js";
		$ruta = DIR_js.$value;
		if ((file_exists($ruta)) AND (is_file($ruta))) {
			echo "\r\n/* ".$value." */\r\n";
			include($ruta);
		}else{
			echo "\r\n/* El archivo ".$value." no pudo ser encontrado */\r\n";
		}
	}
	return;
} // LoadFiles
