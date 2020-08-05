/*
	FormSend ver 1.1
	Author: Fivemedia.
	Developer: DriverOp
	Created: 2019-09-23
	Desc: Esto es para enviar un formulario por AJAX, incluyendo un archivo.
	
	Modif: 2019-10-20
	Desc: Agregado option 'extraHeaders' que son los headers especiales de la petición al servidor que el usuario quiera agregar.

	Documentación en el archivo fmdformsend.doc.txt
*/
(function($) {
	$.fn.fmdFormSend = function(options) {
		this.each(function () {
			
			var element = this;
			var	defaults = {
				method: 'POST',
				url: null,
				onStart: function() {},
				onProgress: function(loaded, total) {},
				onFinish: function(status, statusText, responseText, responseXML) {},
				extraData: {},
				extraHeaders: {}
			};
			settings = $.extend({}, defaults, options);
			if (window.FormData === undefined) {
				console.log('Tu navegador no soporta la API FormData. Lo siento, no puedo continuar...');
				return false;
			}
			if (element.tagName != 'FORM') {
				console.log('Solamente puedo actuar en tags <form>.');
				return false;
			}
			if(window.XMLHttpRequest) { var xhr = new XMLHttpRequest(); }
			else {
				console.log('Tu navegador no soporta peticiones AJAX. Lo siento, no puedo continuar');
				return false;
			}

			if (settings.url == null) {
				settings.url = element.action;
			}
			//console.log('Petición a: '+settings.url);
			xhr.open(settings.method, settings.url, true); // Método POST a la URL indicada en modo asíncrono.
			
			
			xhr.upload.addEventListener('progress', function (ev) {// ev es el objeto evento. Esto mide cuántos bytes fueron enviados al servidor.
				var porc = 0;
				var cargado = ev.loaded || ev.position;
				var total = ev.total || ev.totalSize;
				if(ev.lengthComputable){
					porc = Math.ceil(cargado / total * 100);
				}
				settings.onProgress.call(element, cargado, total, porc);
			/*
				if (settings.progress && (typeof settings.progress == 'function')) {
					settings.progress(ev.loaded, ev.total);
				}
			*/
				/*
				if (ev.lengthComputable) {
					console.log('Progress: '+ev.loaded+' de '+ev.total);
				}
				*/
			});
			xhr.onprogress = function (ev) { // Esto mide cuántos bytes fueron recibidos desde el servidor.
				/*
				if (ev.lengthComputable) {
					console.log('Onprogress: '+ev.loaded+' de '+ev.total);
				}
				*/
			};
			
			xhr.onreadystatechange = function (ev) { // ev es el objeto evento.
				switch (xhr.readyState) {
					case 0:  // nada
						// console.log('No se ha enviado nada todavía.'); 
						break;
					case 1: // Hay una petición que se acaba de abrir con open()
						 //console.log('Petición abierta.');
						break;
					case 2: // Se recibieron las cabeceras del servidor.
						// console.log('Cabeceras recibidas.');
						break;
					case 3:  // Se recibió un paquete de datos desde el servidor.
						// console.log('Datos llegando.');
						break;
					case 4: // La petición se completó. Esto es lo que nos interesa.
						// console.log('Petición completada');
						settings.onFinish.call(element, xhr.status, xhr.statusText, xhr.responseText, xhr.responseXML);
						break;
				}
			}
			
			var theForm = new FormData(element);
		    // Agregar los datos extra que el usuario quiera enviar además de los campos del formulario.
			$.each(settings.extraData, function(key, value){
				theForm.append(key, value);
			});
			$.each(settings.extraHeaders, function (key, value) {
				xhr.setRequestHeader(key, value);
			});

			settings.onStart.call(element);
			xhr.send(theForm); // Dispara la petición.
			
		}); // each
		return false;
	}
})(jQuery);