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
define("PAGE_TITLE", "Folios");

$module = 15;

$common->sentinel($module, 'cambios.php');

//Se definen los js y css - sólo poner los nombres de los archivos no la terminación
$css = array();
$js = array('index');

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
    <link href="https://maxcdn.icons8.com/fonts/line-awesome/1.1/css/line-awesome.min.css" rel="stylesheet">
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
    <!-- WYSIWYG -->
    <link rel="stylesheet" type="text/css" href="<?php echo $ruta;?>app-assets/css/plugins/summernote/summernote.css?1">
    <!-- TAGS -->
    <link rel="stylesheet" type="text/css" href="<?php echo $ruta;?>app-assets/css/plugins/forms/tags/tagging.css">
    <!-- SWITCH -->
    <link rel="stylesheet" type="text/css" href="<?php echo $ruta;?>app-assets/vendors/css/forms/toggle/switchery.min.css">
    <!-- COLORPICKER -->
    <link rel="stylesheet" type="text/css" href="<?php echo $ruta;?>app-assets/css/plugins/colorpicker/colorpicker.css">

    <!-- CSS -->
    <?php 
      if (count($css) > 0) {
        foreach ($css as $clave => $valor) {
          echo '<link rel="stylesheet" href="'.$ruta.'css/'.$valor.'.css" />';
        }
      }
    ?>

    <style type="text/css">
      .btn-act {
         padding: 10px;
      }

      .cont-categorias .row {
         margin-bottom: 5px;
      }

      .tagging {
         padding: .75rem 1.5rem;
      }

      .img-galeria {
         margin-bottom: 20px;
      }

      #indx, #indx_en {
        height: 200px;
      }

      .img-aseguradora {
        background: lightgray;
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
            <form id="frm-password" class="form form-horizontal">
               <div class="row">
                  <div class="col-md-12">
                     <div class="card">
                        <div class="card-header">
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
                                <div class="row">
                                  <div class="col-sm-12">
                                    <small>*Los folios a continuación representan el SIGUIENTE consecutivo que se utilizará en la Remisión</small>
                                  </div>
                                </div>
                                <br>
                                <div class="row">
                                  <div class="form-group col-sm-4">
                                    <label class="" for="folio-1">Alliance Soluciones*</label>
                                    <input type="number" id="folio-1" name="folio-1" class="form-control">
                                  </div>
                                  <div class="form-group col-sm-4">
                                    <label class="" for="folio-2">3G Consulting y Asesoría*</label>
                                    <input type="number" id="folio-2" name="folio-2" class="form-control">
                                  </div>
                                  <div class="form-group col-sm-4">
                                    <label class="" for="folio-3">Sky Consulting Partners*</label>
                                    <input type="number" id="folio-3" name="folio-3" class="form-control">
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
    <!-- WYSIWYG -->
    <script src="<?php echo $ruta;?>app-assets/js/scripts/summernote/summernote-bs4.min.js" type="text/javascript"></script>
    <!-- SELECT2 -->
    <script src="<?php echo $ruta;?>app-assets/vendors/js/forms/select/select2.full.min.js" type="text/javascript"></script>
    <!-- TAGS -->
    <script src="<?php echo $ruta;?>app-assets/vendors/js/forms/tags/tagging.min.js" type="text/javascript"></script>
    <!-- SWITCH -->
    <script src="<?php echo $ruta;?>app-assets/vendors/js/forms/toggle/switchery.min.js" type="text/javascript"></script>
    <script src="<?php echo $ruta;?>app-assets/js/scripts/forms/switch.js" type="text/javascript"></script>
    <!-- COLORPICKER -->
    <script src="<?php echo $ruta;?>app-assets/js/scripts/colorpicker/colorpicker.js" type="text/javascript"></script>


    <!-- JS -->
    <?php 
      if (count($js) > 0) {
        foreach ($js as $clave => $valor) {
          echo '<script type="text/javascript" src="js/'.$valor.'.js"></script>';
        }
      }
    ?>
    <!-- /JAVASCRIPTS -->
  </body>
</html>