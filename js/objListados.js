/*
	EvalResult 2.0
	Created: 2019-10-22
	Author: DriverOp
	Desc: Evalúa las respuestas custom en formato JSON enviadas en el tag <json> desde el servidor.
	Arma y pone la caja con el mensaje.
	Modif: Agregado evento manageMessage para que sea una función externa la que ponga el mensaje.
	Modif: 2019-11-18
	Desc: Ahora no intenta mostrar un mensaje si el resultado no incluye los campos generr o goodmsg
	Modif: 2020-01-07
	Desc: Agregado soporte para <SELECT> con atributo múltiple.

*/

var mgrEvalResult = function (param) {

	this.defaultOptions = {
		ownClass: 'msgaviso',
		idPrefix: 'aviso_',
		manageMessage: null
	}
	this.TheResult = null;
	this.success = false;
	
	this.options = Object.assign({},this.defaultOptions,param);
	
	this.mode = 'err';
	
	this.theDiv = document.createElement('DIV');
	this.theDiv.setAttribute('id',this.options.idPrefix+'box');
	this.theDiv.classList.add(this.options.ownClass);
	this.theDiv.setAttribute("style","display:block;position:fixed;top:101%;left:101%;min-width:25%;max-width:50%;box-sizing:border-box;");
	
	this.theP = document.createElement('P');
	this.theP.setAttribute("id",this.options.idPrefix+'texto');
	this.theP.setAttribute("style", "display:block;font-size:1em;margin:0;padding:0.5em;text-align:center;background-color:transparent;vertical-align:baseline;font-size:12pt;line-height:1rem;");
	this.theDiv.appendChild(this.theP);
	
	this.theVail = document.createElement('DIV');
	this.theVail.setAttribute("id",this.options.idPrefix+'cortina');
	this.theVail.setAttribute("style", "display:block;position:fixed;z-index:32000;background-color:black;opacity:0.4;width:100%;height:100%;top:0;left:0;");
	var TheEval = this;
	
	this.theVail.addEventListener('click',function () {
		document.getElementsByTagName('body')[0].removeChild(TheEval.theVail);
		document.getElementsByTagName('body')[0].removeChild(TheEval.theDiv);
		TheEval.theP.innerHTML = '';
	});
	
	
	
	this.parseJson = function (texto) {
		var regexjson = /<json>(.*?)<\/json>/m;
		var json = "{}";
		var jsondata;
		if (regexjson.test(texto)) {
			aux = regexjson.exec(texto);
			json = aux[1];
			jsondata = eval("("+json+")");
			TheEval.success = true;
			return jsondata;
		} else {
			TheEval.success = false;
			return texto;
		}
	}

	this.ShowBox = function () {
		var texto = '';
		if (TheEval.mode == 'gen') {
			TheEval.theP.style.color = 'red';
			texto = TheEval.TheResult.generr;
		}
		if (TheEval.mode == 'good') {
			TheEval.theP.style.color = 'green';
			texto = TheEval.TheResult.goodmsg;
		}
		TheEval.theP.innerHTML = texto;
		document.getElementsByTagName('body')[0].appendChild(TheEval.theDiv);
		document.getElementsByTagName('body')[0].appendChild(TheEval.theVail);
		var w = TheEval.theDiv.offsetWidth;
		var h = TheEval.theDiv.offsetHeight;
		TheEval.theDiv.style.display = 'none';
		TheEval.theDiv.style.top = '50%';
		TheEval.theDiv.style.left = '50%';
		TheEval.theDiv.style.width = '20px';
		TheEval.theDiv.style.height = '20px';
		TheEval.theDiv.style.display = 'block';
		TheEval.theDiv.style.zIndex = 32001;
		
		$(TheEval.theDiv).animate({
				width:w,
				height:h,
				left:GetMidWidth()-parseInt(w/2),
				top:GetMidHeight()-parseInt(h/2),
			},200, 'linear', function () {
				TheEval.theDiv.style.overflow = 'auto';
			});
		
	}
	
	this.Eval = function (Content) {
		TheEval.TheResult = TheEval.parseJson(Content);
		if (!TheEval.success) { return true; }
		if (typeof TheEval.TheResult.dataerr != 'undefined') {
			if (TheEval.TheResult.dataerr) {
				TheEval.mode = 'data';
				var x;
				for (x in TheEval.TheResult.dataerr) {
					if (typeof $().msgerr == 'function') {
						$(document.getElementById(x)).msgerr(TheEval.TheResult.dataerr[x]);
					} else {
						SetMsgErr(document.getElementById(x),TheEval.TheResult.dataerr[x]);
					}
					if (typeof addclass != 'undefined') {
						$(document.getElementById(x)).classLis.add(addclass);
					}
				}
			}
			return false;
		}
		if (typeof TheEval.TheResult.ok != 'undefined') {
			TheEval.mode = 'ok';
			return true;
		}
		var result = false;
		var theMessage = '';
		if (typeof TheEval.TheResult.generr != 'undefined') {
			TheEval.mode = 'gen';
			theMessage = TheEval.TheResult.generr;
		}
		
		
		if (typeof TheEval.TheResult.goodmsg != 'undefined') {
			TheEval.mode = 'good';
			theMessage = TheEval.TheResult.goodmsg;
			result = true;
		}
		if ((TheEval.options.manageMessage != null) && (typeof TheEval.options.manageMessage == 'function')) {
			TheEval.options.manageMessage(TheEval.mode, theMessage);
		} else {
			if ((TheEval.mode == 'gen') || (TheEval.mode == 'good')) {
				TheEval.ShowBox();
			}
		}
		return result;

	} // Eval
} // EvalResult 2.0

