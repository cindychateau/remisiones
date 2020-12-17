<?php

/*
 *  Se identifica la ruta 
 */
/*$url = explode("/aliados/admin", $_SERVER["REQUEST_URI"]);
$url = explode("/", $url[1]);*/

$url = explode("/remisiones", $_SERVER["REQUEST_URI"]);
$url = explode("/", $url[1]);

//$url = explode("/", $_SERVER["REQUEST_URI"]);

$ruta = "";
$file=$url[count($url)-1];
for ($i=1; $i < (count($url) - 1); $i++){
  $ruta .= "../";
}

//Se incluye la clase Common
include_once($ruta."include/Common.php");


/*
 *  Se definen los parámetros de la página
 */
define("PAGE_TITLE", "Alta de Remisión");

$module = 8;

$common->sentinel($module, 'alta.php');

//Se definen los js y css - sólo poner los nombres de los archivos no la terminación
$css = array();
$js = array('alta-pedido');

?>
<!DOCTYPE html>
<html class="loading" lang="es" data-textdirection="ltr">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo(TITLE_MAIN); ?></title>
    <link rel="apple-touch-icon" href="<?php echo $ruta;?>app-assets/images/ico/apple-icon-120.png">
    <link rel="shortcut icon" type="image/x-icon" href="<?php echo $ruta;?>app-assets/images/ico/favicon.png">
    <link href="https://fonts.googleapis.com/css?family=Muli:300,300i,400,400i,600,600i,700,700i%7CComfortaa:300,400,700" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="<?php echo $ruta;?>app-assets/fonts/line-awesome/css/line-awesome.min.css">
    <!-- BEGIN VENDOR CSS-->
    <link rel="stylesheet" type="text/css" href="<?php echo $ruta;?>app-assets/css/vendors.css">
    <link rel="stylesheet" type="text/css" href="<?php echo $ruta;?>app-assets/vendors/css/charts/chartist.css">
    <link rel="stylesheet" type="text/css" href="<?php echo $ruta;?>app-assets/vendors/css/charts/chartist-plugin-tooltip.css">
    <!-- END VENDOR CSS-->
    <!-- BEGIN CHAMELEON  CSS-->
    <link rel="stylesheet" type="text/css" href="<?php echo $ruta;?>app-assets/css/app.css">
    <!-- END CHAMELEON  CSS-->
    <!-- BEGIN Page Level CSS-->
    <link rel="stylesheet" type="text/css" href="<?php echo $ruta;?>app-assets/css/core/menu/menu-types/horizontal-menu.css">
    <link rel="stylesheet" type="text/css" href="<?php echo $ruta;?>app-assets/css/core/colors/palette-gradient.css">
    <link rel="stylesheet" type="text/css" href="<?php echo $ruta;?>app-assets/css/core/colors/palette-gradient.css">
    <link rel="stylesheet" type="text/css" href="<?php echo $ruta;?>app-assets/css/pages/chat-application.css">
    <link rel="stylesheet" type="text/css" href="<?php echo $ruta;?>app-assets/css/pages/dashboard-analytics.css">
    <!-- END Page Level CSS-->
    <!-- BEGIN Custom CSS-->
    <link rel="stylesheet" type="text/css" href="<?php echo $ruta;?>assets/css/style.css">
    <!-- END Custom CSS-->
    <!-- DATATABLES -->
    <link rel="stylesheet" type="text/css" href="<?php echo $ruta;?>app-assets/vendors/css/tables/datatable/datatables.min.css">
    <!-- CHECKBOXES STYLE -->
    <link rel="stylesheet" type="text/css" href="<?php echo $ruta;?>app-assets/vendors/css/forms/icheck/icheck.css">
    <link rel="stylesheet" type="text/css" href="<?php echo $ruta;?>app-assets/vendors/css/forms/icheck/custom.css">
    <!-- SELECT2 -->
    <link rel="stylesheet" type="text/css" href="<?php echo $ruta;?>app-assets/vendors/css/forms/selects/select2.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo $ruta;?>app-assets/vendors/css/pickers/daterange/daterangepicker.css">
    <!-- SWITCH -->
    <link rel="stylesheet" type="text/css" href="<?php echo $ruta;?>app-assets/vendors/css/forms/toggle/switchery.css">
    <!-- SWITCH -->
    <link rel="stylesheet" type="text/css" href="<?php echo $ruta;?>app-assets/vendors/css/ui/autocomplete/jquery-ui.css">
    <!-- TAGS -->
    <link rel="stylesheet" type="text/css" href="<?php echo $ruta;?>app-assets/css/plugins/forms/tags/tagging.css">
    <!-- CSS -->
    <?php 
      if (count($css) > 0) {
        foreach ($css as $clave => $valor) {
          echo '<link rel="stylesheet" href="'.$ruta.'css/'.$valor.'.css" />';
        }
      }
    ?>

    <style type="text/css">
      .tagging {
         padding: .75rem 1.5rem;
      }

      textarea {
        resize: none;
      }

      .select2-container {
        width: 100% !important;
      }
    </style>

  </head>
  <body class="horizontal-layout horizontal-menu 2-columns   menu-expanded" data-open="hover" data-menu="horizontal-menu" data-color="bg-gradient-x-blue-cyan" data-col="2-columns">

    <!-- fixed-top-->
    <nav class="header-navbar navbar-expand-md navbar navbar-with-menu navbar-without-dd-arrow navbar-static-top navbar-light navbar-brand-center">
      <?php echo $common->printHeader(); ?>
    </nav>

    <!-- ////////////////////////////////////////////////////////////////////////////-->

    <div class="header-navbar navbar-expand-sm navbar navbar-horizontal navbar-fixed navbar-dark navbar-without-dd-arrow navbar-shadow" role="navigation" data-menu="menu-wrapper">
      <div class="navbar-container main-menu-content" data-menu="menu-container">
        <?php echo $common->printMenu(); ?>
      </div>
    </div>

    <div class="app-content content">
      <div class="content-wrapper">
        <div class="content-wrapper-before"></div>
        <div class="content-header row">
          <div class="content-header-left col-md-4 col-12 mb-2">
            <h3 class="content-header-title"><?php echo(PAGE_TITLE); ?></h3>
          </div>
          <div class="content-header-right col-md-8 col-12">
            <?php echo $common->printBreadcrumbs($module); ?>
          </div>
        </div>
        <div class="content-body">
          <!-- CONTENIDO -->
          <div class="container main-container">
            <form id="frm-cliente" class="form form-horizontal">
              <div class="row">
                <div class="col-md-12">
                   <div class="card">
                      <div class="card-header">
                         <h4 class="card-title" id="horz-layout-colored-controls"><i class="ft-info"></i> DATOS DEL CLIENTE</h4>
                         <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
                         <div class="heading-elements">
                            <ul class="list-inline mb-0">
                               <li><a data-action="collapse"><i class="ft-minus"></i></a></li>
                            </ul>
                         </div>
                      </div>
                     <div class="card-content collpase show">
                         <div class="card-body">
                            <div class="form-body">
                              <div class="form-group row">
                                <label class="col-sm-2 label-control" for="folio">Folio*</label>
                                <div class="col-sm-2">
                                  <input type="text" id="folio" class="form-control border-primary" name="folio" readonly="readonly">
                                </div>
                                <div class="col-sm-4">
                                  <small>*En caso de que el folio no corresponda al de la remisión, favor de comunicarse de inmediato con un administrador.</small>
                                </div>
                                <label class="col-sm-2 label-control" for="fecha">Fecha*</label>
                                <div class="col-sm-2">
                                  <input type="text" id="fecha" class="form-control border-primary fecha" name="fecha" readonly="readonly">
                                </div>
                              </div>
                              <div class="form-group row">
                                <label class="col-sm-2 label-control" for="ejecutivo">Responsable*</label>
                                <div class="col-sm-4 cont-ejecutivo">
                                  <select disabled="disabled" class="form-control">
                                    <option>Cargando...</option>
                                  </select>
                                </div>
                                <div class="col-sm-6 cont-btns-ev text-right">
                                  
                                </div>
                              </div>
                              <div class="form-group row">
                                <label class="col-sm-2 label-control" for="cliente">Cliente*</label>
                                <div class="col-sm-4 cont-clientes">
                                  <select disabled="disabled" class="form-control">
                                    <option>Cargando...</option>
                                  </select>
                                </div>
                                <div class="col-sm-6 text-right">
                                  <span class="cont-btns-cliente">
                                    
                                  </span>
                                  <button class="btn btn-outline-success btn-action recargar-clientes"><i class="fa ft-refresh-cw"></i>Recargar Clientes</button>
                                </div>
                              </div>
                              <div class="form-group row">
                                <label class="col-sm-2 label-control" for="direccion_fiscal">Contacto*</label>
                                <div class="col-sm-4 cont-contacto">
                                  <select disabled="disabled" class="form-control">
                                    <option>Elegir un Cliente</option>
                                  </select>
                                </div>
                                <div class="col-sm-6 text-right">
                                  <span class="cont-btns-contacto">
                                    
                                  </span>
                                  <button class="btn btn-outline-success btn-action recargar-contacto"><i class="fa ft-refresh-cw"></i>Recargar Contacto</button>
                                </div>
                              </div>
                              <div class="form-group row">
                                <label class="col-sm-2 label-control" for="entrega">Dirección*</label>
                                <div class="col-sm-10 cont-entrega">
                                  <textarea class="form-control" name="direccion" id="direccion"></textarea>
                                </div>
                              </div>
                              <div class="form-group row">
                                <label class="col-sm-2 label-control" for="urgencia">Empresa*</label>
                                <div class="col-sm-4">
                                  <select id="empresa" name="empresa" class="form-control">
                                    <option value="Alliance Soluciones">Alliance Soluciones</option>
                                    <option value="3G Consulting y Asesoría">3G Consulting y Asesoría</option>
                                    <option value="Sky Consulting Partners">Sky Consulting Partners</option>
                                  </select>
                                </div>
                                <label class="col-sm-2 label-control" for="servicio">Tipo de Servicio*</label>
                                <div class="col-sm-4">
                                  <select id="servicio" name="servicio" class="form-control">
                                    <option>Garantía</option>
                                    <option>Regalo</option>
                                    <option>Venta</option>
                                    <option>Renta</option>
                                    <option>Servicio o Mmto</option>
                                    <option>Préstamo</option>
                                  </select>
                                </div>
                              </div>
                              <div class="form-group row">
                                <label class="col-sm-2 label-control" for="precio">Orden(es) de Compra <small>*Presione la tecla TAB para agregar otra Orden de Compra</small></label>
                                <div class="col-sm-4">
                                   <div class="oc form-control" data-tags-input-name="oc"></div>
                                </div>
                              </div>
                            </div>
                         </div>
                     </div>
                   </div>
                   <div class="card">
                    <div class="card-header">
                      <h4 class="card-title" id="horz-layout-colored-controls"><i class="ft-package"></i> INFORMACIÓN DE REMISIÓN</h4>
                      <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
                      <div class="heading-elements">
                          <ul class="list-inline mb-0">
                              <li><a data-action="collapse"><i class="ft-minus"></i></a></li>
                          </ul>
                      </div>
                    </div>
                    <div class="card-content collpase show">
                      <div class="card-body">
                        <div class="cont-legend">
                          <small>*El campo de IMPORTE no se ve en la remisión esta empresa.</small>
                        </div>
                        <br>
                        <div class="form-body">
                          <table class="table table-bordered rem">
                            <thead>
                              <tr>
                                <th>Cantidad</th>
                                <th>Importe</th>
                                <th>Descripción</th>
                                <th><i class="ft ft-plus add-rem"></i></th>
                              </tr>
                            </thead>
                            <tbody>
                              <tr>
                                <td>
                                  <input type="text" name="cantidad[0]" class="form-control">
                                </td>
                                <td>
                                  <input type="text" name="importe[0]" class="form-control">
                                </td>
                                <td>
                                  <textarea class="form-control" name="descripcion[0]"></textarea>
                                </td>
                                <td><i class="ft ft-trash-2"></i></td>
                              </tr>
                            </tbody>
                          </table>
                          <div class="form-group row">
                            <label class="col-sm-1 label-control" for="entrega">Notas</label>
                            <div class="col-sm-11">
                              <textarea class="form-control" name="notas" id="notas" height="300"></textarea>
                            </div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-1 label-control" for="subtotal">Subtotal</label>
                            <div class="col-sm-3">
                              <input class="form-control" name="subtotal" id="subtotal" type="number">
                            </div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-1 label-control" for="iva">IVA</label>
                            <div class="col-sm-3">
                              <input class="form-control" name="iva" id="iva" type="number">
                            </div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-1 label-control" for="total">Total</label>
                            <div class="col-sm-3">
                              <input class="form-control" name="total" id="total" type="number">
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
             </div>
              <div class="row">
                <div class="col-md-12">
                   <div class="card">
                      <div class="card-content collpase show">
                         <div class="card-body">
                            <div class="form-actions right">
                               <a href="index.php">
                                  <button type="button" class="btn btn-danger mr-1">
                                     <i class="ft-x"></i> Cancelar
                                  </button>
                               </a>
                               <button type="submit" class="btn btn-primary guardar">
                                  <i class="la la-check-square-o"></i> Guardar
                               </button>
                            </div>
                         </div>
                      </div>
                   </div>
                </div>
             </div>
            </form>
          </div>
          <!-- /CONTENIDO -->
        </div>
      </div>
    </div>
    <!-- ////////////////////////////////////////////////////////////////////////////-->

    <?php echo $common->printFooter(); ?>

    <!-- BEGIN VENDOR JS-->
    <script src="<?php echo $ruta;?>app-assets/vendors/js/vendors.min.js" type="text/javascript"></script>
    <!-- BEGIN VENDOR JS-->
    <!-- BEGIN PAGE VENDOR JS-->
    <script type="text/javascript" src="<?php echo $ruta;?>app-assets/vendors/js/ui/jquery.sticky.js"></script>
    <!-- END PAGE VENDOR JS-->
    <!-- BEGIN CHAMELEON  JS-->
    <script src="<?php echo $ruta;?>app-assets/js/core/app-menu.js" type="text/javascript"></script>
    <script src="<?php echo $ruta;?>app-assets/js/core/app.js" type="text/javascript"></script>
    <!-- END CHAMELEON  JS-->
    <!-- DATATABLES -->
    <script src="<?php echo $ruta;?>app-assets/vendors/js/tables/datatable/datatables.min.js" type="text/javascript"></script>
    <!-- BOOTBOX -->
    <script src="<?php echo $ruta;?>assets/js/bootbox.min.js" type="text/javascript"></script>
    <!-- CHECKBOX STYLE -->
    <script src="<?php echo $ruta;?>app-assets/vendors/js/forms/icheck/icheck.min.js" type="text/javascript"></script>
    <script src="<?php echo $ruta;?>app-assets/js/scripts/forms/checkbox-radio.js" type="text/javascript"></script>
    <!-- SELECT2 -->
    <script src="<?php echo $ruta;?>app-assets/vendors/js/forms/select/select2.full.min.js" type="text/javascript"></script>
    <!-- DATEPICKER -->
    <script src="<?php echo $ruta;?>app-assets/vendors/js/pickers/dateTime/moment-with-locales.min.js" type="text/javascript"></script>
    <script src="<?php echo $ruta;?>app-assets/vendors/js/pickers/daterange/daterangepicker.js" type="text/javascript"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.6/locale/es.js"></script>
    <!-- SWITCH -->
    <script src="<?php echo $ruta;?>app-assets/vendors/js/forms/toggle/switchery.js" type="text/javascript"></script>
    <!-- AUTOCOMPLETE -->
    <script src="<?php echo $ruta;?>app-assets/vendors/js/ui/autocomplete/jquery-ui.js" type="text/javascript"></script>
    <!-- TAGS -->
    <script src="<?php echo $ruta;?>app-assets/vendors/js/forms/tags/tagging.min.js" type="text/javascript"></script>
    <!-- JS -->
    <?php 
      if (count($js) > 0) {
        foreach ($js as $clave => $valor) {
          echo '<script type="text/javascript" src="js/'.$valor.'.js?1"></script>';
        }
      }
    ?>
    <!-- /JAVASCRIPTS -->
  </body>
</html>