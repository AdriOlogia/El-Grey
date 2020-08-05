$(".txtb input").on("focus",function(){
	$(this).addClass("focus");
});

$(".txtb input").on("blur",function(){
	if($(this).val() == "")
		$(this).removeClass("focus");
});
$(function(){$('[data-toggle="tooltip"]').tooltip();});


var evalresult = new mgrEvalResult;
var rulo = new fmdRulo();


function CheckLogin() {
	var result = true;
	var frm = document.getElementById('loginform');
	var ele = frm.username;

	if (ele.value.trim().length == 0) {
		result = $(ele).msgerr({msg:'Ingrese su nombre de usuario'});
	} else {
		if (!CheckUsername(ele)) {
			result = false;
		}
	}

	ele = frm.password;
	if (ele.value.trim().length == 0) {
		result = $(ele).msgerr({msg:'Escriba la contraseña'});
	} else {
		if (!CheckPassword(ele)) {
			result = false;
		}
	}
	
	if (result) {
		$("#btn-acceder").attr('disabled','disabled');
		$("#btn-acceder span.accesing").show();
		$("#btn-acceder span.btn-label").html('Accediendo...');
		getAjax({
			archivo:'checkLogin',
			extraparams:'username='+frm.username.value+'&password='+frm.password.value
		}, function (a,b,c,d) {
			$("#btn-acceder span.accesing").hide();
			$("#btn-acceder span.btn-label").html('Acceder');
			$("#btn-acceder").removeAttr('disabled');
<?php
	if (DEVELOPE) {
?>
			//Debug(c);
<?php
	}
?>
			if (evalresult.Eval(c)) {
				window.location.reload();
			}
		});
	}
	return result;
}


function ForgotPass() {
	rulo.Show();
	getAjax({
		archivo: 'frm_forgotPass'
	}, function (a,b,c,d) {
<?php
	if (DEVELOPE) {
?>
		//Debug(c);
<?php
	}
?>
		if (a == 200) {
			rulo.Hide();
			document.getElementById('form_forgot').innerHTML = c;
			$("#loginform").hide();
			$("div#form_forgot").show();
			$(function(){$('[data-toggle="tooltip"]').tooltip();});
		}
	}
	);
}

function GoBack() {
	$("#loginform").show();
	$("div#form_forgot").hide();
}

function CheckTel() {
	var result = true;
	var codigo_area = document.getElementById('codigo_area');
	var numero_abonado = document.getElementById('numero_abonado');

	if (codigo_area.value.trim().length == 0) {
		result = $(codigo_area).msgerr('Ingresá el código de área');
	} else {
		if (!isNumeric(codigo_area.value.trim())) {
			result = $(codigo_area).msgerr('Debe ser un número');
		} else {
			if ((codigo_area.value.trim().length < 2) || (codigo_area.value.trim().length > 4)) {
				result = $(codigo_area).msgerr('De 2 a 4 cifras');
			}
		}
	}

	if (numero_abonado.value.trim().length == 0) {
		result = $(numero_abonado).msgerr('Ingresá el número de abonado');
	} else {
		if (!isNumeric(numero_abonado.value.trim())) {
			result = $(numero_abonado).msgerr('Debe ser un número');
		} else {
			if ((numero_abonado.value.trim().length < 6) || (numero_abonado.value.trim().length > 8)) {
				result = $(numero_abonado).msgerr('De 6 a 8 cifras');
			}
		}
	}
	if (result) {
		rulo.Show('login-area');
		getAjax({
			archivo: 'checkTel',
			content: 'login',
			extraparams: 'codigo_area='+codigo_area.value.trim()+'&numero_abonado='+numero_abonado.value.trim()
		}, function (a,b,c,d) {
			//Debug(c);
			if (evalresult.Eval(c)) {
				getAjax({
					archivo: 'frm_askPIN',
					content: 'login'
				}, function (a,b,c,d) {
					//Debug(c);
					if (evalresult.Eval(c)) {
						rulo.Hide();
						document.getElementById('form_forgot').innerHTML = c;
					}
				})
			} else {
				rulo.Hide();
			}
		});
	}
} // CheckTel