/*
	ListadoCreators.
	Created: 2019-10-22
	Author: DriverOp
	Desc: Objeto para cargar listados en el backend de OmbuCore.
	Modif: 2020-02-04
	Desc: Reparado bug. Evento de ordenación de columnas se establecía para TODAS las tablas al mismo tiempo. Ahora solo para la que corresponde con la instancia.
*/
var mgrListadoCreator = function (param) {
	
	this.defaultOptions = {
		archivo: '<?php echo $objeto_contenido->alias; ?>',
		content: '<?php echo (isset($objeto_contenido->content))?$objeto_contenido->content:$objeto_contenido->alias; ?>',
		tableClassName: 'tabla-general',
		divListIdName: 'mainlist',
		idPrefix: '',
		onFinish: function (idTable) {},
		fixedParams: {}
	}
	
	this.ttm = null; // temporizador
	
	this.userParams = param;
	
	this.options = Object.assign({},this.defaultOptions,param);
	this.EvalResult = new mgrEvalResult();
	this.theRulo = document.createElement('DIV');
	this.theRulo.setAttribute('id','the_rulo');
	this.theRulo.setAttribute("style","display:block;position:absolute;width:100%;height:100%;top:0;left:0;background-color:rgba(0,0,0,0.5);background-position:center center;background-repeat:no-repeat;");
	this.theRulo.style.backgroundImage = 'url("<?php echo URL_imgs; ?>indicator.gif")';
	
	this.extraparams = {};
	
	var TheList = this;
	var theId = TheList.options.idPrefix+TheList.options.divListIdName;	
	
	
	this.Get = function (ele) {
		
		if (ele) {
			if ((ele.tagName == 'SELECT') && (ele.multiple == true)) {
				console.log('Es un select múltiple');
				if (ele.selectedOptions.length > 0) {
					for (var x = 0;x<ele.selectedOptions.length;x++) {
						TheList.extraparams[ele.name+'['+x+']'] = (typeof ele.selectedOptions[x].value == 'string')?ele.selectedOptions[x].value.trim():ele.selectedOptions[x].value;
					}
				} else {
					TheList.extraparams[ele.name] = '';
				}
			} else {
				TheList.extraparams[ele.name] = (typeof ele.value == 'string')?ele.value.trim():ele.value;
			}
		}
		TheList.Trigger();
	} // Get
	
	this.Trigger = function () {
		if (TheList.ttm != null) {
			clearTimeout(TheList.ttm);
		}
		TheList.ttm = setTimeout(TheList.CoreFunction,500);
	} // Trigger
	
	this.CoreFunction = function () {
		var queryStrings = new Object();
		
		if (TheList.options.content) {
			queryStrings.content = TheList.options.content;
		}
		if (TheList.options.archivo) {
			queryStrings.archivo = TheList.options.archivo;
		}
		if (TheList.userParams.fixedParams) { // Estos son los parámetros fijos puestos por el usuario.
			queryStrings = Object.assign({},queryStrings,TheList.userParams.fixedParams);
		}
		
		if (TheList.extraparams) {
			
			queryStrings = Object.assign({},queryStrings,TheList.extraparams);
			TheList.extraparams = {}; // Limpiar los parámetros extra para que no se acumulen.
			
		}
		var cadena = '';
		for(var indice in queryStrings) {
			cadena = cadena + indice + '=' + encodeURIComponent(queryStrings[indice]) + '&';
		};
		if (cadena.length > 0) {
			cadena = cadena.slice(0,-1);
		}
		var divList = document.getElementById(theId);
		if (divList) {
			divList.style.position = 'relative';
			divList.appendChild(TheList.theRulo);
			if (parseInt(divList.offsetHeight) < 100) { divList.style.height = '100px'; }
		} else {
			console.log('No encontré un elemento con ID: '+theId);
		}
		TheList.objAjax.Get("<?php echo URL_ajax; ?>", cadena);
	} // CoreFunction
	
	this.SetOrder = function (ele) {
		TheList.extraparams.ord = ele.dataset.field;
		TheList.Trigger();
	}

	/* Esto se ejecuta cuando la petición Ajax regresa el resultado del servidor */
	this.Finished = function (statusCode, statusMessage, Content, XMLContent) {
		var divList = document.getElementById(theId);
		if (divList) {
			divList.removeChild(TheList.theRulo);
			divList.style.position = 'static';
			divList.style.height = 'auto';
			divList.innerHTML = Content;
			var columnOrder = divList.querySelectorAll('.col-order');
			if (columnOrder.length > 0) {
				columnOrder.forEach(function (th) {
					th.addEventListener('click', function () { TheList.SetOrder(th); });
				});
				if (typeof TheList.options.onFinish == 'function') {
					TheList.options.onFinish(divList.querySelector('.tabla-general'));
				}
			} else {
				if (typeof TheList.options.onFinish == 'function') {
					TheList.options.onFinish(divList);
				}
			}
		} else {
			console.log('No encontré un elemento con ID: '+theId);
		}
		TheList.EvalResult.Eval(Content);
	} // Finished
	
	this.setFinish = function (fnc) {
		TheList.options.onFinish = fnc;
	}

	this.objAjax = new objetoAjax("POST"); // Está definido en microajax.js
	this.objAjax.Finished = this.Finished;
	
} // ListadoCreator
