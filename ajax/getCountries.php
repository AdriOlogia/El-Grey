<?php
/*
	Devuelve la lista de países para el autocompletar del formulario de la home.
	Created: 2020-04-27
	Author: DriverOp.

*/
$this_file = substr(__FILE__,strlen(DIR_BASE))." ";



try {
	
	$sql = "SELECT * FROM `paises` WHERE `mostrar` = 'SI' ORDER BY `orden`, `name` ";
	$objeto_db->Query($sql);
	
	if ($objeto_db->error) { throw new Exception(__LINE__." DBErr: ".$objeto_db->errmsg); }
} catch(Exception $e) {
	if (DEVELOPE) { EchoLog($e->getMessage()); }
	cLogging::Write($this_file.__LINE__." ".$e->getMessage());
}
?>