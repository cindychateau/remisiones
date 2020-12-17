$(document).ready(function(){

  getUsuarios();
  getClientes();

  $('.fecha').daterangepicker({
      autoUpdateInput: false,
      singleDatePicker: true,
      showDropdowns: true,
      format: 'dd/mm/yyyy',
      locale: 'es'
  }, function(chosen_date) {
    $(this.element[0]).val(chosen_date.format('DD/MM/YYYY'));
  });

  $(document).on('click','.btn-filtrar',function(e) {
    e.preventDefault();
    getRecords();
  });

  $(document).on('change','#cliente',function(e) {
    getContacto();
  });

  $(document).on('click','.btn-generar',function(e) {
    e.preventDefault();
    $.ajax({
      type: 'POST',
      url: 'include/Libs.php?accion=excel',
      data: $("#frm-remisiones").serialize(),
      dataType:'json',
      beforeSend: function() {
        $('.cont-loader').html('<div class="spinner ft-loader"></div>');
        $('.cont-descargar a').attr('disabled', 'disabled');
      },
      error: function(){
        bootbox.dialog({
          message: "Experimentamos Fallas Técnicas. Comuníquese con su proveedor.",
          buttons: {
            cerrar: {
              label: "Cerrar",
              callback: function() {
                bootbox.hideAll();
                $('.cont-loader').html('');
              }
            }
          }
        });
      },
      success: function(result){
        $('.cont-loader').html('');
        if(!result.error){
          $('.cont-descargar a').removeAttr('disabled');
          $('.cont-descargar button').removeAttr('disabled');
        }
      }
    });
  });

});

/*
 * @author: Cynthia Castillo
 * 
 * Imprime la tabla y le da la funcionalidad adecuada
 */
function getRecords(){
	$.ajax({
    type: 'POST',
    url: 'include/Libs.php?accion=printTable',
    data: $("#frm-remisiones").serialize(),
    dataType:'json',
    beforeSend: function() {
      $('.cont-loader').html('<div class="spinner ft-loader"></div>');
      $('.cont-descargar a').attr('disabled', 'disabled');
      $('.cont-descargar button').attr('disabled', 'disabled');
    },
    error: function(){
      bootbox.dialog({
        message: "Experimentamos Fallas Técnicas. Comuníquese con su proveedor.",
        buttons: {
          cerrar: {
            label: "Cerrar",
            callback: function() {
              bootbox.hideAll();
              $('.cont-loader').html('');
            }
          }
        }
      });
    },
    success: function(result){
      $('.cont-loader').html('');
      if(!result.error){
        $(".table-remisiones tbody").html(result.remisiones);
      }
    }
  });
}

function getUsuarios(){
  $.ajax({
    type: 'POST',
    url: 'include/Libs.php?accion=usuarios',
    dataType:'json',
    error: function(){
      bootbox.dialog({
        message: "Experimentamos Fallas Técnicas. Comuníquese con su proveedor.",
        buttons: {
          cerrar: {
            label: "Cerrar",
            callback: function() {
              bootbox.hideAll();
              $('.cont-loader').html('');
            }
          }
        }
      });
    },
    success: function(result){
      $('.cont-responsable').html(result.responsable);
      $('#responsable').select2();

      $('.cont-siu-cerro').html(result.cerro);
      $('#cerro').select2();
    }
  });
}

function getClientes(){
  $.ajax({
    type: 'POST',
    url: 'include/Libs.php?accion=clientes',
    dataType:'json',
    error: function(){
      bootbox.dialog({
        message: "Experimentamos Fallas Técnicas. Comuníquese con su proveedor.",
        buttons: {
          cerrar: {
            label: "Cerrar",
            callback: function() {
              bootbox.hideAll();
              $('.cont-loader').html('');
            }
          }
        }
      });
    },
    success: function(result){
      $('.cont-cliente').html(result.clientes);
      $('#cliente').select2();
    }
  });
}

function getContacto() {
    params = {};
    params.cli_id = $('#cliente').val();
    $.ajax({
        type: 'POST',
        url: 'include/Libs.php?accion=contactos',
        dataType:'json',
        data: params,
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
            $('#contacto').select2();
          }
          else {
            window.location = "index.php";
          }
        }
    });
}