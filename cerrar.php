<?php
$ruta = '';
include_once("include/Common.php");

if(isset($_GET['r']) && isset($_GET['cs']) && isset($_GET['s']) ) {
    $db = $common->_conexion;

    $sql_check = 'SELECT SIU_ID FROM SISTEMA_USUARIO
                  WHERE SIU_ID = ? AND SUP_ID = 2';
    $values_check = array($_GET['s']);
    $consulta_check = $db->prepare($sql_check);
    $consulta_check->execute($values_check);

    if($consulta_check->rowCount()) {

        $fecha = date('Y-m-d');
        $sql = 'UPDATE rem SET  estatus = "Cerrada",
                                fecha_cerrada = ?,
                                siu_cerrada = ?
                WHERE id = ?
                AND notas2 = ?';

        $values = array($fecha,
                        $_GET['s'],
                        $_GET['r'],
                        $_GET['cs']);
        $consulta = $db->prepare($sql);
        $consulta->execute($values);

        if($consulta->rowCount()) {
            $html = '<h2 class=" text-center mb-2 white">Remisión Cerrada</h2>
                        <h3 class="text-center white">La remisión fue cerrada con éxito. Puede encontrarla ahora en el módulo de Remisiones Cerradas</h3>';
        } else {
            $html = '<h2 class=" text-center mb-2 white">ERROR</h2>
                        <h3 class="text-center white">La remisión no pudo ser cerrada o ya se encontraba cerrada. Favor de revisar el enlace e intentarlo de nuevo más tarde.</h3>';
        }

    } else {
       header("Location: home.php"); 
    }


} else {
    header("Location: home.php");
}


?>
<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="">
    <meta name="author" content="ThemeSelect">
    <title>Sistema de Remisiones</title>
    <link rel="apple-touch-icon" href="app-assets/images/ico/apple-icon-120.png">
    <link rel="shortcut icon" type="image/x-icon" href="app-assets/images/ico/favicon.ico">
    <link href="https://fonts.googleapis.com/css?family=Muli:300,300i,400,400i,600,600i,700,700i%7CComfortaa:300,400,700" rel="stylesheet">
    <link href="https://maxcdn.icons8.com/fonts/line-awesome/1.1/css/line-awesome.min.css" rel="stylesheet">
    <!-- BEGIN VENDOR CSS-->
    <link rel="stylesheet" type="text/css" href="app-assets/css/vendors.css">
    <!-- END VENDOR CSS-->
    <!-- BEGIN CHAMELEON  CSS-->
    <link rel="stylesheet" type="text/css" href="app-assets/css/app.css">
    <!-- END CHAMELEON  CSS-->
    <!-- BEGIN Page Level CSS-->
    <link rel="stylesheet" type="text/css" href="app-assets/css/core/menu/menu-types/horizontal-menu.css">
    <link rel="stylesheet" type="text/css" href="app-assets/css/core/colors/palette-gradient.css">
    <link rel="stylesheet" type="text/css" href="app-assets/css/pages/error.css">
    <!-- END Page Level CSS-->
    <!-- BEGIN Custom CSS-->
    <link rel="stylesheet" type="text/css" href="assets/css/style.css">
    <!-- END Custom CSS-->
  </head>
  <body class="horizontal-layout horizontal-menu 1-column  bg-gradient-directional-danger menu-expanded blank-page blank-page" data-open="hover" data-menu="horizontal-menu" data-color="bg-gradient-x-purple-blue" data-col="1-column">
    <!-- ////////////////////////////////////////////////////////////////////////////-->
    <div class="app-content content">
      <div class="content-wrapper">
        <div class="content-wrapper-before"></div>
        <div class="content-header row">
        </div>
        <div class="content-body"><section class="flexbox-container bg-hexagons-danger">
        <div class="col-12 d-flex align-items-center justify-content-center">
            <div class="col-md-4 col-10 p-0">
                <div class="card-header bg-transparent border-0">
                    <?=$html;?>
                </div>
                <div class="card-content">
                    <div class="row py-2 text-center">
                        <div class="col-12">
                            <a href="home.php" class="btn btn-white danger box-shadow-4"><i class="ft-home"></i> Regresar al Sistema</a>
                        </div>
                        
                    </div>
                </div>
                <div class="card-footer bg-transparent">
                    <div class="row">
                    </div>
                </div>
            </div>
        </div>
    </section>
    
        </div>
      </div>
    </div>
    <!-- ////////////////////////////////////////////////////////////////////////////-->

    <!-- BEGIN VENDOR JS-->
    <script src="app-assets/vendors/js/vendors.min.js" type="text/javascript"></script>
    <!-- BEGIN VENDOR JS-->
    <!-- BEGIN PAGE VENDOR JS-->
    <script type="text/javascript" src="app-assets/vendors/js/ui/jquery.sticky.js"></script>
    <script src="app-assets/vendors/js/forms/validation/jqBootstrapValidation.js" type="text/javascript"></script>
    <!-- END PAGE VENDOR JS-->
    <!-- BEGIN CHAMELEON  JS-->
    <script src="app-assets/js/core/app-menu.js" type="text/javascript"></script>
    <script src="app-assets/js/core/app.js" type="text/javascript"></script>
    <!-- END CHAMELEON  JS-->
    <!-- BEGIN PAGE LEVEL JS-->
    <script src="app-assets/js/scripts/forms/form-login-register.js" type="text/javascript"></script>
    <!-- END PAGE LEVEL JS-->
  </body>
</html>