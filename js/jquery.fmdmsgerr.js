;/*
	msgerr ver 1.2.2
	Author: Fivemedia.
	Created: 2013-11-26
	Modified: 2013-12-12
	Desc: Pone mensajes de error en los elementos input, select.
	ModifDesc: Mejora la extracción del mensaje de error. Causaba problemas con elementos encerrados en <div>
	Modified: 2014-07-11
	ModifDesc: Ahora las opciones top, bottom, left y right pueden ser literales y no solo enteros. Se comprueba la existencia de un objeto con identificador 'msgerrdefaults' que debe ser global para pasar opciones de una sola vez.
	Arreglado bug en el ancho del mensaje 'p'.
	Opciones:
		String u Objeto: {
			msg: El mensaje de error,
			top o bottom: posición por arriba o por abajo del input/select
			left o right: posición a la izquierda o a la derecha del input/select
			cssclass: el nombre de la clase CSS a aplicar al mensaje
			surrogate: elemento que al hacer foco quita el mensaje, en vez del propio elemento.
			respetarMargenes: si respeta los margenes del elemento
		}

	Modified: 2014-08-13
	ModifDesc: agregado respetarMargenes
	Modified: 2017-12-14
	ModifDesc: Ahora se saltea los inputs con type=hidden.
*/
(function($){
	$.fn.msgerr = function(options) {
		this.each(
			function () {
				var element = this;
				if (element.type == 'hidden') {
					return false;
				}
				var
				defaults = {
					cssclass: 'msgerr',
					msg: ''
				};
				if (typeof options == 'string') {
					options = {msg:options}
				}
				settings = $.extend({}, defaults, options);
				if (typeof msgerrdefaults != 'undefined') {
					if (typeof msgerrdefaults == 'object') {
						var temp = {};
						var x;
						for (x in msgerrdefaults) {
							if (typeof options[x] != 'undefined') {
								temp[x] = options[x];
							} else {
								temp[x] = msgerrdefaults[x];
							}
						}
						settings = $.extend({}, settings, temp);
					}
				}
				if ((element.id == undefined)||(element.id == null)||(element.id == '')) {
					if ((element.name == undefined)||(element.name == null)||(element.name == '')) {
						element.id = 'elem-'+Math.floor(Math.random()*11);
					} else {
						element.id = element.name;
					}
				}

				var d = document.getElementById('div-'+element.id);
				if (d == undefined) {
					d = document.createElement('div');
					d.style.cssText = 'margin: 0px; padding: 0px; border: 0;display:inline-block;';
					d.style.position = 'relative';
					d.style.width = element.offsetWidth+'px';
					d.setAttribute('id','div-'+element.id);
					d.classList.add('div-'+settings.cssclass);

					if (options.respetarMargenes) {
						var tmp_estilos = element.currentStyle || window.getComputedStyle(element);
						d.style.marginTop = tmp_estilos.marginTop;
						d.style.marginBottom = tmp_estilos.marginBottom;
						d.style.marginLeft = tmp_estilos.marginLeft;
						d.style.marginRight = tmp_estilos.marginRight;						
					};
					
					$(element).wrap(d);
				}

				var m = document.getElementById('msgerr-'+element.id);
				if ((typeof m != 'undefined') || (m != null)) {
					$('p#msgerr-'+element.id).remove();
				}
				m = document.createElement('p');
				m.className = settings.cssclass;
				m.style.width = 'auto';
				m.innerHTML = settings.msg;
				m.style.display = 'none';
				m.style.position = 'fixed';
				m.setAttribute('id','msgerr-'+element.id);
				
				m.style.top = '-100px';
				m.style.display = 'block';
				$('body').append(m);
				ow = $(m).outerWidth()+18;
				m.style.position = 'absolute';
				m.style.top = null;

				if ((settings.top == undefined) && (settings.bottom == undefined)) {
					m.style.top = '-23px';
				} else {
					if (settings.top != undefined) {
						if (settings.top.isint()) {
							m.style.top = settings.top+'px';
						} else {
							m.style.top = settings.top;
						}
						
					} else {
						if (settings.bottom.isint()) {
							m.style.bottom = settings.bottom+'px';
						} else {
							m.style.bottom = settings.bottom;
						}
						
					}
				}

				if ((settings.left == undefined) && (settings.right == undefined)) {
					m.style.right = '0px';
				} else {
					if (settings.right != undefined) {
						if (typeof settings.right == 'number') {
							m.style.right = settings.right+'px';
						} else {
							m.style.right = settings.right;
						}
						
					} else {
						if (settings.left.isint()) {
							m.style.left = settings.left+'px';
						} else {
							m.style.left = settings.left;
						}
					}
				}

				if (settings.minWidth != undefined) {
					if (typeof settings.minWidth == 'number') {
						m.style.minWidth = settings.minWidth+'px';
					} else {
						if (settings.minWidth.isint()) {
							m.style.minWidth = settings.minWidth+'px';
						} else {
							m.style.minWidth = settings.minWidth;
						}
					}
					if (parseInt(settings.minWidth) < parseInt(ow)) {
						m.style.width = ow+'px';
					}
				} else {
					m.style.width = ow+'px';
				}
				
				$(m).insertAfter(element);

				$(m).click(function () {
					if ($('div#div-'+element.id).length != 0) {
						$(this).remove();
						$('div#div-'+element.id).replaceWith(function () {
							return $(this).contents();
						});
						$(element).focus(); 
					}
				});

				if (settings.surrogate != undefined) {
					$(settings.surrogate).focus(function () {
						if ($('div#div-'+element.id).length != 0) {
							$('p#msgerr-'+element.id).remove();
							$('div#div-'+element.id).replaceWith(function () {
								return $(this).contents();
							});
							//$(element).focus(); 
						}
					}); // focus
				} else {
					$(element).focus(function () {
						if ($('div#div-'+element.id).length != 0) {
							$('p#msgerr-'+element.id).remove();
							$('div#div-'+element.id).replaceWith(function () {
								return $(this).contents();
							});
							$(element).focus(); 
						}
					}); // focus
				}
			} // function each
		); // each
		return false;
	}
})(jQuery);