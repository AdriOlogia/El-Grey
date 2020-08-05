;
/*
	checkinputs.js
	created: 2012-04-18
	modified: 2013-04-18
	Modified: 2013-12-12

	Modif-desc: 
		- Se elimino SetMsjErr la función.
		- El plugin jquery.fmdmsgerr.js se agrego como dependencia.
		- En caso de no existir el plugin requerido, se retorna FALSE si la validación no es exitosa.
	Modificado: 2014-06-16
*/
function PonerMensaje(ele, opciones, textmsg){
	var result = false;
	if (typeof $().msgerr == 'function') {
		if (opciones == null) {
			opciones = {msg : textmsg};
		} else {
			opciones.msg = textmsg;
		}
		result = $(ele).msgerr(opciones);
	}
	return result;
}

function CheckName(ele, opciones) {
	var result = true;
	var valor = ele.value;
	if (!/^[A-Za-zƒŠŒŽšœžŸÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýþÿ_\-\s']*$/i.test(valor.trim())) {
		result = PonerMensaje(ele, opciones, "This name is not valid.");
	}
	return result;
}

function CheckTel(ele,opciones) {
	var result = true;
	var valor = ele.value;
	if (/^[ext\d\-\/\+\(\)\s\.]{6,}$/im.test(valor.trim()) == false) {
		result = PonerMensaje(ele, opciones, "Invalid telephone number format.");
	}
	return result;
}

function CheckDirPos(ele, opciones) {
	var result = true;
	var valor = ele.value;
	if (!/^[0-9A-Za-zƒŠŒŽšœžŸÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýþÿ_\-\s\.\(\),']*$/i.test(valor.trim())) {
		if (opciones == null) {
			opciones = 'Invalid postal address.';
		}
		result = PonerMensaje(ele, null, opciones);
	}
	return result;
}

function CheckDirNro(ele) {
	var result = true;
	var valor = ele.value;
	if (!/^[0-9KkMmNnSs\/\-\.]*$/i.test(valor.trim())) {
		result = PonerMensaje(ele, null, "Invalid door number.");
	}
	return result;
}

function CheckCPA(ele) {
	var result = true;
	var valor = ele.value;
	var textmsg = null;
	if (/^[a-zA-Z]?\d{4}([a-zA-Z]{3})?$/im.test(valor.trim()) == false) {
		textmsg = "Invalid postal code.<br />Must be four numbers or a letter, four numbers followed by three letters.";
	} else {
		if (/^\d{4}$/im.test(valor.trim()) == false) {
			if (/^[a-zA-Z]?\d{4}$/im.test(valor.trim())) {
				textmsg = "Invalid postal code.<br />Must be four numbers or a letter, four numbers followed by three letters.";
			} else {
				if (/^\d{4}([a-zA-Z]{3})?$/im.test(valor.trim())) {
					textmsg = "Invalid postal code.<br />Must be four numbers or a letter, four numbers followed by three letters.";
				}
			}
		}
	}
	if (textmsg !== null) {
		result = PonerMensaje(ele, null, textmsg);
	};
	return result;
}

function CheckEmail(ele, opciones) {
	var result = true;
	var valor = ele.value;
	if (!ValidarCorreo(valor.trim())) {
		result = PonerMensaje(ele, opciones, "Invalid email address.");
	}
	return result;
}

function CheckDNI(ele) {
	var result = true;
	var valor = ele.value;
	var textmsg = null;
	if (!valor.isint()) {
		textmsg = "Esto no es un DNI válido.<br /> El DNI deben ser solo números.";
	} else {
		if (valor.trim().length < 7) {
			textmsg = "DNI no válido.<br /> El DNI debe ser de 7 cifras o más.";
		} else {
			if (valor.trim().length > 8) {
				textmsg = "DNI no válido.<br /> El DNI no debe ser mayor a 8 cifras.";
			}
		}
	}
	if (textmsg !== null) {
		result = PonerMensaje(ele, null, textmsg);
	};
	return result;
}

function CheckPass(ele) {
	var result = true;
	var valor = ele.value;
	var textmsg = null;
	if (valor.trim().length < 8) {
			textmsg = "Nº de Pasaporte no válido.<br /> Debe ser de 8 cifras o más.";
	} else {
		if (valor.trim().length > 11) {
			textmsg = "Nº de Pasaporte no válido.<br /> No debe ser mayor a 11 cifras.";
		} else {
			if (/^[0-9A-Za-z]*$/im.test(valor.trim())==false) {
				textmsg = "Esto no es un Nº de Pasaporte válido.";
			}
		}
	}
	if (textmsg !== null) {
		result = PonerMensaje(ele, null, textmsg);
	};
	return result;
}

function CheckCUIT(ele) {
	var result = true;
	var textmsg = null;
	cuit = ele.value.trim().toString().replace(/[-_]/g, "");
	if (cuit.length != 11) {
		textmsg = "CUIT no válido. El CUIT debe ser de 11 cifras.";
	} else {
		var mult = [5, 4, 3, 2, 7, 6, 5, 4, 3, 2];
		var total = 0;
		for (var i = 0; i < mult.length; i++) {
			total += parseInt(cuit[i]) * mult[i];
		}
		var mod = total % 11;
		var digito = mod == 0 ? 0 : mod == 1 ? 9 : 11 - mod;
		if (digito != parseInt(cuit[10])) {
			textmsg = "CUIT no válido.";
		};
	}
	if (textmsg !== null) {
		result = PonerMensaje(ele, null, textmsg);
	};
	return result;
}

function CheckUsername(ele) {
	var result = true;
	var valor = ele.value;
	if (/^[0-9A-Za-z\-]*$/im.test(valor.trim()) == false) {
		result = PonerMensaje(ele, null, "Nombre de usuario no válido. Retire los caracteres extraños.");
	}
	return result;
}

function CheckPassword(ele) {
	var result = true;
	var textmsg = null;
	var valor = ele.value;
	if (/\s+/im.test(valor.trim())) {
		textmsg = "La contraseña no debe contener espacios.";
	} else {
		if (/^[0-9A-Za-z]*$/im.test(valor.trim()) == false) {
			textmsg = "La contraseña solo debe tener números y/o letras (sin acentos).";
		}
	}
	if (textmsg !== null) {
		result = PonerMensaje(ele, null, textmsg);
	};
	return result;
}

function CheckNumber(ele,min,max) {
	var result = true;
	var textmsg = null;
	var valor = ele.value;
	if (/^[0-9]+$/.test(valor.trim()) == false) {
		textmsg = "Debe ser un número.";
	} else {
		valor = parseInt(valor,10);
		if (valor < min) {
			textmsg = "Debe ser un número mayor o igual a "+min+".";
		} else {
			if (valor > max) {
				textmsg = "Debe ser un número menor o igual a "+max+".";
			}
		}
	}
	if (textmsg !== null) {
		result = PonerMensaje(ele, null, textmsg);
	};
	return result;
}

function CheckHora(ele,opciones) {
	var valor = ele.value.trim();
	var result = true;
	if (/^[0-2]?\d:[0-5]?\d:[0-5]?\d$/.test(valor) == false) {
		result = PonerMensaje(ele, opciones, "Formato incorrecto.");
	}
	return result;
}

function CheckFecha(ele, opciones) {
	if (ele.getAttribute('disabled') == 'disabled') { return; }
	var result = false;
	var textmsg = null;
	var valor = ele.value.trim();
	var formato = 'no-valido';
	
	if (/^[0-3]?[0-9](-|\/)[0-1]?[0-9](-|\/)[0-9]{2}$/.test(valor)) { // Es formato latino año a dos cifras?
		formato = 'latin';
		año = valor.substr(-2);
		año = ((año < '50')?'20':'19')+año;
		valor = valor.substr(0,(valor.length-2))+año;
	} else {
		if (/^[0-3]?[0-9](-|\/)[0-1]?[0-9](-|\/)[0-9]{4}$/.test(valor)) { // Es formato latino año a cuatro cifras?
			formato = 'latin';
		}
	}

	if (formato == 'no-valido') {
		if (/^[0-9]{2}-[0-1]?[0-9]-[0-3]?[0-9]$/.test(valor)) { // Es formato ISO año a dos cifras?
			formato = 'ISO';
			valor = ((valor.substr(0,2) < '50')?'20':'19')+valor;
		} // if
		else {
			if (/^[0-9]{4}-[0-1]?[0-9]-[0-3]?[0-9]$/.test(valor)) { // Es formato ISO año a cuatro cifras?
				formato = 'ISO';
			}
		} // else
	}
	if (formato != 'no-valido') {
		var arr;
		if (formato == 'latin') {
			arr = valor.replace(/\//g,'-').split('-');
			arr.reverse();
		} else {
			arr = valor.split('-');
		}
		var d = new Date(parseInt(arr[0],10), parseInt(arr[1],10)-1, parseInt(arr[2],10), 0, 0, 0, 0);
		if (ele.type == 'date') {
			ele.value = d.getFullYear()+"-"+(d.getMonth()+1).lpad(2)+"-"+d.getDate().lpad(2);
		} else {
			ele.value = d.getDate().lpad(2)+"/"+(d.getMonth()+1).lpad(2)+"/"+d.getFullYear();
		}
		result = true;
	}
	if (!result) {
		PonerMensaje(ele, null, 'La fecha no es válida');
	};
	
	return result;
}

function CheckVin(ele) {
	var result = true;
	if (ele.value.trim().length < 17) {
		result = PonerMensaje(ele, null, "VIN no es válido.");
	} else {
		if (ele.value.trim().length > 19) {
			ele.value = ele.value.slice(0,18);
		}
	}
	return result;
}

function CheckDominio(ele) {
	var result = true;
	if ((/^[a-zA-Z]{3}\d{3}$/.test(ele.value.trim()) == false) && (/^[a-zA-Z]{2}\d{3}[a-zA-Z]{2}$/.test(ele.value.trim()) == false)) {
		result = SetMsgErr(ele, 'Dominio no es válido.');
	}
	return result;
}

function CheckCBU(ele) {
	var result = true;
	ele.value.replace(' ','');
	ele.value.replace('-','');
	if (/^[0-9]+$/.test(ele.value.trim()) == false) {
		result = PonerMensaje(ele, null, "Deben ser solo números.");
	} else {
		if (ele.value.trim().length != 22) {
			result = PonerMensaje(ele, null, "CBU debe ser exactamente 22 números.");
		}
	}
	return result;
}

var isRadioSelected = function(radios) {
	for(var i=0; i<radios.length; i++) {
		if( radios[i].checked ) {
			return true;
		}
	}

	return false;
};

function CalcularEdad(fecha) {
	if (!/^[0-9]{4}-[0-1]?[0-9]-[0-3]?[0-9]$/.test(fecha)) {
		console.log('Fecha debe estar en formato ISO');
		return null;
	}
	var hoy = new Date();
    var cumpleanos = new Date(fecha);
    var edad = hoy.getFullYear() - cumpleanos.getFullYear();
    var m = hoy.getMonth() - cumpleanos.getMonth();

    if (m < 0 || (m === 0 && hoy.getDate() < cumpleanos.getDate())) {
        edad--;
    }

    return edad;
	
}