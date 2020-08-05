function getAjax(objeto, funcion){
	var objAjax = new objetoAjax("POST");
	objAjax.Finished = funcion;
	var cadena = '';
	if (objeto.modulo) {
		cadena = cadena+'&modulo='+objeto.modulo;
	};
	if (objeto.archivo) {
		cadena = cadena+'&archivo='+objeto.archivo;
	};
	if (objeto.accion) {
		cadena = cadena+'&accion='+objeto.accion;
	};
	if (objeto.content) {
		cadena = cadena+'&content='+objeto.content;
	};
	if (objeto.elid) {
		cadena = cadena+'&id='+objeto.elid;
	};
	if (objeto.extraparams) {
		cadena = cadena+'&'+objeto.extraparams;
	};
	objAjax.Get("ajax/", cadena);
} //function getAjax
