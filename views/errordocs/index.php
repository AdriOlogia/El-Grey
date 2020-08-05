<?php
/*
	EchoLog(DIR_errordocs);
	ShowVar($handler);
*/
if (CanUseArray($handler)) {
	switch ($handler[0]) {
		case '403':include("403forbid.htm"); break;
		case '404':include("404c.htm"); break;
		case '500':include("500c.htm"); break;
	}
}
?>