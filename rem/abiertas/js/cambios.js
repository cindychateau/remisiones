var info = 1;
var renglones_3g = 24;
var renglones_alliance = 15;
var renglones_sky = 15;
$(document).ready(function(){

	getRecord();

	$(document).on('click','.btn-no',function(e) {
        e.preventDefault();
    });

    $(document).on('change','#cliente',function(e) {
        getContacto();
    });

    $(document).on('click','.recargar-ejecutivos',function(e) {
        e.preventDefault();
        getEjecutivos();
    });

    $(document).on('click','.recargar-clientes',function(e) {
        e.preventDefault();
        getClients();
    });    

    $(document).on('click','.recargar-contacto',function(e) {
        e.preventDefault();
        getContacto();
    }); 

    $(document).on('click','.add-rem',function(e) {
        e.preventDefault();
        //Verificamos si no ha llegado al límite de su empresa
        var empresa = $('#empresa').val();
        var cant_renglones = $('.rem tr').length - 1;
        //console.log(cant_renglones);
        var limite = 1;
        if(empresa == 'Alliance Soluciones') {
          limite = renglones_alliance;
        } else if (empresa == '3G Consulting y Asesoría') {
          limite = renglones_3g;
        } else if (empresa == 'Sky Consulting Partners') {
          limite = renglones_sky;
        }

        if(limite > cant_renglones) {
          var html_rem = '<tr>'+
                          '  <td>'+
                          '    <input type="text" name="cantidad['+info+']" class="form-control">'+
                          '  </td>'+
                          '  <td>'+
                          '    <input type="text" name="importe['+info+']" class="form-control">'+
                          '  </td>'+
                          '  <td>'+
                          '    <textarea class="form-control" name="descripcion['+info+']"></textarea>'+
                          '  </td>'+
                          '  <td><i class="ft ft-trash-2"></i></td>'+
                          '</tr>';
          $('.rem tbody').append(html_rem);
          info++;
        } else {
          bootbox.alert("El límite de renglones para <b>"+empresa+"</b> es de "+limite);
        }
    }); 

    $(document).on('click','.ft-trash-2',function(e) {
      e.preventDefault();
      $(this).closest('tr').remove();
      if($(this).hasClass('eliminar')) {
      	var info_id = $(this).attr("data-id");
      	var html_elim = '<input type="hidden" name="del_info_id[]" value="'+info_id+'">';
      	$('.rem tbody').append(html_elim);
      }
  });
	

	/*
	 * @author: Cynthia Castillo
	 * 
	 * Guardar
	 */
	$(document).on('click','.guardar',function(e) {
		e.preventDefault();
		$('#frm-cliente').submit();
	});

	$(document).on('submit','#frm-cliente',function(e) {
		e.preventDefault();
		$.ajax({
			type: 'POST',
			url: 'include/Libs.php?accion=saveRecord',
			data: $('#frm-cliente').serialize(),
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

function getRecord() {
	params = {};
	params.id = $("#id").val();
	$.ajax({
		type: 'POST',
		url: 'include/Libs.php?accion=showRecord',
		dataType:'json',
		data: params,
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
				$("#folio").val(result.folio);
				$("#direccion").val(result.direccion);
				$("#notas").val(result.notas);
				$("#empresa").val(result.empresa);
				$("#servicio").val(result.servicio);
				$("#fecha").val(result.fecha);

				//INFO
				$('.rem tbody').html(result.html_info);
				info = result.num_info;

				getEjecutivos(result.siu_id);
				getClients(result.cli_id, result.cont_id);


				$(".oc").html(result.ocs);
				$(".oc").tagging({
			      "edit-on-delete": false,
			      "no-spacebar": true
			    });

			    $('.fecha').daterangepicker({
			        autoUpdateInput: false,
			        singleDatePicker: true,
			        showDropdowns: true,
			        format: 'dd/mm/yyyy',
			        locale: 'es'
			    }, function(chosen_date) {
			      $(this.element[0]).val(chosen_date.format('DD/MM/YYYY'));
			    });

				//getContacto2(result.cont_id);
			}
			else {
				//window.location = "index.php";
			}
		}
	});	
}

function getEjecutivos(siu_id) {
	var params = {};
	params.id = siu_id;
    $.ajax({
        type: 'POST',
        url: 'include/Libs.php?accion=getEjecutivos',
        dataType:'json',
        data: params,
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
            $('.cont-ejecutivo').html(result.ejecutivo);
            $('.cont-btns-ev').html(result.btns);
            $('#ejecutivo').select2();

          }
          else {
            //window.location = "index.php";
          }
        }
    });
}

function getClients(cli_id, cont_id) {
    params = {};
    params.siu_id = $('#ejecutivo').val();
    params.id = cli_id;
    $.ajax({
        type: 'POST',
        url: 'include/Libs.php?accion=getClients',
        dataType:'json',
        data: params,
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
            $('.cont-clientes').html(result.clientes);
            $('#cliente').select2({
              placeholder: "Seleccione el Cliente"
            });

            $('.cont-btns-cliente').html(result.btns);
            getContacto2(cont_id);

          }
          else {
            //window.location = "index.php";
          }
        }
    });
}

function getContacto2(cont_id) {
    params = {};
    params.cli_id = $('#cliente').val();
    params.id = cont_id;
    $.ajax({
        type: 'POST',
        url: 'include/Libs.php?accion=getContacto',
        dataType:'json',
        data: params,
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
            $('.cont-contacto').html(result.contacto);
            $('#contacto').select2({
              placeholder: "Seleccione el Contacto"
            });

            $('.cont-btns-contacto').html(result.btns);
          }
          else {
            //window.location = "index.php";
          }
        }
    });
}

function getContacto() {
    params = {};
    params.cli_id = $('#cliente').val();
    $.ajax({
        type: 'POST',
        url: 'include/Libs.php?accion=getContacto',
        dataType:'json',
        data: params,
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
            $('.cont-contacto').html(result.contacto);
            $('#contacto').select2({
              placeholder: "Seleccione el Contacto"
            });

            $('.cont-btns-contacto').html(result.btns);
          }
          else {
            //window.location = "index.php";
          }
        }
    });
}