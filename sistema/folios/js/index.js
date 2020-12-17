$(document).ready(function(){
	
	getRecord();

	$(document).on('click','.guardar',function(e) {
		e.preventDefault();
		$('#frm-password').submit();
	});

	$(document).on('submit','#frm-password',function(e) {
		e.preventDefault();
		$.ajax({
			type: 'POST',
			url: 'include/Libs.php?accion=saveRecord',
			data: $('#frm-password').serialize(),
			dataType:'json',
			beforeSend: function(){
				$('input, file, textarea, button, select').each(function(){
					$(this).attr('disabled','disabled');
				});
			},
			error: function(){
				$('input, file, textarea, button, select').each(function(){
					$(this).removeAttr('disabled');
				});
				bootbox.dialog({
					message: "Experimentamos Fallas Técnicas. Comuníquese con su proveedor.",
					buttons: {
						cerrar: {
							label: "Cerrar",
							callback: function() {
								bootbox.hideAll();
							}
						}
					}
				});
			},
			success: function(result){
				$('input, file, textarea, button, select').each(function(){
					$(this).removeAttr('disabled');
				});
				bootbox.dialog({
					message: result.msg,
					buttons: {
						cerrar: {
							label: "Cerrar",
							callback: function() {
								if(result.error) {
									bootbox.hideAll();
									$('#'+result.focus).focus();
								}
							}
						}
					}
				});
			}
		});
	});

});

function getRecord() {
	$.ajax({
		type: 'POST',
		url: 'include/Libs.php?accion=showRecord',
		dataType:'json',
		beforeSend: function(){
			$('#table-content').html("<div class='loader'></div>");
		},
		error: function(){
			bootbox.dialog({
				message: "Experimentamos Fallas Técnicas. Comuníquese con su proveedor.",
				buttons: {
					cerrar: {
						label: "Cerrar",
						callback: function() {
							bootbox.hideAll();
							window.location = "index.php";
						}
					}
				}
			});
		},
		success: function(result){
			if(!result.error){
				$("#folio-1").val(result.folio_1);
				$("#folio-2").val(result.folio_2);
				$("#folio-3").val(result.folio_3);
			}
			else {
				window.location = "index.php";
			}
		}
	});	
}
