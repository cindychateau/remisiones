<?php
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
require_once($ruta."include/PHPExcel/PHPExcel.php");
$module = 9;

class Libs extends Common {
	/*
	 * @author: Cynthia Castillo 
	 *  
	 * Imprime la tabla de registros de perfil de usuarios EXCEPTUANDO 'daemon'
	 */
	function printTable() {
		global $module;
		$json = array();
		$json['remisiones'] = '';

		if(!isset($_SESSION)){
			@session_start();
		}

		$where = '';
		$values = array();
		if(isset($_POST['folio']) && !empty($_POST['folio'])) {
			$where .= ' AND folio = ? ';
			$values[]= $_POST['folio'];
		}

		if(isset($_POST['estatus']) && $_POST['estatus'] != -1) {
			$where .= ' AND  estatus = ? ';
			$values[]= $_POST['estatus'];
		}

		if(isset($_POST['responsable']) && $_POST['responsable'] > -1) {
			$where .= ' AND rem.siu_id = ? ';
			$values[]= $_POST['responsable'];
		}

		if(isset($_POST['cliente']) && $_POST['cliente'] > -1) {
			$where .= ' AND cli_id = ? ';
			$values[]= $_POST['cliente'];

			if(isset($_POST['contacto']) && $_POST['contacto'] > -1) {
				$where .= ' AND cont_id = ? ';
				$values[]= $_POST['contacto'];
			}
		}

		if(isset($_POST['empresa']) && $_POST['empresa'] > -1) {
			$where .= ' AND rem.empresa = ? ';
			$values[]= $_POST['empresa'];
		}
 
		if(isset($_POST['servicio']) && $_POST['servicio'] > -1) {
			$where .= ' AND servicio = ? ';
			$values[]= $_POST['servicio'];
		}

		if(isset($_POST['servicio']) && $_POST['servicio'] > -1) {
			$where .= ' AND servicio = ? ';
			$values[]= $_POST['servicio'];
		}

		if(isset($_POST['oc']) && !empty($_POST['oc'])) {
			$where .= ' AND ocs LIKE "%'.$_POST['oc'].'%"';
		}

		if(isset($_POST['alta-inicio']) && !empty($_POST['alta-inicio'])) {
			$fecha = date("Y-m-d",strtotime(str_replace("/", "-", $_POST['alta-inicio'])));
			$where .= ' AND fecha >= "'.$fecha.'"';
		}

		if(isset($_POST['alta-fin']) && !empty($_POST['alta-fin'])) {
			$fecha = date("Y-m-d",strtotime(str_replace("/", "-", $_POST['alta-fin'])));
			$where .= ' AND fecha <= "'.$fecha.'"';
		}

		if(isset($_POST['cerrar-inicio']) && !empty($_POST['cerrar-inicio'])) {
			$fecha = date("Y-m-d",strtotime(str_replace("/", "-", $_POST['cerrar-inicio'])));
			$where .= ' AND fecha_cerrada >= "'.$fecha.'"';
		}

		if(isset($_POST['cerrar-fin']) && !empty($_POST['cerrar-fin'])) {
			$fecha = date("Y-m-d",strtotime(str_replace("/", "-", $_POST['cerrar-fin'])));
			$where .= ' AND fecha_cerrada <= "'.$fecha.'"';
		}

		if(isset($_POST['factura']) && !empty($_POST['factura'])) {
			$where .= ' AND factura LIKE "%'.$_POST['factura'].'%"';
		}

		if(isset($_POST['cerro']) && $_POST['cerro'] > -1) {
			$where .= ' AND siu_cerrada = ? ';
			$values[]= $_POST['cerro'];
		}

		/*
		 * Query principal
		 */
		$db = $this->_conexion;
		$sqlQuery = "SELECT rem.*,
							shortname,
							nombre,
							SISTEMA_USUARIO.SIU_NOMBRE as responsable,
							SISTEMA_USUARIO_2.SIU_NOMBRE as cerro
					 FROM rem
					 LEFT JOIN clientes ON rem.cli_id = clientes.id
					 LEFT JOIN contactos ON rem.cont_id = contactos.id
					 LEFT JOIN SISTEMA_USUARIO ON rem.siu_id = SISTEMA_USUARIO.SIU_ID
					 LEFT JOIN SISTEMA_USUARIO SISTEMA_USUARIO_2 ON rem.siu_cerrada = SISTEMA_USUARIO_2.SIU_ID
					 WHERE 1 = 1 ".$where;			 
		
		//Se prepara la consulta de extración de datos
		$consulta = $db->prepare($sqlQuery);

		//echo $sqlQueryFiltered;

		//Se ejecuta la consulta
		try {
			
			$consulta->execute($values);
			
			//Se imprime la tabla
			$puntero = $consulta->fetchAll(PDO::FETCH_ASSOC);
			
			/*
			* Salida de Datos
			*/
			$data = array();

			if($consulta->rowCount()) {
				foreach ($puntero as $row) {

					//Consultamos detalles de la remisión
					$sql_info = 'SELECT * FROM rem_info WHERE rem_id = ?';
					$values_info = array($row['id']);
					$consulta_info = $db->prepare($sql_info);
					$tabla_info = '';

					try {
						$consulta_info->execute($values_info);
						$infos = $consulta_info->fetchAll(PDO::FETCH_ASSOC);

						$tabla_info = '<table class="table table-bordered">
												<thead>
													<tr>
														<th class="text-center">Cantidad</th>
														<th class="text-center">Importe</th>
														<th class="text-center">Descripción</th>
													</tr>
												</thead>
												<tbody>';				

						foreach ($infos as $info) {
							$tabla_info .= '<tr>
													<td class="text-center">'.$info['cantidad'].'</td>
													<td class="text-center">'.$info['importe'].'</td>
													<td class="text-center">'.$info['descripcion'].'</td>
												</tr>';

						}

						$tabla_info .= '	</tbody>
											</table>';

					} catch (PDOException $e) {
						die($e->getMessage());
					}	   
					
					$row['fecha'] = date("d/m/Y",strtotime($row['fecha']));
					$row['fecha_cerrada'] = date("d/m/Y",strtotime($row['fecha_cerrada']));

					$json['remisiones'] .= '<tr>
												<td>'.$row['folio'].'</td>
												<td>'.$row['estatus'].'</td>
												<td>'.$row['responsable'].'</td>
												<td>'.$row['shortname'].'</td>
												<td>'.$row['nombre'].'</td>
												<td>'.$row['empresa'].'</td>
												<td>'.$row['servicio'].'</td>
												<td>'.$row['ocs'].'</td>
												<td>'.$row['fecha'].'</td>
												<td>'.$row['fecha_cerrada'].'</td>
												<td>'.$row['factura'].'</td>
												<td>'.$row['cerro'].'</td>
												<td>'.$row['direccion'].'</td>
												<td>'.$tabla_info.'</td>
												<td>'.$row['notas'].'</td>
											</tr>';
				}
			} else {
				$json['remisiones'] = '<tr><td colspan="15"><h1 class="text-center">No se encontraron resultados con los filtros ingresados.</h1></td></tr>';
			}

			echo json_encode($json);

		} catch(PDOException $e) {
			die($e->getMessage());
		}
	}

