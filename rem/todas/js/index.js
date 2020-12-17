$(document).ready(function(){
	getRecords();

	$(document).on('click','.btn-editar',function(e) {
		e.preventDefault();
      var html_msg = "<form id='form-editar' name='form-cancelar' role='form' class='form-horizontal'>"+
              "<p>Ingrese los siguientes datos para notificar a un administrador sobre los cambios</p>"+
              "<div class='form-group row'>"+
                  "<label for='motivo' class='col-sm-12 control-label'>Describa los cambios que deben aplicarse</label>"+
                  "<div class='col-sm-12'>" +
                    "<textarea id='descripcion' name='descripcion' class='form-control'></textarea>"+
                    "<input id='id' name='id' class='form-control' type='hidden' value='"+($(this).attr('data-id'))+"'>"+
                  "</div>"+
                  "<br><label for='motivo' class='col-sm-12 control-label'>Motivo</label>"+
                  "<div class='col-sm-12'>" +
                    "<textarea id='motivo' name='motivo' class='form-control'></textarea>"+
                  "</div>"+ 
                "</div>"+
              "</form>";
      bootbox.dialog({
        message: html_msg,
        buttons: {
          guardar: {
              label: "Enviar Solicitud",
              className: "btn-success",
              callback: function() {
                $.ajax({
                url: 'include/Libs.php?accion=solicitud_cambio',
                type: 'POST',
                data: $('#form-editar').serialize(),
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

	$(document).on('click','.btn-factura',function(e) {
		e.preventDefault();
      var html_msg = "<form id='form-editar' name='form-cancelar' role='form' class='form-horizontal'>"+
              "<p>Ingrese los siguientes datos para cerrar la remisión</p>"+
              "<div class='form-group row'>"+
                  "<label for='motivo' class='col-sm-12 control-label'>Factura</label>"+
                  "<div class='col-sm-12'>" +
                    "<textarea id='factura' name='factura' class='form-control'></textarea>"+
                    "<input id='id' name='id' class='form-control' type='hidden' value='"+($(this).attr('data-id'))+"'>"+
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
                url: 'include/Libs.php?accion=cerrar_factura',
                type: 'POST',
                data: $('#form-editar').serialize(),
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
                            $('.table-proveedores').DataTable().destroy();
                            getRecords();
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

	$(document).on('click','.btn-solicitar',function(e) {
		e.preventDefault();
      var html_msg = "<form id='form-editar' name='form-cancelar' role='form' class='form-horizontal'>"+
              "<p>Ingrese los siguientes datos para cerrar la remisión</p>"+
              "<div class='form-group row'>"+
                  "<label for='motivo' class='col-sm-12 control-label'>Motivo</label>"+
                  "<div class='col-sm-12'>" +
                    "<textarea id='motivo' name='motivo' class='form-control'></textarea>"+
                    "<input id='id' name='id' class='form-control' type='hidden' value='"+($(this).attr('data-id'))+"'>"+
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
                url: 'include/Libs.php?accion=cerrar_solicitud',
                type: 'POST',
                data: $('#form-editar').serialize(),
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
                            $('.table-proveedores').DataTable().destroy();
                            getRecords();
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

	/*
	 * @author: Cynthia Castillo 
	 * @version: 0.1 2013-12-27
	 * 
	 * Acción de Borrar Perfil
	 */
	$(document).on('click','.borrar',function(e){
		e.preventDefault();
		var nombre = $(this).attr("data-name");
		var id = $(this).attr("data-id");
		var params = {};
		params.id = id;
		params.accion = 'deleteRecord';
		bootbox.dialog({
			message: "¿Desea eliminar el Proveedor "+nombre+"?",
			buttons: {
				aceptar: {
					label: "Aceptar",
					className: "btn-primary",
					callback: function() {
						$.ajax({
							type:'post',
							data:params,
							url:'include/Libs.php',
							dataType:'json',
							error:function(){
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
							success:function(result){
								bootbox.dialog({
									message: result.msg,
									title: result.title,
									buttons: {
										cerrar: {
											label: "Cerrar",
											callback: function() {
												bootbox.hideAll();
												$('.table-proveedores').DataTable().destroy();
												getRecords();
											}
										}
									}
								});
							}
						});
					}
				},
				cancelar: {
					label: "Cancelar",
					className: "btn-danger",
					callback: function() {
						$('.modal-dialog').modal('hide');
					}
				}
			}
		});
	});	

});

/*
 * @author: Cynthia Castillo <cynthia.castillo@metodika.mx>
 * @version: 0.1 2013­12-27
 * 
 * Imprime la tabla y le da la funcionalidad adecuada
 */
function getRecords(){
	$('.table-proveedores').DataTable( {
        "ajax": {
		    "url": "include/Libs.php?accion=printTable",
		    "type": "POST"
		  },
		  "language": {
		    "emptyTable":     "No se encontraron registros",
		    "info":           "Mostrando _START_ a _END_ de _TOTAL_",
		    "infoEmpty":      "Mostrando 0 de 0",
		    "lengthMenu":     "Mostrando _MENU_ registros",
		    "loadingRecords": "Cargando...",
		    "processing":     "Procesando...",
		    "search":         "Buscar:",
		    "zeroRecords":    "No se encontraron registros",
		    "paginate": {
		        "first":      "Primera",
		        "last":       "Última",
		        "next":       "Siguiente",
		        "previous":   "Anterior"
		    }
	    },
	    "pageLength": 50
    } );
}