function CheckByEmail() {
	var result = true;
	var ele = document.getElementById('email');
	if (ele.value.trim().length == 0) {
		result = $(ele).msgerr('Debes indicar tu dirección de correo electrónico');
	}
	if (result) {
		if (!CheckEmail(ele)) {
			result = false;
		}
	}
	
	if (result) {
		rulo.Show('login-area');
		getAjax({
			archivo: 'checkEmail',
			content: 'login',
			extraparams: 'email='+ele.value.trim()
		}, function (a,b,c,d) {
			//Debug(c);
			if (evalresult.Eval(c)) {
				getAjax({
					archivo: 'frm_askJumble',
					content: 'login'
				}, function (a,b,c,d) {
					//Debug(c);
					if (evalresult.Eval(c)) {
						rulo.Hide();
						document.getElementById('form_forgot').innerHTML = c;
					}
				})
			} else {
				rulo.Hide();
			}
		});
	}
} // CheckBymail

function CheckPIN() {
	var result = true;
	var pin = document.getElementById('pin');
	if (pin.value.trim().length == 0) {
		result = $(pin).msgerr('Debes ingresar un número.');
	} else {
		if (!isNumeric(pin.value.trim())) {
			result = $(pin).msgerr('Deben ser solo números.');
		}
	}
	if (result) {
		rulo.Show('login-area');
		getAjax({
			archivo: 'checkPIN',
			content: 'login',
			extraparams: 'PIN='+pin.value.trim()
		}, function (a,b,c,d) {
			//Debug(c);
			if (evalresult.Eval(c)) {
				getAjax({
					archivo: 'frm_askPass',
					content: 'login'
				}, function (a,b,c,d) {
					//Debug(c);
					if (evalresult.Eval(c)) {
						rulo.Hide();
						document.getElementById('form_forgot').innerHTML = c;
					}
				});
			} else {
				rulo.Hide();
			}
		});
	}
} // CheckPIN

function CheckJumble() {
	var result = true;
	var jumble = document.getElementById('jumble');
	if (jumble.value.trim().length == 0) {
		result = $(jumble).msgerr('Debes ingresar un código.');
	} else {
		if (jumble.value.trim().length != 8) {
			result = $(jumble).msgerr('El código son exactamente ocho caracteres.');
		}
	}
	if (result) {
		rulo.Show('login-area');
		getAjax({
			archivo: 'checkJumble',
			content: 'login',
			extraparams: 'jumble='+jumble.value.trim()
		}, function (a,b,c,d) {
			//Debug(c);
			if (evalresult.Eval(c)) {
				getAjax({
					archivo: 'frm_askPass',
					content: 'login'
				}, function (a,b,c,d) {
					//Debug(c);
					if (evalresult.Eval(c)) {
						rulo.Hide();
						document.getElementById('form_forgot').innerHTML = c;
					}
				});
			} else {
				rulo.Hide();
			}
		});
	}
}

function CheckNewPass() {
	var result = true;
	var newpass1 = document.getElementById('newpass1');
	if (newpass1.value.trim().length == 0) {
		result = $(newpass1).msgerr('Debes ingresar una contraseña.');
	} else {
		if (newpass1.value.trim().length < 8) {
			result = $(newpass1).msgerr('Deben ser al menos 8 caracteres.');
		} else {
			if (newpass1.value.trim().length > 32) {
				result = $(newpass1).msgerr('Deben ser hasta 32 caracteres.');
			} else {
				if (!CheckPassword(newpass1)) {
					result = false;
				}
			}
		}
	}
	if (result) {
		var newpass2 = document.getElementById('newpass2');
		if (newpass2.value != newpass1.value) {
			result = $(newpass2).msgerr('La contraseña debe coincidir');
		}
	}
	if (result) {
		rulo.Show();
		getAjax({
			archivo: 'frm_SetNewPass',
			content: 'login',
			extraparams: 'newpass='+newpass1.value.trim()
		},function (a,b,c,d) {
			rulo.Hide();
			if (evalresult.Eval(c)) {
				document.getElementById('form_forgot').innerHTML = c;
			}
		});
	}
}

function GenNewPass() {
	var newpass1 = document.getElementById('newpass1');
	newpass1.setAttribute('type','text');
	newpass1.setAttribute('disabled','disabled');
	var newpass2 = document.getElementById('newpass2');
	newpass2.setAttribute('type','text');
	newpass2.setAttribute('disabled','disabled');
	getAjax({
		archivo: 'genPass',
		content: 'login'
	},function (a,b,c,d) {
		newpass.removeAttribute('disabled');
		if (evalresult.Eval(c)) {
			newpass1.value = evalresult.TheResult.ok;
			newpass2.value = evalresult.TheResult.ok;
		}
	});
}