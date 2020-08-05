<?php
/*
	Author: DriverOp.
	Created: 2018-11-23
	Description: Vamos a intentar que todo el sitio pase por acá (excepto las imágenes, claro).
*/

define("DEVELOPE",true);

if (DEVELOPE) {
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
}

header("Cache-Control: no-cache, must-revalidate");
// Inicializar todas las constantes para comenzar a laburar...
require_once('initialize.php');
session_start();
// Establecer las constantes de configuración
require_once(DIR_config.'config.inc.php');

/* Ahora se puede comenzar a usar el framework. */
require_once(DIR_includes."common.inc.php"); // Contiene las funciones comunes.
require_once(DIR_includes."class.security.inc.php"); // Contiene funciones para manipular la URL.
//require_once(DIR_model."class.bussinessdata.inc.php"); // Para traer los datos de este negocio.

//$BussinesData = new cBussiness();

$asset = null; // Bandera para determinar si se carga contenido o recurso.
$msgerr = null; // Para almacenar cualquier mensaje de error.
/*
$_GET['_ruta_'] se establece mediante el archivo .htaccess, contiene la lista de directorios virtuales.
*/
$handler = array(); // Acá van a quedar los alias de los contenidos encadenados como directorios virtuales.
if (isset($_GET['_ruta_']) and ($_GET['_ruta_'] != null)) { // Si existe y no está vacío...
	$ruta = explode('/', $_GET['_ruta_']); // Separar los directorios por su barra.
	$i = 0;
	foreach ($ruta as $key => $value) {
		if (($value == null) or ($value == '')) { // Esto es porque en la URL podrían venir dos o más barras seguidas lo que provocaría aliases nulos o vacíos.
			unset($ruta[$key]);
		}else{
			$handler[$i] = cSecurity::StringToUrl($value); // Limpia los caracteres extraños y no permitidos.
			$i++;
		}
	}
}else{
	$handler[0] = DEFAULT_CONTENT;  // Si no existe, entonces el alias a cargar es el que se configuró por omisión.
}

// Se incluyen las clases necesarias para comenzar a trabajar...
require_once(DIR_includes.'class.logging.inc.php'); // Para escribir entradas en el log de eventos.
require_once(DIR_model.'class.dbutili.2.inc.php'); // Clase base para el manejo de la base de datos.
require_once(DIR_model.'class.contenidos.inc.php'); // Clase para acceder a la lista de contenidos en la DB.
if (INTERFACE_TYPE == 'backend') {
	require_once(DIR_model.'class.usuarios_backend.inc.php'); // Clase para manejar los usuarios del sistema.
}

// Una instancia de la base de datos.
$objeto_db = new cDb();
$objeto_db->Connect(DBHOST, DBNAME, DBUSER, DBPASS);
// Primer problema: La conexión a la DB podría ser errónea por alguna razón...
if ($objeto_db->error) {
	include(DIR_errordocs."dberror.htm");
	exit;
}
// Una instancia de la clase para manejar los contenidos.
$objeto_contenido = new cContenido();
require_once(DIR_includes.'class.sidekick.inc.php'); // La biblioteca de funciones para recuperar datos de la base de datos.

if (INTERFACE_TYPE == 'backend') {
	// Una instancia de la clase para manejar los usuarios del sistema.
	$objeto_usuario = new cUsrBackend();
}
$this_file = substr(__FILE__,strlen(DIR_BASE))." ";
/*
	Hay que decidr qué se va a cargar.
	Si un contenido o un recurso.
	Esto lo determina el contenido del array $restricted_aliases.
	Los recursos deberían ser: los script JavaScript, los estilos CSS y las peticiones Ajax ya que estos dependen del contenido.
*/
if (array_key_exists($handler[0],$restricted_aliases)) { // ¿El primer alias de la lista es una petición de recurso?.
	$asset = $handler[0]; // Es un recurso. Se guarda para uso posterior.
	array_shift($handler); // Se elimina de la lista ese alias, para no interferir con lo que sigue.
	//cLogging::Write($this_file." Pasado el control a: ".$restricted_aliases[$asset]."index.php");
	require($restricted_aliases[$asset]."index.php"); // Se le pasa el control al controlador del recurso.
	exit;
}

// El charset por omisión es...
header("Content-type: text/html; charset=UTF-8");

if (INTERFACE_TYPE == 'backend') {
	$user_logged_in = false;
	$objeto_contenido->usuario = @$objeto_usuario;
	$content_found = $objeto_contenido->GetContent($handler); // Se busca el contenido.
	if (@$objeto_contenido->esta_protegido) {
		$user_logged_in = $objeto_usuario->CheckLogin();
		if (!$user_logged_in) {
			$objeto_contenido->GetByController('login');
		}
	}
} else {
	$content_found = $objeto_contenido->GetContent($handler); // Se busca el contenido.
}

// La ruta hacia el supuesto controlador.
if (!empty($objeto_contenido->controlador)) {
	$controllerpath = DIR_controller.'controller_'.$objeto_contenido->controlador.'.php';
	// Comprueba si existe el controlador y en ese caso lo incluye.
	if (ExisteArchivo($controllerpath)) {
		require_once($controllerpath); // Aquí se transfiere el control al controlador del contenido solicitado.
	}else{
		cLogging::Write($this_file.__LINE__." Controlador no encontrado: ".$controllerpath);
		$msgerr = 'Te olvidaste del controlador... '.$controllerpath;
		require_once(DIR_errordocs."500c.htm");
	}
} else {
	cLogging::Write($this_file.__LINE__." No se seleccionó ningún controlador. ¿Problemas con la base de datos?.");
	$msgerr = '$objeto_contenido no devolvió ningún controlador.';
	require_once(DIR_errordocs."500c.htm");
}

$objeto_db->Disconnect(); // Siempre es buena idea no dejar abierta una conexión a la base de datos.
?>