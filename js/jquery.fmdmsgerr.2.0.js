;
/*
	msgerr ver 2.0
	Author: Fivemedia.
	Created: 2020-01-22
	Desc: 
		Plugin JQuery para mostrar mensajes en los inputs y select en forma de globo encima de ellos con la intención de señalar errores en la entrada de datos.
		Necesita de una clase css 'balloon' para formatear el globo con el mensaje (msgerr.balloon.css).
		
		¡Siempre regresa false por lo que no se puede encadenar con otro plugin JQuery!.
		
		Exclamation mark provided by Font Awesome Free 5.11.2 by @fontawesome - https://fontawesome.com. License - https://fontawesome.com/license/free (Icons: CC BY 4.0, Fonts: SIL OFL 1.1, Code: MIT License)
		
	
	Opciones:
		msg: el mensaje a mostrar. Por omisión ninguno.
		cssclass: la clase css básica del globo.
		pos: la posición del pin señalador, puede ser 'bl' (abajo a la izquierda), 'bm' (abajo al centro), 'br' (abajo a la derecha)
		exclam: código HTML con el signo de exclamación.
	
	Ejemplo:
		$("input#").msgerr('Esto es el mensaje de error.');
		
	
*/
(function($) {
	$.fn.msgerr = function (options) {
		this.each(
			function () {
				if (this.type == 'hidden') {
					return false;
				}
				this.defaults = {
					cssclass: 'balloon',
					msg: '',
					pos: 'bl',
					exclam: '<i class="fas fa-exclamation-triangle exclamation"></i> '
				};
				if (typeof options == 'string') {
					options = {msg:options}
				}
				if (this.dataset.msgerrId) {
					$("span#"+this.dataset.msgerrId).remove();
				}
				var thisId = this.id;
				if (thisId == '') {
					thisId = this.name;
					if (thisId == '') {
						thisId = (Math.floor(Math.random()*100000) + 1);
					}
				}
				
				thisId = 'magerr-'+thisId;
				this.dataset.msgerrId = thisId;
				
				
				this.settings = $.extend({}, this.defaults, options);
				
				this.theSpan = document.createElement('SPAN');
				this.theSpan.classList.add(this.settings.cssclass);
				this.theSpan.classList.add(this.settings.pos);
				this.theSpan.setAttribute('id',thisId);
				var theInput = this;
				
				theInput.actualpos = {top:0, left: 0};
				
				this.getOffsetRect = function(elem) {
					var box = elem.getBoundingClientRect();
					var body = document.body
					var docElem = document.documenttheInput
					var scrollTop = window.pageYOffset || docElem.scrollTop || body.scrollTop
					var scrollLeft = window.pageXOffset || docElem.scrollLeft || body.scrollLeft
					var clientTop = docElem.clientTop || body.clientTop || 0
					var clientLeft = docElem.clientLeft || body.clientLeft || 0
					var top = box.top;
					var left = box.left + scrollLeft - clientLeft
					return { top: Math.round(top), left: Math.round(left) }
				} // getOffsetRect
				
				this.Relocate = function () {
					// var rect = theInput.getOffsetRect(theInput);
					var rect = theInput.getClientRects()[0];
					//console.log(rect);
					var Left = (theInput.clientWidth) + rect.left;
					theInput.theSpan.style.top = (rect.top-theInput.theSpan.clientHeight) + "px";
					Left = Left - (theInput.theSpan.clientWidth);
					
					theInput.theSpan.style.left = parseInt(Left) + "px";
				}
				
				this.Show = function () {
					
					//var rect = theInput.getOffsetRect(theInput);
					var rect = theInput.getClientRects()[0];
					var Left = (theInput.clientWidth) + rect.left;
					
					theInput.theSpan.innerHTML = theInput.settings.exclam+theInput.settings.msg;
					
					document.body.appendChild(theInput.theSpan);
					
					theInput.theSpan.style.top = (rect.top-theInput.theSpan.clientHeight) + "px";
					
					Left = Left - (theInput.theSpan.clientWidth);
					
					theInput.theSpan.style.left = parseInt(Left) + "px";
					
				}
				
				this.Hide = function () {
					$(theInput.theSpan).animate({opacity:0},200, 'linear', function () { $(theInput.theSpan).remove(); } );
					$(theInput).removeClass('olred')
				}
				
				$(theInput).focus(theInput.Hide);
				$(theInput).click(theInput.Hide);
				
				theInput.theSpan.addEventListener('click', theInput.Hide);
				window.addEventListener('resize', theInput.Relocate);
				window.addEventListener('scroll', function (e){
					if (window.scrollY != theInput.actualpos.top) {
						rect = theInput.getClientRects()[0];
						theInput.theSpan.style.top = (rect.top-theInput.theSpan.clientHeight) + "px";
						theInput.actualpos.top = window.scrollY;
					}
				});
				
				theInput.Show();
				$(theInput).addClass('olred');
				
			} // function
		); // each
		return false;
	} // function
})(jQuery);
