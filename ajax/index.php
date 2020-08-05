<?php
/*
	Author: DriverOp.
	Created: 2018-11-23
	
	Este es el controlador de los contenidos solicitados por Ajax.
	
	Modif: 2020-04-08
	Desc: Agregada variable bool $new para indicar que el id en ajax_id es para dar de alta un nuevo registro.
*/

	
	if (!isset($asset)) { // Si $asset no está creada, quiere decir que se intenta acceder a este archivo directamente. Hay que fallar.
		$this_file = __FILE__;
		include("404.htm");
		exit;
	}
	
	$this_file = substr(__FILE__,strlen(DIR_BASE))." ";

	header("Cache-Control: no-cache, must-revalidate");
	header("Content-type: text/html; charset=UTF-8");

	$ajax_accion = (isset($_POST['accion']))?$_POST['accion']:@$_GET['accion'];
	$dir_content = null;
	$ajax_id = null;
	$new = false;

	$ajax_archivo = (isset($_POST['archivo']))?$_POST['archivo']:@$_GET['archivo'];
	if (empty($ajax_archivo)) {
		$ajax_archivo = @$handler[0];
	}
	$ajax_archivo = cSecurity::SatinizePathParam(trim($ajax_archivo)); // No se permite incluir directorios.
	
	if (empty($ajax_archivo)) {
		EmitJSON('No se indicó el archivo a cargar.');
		cLogging::Write($this_file.__LINE__." No se indicó el archivo a cargar.");
		exit;
	}
	


	$dir_content = (!empty($_POST['content']))?$_POST['content']:@$_GET['content'];
	
	if (!empty($dir_content)) {
		$dir_content = mb_substr($dir_content,0,128);
		$dir_content = cSecurity::NeutralizeDT(trim($dir_content)); // Si se permite ir a un directorio más profundo, pero no escalar directorio.
		if (!empty($dir_content)) {
			$dir_content = EnsureTrailingSlash($dir_content);
		} else {
			$dir_content = NULL;
		}
	}

/* Si el tipo de interfaz es backend, denegar todo excepto lo desmilitarizado. */
	if (INTERFACE_TYPE == 'backend') {
		if (!cSecurity::Demilitarized($dir_content,$ajax_archivo)) { // El contenido solicitado está desmilitarizado?
			if (!$objeto_usuario->CheckLogin($this_file)) { // Solo si está logueado puede acceder a contenido militarizado.
				header($_SERVER['SERVER_PROTOCOL'].' 401 Authorization Required');
				EmitJSON('Debe iniciar sesión de usuario.');
				cLogging::Write($this_file." Se intentó acceder sin tener sesión de usuario abierta a ".$dir_content.$ajax_archivo);
				exit;
			}
		}
	}

	$rawid = (isset($_POST['id']))?$_POST['id']:@$_GET['id'];
	$ajax_id = SecureInt(substr(trim($rawid),0,11),NULL);
	$new = (strtolower(substr($rawid,0,3)) == 'new');

	$ruta = $dir_content.$ajax_archivo.'.php';

	if (ExisteArchivo(DIR_ajax.$ruta)) {
		include_once($ruta);
	} else {
		header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found');
		EmitJSON("No existe el archivo: ".$ruta);
		cLogging::Write($this_file." No se encontró ".$ruta);
	}
	$objeto_db->Disconnect();
?>