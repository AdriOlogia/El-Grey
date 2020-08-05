;
/*
	modalBsLte
	Created: 2020-03-22
	Author: DriverOp
	
	Crea una ventana modal al estilo Bootstrap 4 y LTE.
	
	Requiere objEvalresult.js,fmdRulo.js
	
*/

var modalBsLTECreator = function (params) {
	this.defaultOptions = {
		archivo: '',
		content: '',
		extraparams: '',
		windowId: 'modalBsLte',
		onShow: null,
		onClose: null
	}
	
	this.options = Object.assign({},this.defaultOptions,params);
	
	this.objAjax = new objetoAjax("POST");
	
	this.rulo = new fmdRulo({id:this.options.windowId});
	this.evalresult = new mgrEvalResult();
	
	var TheWindow = this;
	
	this.Set = function (newParams) {
		this.options = Object.assign({},this.options,newParams);
	}
	
	this.Show = function () {
		TheWindow.rulo.Show();
		var cadena = '';
		if (TheWindow.options.modulo) {
			cadena = cadena+'&modulo='+TheWindow.options.modulo;
		};
		if (TheWindow.options.archivo) {
			cadena = cadena+'&archivo='+TheWindow.options.archivo;
		};
		if (TheWindow.options.accion) {
			cadena = cadena+'&accion='+TheWindow.options.accion;
		};
		if (TheWindow.options.content) {
			cadena = cadena+'&content='+TheWindow.options.content;
		};
		if (TheWindow.options.elid) {
			cadena = cadena+'&id='+TheWindow.options.elid;
		};
		if (TheWindow.options.extraparams) {
			cadena = cadena+'&'+TheWindow.options.extraparams;
		};
		TheWindow.objAjax.Get('<?php echo URL_ajax; ?>', cadena);
	}
	
	this.Finished = function (a,b,c,d) {
		//console.log('a:'+a+' b:'+b+' c:'+c+' d:'+d);
		TheWindow.rulo.Hide();
		if (a == 200) {
			if (TheWindow.evalresult.Eval(c)) {
				var TheDiv = TheWindow.CreateTheWindow(c);
				document.getElementsByTagName('BODY')[0].appendChild(TheDiv);
				$(TheDiv).modal('show');
				$(TheDiv).on('hidden.bs.modal', function (e) {
					if (TheWindow.options.onClose && (typeof TheWindow.options.onClose == 'function')) {
						TheWindow.options.onClose(TheDiv);
					}
					document.getElementsByTagName('BODY')[0].removeChild(TheDiv);
				});
				if (TheWindow.options.onShow && (typeof TheWindow.options.onShow == 'function')) {
					TheWindow.options.onShow(TheDiv);
				}
			}
		}
	}
	this.objAjax.Finished = this.Finished;
	this.CreateTheWindow = function (TheContent) {
		var TheDiv = document.createElement('DIV');
		TheDiv.classList.add('modal');
		TheDiv.classList.add('fade');
		TheDiv.setAttribute('id',TheWindow.options.windowId);
		TheDiv.setAttribute('tabindex','-1');
		TheDiv.setAttribute('role','dialog');
		TheDiv.setAttribute('aria-labelledby','Label'+TheWindow.options.windowId);
		TheDiv.setAttribute('aria-hidden','true');
		
		var TheModal = document.createElement('DIV');
		TheModal.classList.add('modal-dialog');TheModal.classList.add('modal-lg');
		
		var TheContainer = document.createElement('DIV');
		TheContainer.classList.add('modal-content');
		TheContainer.setAttribute('id',TheWindow.options.windowId+'_content');
		TheContainer.innerHTML = TheContent.replaceAll('%winid%', TheWindow.options.windowId);
		
		TheModal.appendChild(TheContainer);
		TheDiv.appendChild(TheModal);
		
		
		return TheDiv;
	}
	this.Hide = function () {
		$('#'+TheWindow.options.windowId).modal('hide');
	}

}
;