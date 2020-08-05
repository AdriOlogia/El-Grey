/*
	EvalResult 2.0
	Created: 2019-10-22
	Author: DriverOp
	Desc: Evalúa las respuestas custom en formato JSON enviadas en el tag <json> desde el servidor.
	Arma y pone la caja con el mensaje.
	Modif: Agregado evento manageMessage para que sea una función externa la que ponga el mensaje.
	Modif: 2019-11-18
	Desc: Ahora no intenta mostrar un mensaje si el resultado no incluye los campos generr o goodmsg
	Modif: 2020-02-03
	Desc: Incorporar la fecha regresada por el servidor si existe.

*/

var cFechas = function () {
	var salida = null;
	var LaFecha = this;
	
	var dia_semana = ["Domingo","Lunes","Martes","Miércoles","Jueves","Viernes","Sábado"];
	var meses = ["Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"];
	
	this.SQLDate2Str = function (entrada) {
		var aux = entrada.split(" ");
		var work = new Date(aux[0]+'T'+aux[1]);
		salida = (dia_semana[work.getDay()]+', '+work.getDate()+' de '+meses[work.getMonth()]+' de '+work.getFullYear()+' a las '+aux[1]);
		return salida;
	}
	
}

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
	
	this.Eval = function (Content, lafecha) {
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
			if (lafecha) {
				if ((document.getElementById(lafecha) != null) && (TheEval.TheResult.time != null)) {
					var f = new cFechas;
					document.getElementById(lafecha).innerHTML = f.SQLDate2Str(TheEval.TheResult.time);
				}
			}
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
;