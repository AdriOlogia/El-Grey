;/* ********************************************************************************************* */
function ConstructorXMLHttpRequest() {
if(window.XMLHttpRequest) { return new XMLHttpRequest(); } 
else if(window.ActiveXObject) { var versionesObj = new Array(
'Msxml2.XMLHTTP.5.0',
'Msxml2.XMLHTTP.4.0',
'Msxml2.XMLHTTP.3.0',
'Msxml2.XMLHTTP',
'Microsoft.XMLHTTP');
     for (var i = 0; i < versionesObj.length; i++) {
       try {
           return new ActiveXObject(versionesObj[i]);
           }
      catch (errorControlado) 
      {
    }
  }
}
throw new Error("No se pudo crear el objeto XMLHttpRequest");
}
function objetoAjax(metodo) { 
  this.objetoRequest = new ConstructorXMLHttpRequest(); 
  this.metodo = metodo;
}
function peticionAsincrona(url,valores) { 
  var objetoActual = this;
  this.objetoRequest.open(this.metodo, url, true);
  this.objetoRequest.onreadystatechange = function() {
         switch(objetoActual.objetoRequest.readyState)
         {
            case 1: 
            objetoActual.Loading();
            break;
            case 2: 
            objetoActual.Loaded();
            break;
            case 3: 
            objetoActual.Interactive();
            break;
            case 4:
                  objetoActual.Finished(objetoActual.objetoRequest.status,
                  objetoActual.objetoRequest.statusText,
                  objetoActual.objetoRequest.responseText,
                  objetoActual.objetoRequest.responseXML);
                  break;
           } // switch
       } // function
  if (this.metodo == "GET") {
    this.objetoRequest.send(null); 
  }
  else if (this.metodo == "POST") {
         this.objetoRequest.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
        this.objetoRequest.send(valores);
  }
} // function
function objetoRequestCargando() {}
function objetoRequestCargado() {}
function objetoRequestInteractivo() {}
function objetoRequestCompletado(estado, estadoTexto, respuestaTexto, respuestaXML) {}
objetoAjax.prototype.Get = peticionAsincrona ;
objetoAjax.prototype.Loading = objetoRequestCargando ;
objetoAjax.prototype.Loaded = objetoRequestCargado ;
objetoAjax.prototype.Interactive = objetoRequestInteractivo ;
objetoAjax.prototype.Finished = objetoRequestCompletado ;


// Esta función simplifica todo lo anterior.
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
	objAjax.Get("<?php echo URL_ajax; ?>", cadena);
} //function getAjax
;