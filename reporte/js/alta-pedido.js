var info = 1;
var select_clientes = '';
$(document).ready(function(){
    getEjecutivos();
    getClients();

    //Ponemos por default la fecha de hoy
    var myDate = new Date();
    var prettyDate =myDate.getDate() + '/' + (myDate.getMonth()+1) + '/' + myDate.getFullYear();
    $("#fecha").val(prettyDate);

    /*
     * @author: Cynthia Castillo 
     * 
     * Datepickers
     */
    $('.fecha').daterangepicker({
        autoUpdateInput: false,
        singleDatePicker: true,
        showDropdowns: true,
        format: 'dd/mm/yyyy',
        locale: 'es'
    }, function(chosen_date) {
      $(this.element[0]).val(chosen_date.format('DD/MM/YYYY'));
    });

    $(".oc").tagging({
      "edit-on-delete": false,
      "no-spacebar": true
    });

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
    }); 

    $(document).on('click','.ft-trash-2',function(e) {
      e.preventDefault();
      $(this).closest('tr').remove();
    });

    $(document).on('click','.btn-add-client',function(e) {
      e.preventDefault();
      var html_msg = "<form id='form-cliente' name='form-cancelar' role='form' class='form-horizontal'>"+
              "<p>Ingrese los siguientes datos para notificar a un administrador sobre el nuevo Cliente</p>"+
              "<div class='form-group row'>"+
                  "<label for='motivo' class='col-sm-3 control-label'>Nombre del Cliente</label>"+
                  "<div class='col-sm-9'>" +
                    "<input type='text' id='nuevo_cliente' name='nuevo_cliente' class='form-control'>"+
                  "</div>"+ 
                "</div>"+
                "<div class='form-group row'>"+
                  "<label for='motivo' class='col-sm-3 control-label'>Nombre del Contacto</label>"+
                  "<div class='col-sm-9'>" +
                    "<input type='text' id='nuevo_contacto' name='nuevo_contacto' class='form-control'>"+
                  "</div>"+ 
                "</div>"+
              "</form>";
      bootbox.dialog({
        message: html_msg,
        buttons: {
          guardar: {
              label: "Guardar",
              className: "btn-success",
              callback: function() {
                $.ajax({
                url: 'include/Libs.php?accion=nuevo_cliente',
                type: 'POST',
                data: $('#form-cliente').serialize(),
                dataType: 'JSON',
                beforeSend: function(){
                  $('input, file, textarea, button, select').each(function(){
                    $(this).attr('disabled','disabled');
                  });
                },
                error: function (){
                  $('input, file, textarea, button, select').each(function(){
                    $(this).removeAttr('disabled');
                  });
                  bootbox.alert("Experimentamos fallas técnicas. Comuníquese con su proveedor.");
                }, success: function (result) {
                  $('input, file, textarea, button, select').each(function(){
                    $(this).removeAttr('disabled');
                  });
                  bootbox.dialog({
                    message: result.msg,
                    buttons: {
                      cerrar: {
                        label: "Cerrar",
                        callback: function() {
                          if(!result.error) {
                            bootbox.hideAll();
                          }
                        }
                      }
                    }
                  });
                }
              });
              return false;
              }
          },
          cancelar: {
              label: "Cancelar",
              className: "btn-danger",
              callback: function() {
                bootbox.hideAll();
              }
          }
        }
      });
    });

    $(document).on('click','.btn-add-contact',function(e) {
      e.preventDefault();
      var html_msg = "<form id='form-cliente' name='form-cancelar' role='form' class='form-horizontal'>"+
              "<p>Ingrese los siguientes datos para notificar a un administrador sobre el nuevo Contacto</p>"+
                "<div class='form-group row'>"+
                  "<label for='motivo' class='col-sm-3 control-label'>Cliente</label>"+
                  "<div class='col-sm-9'>" +
                    select_clientes+
                  "</div>"+ 
                "</div>"+
                "<div class='form-group row'>"+
                  "<label for='motivo' class='col-sm-3 control-label'>Nombre del Contacto</label>"+
                  "<div class='col-sm-9'>" +
                    "<input type='text' id='nuevo_contacto' name='nuevo_contacto' class='form-control'>"+
                  "</div>"+ 
                "</div>"+
              "</form>";
      bootbox.dialog({
        message: html_msg,
        buttons: {
          guardar: {
              label: "Guardar",
              className: "btn-success",
              callback: function() {
                $.ajax({
                url: 'include/Libs.php?accion=nuevo_contacto',
                type: 'POST',
                data: $('#form-cliente').serialize(),
                dataType: 'JSON',
                beforeSend: function(){
                  $('input, file, textarea, button, select').each(function(){
                    $(this).attr('disabled','disabled');
                  });
                },
                error: function (){
                  $('input, file, textarea, button, select').each(function(){
                    $(this).removeAttr('disabled');
                  });
                  bootbox.alert("Experimentamos fallas técnicas. Comuníquese con su proveedor.");
                }, success: function (result) {
                  $('input, file, textarea, button, select').each(function(){
                    $(this).removeAttr('disabled');
                  });
                  bootbox.dialog({
                    message: result.msg,
                    buttons: {
                      cerrar: {
                        label: "Cerrar",
                        callback: function() {
                          if(!result.error) {
                            bootbox.hideAll();
                          }
                        }
                      }
                    }
                  });
                }
              });
              return false;
              }
          },
          cancelar: {
              label: "Cancelar",
              className: "btn-danger",
              callback: function() {
                bootbox.hideAll();
              }
          }
        }
      });

      $('#cliente_select').select2({
        placeholder: "Selec. Cliente para el nuevo Contacto"
      });
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


function getEjecutivos() {
    $.ajax({
        type: 'POST',
        url: 'include/Libs.php?accion=getEjecutivos',
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
            $('.cont-ejecutivo').html(result.ejecutivo);
            $('.cont-btns-ev').html(result.btns);
            $('#ejecutivo').select2();

          }
          else {
            window.location = "index.php";
          }
        }
    });
}


function getClients() {
    params = {};
    params.siu_id = $('#ejecutivo').val();
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

            select_clientes = result.cliente_select;

            $('.cont-btns-cliente').html(result.btns);

            getContacto();
          }
          else {
            window.location = "index.php";
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
            window.location = "index.php";
          }
        }
    });
}


function isEmpty(str) {
    return (!str || 0 === str.length || str == "");
}






