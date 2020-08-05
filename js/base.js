/*
	File: base.js
	Author: DriverOp
	Created: 2010-07-11
	Modified: 2014-06-10
	Last modif: Improved LoadJS and LoadCSS functions. Added replaceDots().
	Added deleteDots().
	Added restoreComma().
	Modif: 2018-10-26
	Desc: Agregado String.sprintf
	Modif: 2018-11-04
	Desc: Agregado String.stripSpaces
	Modif: 2018-11-14
	Desc: Agregado String.replaceSpaces
	Modif: 2020-01-08
	Desc: isNumeric() ahora pasa la validación cuando n contiene la coma como separador de decimales.
*/
;
String.prototype.trim = function() { return this.replace(/^\s+|\s+$/g, "");    };
String.prototype.replaceComma = function() { return this.replace(/,/g, ".");    };
String.prototype.restoreComma = function() { return this.replace(/\./g, ",");    };
String.prototype.replaceDecimalDot = function() { return this.replace(/\./g, ",");    };
String.prototype.isint = function() {
	var regint = /^[\+|\-]*\d+$/;
	return regint.test(this.trim());
};
String.prototype.isfloat = function() {
	var regfloat = /^[\+|\-]*\d+(\.?\d*)$/;
	return regfloat.test(this.trim().replaceComma());
};
String.prototype.replaceDots = function() {	return this.replace(/\./g,"-"); }
String.prototype.deleteDots = function() {	return this.replace(/\./g,''); }
String.prototype.replaceHypen = function() { return this.replace(/-/g,"/"); }
String.prototype.deleteNonNumericChars = function() {
	if (this.length == 0) {
		return this;
	}
	var aux = '';
	for (x=0;x<this.length;x++) {
		if (/[\d,\.]/.test(this[x])) {
			aux = aux+this[x];
		}
	}
	return aux;
}
String.prototype.sprintf = function () {
	var args = arguments;
	return this.replace(/\[(\d+)\]/g,
		function (coincidencia, numero) {
			return (typeof args[numero] != 'undefined'?args[numero]:coincidencia);
		}
	);
}

String.prototype.stripSpaces = function() {
	return this.replace(/\s+/g, "");
}
String.prototype.replaceSpaces = function(rip) {
	return this.replace(/\s+/g, rip);
}
String.prototype.replaceAll = function(str1, str2, ignorar) {
    return this.replace(new RegExp(str1.replace(/([\/\,\!\\\^\$\{\}\[\]\(\)\.\*\+\?\|\<\>\-\&])/g,"\\$&"),(ignorar?"gi":"g")),(typeof(str2)=="string")?str2.replace(/\$/g,"$$$$"):str2);
}

Number.prototype.lpad = function (width, z) {
  z = z || '0';
  n = this + '';
  return n.length >= width ? n : new Array(width - n.length + 1).join(z) + n;
}

Number.prototype.rpad = function (width, z) {
  z = z || '0';
  n = this + '';
  return n.length >= width ? n : n + new Array(width - n.length + 1).join(z);
}

function isNumeric(n) {
  return (Object.prototype.toString.call(n) === '[object Number]' || Object.prototype.toString.call(n) === '[object String]') &&!isNaN(parseFloat(n.toString().replaceComma())) && isFinite(n.toString().replaceComma().replace(/^-/, ''));
}

String.prototype.capitalize = function () {
	return this.charAt(0).toUpperCase() + this.slice(1);
}

var regexjson = /<json>(.*?)<\/json>/m;
var objPost = new objetoAjax("POST");
var objGet = new objetoAjax("GET");

function Debug(texto,add) {
<?php
	if (DEVELOPE) {
?>
	var d = document.getElementById("debug");
	if (d == null) {
		d = document.createElement('DIV');
		d.id = 'debug';
		d.setAttribute("style","display:block;position:fixed;background-color:white;z-index:5001;width:100%;min-height:4em;max-height:10em;color:black;font-family:monospace;padding:5px;top:0;left:0;overflow:auto;max-height:200px;");
		document.getElementsByTagName('body')[0].appendChild(d);
	}
	d.ondblclick = function () {
		this.style.display = 'none';
	}
	if (typeof texto == 'object') {
		texto = JSON.stringify(texto);
	}
	if (add) {
		d.innerHTML = d.innerHTML + texto;
	} else {
		d.innerHTML = texto;
	}
	d.style.display = 'block';
<?php
	}
?>
}

function parseJson(texto) {
	var json = "{}";
	var jsondata;
	if (regexjson.test(texto)) {
		aux = regexjson.exec(texto);
		json = aux[1];
		jsondata = eval("("+json+")");
		return jsondata;
	} else {
		return texto;
	}
}

function GetWinWidth() { 
   var w; 
   if (typeof window.innerWidth != 'undefined') { 
      w = window.innerWidth; 
   } else { 
      if (typeof document.documentElement != 'undefined' 
        && typeof document.documentElement.clientWidth !=  
        'undefined' && document.documentElement.clientWidth != 0) { 
         w = document.documentElement.clientWidth; 
      } else {    
         w = document.getElementsByTagName('body')[0].clientWidth; 
      } 
   } 
   return w; 
} 

function GetWinHeight() { 
   var h; 
   if (typeof window.innerHeight != 'undefined') { 
      h = window.innerHeight; 
   } else { 
      if (typeof document.documentElement != 'undefined' 
        && typeof document.documentElement.clientHeight !=  
        'undefined' && document.documentElement.clientHeight != 0) { 
         h = document.documentElement.clientHeight; 
      } else {    
         h = document.getElementsByTagName('body')[0].clientHeight; 
      } 
   } 
   return h; 
}

