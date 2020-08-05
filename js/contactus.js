
var rulo = new fmdRulo();
var evalresult = new mgrEvalResult();


function SendForm() {
	var frm = document.getElementById('frmContacus');
	var result = true;
	var ele = null;
	ele = frm.companyName;

	ele = frm.industry;
	
	ele = frm.country;
	
	ele = frm.contactName;
	
	ele = frm.titleName;
	
	ele = frm.telNumber;
	if (ele.value.trim().length != 0) {
		if (!CheckTel(ele)) {
			result = false;
		}
	}
	
	ele = frm.email;
	if (ele.value.trim().length == 0) {
		result = $(ele).msgerr('Please provide an email address');
	} else {
		if (!CheckEmail(ele)) {
			result = false;
		}
	}
	
	if (result) {
		rulo.Show('frmContacus');
		$(frm).fmdFormSend({
			url: '<?php echo URL_ajax; ?>',
			extraData: {
				archivo: 'getForm'
			},
			onFinish: function (a,b,c,d) {
				rulo.Hide();
				//Debug(c);
				if (evalresult.Eval(c)) {
					frm.reset();
					$('.bd-example-modal-lg').modal('show');
				}
			}
		});
	}
}