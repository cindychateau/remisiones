var contacto = 1;
$(document).ready(function(){

	$(document).on('click','.agregar-contacto',function(e) {
		e.preventDefault();
		contacto++;
		nuevo_contacto = '<div id="contacto_'+contacto+'">'+
                         '     <div class="row">'+
                         '       <div class="col-sm-12">'+
                         '         <div class="form-group row">'+
                         '           <label class="col-sm-2 label-control" for="contacto_nombre_'+contacto+'">Nombre Completo*</label>'+
                         '           <div class="col-sm-10">'+
                         '             <input type="text" id="contacto_nombre_'+contacto+'" class="form-control border-primary" name="contacto_nombre['+contacto+']">'+
                         '           </div>'+
                         '         </div>'+
                         '       </div>'+
                         '     </div>'+
                         '     <div class="row">'+
                         '       <div class="col-md-12">'+
                         '         <div class="form-group row">'+
                         '           <label class="col-sm-2 label-control" for="contacto_area_'+contacto+'">Área</label>'+
                         '           <div class="col-sm-4">'+
                         '             <input type="text" id="contacto_area_'+contacto+'" class="form-control border-primary" name="contacto_area['+contacto+']">'+
                         '           </div>'+
                         '           <label class="col-sm-2 label-control" for="contacto_puesto_'+contacto+'">Puesto</label>'+
                         '           <div class="col-sm-4">'+
                         '             <input type="text" id="contacto_puesto_'+contacto+'" class="form-control border-primary" name="contacto_puesto['+contacto+']">'+
                         '           </div>'+
                         '         </div>'+
                         '       </div>'+
                         '     </div>'+
                         '     <div class="row">'+
                         '       <div class="col-md-12">'+
                         '         <div class="form-group row">'+
                         '           <label class="col-sm-2 label-control" for="contacto_email_'+contacto+'">E-mail*</label>'+
                         '           <div class="col-sm-4">'+
                         '             <input type="text" id="contacto_email_'+contacto+'" class="form-control border-primary" name="contacto_email['+contacto+']">'+
                         '           </div>'+
                         '           <label class="col-sm-2 label-control" for="contacto_telefono_'+contacto+'">Teléfono*</label>'+
                         '           <div class="col-sm-4">'+
                         '             <input type="text" id="contacto_telefono_'+contacto+'" class="form-control border-primary" name="contacto_telefono['+contacto+']">'+
                         '           </div>'+
                         '         </div>'+
                         '       </div>'+
                         '     </div>'+
                         '     <div class="row">'+
                         '       <div class="col-md-12">'+
                         '         <div class="form-group row">'+
                         '           <label class="col-sm-2 label-control" for="contacto_celular_'+contacto+'">Celular</label>'+
                         '           <div class="col-sm-4">'+
                         '             <input type="text" id="contacto_celular_'+contacto+'" class="form-control border-primary" name="contacto_celular['+contacto+']">'+
                         '           </div>'+
                           '		<div class="form-group col-sm-2 offset-4 text-center mt-2">'+
                           '             <button type="button" class="btn btn-danger eliminar-contacto" data-id="'+contacto+'">'+
                           '             	<i class="ft-x"></i> Eliminar <br>Contacto'+
                           '             </button>'+
                           '         </div>'+
                           '       </div>'+
                           '     </div>'+
                           '   </div>'+
                           '   <hr>'+
                           ' </div>';

        $('#cont-contacto').append(nuevo_contacto);
	});

	$(document).on('click','.eliminar-contacto',function(e) {
		e.preventDefault();
		cont = $(this).attr("data-id");
		$('#contacto_'+cont).remove();
	});
	
	/*
	 * @author: Cynthia Castillo
	 * 
	 * Guardar
	 */
	$(document).on('click','.guardar',function(e) {
		e.preventDefault();
		$('#frm-marca').submit();
	});

	$(document).on('submit','#frm-marca',function(e) {
		e.preventDefault();
    	var formdata = new FormData($('form[id="frm-marca"]')[0]);
		$.ajax({
			type: 'POST',
			url: 'include/Libs.php?accion=saveRecord',
			data: formdata,
  			processData: false,
  			contentType: false,
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
								} else {
									window.location = "index.php";
								}
							}
						}
					}
				});
			}
		});
	});

});