function GetMidWidth() {
	return parseInt(GetWinWidth() / 2);
}

function GetMidHeight() {
	return parseInt(GetWinHeight() / 2);
}

function FindLeft(ele) {
	return GetMidWidth() - parseInt(ele.clientWidth / 2);
}

function GetKey(e) {
	var keynum;
	if(window.event) { keynum = e.keyCode; }
	else if(e.which) { keynum = e.which; }
	return keynum;
}

function ValidarCorreo(email){
	var formato = /^([\w-\.\+])+@([\w-]+\.)+([a-z]){2,4}$/;
	var comparacion = formato.test(email);
	return comparacion;
}

function ValidarFecha(fecha) {
	var formato = /^[0-3]?[0-9](-|\/)[0-1]?[0-9](-|\/)[0-9]{4}$/;
	var comparacion = formato.test(fecha);
	return comparacion;
}

function scrollableElement(els) {
	for (var i = 0, argLength = arguments.length; i <argLength; i++) {
		var el = arguments[i],
			$scrollElement = $(el);
		if ($scrollElement.scrollTop()> 0) {
			return el;
		} else {
			$scrollElement.scrollTop(1);
			var isScrollable = $scrollElement.scrollTop()> 0;
			$scrollElement.scrollTop(0);
			if (isScrollable) {
			return el;
			}
		}
	}
    return [];
}

function SmoothScroll(id, Ejecutar) {
		var scrollElem = scrollableElement('html', 'body');
		var $target = $("#"+id);
		var targetOffset = $target.offset().top;
		$(scrollElem).animate({scrollTop: targetOffset}, 1000, 'linear', function() {
			if (typeof Ejecutar == 'function') {
				Ejecutar();
			}
		});
}

function LoadCSS(cssFile) {  
	var d = new Date();
	var id = "css-"+cssFile.trim().replaceDots();
	var e = document.getElementById(id);
	if (e != undefined) {
		document.getElementsByTagName("head")[0].removeChild(e);
	}
	var cssLink=document.createElement("link");
	cssLink.setAttribute("rel", "stylesheet");
	cssLink.setAttribute("type", "text/css");
	cssLink.setAttribute("media", "all");
	cssLink.setAttribute("href", "<?php echo BASE_URL; ?>css/f/"+cssFile+"/"+d.getTime());
	cssLink.setAttribute("id",id);
	document.getElementsByTagName("head")[0].appendChild(cssLink);
}  

function LoadJS(jsFile, loadAsync, fncCallback) {
	var d = new Date();
	var id = "js-"+jsFile.trim().replaceDots();
	var e = document.getElementById(id);
	if (e != undefined) {
		document.getElementsByTagName("head")[0].removeChild(e);
	}
	var jsScript=document.createElement("script");
	jsScript.setAttribute("type", "text/javascript");
	jsScript.setAttribute("id", id);
	jsScript.setAttribute("src", "<?php echo BASE_URL; ?>js/f/"+jsFile+"/"+d.getTime());
	if (loadAsync) {
		jsScript.setAttribute("async","true");
	}
	if (typeof fncCallback == 'function') {
		jsScript.addEventListener('load', function () { fncCallback() });
	}
	document.getElementsByTagName("head")[0].appendChild(jsScript);
}

function ReplaceCSS(cssFile, idlink) {  
	var d = new Date();
	var e = document.getElementById(idlink);
	if (e != undefined) {
		document.getElementsByTagName("head")[0].removeChild(e);
	}
	var cssLink=document.createElement("link");
	cssLink.setAttribute("rel", "stylesheet");
	cssLink.setAttribute("type", "text/css");
	cssLink.setAttribute("media", "all");
	cssLink.setAttribute("href", "<?php echo BASE_URL; ?>f/"+cssFile+"&rnd="+d.getTime());
	cssLink.setAttribute("id",idlink);
	document.getElementsByTagName("head")[0].appendChild(cssLink);
}

function RandStr(len, t) {
	var uppernumchars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXTZabcdefghiklmnopqrstuvwxyz";
	var numchars = "0123456789abcdefghiklmnopqrstuvwxyz";
	var num = "0123456789";
	var chars = "abcdefghiklmnopqrstuvwxyz";
	var string_length = 8;
	if (len) {
		string_length = len;
	}
	switch (t) {
		case 0:chars = uppernumchars; break;
		case 1:chars = numchars; break;
		case 2:chars = num; break;
	}
	var result = '';
	for (var i=0; i<string_length; i++) {
		var rnum = Math.floor(Math.random() * chars.length);
		result += chars.substring(rnum,rnum+1);
	}
	return result;
}

var CKEDITOR_BASEPATH = '<?php echo BASE_URL; ?>js/';

function formatMoney(number, decPlaces, decSep, thouSep) {
	decPlaces = isNaN(decPlaces = Math.abs(decPlaces)) ? 2 : decPlaces,
	decSep = typeof decSep === "undefined" ? "," : decSep;
	thouSep = typeof thouSep === "undefined" ? "." : thouSep;
	var sign = number < 0 ? "-" : "";
	var i = String(parseInt(number = Math.abs(Number(number) || 0).toFixed(decPlaces)));
	var j = (j = i.length) > 3 ? j % 3 : 0;

	return sign +
		(j ? i.substr(0, j) + thouSep : "") +
		i.substr(j).replace(/(\decSep{3})(?=\decSep)/g, "$1" + thouSep) +
		(decPlaces ? decSep + Math.abs(number - i).toFixed(decPlaces).slice(2) : "");
}