	function clientes() {
		$json = array();
		$json['error'] = false;
		$json['msg'] = '';
		$json['clientes'] = '';

		$db = $this->_conexion;
		//if(isset($_POST['siu_id'])) {
			$sql = "SELECT * FROM clientes";
			$value = array();
			$consulta = $db->prepare($sql);

			try {
				$consulta->execute($value);
				$result = $consulta->fetchAll(PDO::FETCH_ASSOC);
				$json['clientes'] = '<select id="cliente" name="cliente" class="form-control">
										<option value="-1">Todos</option>';					

				foreach ($result as $row) {
					$json['clientes'] .= '<option value="'.$row['id'].'">'.$row['shortname'].' - '.$row['empresa'].'</option>';
				}

				$json['clientes'] .= '</select>';

			} catch(PDOException $e) {
				die($e->getMessage());
			}
		// } else {
		// 	$json['error'] = true;
		// 	$json['msg'] = 'Favor de elegir un Ejecutivo de Cuenta';
		// }

		echo json_encode($json);	
	}

	function usuarios() {
		$json = array();
		$json['error'] = false;
		$json['msg'] = '';
		$json['responsable'] = '';
		$json['btns'] = '';
		$json['admin'] = false;

		$db = $this->_conexion;

		if(!isset($_SESSION)){
			@session_start();
		}

		$sql = "SELECT SIU_ID, SIU_NOMBRE FROM SISTEMA_USUARIO WHERE SUP_ID != 1";
		$consulta = $db->prepare($sql);
		try {
			$consulta->execute();
			$result = $consulta->fetchAll(PDO::FETCH_ASSOC);
			$json['responsable'] = '<select id="responsable" name="responsable" class="form-control">
										<option value="-1">Todos</option>';
			$json['cerro'] = '<select id="cerro" name="cerro" class="form-control">
								<option value="-1">Todos</option>';

			foreach ($result as $row) {
				$json['responsable'] .= '<option value="'.$row['SIU_ID'].'" '.(isset($_POST['id']) && $_POST['id'] == $row['SIU_ID'] ? 'selected="selected"' : '' ).'>'.$row['SIU_NOMBRE'].'</option>';
				$json['cerro'] .= '<option value="'.$row['SIU_ID'].'" '.(isset($_POST['id']) && $_POST['id'] == $row['SIU_ID'] ? 'selected="selected"' : '' ).'>'.$row['SIU_NOMBRE'].'</option>';
			}

			$json['responsable'] .= '</select>';
			$json['cerro'] .= '</select>';

		} catch(PDOException $e) {
			die($e->getMessage());
		}

		echo json_encode($json);
	}

