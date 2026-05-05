// JavaScript Document

//********************
//********************
//****
//**** Carrega corpo do diário do registro
//****
//********************
//********************
$(document).on("click", "#SHOW_DIARIO_EXC", function(event) {
	var Chave = $(this).data('id');
	var Campo = $(this).data('campo-id');
	var Caixa = $(this).data('caixa-id');
	var Tabela = $(this).data('tabela-id');
	var Url = $(this).data('url-id');
	$.get(Url + "include/diario/diario_exc2_modal.php?ID=" + Chave + "&CA=" + Campo + "&CX=" + Caixa + "&TB=" + Tabela + "&URL=" + Url, function(data) {
		$('#MODAL_DIARIO_EXC').find('.modal-body').html(data);				
		$('#MODAL_DIARIO_EXC').one('hidden.bs.modal', function () {
			$(document).removeData('bs.modal');
		})
	})		 
});
//********************
//********************
//****
//**** EOF Carrega corpo do diário do registro
//****
//********************
//********************

//********************
//********************
//****
//**** Carrega corpo do diário do registro
//****
//********************
//********************
$(document).on("click", "#SHOW_DIARIO", function() {
	var Chave = $(this).data('id');
	var Campo = $(this).data('campo-id');
	var Tabela = $(this).data('tabela-id');
	var Url = $(this).data('url-id');
	$.get(Url + "include/diario/diario2_modal.php?ID=" + Chave + "&CA=" + Campo + "&TB=" + Tabela + "&URL=" + Url, function(data) {
		$('#MODAL_DIARIO').find('.modal-body').html(data);				
		$('#MODAL_DIARIO').one('hidden.bs.modal', function () {
			$(document).removeData('bs.modal');
		})
	})		 
});
//********************
//********************
//****
//**** EOF Carrega corpo do diário do registro
//****
//********************
//********************

//********************
//********************
//****
//**** Processa teclas de submit do modal
//****
//********************
//********************
$(document).ready(function () {
	// EXCLUSÃO
	// Serialização para submit via modal
	$("#FORM_DIARIO_EXC").on("submit", function(e) {
		var postData = $(this).serializeArray();
		var formURL = $(this).attr("action");
		$.ajax({
			url: formURL,
			type: "POST",
			data: postData,
			success: function(data, textStatus, jqXHR) {
				$('#MODAL_DIARIO_EXC .modal-body').html(data);
				$("#BTN_FORM_DIARIO_EXC_SUBMIT").remove();
			},
			error: function(jqXHR, status, error) {
				console.log(status + ": " + error);
			}
		});
		e.preventDefault();
	});   
	// EOF Serialização para submit via modal
   
	$("#BTN_FORM_DIARIO_EXC_SUBMIT").on('click', function() {
		var vok = true;	
		if (document.FORM_DIARIO_EXC.obs_diario.value == '') {
			vok = false;
			alert("Motivo inválido.");
			document.FORM_DIARIO_EXC.obs_diario.focus();
		}
		if (vok) {
			$("#FORM_DIARIO_EXC").submit();
		}
	});
});
//********************
//********************
//****
//**** EOF Processa teclas de submit do modal
//****
//********************
//********************
// EOF JavaScript Document