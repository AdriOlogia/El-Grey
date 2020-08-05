;/*
	Autocomplete ver 1.1
	Author: Fivemedia.
	Developer: DriverOp
	Created: 2019-04-10
	Desc: Porque me cansé de probar autocompletadores que tienen todo mal hecho o hecho a la necesidad del programador del plugin y no de quien lo va a usar. Esta es mi versión.

	Modif: 2020-02-06
	Desc: Reparado bug en regex que evalúa la tecla presionada
	Requiere: fmdAutocomplete.css
	Documentación en el archivo fmdAutocomplete.doc.txt
	
	Modif: 2020-03-20
	Desc: Ahora se posiciona en relación al body y no en relación a la ventana.
		Ojo con la posición del UL que acá está cambiada. Se agregó la opción adjTop para ajustar la posición vertical del <UL>. Por alguna razón que no entiendo, en el modal de LTE getClientRects() miente con la posición real.
*/

(function($){
	$.fn.fmdAutocomplete = function(options) {
		var idcounter = 0;
		var tt = null;
		this.each(
			function () {
				var element = this;
				if (element.type == 'hidden') {
					return false;
				}
				var defaults = {
					source: window.location.href,
					select: null,
					delay: 250,
					adjTop: 0
				}
				if (typeof options == 'string') {
					options.toLowerCase();
					switch(options) {
						case 'hide': {
							var ul_id = 'fmd_autocomplete_'+element.getAttribute('fmd_autocomplete');
							var ul = document.getElementById(ul_id);
							document.getElementsByTagName('body')[0].removeChild(ul);
							/*
							var list = document.querySelectorAll('ul.fmd-autocomplete');
							for(i=(list.length-1);i>=0;i--) {
								document.getElementsByTagName('body')[0].removeChild(list[i]);
							}
							*/
							break;
						}
						case 'remove': {
							var ul_id = 'fmd_autocomplete_'+element.getAttribute('fmd_autocomplete');
							var ul = document.getElementById(ul_id);
							document.getElementsByTagName('body')[0].removeChild(ul);
						}
					}
					return element;
				}
				var settings = $.extend({}, defaults, options);
				
				var autocompleteExist = element.getAttribute('autocomplete');
				
				if (!autocompleteExist) {
					var theID = ++idcounter;
					var theUl = document.createElement('ul');
					theUl.classList.add('fmd-autocomplete');
					while (document.getElementById('fmd_autocomplete_'+theID)) { theID = ++idcounter; }
					theUl.setAttribute('id','fmd_autocomplete_'+theID);
					element.setAttribute('fmd_autocomplete',theID);
					element.classList.add('frm-autocomplete-trigger');
					
					$(element).on('keyup', function (e) {
						//console.log('Tecla: '+e.which);
						switch (e.which) {
							case 9: theUl.style.display = 'none'; element.setAttribute('autocomplete','off'); break; // Tab
							case 16: theUl.style.display = 'none'; element.setAttribute('autocomplete','off'); break; // Alt+tab
							case 27: theUl.style.display = 'none'; element.setAttribute('autocomplete','off'); break; // Esc
							case 38: Prev(element, theUl); break; // Arrow Up
							case 40: Sig(element, theUl); break; // Arrow Down
							case 13: Select(element, theUl); break; // Enter
						}
						
						if (/^(38|40|27|16|13|9)$/.test(e.which)) return
						if (tt != null) {
							clearTimeout(tt);
						}
						if (element.value.replace(/^\s+|\s+$/gm,'') == '') { return; } // Evita que se dispare la petición ajax con una consulta vacía.
						tt = setTimeout(function () {
							triggerAjax(element, theUl, settings);
						}, settings.delay);
						e.preventDefault()
						e.stopPropagation()
					});
			
					
				} else {
					var theUl = document.getElementById('fmd_autocomplete_'+element.getAttribute('fmd_autocomplete'));
				}
				element.setAttribute('autocomplete','off');
				theUl.style.display = 'none';
				document.getElementsByTagName('BODY')[0].style.position = 'relative';
				document.getElementsByTagName('BODY')[0].appendChild(theUl);
			} // function 
		) // each
		
		function Select(ele, theUl) {
			var lis = $(theUl).children('li.fmd-autocomplete-item.selected').first();
			ele.setAttribute('autocomplete','off');
			theUl.style.display = 'none';
			lis.click();
		}

		function Sig(ele, theUl) {
			var lis = $(theUl).children('li.fmd-autocomplete-item.selected').first();
			if (lis.length == 0) {
				lis = $(theUl).children('li.fmd-autocomplete-item').first();
			} else {
				$(theUl).children('li.fmd-autocomplete-item').removeClass('selected');
				lis = $(lis).next('li.fmd-autocomplete-item');
				if (lis.length == 0) {
					lis = $(theUl).children('li.fmd-autocomplete-item').first();
				}
			}
			lis.addClass('selected');
		}

		function Prev(ele, theUl) {
			var lis = $(theUl).children('li.fmd-autocomplete-item.selected').first();
			if (lis.length == 0) {
				lis = $(theUl).children('li.fmd-autocomplete-item').last();
			} else {
				$(theUl).children('li.fmd-autocomplete-item').removeClass('selected');
				lis = $(lis).prev('li.fmd-autocomplete-item');
				if (lis.length == 0) {
					lis = $(theUl).children('li.fmd-autocomplete-item').last();
				}
			}
			lis.addClass('selected');
		}
	
		function triggerAjax(ele, theUl, settings) {
			$(ele).addClass('fmd-autocomplete-loading');
			$.ajax(settings.source,{
				dataType: 'json',
				method: 'POST',
				data: {term: ele.value.replace(/^\s+|\s+$/gm,'')},
				error: function (jq, textStatus, errorThrrown) { console.log('textStatus: '+textStatus); }
				
			}).done(function (data) {
				$(ele).removeClass('fmd-autocomplete-loading');
				$(theUl).empty();
				ele.setAttribute('autocomplete','on');
				if (data.length > 0) {
					for (var i=0;i<data.length;i++) {
						var li = document.createElement('li');
						li.classList.add('fmd-autocomplete-item');
						if (data[i].label) {
							li.innerHTML = data[i].label;
						} else {
							li.innerHTML = data[i];
						}
						li.frmdata = data[i];
						/* Esto es lo que pasa cuando el usuario selecciona un resultado */
						li.addEventListener("click", function () { 
							if (typeof this.frmdata == 'string') {
								ele.value = this.frmdata;
							} else {
								if (this.frmdata.value) {
									ele.value = this.frmdata.value;
								} else {
									if (this.frmdata.label) {
										ele.value = this.frmdata.label;
									}
								}
							}
							if (settings.select && (typeof settings.select == 'function')) {
								settings.select(this, this.frmdata);
							}
						});
						$(theUl).append(li);
					}
					var rect = ele.getBoundingClientRect();
					theUl.style.width = rect.width+'px';
					//theUl.style.top = (rect.top+rect.height)+'px';
					theUl.style.top = ((rect.top+rect.height)+(settings.adjTop))+'px'; // El viejo truco...
					theUl.style.left = rect.left+'px';
					theUl.style.display = 'block';
				} else {
					$(theUl).html('<li class="fmd-autocomplete-item">No hay resultados.</li>');
				}
			});
		}
/* El <body> debe tener position: relative */
		document.getElementsByTagName('body')[0].style.position = 'relative';

/* Si se hace clic en cualquier parte, hay que cerrar el UL */
		document.getElementsByTagName('body')[0].addEventListener("mouseup", function () {
			$('ul.fmd-autocomplete').hide();
			$('input.frm-autocomplete-trigger').attr('autocomplete','off');
		});

/* Si la ventana se redimensiona, hay que redimensionar los UL según cómo se redimensionen los inputs al que están atados. */
		window.addEventListener('resize', function () {
			$('input.frm-autocomplete-trigger').each(function () {
				theUl = document.getElementById('fmd_autocomplete_'+this.getAttribute('fmd_autocomplete'));
				if (theUl) {
					var rect = this.getBoundingClientRect();
					theUl.style.width = rect.width+'px';
					theUl.style.top = ((rect.top+rect.height)+(settings.adjTop))+'px'; // El viejo truco...
					theUl.style.left = rect.left+'px';
				} // if
			}); // each
		}); // addEventListener
		
		

	} // function Autocomplete
})(jQuery);