	function contactos() {
		$json = array();
		$json['msg'] = '';
		$json['error'] = false;
		$json['contacto'] = '';

		$db = $this->_conexion;

		if(!isset($_SESSION)){
			@session_start();
		}

		if(isset($_POST['cli_id'])){

			$sql = "SELECT * FROM contactos WHERE cli_id = ?";
			$value = array($_POST['cli_id']);
			$consulta = $db->prepare($sql);
			try {
				$consulta->execute($value);
				$result = $consulta->fetchAll(PDO::FETCH_ASSOC);
				$json['contacto'] = '<select id="contacto" name="contacto" class="form-control">
										<option value="-1">Todos</option>';

				foreach ($result as $row) {
					$json['contacto'] .= '<option value="'.$row['id'].'>'.$row['nombre'].'</option>';
				}

				$json['contacto'] .= '</select>';

			} catch(PDOException $e) {
				die($e->getMessage());
			}

		} else {
			$json['error'] = true;
			$json['msg'] = 'Favor de elegir Cliente';
		}

		echo json_encode($json);
	}

	function excel() {
		$json = array();
		$json['completado'] = false;

		$columns = array("A",
						 "B",
						 "C",
						 "D",
						 "E",
						 "F",
						 "G",
						 "H",
						 "I",
						 "J",
						 "K",
						 "L",
						 "M",
						 "N",
						 "O",
						 "P",
						 "Q");

		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getProperties()->setCreator("Alliance Soluciones")
					 ->setLastModifiedBy("Alliance Soluciones")
					 ->setTitle("Remisiones")
					 ->setSubject("Remisiones")
					 ->setDescription("Remisiones")
					 ->setKeywords("Remisiones");

		$styleArray = array(
			        'font' => array(
			            'bold' => true
			        ),
			        'alignment' => array(
			            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
			            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			        ),
			        'borders' => array(
					    'bottom' => array(
					    	'style' => PHPExcel_Style_Border::BORDER_THIN
					    )
					  )
			    );
		$styleArray2 = array('alignment' => array(
				            	'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				            	'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
				        	)
						);

		$styleArray3 = array(
							'borders' => array(
							    'bottom' => array(
							      'style' => PHPExcel_Style_Border::BORDER_THIN
							    )
							),
							'alignment' => array(
				            	'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				        	)
						);	    			 

		//Hacemos más grande las columnas, bold la primera y text-center
		foreach ($columns as $column) {
			$objPHPExcel->getActiveSheet()->getStyle($column."1")->applyFromArray($styleArray);
			$objPHPExcel->getActiveSheet()->getStyle($column)->applyFromArray($styleArray2);
		}

		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(14);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(18);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(12);
		$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(18);
		$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(13);
		$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(13);
		$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(18);
		$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(14);
		$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(14);
		$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(40);
		$objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(20);

		//$objPHPExcel->getStyle("M")->getNumberFormat()->setFormatCode('0'); 
		$objPHPExcel->setActiveSheetIndex(0)
		            ->setCellValue('A1', 'FOLIO')
		            ->setCellValue('B1', 'ESTATUS')
		            ->setCellValue('C1', 'RESPONSABLE')
		            ->setCellValue('D1', 'CLIENTE')
		            ->setCellValue('E1', 'CONTACTO')
		            ->setCellValue('F1', 'EMPRESA')
		            ->setCellValue('G1', 'SERVICIO')
		            ->setCellValue('H1', 'ORDEN(ES) DE COMPRA')
		            ->setCellValue('I1', 'FECHA ALTA')
		            ->setCellValue('J1', 'FECHA CERRADA')
		            ->setCellValue('K1', 'FACTURA')
		            ->setCellValue('L1', 'QUIEN CERRO')
		            ->setCellValue('M1', 'DIRECCION')
		            ->setCellValue('N1', 'CANTIDAD')
		            ->setCellValue('O1', 'IMPORTE')
		            ->setCellValue('P1', 'DESCRIPCION')
		            ->setCellValue('Q1', 'NOTAS');         


		$where = '';
		$values = array();
		if(isset($_POST['folio']) && !empty($_POST['folio'])) {
			$where .= ' AND folio = ? ';
			$values[]= $_POST['folio'];
		}

		if(isset($_POST['estatus']) && $_POST['estatus'] != -1) {
			$where .= ' AND  estatus = ? ';
			$values[]= $_POST['estatus'];
		}

		if(isset($_POST['responsable']) && $_POST['responsable'] > -1) {
			$where .= ' AND rem.siu_id = ? ';
			$values[]= $_POST['responsable'];
		}

		if(isset($_POST['cliente']) && $_POST['cliente'] > -1) {
			$where .= ' AND cli_id = ? ';
			$values[]= $_POST['cliente'];

			if(isset($_POST['contacto']) && $_POST['contacto'] > -1) {
				$where .= ' AND cont_id = ? ';
				$values[]= $_POST['contacto'];
			}
		}

		if(isset($_POST['empresa']) && $_POST['empresa'] > -1) {
			$where .= ' AND rem.empresa = ? ';
			$values[]= $_POST['empresa'];
		}
 
		if(isset($_POST['servicio']) && $_POST['servicio'] > -1) {
			$where .= ' AND servicio = ? ';
			$values[]= $_POST['servicio'];
		}

		if(isset($_POST['servicio']) && $_POST['servicio'] > -1) {
			$where .= ' AND servicio = ? ';
			$values[]= $_POST['servicio'];
		}

		if(isset($_POST['oc']) && !empty($_POST['oc'])) {
			$where .= ' AND ocs LIKE "%'.$_POST['oc'].'%"';
		}

		if(isset($_POST['alta-inicio']) && !empty($_POST['alta-inicio'])) {
			$fecha = date("Y-m-d",strtotime(str_replace("/", "-", $_POST['alta-inicio'])));
			$where .= ' AND fecha >= "'.$fecha.'"';
		}

		if(isset($_POST['alta-fin']) && !empty($_POST['alta-fin'])) {
			$fecha = date("Y-m-d",strtotime(str_replace("/", "-", $_POST['alta-fin'])));
			$where .= ' AND fecha <= "'.$fecha.'"';
		}

		if(isset($_POST['cerrar-inicio']) && !empty($_POST['cerrar-inicio'])) {
			$fecha = date("Y-m-d",strtotime(str_replace("/", "-", $_POST['cerrar-inicio'])));
			$where .= ' AND fecha_cerrada >= "'.$fecha.'"';
		}

		if(isset($_POST['cerrar-fin']) && !empty($_POST['cerrar-fin'])) {
			$fecha = date("Y-m-d",strtotime(str_replace("/", "-", $_POST['cerrar-fin'])));
			$where .= ' AND fecha_cerrada <= "'.$fecha.'"';
		}

		if(isset($_POST['factura']) && !empty($_POST['factura'])) {
			$where .= ' AND factura LIKE "%'.$_POST['factura'].'%"';
		}

		if(isset($_POST['cerro']) && $_POST['cerro'] > -1) {
			$where .= ' AND siu_cerrada = ? ';
			$values[]= $_POST['cerro'];
		}


		/*DATOS*/
		$sql = "SELECT rem.*,
						shortname,
						nombre,
						SISTEMA_USUARIO.SIU_NOMBRE as responsable,
						SISTEMA_USUARIO_2.SIU_NOMBRE as cerro
				 FROM rem
				 LEFT JOIN clientes ON rem.cli_id = clientes.id
				 LEFT JOIN contactos ON rem.cont_id = contactos.id
				 LEFT JOIN SISTEMA_USUARIO ON rem.siu_id = SISTEMA_USUARIO.SIU_ID
				 LEFT JOIN SISTEMA_USUARIO SISTEMA_USUARIO_2 ON rem.siu_cerrada = SISTEMA_USUARIO_2.SIU_ID
				 WHERE 1 = 1 ".$where;

		$db = $this->_conexion;
		$consulta = $db->prepare($sql);

		$n = 2;

		try {
			
			$consulta->execute();
			$result = $consulta->fetchAll(PDO::FETCH_ASSOC);

			foreach ($result as $row) {

				$row_inicial = $n;

				$fecha = (is_null($row['fecha']) ? '' : date("d/m/Y",strtotime($row['fecha'])));
				$fecha_cerrada = (is_null($row['fecha_cerrada']) ? '' : date("d/m/Y",strtotime($row['fecha_cerrada'])));

				//AGREGAMOS LA ROW
				$objPHPExcel->setActiveSheetIndex(0)
		            ->setCellValue('A'.$n, $row['folio'])
		            ->setCellValue('B'.$n, $row['estatus'])
		            ->setCellValue('C'.$n, $row['responsable'])
		            ->setCellValue('D'.$n, $row['shortname'])
		            ->setCellValue('E'.$n, $row['nombre'])
		            ->setCellValue('F'.$n, $row['empresa'])
		            ->setCellValue('G'.$n, $row['servicio'])
		            ->setCellValue('H'.$n, $row['ocs'])
		            ->setCellValue('I'.$n, $fecha)
		            ->setCellValue('J'.$n, $fecha_cerrada)
		            ->setCellValue('K'.$n, $row['factura'])
		            ->setCellValue('L'.$n, $row['cerro'])
		            ->setCellValue('M'.$n, $row['direccion'])
		            ->setCellValue('Q'.$n, $row['notas']);

		        $objPHPExcel->getActiveSheet()->getStyle('M'.$n)->getAlignment()->setWrapText(true);
		        $objPHPExcel->getActiveSheet()->getStyle('Q'.$n)->getAlignment()->setWrapText(true);

		        //RECORRE LOS PRODUCTOS
		        $sql_prods = "SELECT * FROM rem_info WHERE rem_id = ?";
				$values_prods = array($row['id']);
				$consulta_prods = $db->prepare($sql_prods);	
				
				try {
					$consulta_prods->execute($values_prods);
					$result_prods = $consulta_prods->fetchAll(PDO::FETCH_ASSOC);
					$n_p = 0;						

					foreach ($result_prods as $row_prods) {
						$n_p++;
						
						$objPHPExcel->setActiveSheetIndex(0)
						            ->setCellValue('N'.$n, $row_prods['cantidad'])
						            ->setCellValue('O'.$n, $row_prods['importe'])
						            ->setCellValue('P'.$n, $row_prods['descripcion']);

						$n++;

					}

					$n--;        

				} catch (PDOException $e) {
					die($e->getMessage());
				}

				//MERGE
				$objPHPExcel->getActiveSheet()->mergeCells('A'.$row_inicial.':A'.$n);		    	 
				$objPHPExcel->getActiveSheet()->mergeCells('B'.$row_inicial.':B'.$n);		    	 
				$objPHPExcel->getActiveSheet()->mergeCells('C'.$row_inicial.':C'.$n);		    	 
				$objPHPExcel->getActiveSheet()->mergeCells('D'.$row_inicial.':D'.$n);		    	 
				$objPHPExcel->getActiveSheet()->mergeCells('E'.$row_inicial.':E'.$n);		    	 
				$objPHPExcel->getActiveSheet()->mergeCells('F'.$row_inicial.':F'.$n);	 
				$objPHPExcel->getActiveSheet()->mergeCells('G'.$row_inicial.':G'.$n);
				$objPHPExcel->getActiveSheet()->mergeCells('H'.$row_inicial.':H'.$n);
				$objPHPExcel->getActiveSheet()->mergeCells('I'.$row_inicial.':I'.$n);
				$objPHPExcel->getActiveSheet()->mergeCells('J'.$row_inicial.':J'.$n);
				$objPHPExcel->getActiveSheet()->mergeCells('K'.$row_inicial.':K'.$n);
				$objPHPExcel->getActiveSheet()->mergeCells('L'.$row_inicial.':L'.$n);
				$objPHPExcel->getActiveSheet()->mergeCells('M'.$row_inicial.':M'.$n);

				$objPHPExcel->getActiveSheet()->getStyle('A'.$n.':Q'.$n)->applyFromArray($styleArray3);

		    	$n++;

			}

		} catch (PDOException $e) {
			die($e->getMessage().$sql);
		}
		

		$objPHPExcel->getActiveSheet()->setTitle('Remisiones');  
		$objPHPExcel->setActiveSheetIndex(0);    
		//$objPHPExcel->setOutputEncoding('UTF-8');
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save(str_replace('Libs.php', 'remisiones.xlsx', __FILE__));      


		$json['completado'] = true;

		echo json_encode($json);
	}
	
}

if(isset($_REQUEST['accion'])){
	//Se inicializa la clase
	$libs = new Libs;
	switch($_REQUEST['accion']){
		case "printTable":
			$libs->printTable();
			break;	
		case "clientes":
			$libs->clientes();
			break;	
		case "usuarios":
			$libs->usuarios();
			break;	
		case "contactos":
			$libs->contactos();
			break;
		case "excel":
			$libs->excel();
			break;																	
	}
}

?>