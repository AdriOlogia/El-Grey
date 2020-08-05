<?php
/*
	Establece los directorios de trabajo del sitio.
	DIR_BASE tiene el path a la raíz del sitio en el sistema de archivos local.
	BASE_URL tiene la URL a la raíz del sitio web.
*/
/*
	Rutas a partir del sistema de archivos.
*/
	/********************************************/
	/*			Directorios particulares 	 	*/
	/********************************************/
	
	define("SYSTEM_DIR","system".DS);
	define("MODEL_DIR","model".DS);
	define("CONTROLLER_DIR","controllers".DS);
	define("CONFIG_DIR","config".DS);
	define("INCLUDES_DIR","includes".DS);

	define("VIEWS_DIR","views".DS);
	define("AJAX_DIR","ajax".DS);
	define("CSS_DIR","css".DS);
	define("JS_DIR","js".DS);
	define("IMG_DIR","imgs".DS);
	define("LANGS_DIR","langs".DS);
	define("LOGGING_DIR","logs".DS);
	define("BACKEND_DIR","backend".DS);
	define("ERROR_DOCS","errordocs".DS);
	define("TEMP_DIR","temp".DS);
	
	/************************************/
	/*			Rutas completas 	 	*/
	/************************************/

	define("DIR_system",DIR_BASE.SYSTEM_DIR);
	define("DIR_model",DIR_system.MODEL_DIR);
	define("DIR_controller",DIR_system.CONTROLLER_DIR);
	define("DIR_config",DIR_system.CONFIG_DIR);
	define("DIR_includes",DIR_system.INCLUDES_DIR);

	define("DIR_views",DIR_BASE.VIEWS_DIR);
	define("DIR_common",DIR_views.'common'.DS);
	define("DIR_site",DIR_views.'site'.DS);
	define("DIR_ajax",DIR_BASE.AJAX_DIR);

	define("DIR_js", DIR_BASE.JS_DIR);
	define("DIR_css", DIR_BASE.CSS_DIR);
	define("DIR_img", DIR_BASE.IMG_DIR);
	define("DIR_imgs", DIR_BASE.IMG_DIR);
	define("DIR_logging",DIR_BASE.LOGGING_DIR);
	define("DIR_backend",DIR_BASE.BACKEND_DIR);
	define("DIR_errordocs",DIR_views.ERROR_DOCS);
	define("DIR_plantillas",DIR_views."plantillas".DS);
	define("DIR_downloader_files",DIR_BASE."downloaded".DS);
	define("DIR_temp",DIR_BASE.TEMP_DIR);
	

/*
	URIs a los recursos del sitio.
*/


define("BASE_IMG","imgs/");
define("URL_fonts", BASE_URL.'fonts/');
define("URL_img", BASE_URL.BASE_IMG);
define("URL_imgs", BASE_URL.BASE_IMG); // yep, it's the same
define("URL_ajax", BASE_URL.'ajx/');
?>