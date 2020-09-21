<?php
// Configuración de la base de datos
require_once(DIR_config."database.config.inc.php");

define("JSON_HACELO_BONITO",JSON_FORCE_OBJECT+JSON_PRETTY_PRINT+JSON_NUMERIC_CHECK+JSON_UNESCAPED_UNICODE);
define("JSON_HACELO_BONITO_CON_ARRAY",JSON_PRETTY_PRINT+JSON_NUMERIC_CHECK+JSON_UNESCAPED_UNICODE);

// Qué tipo de interfaz es. Esto determina si se pide siempre un usuario logueado ('backend') o no ('frontend');
define("INTERFACE_TYPE",'frontend');

// Título por omisión.
define("MAINTITLE","El Grey");

// Archivos del tinglado
define("DEFAULT_SITE_HEAD","head");
define("DEFAULT_SITE_HEADER","header");
define("DEFAULT_SITE_FOOTER","footer");
define("DEFAULT_SITE_MENU","mainmenu");
define("DEFAULT_SITE_SUB_MENU","submenu");
define("DEFAULT_SITE_TEMPLATE","main_content");
// El contenido por omisión (su alias).
define("DEFAULT_CONTENT","inicio"); // Debería ser siempre 'inicio' pero si 'inicio' está protegido, redirige a 'login' en index.php

define("SITE_mainlogo", URL_img."favicon-elgrey.png");
define("SITE_humans", BASE_URL."humans.txt");

// Siempre cargar estos css
define('DEFAULT_CSS','{"css":["font-awesome","main","msgerr","select2.min","select2-bootstrap4-theme/select2-bootstrap4.min"]}');
// Y estos JS
define('DEFAULT_JS','{"js":["microajax","base","main","select2/select2.full.min","select2/i18n/es"]}');
// Estos directorios AJAX está desmilitarizados
define('DMZ_CONTENTS','*login');
// Estos archivos AJAX están desmilitarizados
define('DMZ_ARCHIVOS','login_form|checkLogin|frm_forgotPass|userTimeleft');
/*
	MUY importante
	Esta es la lista de aliases restringidos. Tienen un significado especial.
	Los aliases listados aquí no se validan contra la base de datos sino que redirigen a directorios donde se tratan por separado.
*/
$restricted_aliases = array('css'=>DIR_css,'js'=>DIR_js,'ajx'=>DIR_ajax,'errordocs'=>DIR_errordocs);

define("APP_NAME","Ombutech Services");
define("APP_DESCRIPTION","Ombutech Services institutional site");
define("APP_VERSION_NUMBER","1.0α");

?>