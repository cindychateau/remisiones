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
$module = 8;

$url_solicitud = 'localhost/remisiones/';

class Libs extends Common {
	/*
	 * @author: Cynthia Castillo 
	 *  
	 * Imprime la tabla de registros de perfil de usuarios EXCEPTUANDO 'daemon'
	 */
	function printTable() {
		global $module;

		if(!isset($_SESSION)){
			@session_start();
		}

		/*
		 * Query principal
		 */

		$sup_id = $_SESSION["gm_adm"]["userprofile"];
		$siu_id = $_SESSION["gm_adm"]["userid"];
		$where = '';
		$values = array();

		if($sup_id == 3) {
			$where = ' AND rem.siu_id = ? ';
			$values[]= $siu_id;
		}
		$db = $this->_conexion;
		$sqlQuery = "SELECT rem.*,
							shortname,
							nombre,
							SIU_NOMBRE
					 FROM rem
					 LEFT JOIN clientes ON rem.cli_id = clientes.id
					 LEFT JOIN contactos ON rem.cont_id = contactos.id
					 LEFT JOIN SISTEMA_USUARIO ON rem.siu_id = SISTEMA_USUARIO.SIU_ID
					 WHERE estatus = 'Abierta' ".$where;
		
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
			$counter = 0;
			
			foreach ($puntero as $row) {
				$counter++;

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

				//Botones
				if($sup_id == 3) {
					$btn_editar = '<a class="btn-editar btn-action" href="#" data-id="'.$row['id'].'">
									<button type="button" class="btn btn-info"><i class="fa ft-edit-2"></i>Ver/Editar</button>
								   </a>';
					$btn_solicitar_cerrar = '<a class="btn-solicitar btn-action" href="#" data-id="'.$row['id'].'">
												<button type="button" class="btn btn-primary"><i class="fa ft-alert-circle"></i>Cerrar con Solicitud</button>
											   </a>';

				} else {
					$params_editar = array(	"link"		=>	"cambios.php?id=".$row['id'],
										"title"		=>	"Ver/Editar");
					$btn_editar = $this->printButton($module, "cambios", $params_editar);
					$btn_solicitar_cerrar = '<a class="btn-cerrar btn-action" href="#" data-id="'.$row['id'].'" data-name="'.$row['folio'].'">
												<button type="button" class="btn btn-primary"><i class="fa ft-user-x"></i>Cerrar sin Factura</button>
											   </a>';
				}

				$btn_cerrar= '<a class="btn-factura btn-action" href="#" data-id="'.$row['id'].'">
								<button type="button" class="btn btn-primary"><i class="fa ft-log-in"></i>Cerrar con Factura</button>
							   </a>';

				$btn_imprimir= '<a class="btn-action" target="_blank" href="include/print.php?id='.$row['id'].'" data-id="'.$row['id'].'">
								<button type="button" class="btn btn-blue"><i class="fa ft-printer"></i>Imprimir con Fondo</button>
							   </a>';

				$btn_imprimir.= '<a class="btn-action" target="_blank" href="include/print.php?id='.$row['id'].'&fondo=0" data-id="'.$row['id'].'">
								<button type="button" class="btn btn-blue"><i class="fa ft-file"></i>Imprimir sin Fondo</button>
							   </a>';			   			   
				
				$row['fecha'] = date("d/m/Y",strtotime($row['fecha']));

				$aRow = array($row['folio'],$row["SIU_NOMBRE"], $row['shortname'], $row['nombre'], $row['empresa'],$row['servicio'], $row['ocs'], $row['fecha'], $row['direccion'], $tabla_info, $btn_editar.$btn_cerrar.$btn_solicitar_cerrar.$btn_imprimir);
				
				//Se guarda la fila en la matriz principal
				$data[] = $aRow;
			}

			$json = array();
			$json['data'] = $data;

			echo json_encode($json);
		} catch(PDOException $e) {
			die($e->getMessage());
		}
	}

	/*
	 * @author: Cynthia Castillo
	 * 
	 * @param '$id'		int. 	ID de perfil de usuario
	 * 
	 * @return '$json'	array. 	Indica si la acción se hizo con éxito
	 * 
	 * Metodo que borra una fila de la BD
	 */
	function deleteRecord() {
		$json = array();
		$json['error'] = true;
		$json['msg'] = "Experimentamos fallas técnicas.";
		if(isset($_POST['id'])){
			try{
				$consulta = $this->_conexion->prepare("DELETE FROM PROVEEDOR WHERE PRV_ID = :valor");
				$consulta->bindParam(':valor', $_POST['id']);
				$consulta->execute();
				if($consulta->rowCount()){
					$json['msg'] = "El Proveedor fue eliminado con éxito.";
					$json['error'] = false;
				} else{
					$json['error'] = true;
					$json['msg'] = "El Proveedor elegido no pudo ser eliminado.";
				}
			}catch(PDOException $e){
				die($e->getMessage());
			}	
		}

		echo json_encode($json);
	}

	/*
	 * @author: Cynthia Castillo
	 * 
	 * @param '$id'		int. 	ID de perfil de usuario
	 * 
	 * Metodo que imprime la tabla de permisos de un perfil de usuario en base a su id
	 */
	function showRecord() {
		$json = array();
		$json['error'] = false;
		$json['msg'] = "Experimentamos fallas técnicas.";
		if(isset($_POST['id'])){
			try{
				$sql = "SELECT *
						FROM rem
						WHERE id = :valor";

				$consulta = $this->_conexion->prepare($sql);
				$consulta->bindParam(':valor', $_POST['id']);
				$consulta->execute();
				$result = $consulta->fetchAll(PDO::FETCH_ASSOC);

				if ($consulta->rowCount() > 0) {
					$row = $result[0];

					$json = array_merge($json, $row);    

					$json['fecha'] = date("d/m/Y",strtotime($row['fecha']));

					/*Verifica Dirección de Entrega*/
					$json['num_info'] = 0;
					$json['html_info'] = '';
					$sql_dir_fact = "SELECT *
									 FROM rem_info
									 WHERE rem_id = :valor";

					$consulta_dir_ent = $this->_conexion->prepare($sql_dir_fact);
					$consulta_dir_ent->bindParam(':valor', $_POST['id']);
					$consulta_dir_ent->execute();
					$result_dir_ent = $consulta_dir_ent->fetchAll(PDO::FETCH_ASSOC);
					foreach ($result_dir_ent as $row_dir_ent) {
						$json['html_info'] .= '<tr>
					                                <td>
					                                  <input type="text" name="cantidad['.$json['num_info'].']" class="form-control" value="'.$row_dir_ent['cantidad'].'">
					                                  <input type="hidden" name="info_id['.$json['num_info'].']" class="form-control" value="'.$row_dir_ent['id'].'">
					                                </td>
					                                <td>
					                                  <input type="text" name="importe['.$json['num_info'].']" class="form-control" value="'.$row_dir_ent['importe'].'">
					                                </td>
					                                <td>
					                                  <textarea class="form-control" name="descripcion['.$json['num_info'].']">'.$row_dir_ent['descripcion'].'</textarea>
					                                </td>
					                                <td><i class="ft ft-trash-2 eliminar" data-id="'.$row_dir_ent['id'].'"></i></td>
					                              </tr>';

						$json['num_info']++;                            
					}
						
				} else {
					$json['error'] = true;
				}

			}catch(PDOException $e){
				die($e->getMessage());
			}
		}
		echo json_encode($json);
	}

	/*
	 * @author: Cynthia Castillo 
	 * @version: 0.1 2013-12-27
	 * 
	 * 
	 * Guarda el perfil de un usuario
	 */
	function saveRecord() {
		global $ruta;
		$json = array();
		$json["msg"] = "Todos los campos son obligatorios.";
		$json["error"] = false;
		$json["focus"] = "";

		$obligatorios = array("folio",
							  "ejecutivo",
							  "cliente",
							  "contacto",
							  "direccion",
							  "empresa",
							  "servicio");
		$excepciones = array("oc",
							 "cantidad",
							 "importe",
							 "descripcion",
							 "info_id",
							 "del_info_id");

		//VALIDACIÓN
		foreach($_POST as $clave=>$valor){
			if(!$json["error"] && !in_array($clave, $excepciones)){
				if($this->is_empty(trim($valor)) && in_array($clave, $obligatorios)) {
					$json["error"] = true;
					$json["focus"] = $clave;
					$json['msg'] = "El campo ". lcfirst($clave)." es obligatorio.";	
				}
			}
		}

		/*Verificamos que no haya un folio de esa empresa igual*/
		$db = $this->_conexion;
		if(!$json['error'])  {
			$values_check = array($_POST['folio'],
								  $_POST['empresa']);
			if(!isset($_POST['id'])) {
				$sql_check = "SELECT folio, empresa
							  FROM rem
							  WHERE folio = ?
							  AND empresa = ?";
			} else {
				$sql_check = "SELECT folio, empresa
							  FROM rem
							  WHERE folio = ?
							  AND empresa = ?
							  AND id != ?";
				$values_check[] = $_POST['id'];
			}
			
			$consulta_check = $db->prepare($sql_check);
			$consulta_check->execute($values_check);
			if($consulta_check->rowCount()) {
				$json['error'] = true;
				$json['msg'] = 'El folio ingresado ('.$_POST['folio'].') ya se encuentra registrado para la empresa '.$_POST['empresa'].'. Favor de corregirlo.';
			}
		}

		/*Cambiamos formatos de fechas*/
		if(!$json['error']) {
			$fecha = date("Y-m-d",strtotime(str_replace("/", "-", $_POST['fecha'])));
		}

		if(!$json["error"]) {
			
			$db->beginTransaction();

			$ocs = '';

			if(isset($_POST['oc'])) {
				$ocs = implode(', ', $_POST['oc']);
			}

			$values = array($_POST['folio'],
							$_POST['ejecutivo'],
							$_POST['cliente'],
							$_POST['contacto'],
							$_POST['direccion'],
							$_POST['empresa'],
							$_POST['servicio'],
							$ocs,
							$_POST['notas'],
							$_POST['subtotal'],
							$_POST['iva'],
							$_POST['total'],
							$fecha);

			if(isset($_POST['id'])) { //UPDATE
				$sql = "UPDATE rem SET folio = ?,
								   	   siu_id = ?,
								   	   cli_id = ?,
								   	   cont_id = ?,
								   	   direccion = ?,
								   	   empresa = ?,
								   	   servicio = ?,
								   	   ocs = ?,
								   	   notas = ?,
								   	   subtotal = ?,
								   	   iva = ?,
								   	   total = ?,
								   	   fecha = ?
						WHERE id = ?";

				$values[] = $_POST['id'];

			} else { //INSERCION
				$sql = "INSERT INTO rem (folio,
							   	   		 siu_id,
							   	   		 cli_id,
							   	   		 cont_id,
							   	   		 direccion,
							   	   		 empresa,
							   	   		 servicio,
							   	   		 ocs,
							   	   		 notas,
							   	   		 subtotal,
							   	   		 iva,
							   	   		 total,
							   	   		 fecha)
						VALUES( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? )";
			}

			$consulta = $db->prepare($sql);

			try {
				$consulta->execute($values);

				if(isset($_POST['id'])) {
					$rem_id = $_POST['id'];
				} else {
					$rem_id = $this->last_id();


					//Actualizamos el folio siguiente
					$siguiente_folio = $_POST['folio']+1;
					$sql_folio = 'UPDATE folios SET folio = ?
								  WHERE empresa = ?';
					$values_folio = array($siguiente_folio,
										  $_POST['empresa']);
					$consulta_folio = $db->prepare($sql_folio);
					try {
						$consulta_folio->execute($values_folio);
					} catch(PDOException $e) {
						$db->rollBack();
						die($e->getMessage().$sql_folio);
					}
				}

				//Eliminamos la info de las remisiones que se borraron
				if(isset($_POST['del_info_id'])) {
					foreach ($_POST['del_info_id'] as $info_id){
						$consulta_info = $db->prepare("DELETE FROM rem_info WHERE id = ?");
						$value_info_id = array($info_id);
						try {
							$consulta_info->execute($value_info_id);
						} catch(PDOException $e) {
							$db->rollBack();
							die($e->getMessage().'delete info');
						}
					}
				}

				//Recorremos todas la info
				if(isset($_POST['cantidad'])) {
					foreach ($_POST['cantidad'] as $key => $cantidad) {
						$values_info = array($_POST['cantidad'][$key],
											 $_POST['importe'][$key],
											 $_POST['descripcion'][$key],
											 $rem_id);

						if(isset($_POST['info_id'][$key])) { //INFO ya dada de alta
							$sql_info = 'UPDATE rem_info SET cantidad = ?,
																 importe = ?,
																 descripcion = ?,
																 rem_id = ?
											 WHERE id = ?';
							$values_info[] = $_POST['info_id'][$key];
						} else { //Dirección nueva
							$sql_info = 'INSERT INTO rem_info (cantidad,
																   importe,
																   descripcion,
																   rem_id)
											 VALUES( ?, ?, ?, ? )';
						}

						$consulta_galerias = $db->prepare($sql_info);
							
						try {
							$consulta_galerias->execute($values_info);
						} catch(PDOException $e) {
							$db->rollBack();
							die($e->getMessage().$sql_info);
						}
					}
				}

			} catch(PDOException $e) {
				$db->rollBack();
				die($e->getMessage());
			}

			$db->commit();
			$json['msg'] = 'La Remisión se guardó con éxito.';
		}

		echo json_encode($json);
	}

	function getClients() {
		$json = array();
		$json['error'] = false;
		$json['msg'] = '';
		$json['clientes'] = '';

		$db = $this->_conexion;
		//if(isset($_POST['siu_id'])) {
			$sql = "SELECT * FROM clientes";
			$value = array();
			$consulta = $db->prepare($sql);

			if(!isset($_SESSION)){
				@session_start();
			}

			$sup_id = $_SESSION["gm_adm"]["userprofile"];
			$siu_id = $_SESSION["gm_adm"]["userid"];

			if($sup_id == 3) {
				$json['btns'] = '<a class="btn-action btn-add-client" href="#" data-id="">
	                                <button type="button" class="btn btn-success"><i class="fa ft-plus-circle"></i>Agregar Cliente</button>
	                              </a>';
			} else {
				$json['btns'] = '<a class="btn-action" href="../../clientes/alta.php" data-id="" target="_blank">
	                                <button type="button" class="btn btn-success"><i class="fa ft-plus-circle"></i>Agregar Cliente</button>
	                              </a>';
		        $json['admin'] = true;
			}

			try {
				$consulta->execute($value);
				$result = $consulta->fetchAll(PDO::FETCH_ASSOC);
				$json['clientes'] = '<select id="cliente" name="cliente" class="form-control">
										<option></option>';
				$json['cliente_select'] = '<select id="cliente_select" name="cliente_select" class="form-control">
										<option></option>';						

				foreach ($result as $row) {
					$json['clientes'] .= '<option value="'.$row['id'].'" '.(isset($_POST['id']) && $_POST['id'] == $row['id'] ? 'selected="selected"' : '' ).'>'.$row['shortname'].' - '.$row['empresa'].'</option>';

					$json['cliente_select'] .= '<option value="'.$row['id'].'">'.$row['shortname'].' - '.$row['empresa'].'</option>';
				}

				$json['clientes'] .= '</select>';
				$json['cliente_select'] .= '</select>';

			} catch(PDOException $e) {
				die($e->getMessage());
			}
		// } else {
		// 	$json['error'] = true;
		// 	$json['msg'] = 'Favor de elegir un Ejecutivo de Cuenta';
		// }

		echo json_encode($json);	
	}

	function getEjecutivos() {
		$json = array();
		$json['error'] = false;
		$json['msg'] = '';
		$json['ejecutivo'] = '';
		$json['btns'] = '';
		$json['admin'] = false;

		$db = $this->_conexion;

		if(!isset($_SESSION)){
			@session_start();
		}

		$sup_id = $_SESSION["gm_adm"]["userprofile"];
		$siu_id = $_SESSION["gm_adm"]["userid"];

		$where = '';
		if($sup_id == 3) {
			$where = ' WHERE SIU_ID = '.$siu_id;
		} else {
			$json['btns'] = '<a class="btn-action" href="../../sistema/usuarios/alta.php" data-id="" target="_blank">
	                            <button type="button" class="btn btn-success"><i class="fa ft-plus-circle"></i>Agregar Usuario</button>
	                          </a>
	                          <button class="btn btn-outline-success btn-action recargar-ejecutivos"><i class="fa ft-refresh-cw"></i>Recargar Usuario</button>';
	        $json['admin'] = true;
		}

		$sql = "SELECT SIU_ID, SIU_NOMBRE FROM SISTEMA_USUARIO".$where;
		$consulta = $db->prepare($sql);
		try {
			$consulta->execute();
			$result = $consulta->fetchAll(PDO::FETCH_ASSOC);
			$json['ejecutivo'] = '<select id="ejecutivo" name="ejecutivo" class="form-control">';

			foreach ($result as $row) {
				$json['ejecutivo'] .= '<option value="'.$row['SIU_ID'].'" '.(isset($_POST['id']) && $_POST['id'] == $row['SIU_ID'] ? 'selected="selected"' : '' ).'>'.$row['SIU_NOMBRE'].'</option>';
			}

			$json['ejecutivo'] .= '</select>';

		} catch(PDOException $e) {
			die($e->getMessage());
		}

		echo json_encode($json);
	}

	function getContacto() {
		$json = array();
		$json['msg'] = '';
		$json['error'] = false;
		$json['contacto'] = '';

		$db = $this->_conexion;

		if(!isset($_SESSION)){
			@session_start();
		}

		$sup_id = $_SESSION["gm_adm"]["userprofile"];
		$siu_id = $_SESSION["gm_adm"]["userid"];

		if(isset($_POST['cli_id'])){

			$sql = "SELECT * FROM contactos WHERE cli_id = ?";
			$value = array($_POST['cli_id']);
			$consulta = $db->prepare($sql);
			try {
				$consulta->execute($value);
				$result = $consulta->fetchAll(PDO::FETCH_ASSOC);
				$json['contacto'] = '<select id="contacto" name="contacto" class="form-control">
										<option></option>';

				foreach ($result as $row) {
					$json['contacto'] .= '<option value="'.$row['id'].'" '.(isset($_POST['id']) && $_POST['id'] == $row['id'] ? 'selected="selected"' : '' ).'>'.$row['nombre'].'</option>';
				}

				$json['contacto'] .= '</select>';


				//Revisamos el nombre del cliente
				$sql_cli = "SELECT * FROM clientes WHERE id = ?";
				$value_cli = array($_POST['cli_id']);
				$consulta_cli = $db->prepare($sql_cli);
				$consulta_cli->execute($value_cli);
				$cliente = $consulta_cli->fetch(PDO::FETCH_ASSOC);
				if($sup_id == 3) {
					$json['btns'] = '<a class="btn-action btn-add-contact" href="#" data-id="'.$_POST['cli_id'].'" data-name="'.$cliente['shortname'].' - '.$cliente['empresa'].'">
		                                <button type="button" class="btn btn-success"><i class="fa ft-plus-circle"></i>Agregar Contacto</button>
		                              </a>';
				} else {
					$json['btns'] = '<a class="btn-action" href="../clientes/cambios.php?id='.$_POST['cli_id'].'" data-id="" target="_blank">
		                                <button class="btn btn-success btn-no"><i class="fa ft-plus-circle"></i>Agregar Contacto</button>
		                              </a>';
			        $json['admin'] = true;
				}

			} catch(PDOException $e) {
				die($e->getMessage());
			}

		} else {
			$json['error'] = true;
			$json['msg'] = 'Favor de elegir Cliente';
		}

		echo json_encode($json);
	}

	function nuevo_cliente() {
		global $ruta;
		$json = array();
		$json['msg'] = '';
		$json['error'] = false;

		if(!empty($_POST['nuevo_cliente']) && !empty($_POST['nuevo_contacto'])) {
			//Enviamos correo a los administradores
			require_once($ruta.'/include/class.phpmailer.php'); 
            require_once($ruta.'/include/class.smtp.php'); 
            $mail = new PHPMailer(true);

            $mail->CharSet = $this->CharSet;
            $mail->From = $this->From;
            $mail->FromName = $this->FromName;
            $mail->Host = $this->Host;
            $mail->SMTPAuth = $this->SMTPAuth;
            $mail->Username = $this->Username;
            $mail->Password = $this->Password;
            $mail->Port = $this->Port;

			$mail->IsHTML(true);
            $mail->IsSMTP();

            $mail->Subject = 'Solicitud de Nuevo Cliente';

            if(!isset($_SESSION)){
				@session_start();
			}

			$solicita = $_SESSION["gm_adm"]["username"];
			$email_solicita = $_SESSION["gm_adm"]["email"];

            $body = '<table border="0" cellpadding="0" cellspacing="0" width="600">
						<div style="width: 600px; padding: 20px 40px; font-family: sans-serif; font-size: 14px;">
							<div style="background: #ffffff; padding-bottom: 40px;">
								<div style="text-align: center;">
									<img src="https://maxivision.com.mx/demos/remisiones/app-assets/images/logo/logo.png" style="width: 180px;" width="180">
								</div>
								<div style="clear: both;"></div>
							</div>
							<div style="padding: 25px; background-color: #125dab; color: white; text-align: center;">
								<span style="font-size: 25px; font-weight: bold;">Nueva Solicitud de Cliente</span><br><br>
								<span>Se recibió la siguiente solicitud para dar de alta un cliente</span><br><br>
								<img src="https://promosgmfmx.com.mx/img/emails/corporativa.png">
							</div>
							<div style="background-color: #eff3f5; padding: 15px 40px; color: #626366;">
								<div style="width: 47%; float: left;">
									<b style="font-size: 15px; color: #00439e;">Nombre de Cliente:</b><br> <br>
									<div style="font-size: 12px;">
										<b>'.$_POST['nuevo_cliente'].'</b>
									</div>
								</div>
								<div style="width: 47%; float: right;">
									<b style="font-size: 15px; color: #00439e;">Contacto:</b><br> <br>
									<div style="font-size: 12px;">
										<b>'.$_POST['nuevo_contacto'].'</b>
									</div>
								</div>
								<div style="width: 47%; float: left; margin-top: 20px;">
									<b style="font-size: 15px; color: #00439e;">Solicita:</b><br> <br>
									<div style="font-size: 12px;">
										<b>'.$solicita.'</b> - '.$email_solicita.'
									</div>
								</div>
								<div style="clear: both;"></div>
							</div>
							<div style="padding: 20px 25px; background-color: #125dab; color: white; text-align: center; font-size: 16px;">
								<b>Para darlo de alta ingrese al sistema y vaya al módulo de Clientes. Ingrese los datos solicitados y notifique al usuario del alta.</b>
							</div>
						</div>
					</table>';

            $mail->Body = $body;

            $sql = 'SELECT SIU_NOMBRE, SIU_EMAIL FROM SISTEMA_USUARIO WHERE SUP_ID = 2';
            $consulta = $this->_conexion->prepare($sql);
			$consulta->execute();
			$administradores = $consulta->fetchAll(PDO::FETCH_ASSOC);

			foreach ($administradores as $admin) {
				$mail->AddAddress($admin['SIU_EMAIL'], $admin['SIU_NOMBRE']);
			}

            $mail->Send();

            //Hacemos una nueva notificación
			//Enviamos en orden: Titulo, Mensaje, URL, Si es URL interna (0) o Externa (1), Icono y Color
			$this->newNotification('<b>Solicitud Nuevo Cliente</b>',
								   'Cliente: '.$_POST['nuevo_cliente'].' <br>Contacto: '.$_POST['nuevo_contacto'],
								   'clientes/alta.php',
								   0,
								   'la la-male',
								   'primary');

            $json['msg'] = 'Los administradores fueron notificados y se comunicarán con usted cuando el Cliente solicitado esté en el sistema.';

		} else {
			$json['msg'] = 'Favor de ingresar todos los datos.';
			$json['error'] = true;
		}

		echo json_encode($json);
	}

	function nuevo_contacto() {
		global $ruta;
		$json = array();
		$json['msg'] = '';
		$json['error'] = false;

		if(!empty($_POST['nuevo_contacto'])) {
			//Enviamos correo a los administradores
			require_once($ruta.'/include/class.phpmailer.php'); 
            require_once($ruta.'/include/class.smtp.php'); 
            $mail = new PHPMailer(true);

            $mail->CharSet = $this->CharSet;
            $mail->From = $this->From;
            $mail->FromName = $this->FromName;
            $mail->Host = $this->Host;
            $mail->SMTPAuth = $this->SMTPAuth;
            $mail->Username = $this->Username;
            $mail->Password = $this->Password;
            $mail->Port = $this->Port;

			$mail->IsHTML(true);
            $mail->IsSMTP();

            $mail->Subject = 'Solicitud de Nuevo Contacto';

            if(!isset($_SESSION)){
				@session_start();
			}

			$solicita = $_SESSION["gm_adm"]["username"];
			$email_solicita = $_SESSION["gm_adm"]["email"];

			//Consultamos el cliente al que pertenece el contacto
			$sql = 'SELECT shortname, empresa FROM clientes WHERE id = ?';
			$values = array($_POST['cliente_select']);
            $consulta = $this->_conexion->prepare($sql);
			$consulta->execute($values);
			$cliente = $consulta->fetch(PDO::FETCH_ASSOC);

            $body = '<table border="0" cellpadding="0" cellspacing="0" width="600">
						<div style="width: 600px; padding: 20px 40px; font-family: sans-serif; font-size: 14px;">
							<div style="background: #ffffff; padding-bottom: 40px;">
								<div style="text-align: center;">
									<img src="https://maxivision.com.mx/demos/remisiones/app-assets/images/logo/logo.png" style="width: 180px;" width="180">
								</div>
								<div style="clear: both;"></div>
							</div>
							<div style="padding: 25px; background-color: #125dab; color: white; text-align: center;">
								<span style="font-size: 25px; font-weight: bold;">Nueva Solicitud de Contacto</span><br><br>
								<span>Se recibió la siguiente solicitud para dar de alta un contacto</span><br><br>
								<img src="https://promosgmfmx.com.mx/img/emails/corporativa.png">
							</div>
							<div style="background-color: #eff3f5; padding: 15px 40px; color: #626366;">
								<div style="width: 47%; float: right;">
									<b style="font-size: 15px; color: #00439e;">Contacto:</b><br> <br>
									<div style="font-size: 12px;">
										<b>'.$_POST['nuevo_contacto'].'</b>
									</div>
								</div>

								<div style="width: 47%; float: left;">
									<b style="font-size: 15px; color: #00439e;">Para el cliente:</b><br> <br>
									<div style="font-size: 12px;">
										<b>'.$cliente['shortname'].' - '.$cliente['empresa'].'</b>
									</div>
								</div>

								<div style="width: 47%; float: left; margin-top: 20px;">
									<b style="font-size: 15px; color: #00439e;">Solicita:</b><br> <br>
									<div style="font-size: 12px;">
										<b>'.$solicita.'</b> - '.$email_solicita.'
									</div>
								</div>
								<div style="clear: both;"></div>
							</div>
							<div style="padding: 20px 25px; background-color: #125dab; color: white; text-align: center; font-size: 16px;">
								<b>Para darlo de alta ingrese al sistema y vaya al módulo de Clientes. Ingrese los datos solicitados y notifique al usuario del alta.</b>
							</div>
						</div>
					</table>';

            $mail->Body = $body;

            $sql = 'SELECT SIU_NOMBRE, SIU_EMAIL FROM SISTEMA_USUARIO WHERE SUP_ID = 2';
            $consulta = $this->_conexion->prepare($sql);
			$consulta->execute();
			$administradores = $consulta->fetchAll(PDO::FETCH_ASSOC);

			foreach ($administradores as $admin) {
				$mail->AddAddress($admin['SIU_EMAIL'], $admin['SIU_NOMBRE']);
			}

            $mail->Send();
            //Hacemos una nueva notificación
			//Enviamos en orden: Titulo, Mensaje, URL, Si es URL interna (0) o Externa (1), Icono y Color
			$this->newNotification('<b>Solicitud Nuevo Contacto</b>',
								   'Cliente: '.$cliente['empresa'].'<br>Contacto: '.$_POST['nuevo_contacto'],
								   'clientes/cambios.php?id='.$_POST['cliente_select'],
								   0,
								   'la la-male',
								   'cyan');

            $json['msg'] = 'Los administradores fueron notificados y se comunicarán con usted cuando el Contacto solicitado esté en el sistema.';

		} else {
			$json['msg'] = 'Favor de ingresar todos los datos.';
			$json['error'] = true;
		}

		echo json_encode($json);
	}
	
	function solicitud_cambio() {
		global $ruta;
		$json = array();
		$json['msg'] = '';
		$json['error'] = false;

		if(!empty($_POST['descripcion']) && !empty($_POST['motivo']) && !empty($_POST['id'])) {
			//Enviamos correo a los administradores
			require_once($ruta.'/include/class.phpmailer.php'); 
            require_once($ruta.'/include/class.smtp.php'); 
            $mail = new PHPMailer(true);

            $mail->CharSet = $this->CharSet;
            $mail->From = $this->From;
            $mail->FromName = $this->FromName;
            $mail->Host = $this->Host;
            $mail->SMTPAuth = $this->SMTPAuth;
            $mail->Username = $this->Username;
            $mail->Password = $this->Password;
            $mail->Port = $this->Port;

			$mail->IsHTML(true);
            $mail->IsSMTP();

            $mail->Subject = 'Solicitud de Cambio';

            if(!isset($_SESSION)){
				@session_start();
			}

			//Consultamos datos de remisión
			$db = $this->_conexion;
			$values = array($_POST['id']);
			$sql = "SELECT rem.*,
							shortname,
							nombre,
							SIU_NOMBRE
					 FROM rem
					 LEFT JOIN clientes ON rem.cli_id = clientes.id
					 LEFT JOIN contactos ON rem.cont_id = contactos.id
					 LEFT JOIN SISTEMA_USUARIO ON rem.siu_id = SISTEMA_USUARIO.SIU_ID
					 WHERE rem.id = ?";
			
			//Se prepara la consulta de extración de datos
			$consulta = $db->prepare($sql);
				
			$consulta->execute($values);
			
			//Se imprime la tabla
			$remision = $consulta->fetch(PDO::FETCH_ASSOC);

			$solicita = $_SESSION["gm_adm"]["username"];
			$email_solicita = $_SESSION["gm_adm"]["email"];

            $body = '<table border="0" cellpadding="0" cellspacing="0" width="600">
						<div style="width: 600px; padding: 20px 40px; font-family: sans-serif; font-size: 14px;">
							<div style="background: #ffffff; padding-bottom: 40px;">
								<div style="text-align: center;">
									<img src="https://maxivision.com.mx/demos/remisiones/app-assets/images/logo/logo.png" style="width: 180px;" width="180">
								</div>
								<div style="clear: both;"></div>
							</div>
							<div style="padding: 25px; background-color: #125dab; color: white; text-align: center;">
								<span style="font-size: 25px; font-weight: bold;">Nueva Solicitud de Cambio</span><br><br>
								<span>Se recibió la siguiente solicitud para cambios en la siguiente remisión:</span><br><br>
								<img src="https://promosgmfmx.com.mx/img/emails/solicitud.png">
							</div>
							<div style="background-color: #eff3f5; padding: 15px 40px; color: #626366;">
								<div style="width: 47%; float: left;">
									<b style="font-size: 15px; color: #00439e;">Información de Remisión:</b><br> <br>
									<div style="font-size: 12px;">
										<b>Folio: </b> '.$remision['folio'].'<br>
										<b>Empresa: </b> '.$remision['empresa'].'<br>
										<b>Cliente: </b> '.$remision['shortname'].'
									</div>
								</div>
								<div style="width: 47%; float: right;">
									<b style="font-size: 15px; color: #00439e;">Cambios Solicitados:</b><br> <br>
									<div style="font-size: 12px;">
										<b>Cambios: </b>'.$_POST['descripcion'].' <br>
										<b>Motivo: </b>'.$_POST['motivo'].' <br>
									</div>
								</div>
								<div style="width: 47%; float: left; margin-top: 20px;">
									<b style="font-size: 15px; color: #00439e;">Solicita:</b><br> <br>
									<div style="font-size: 12px;">
										<b>'.$solicita.'</b> - '.$email_solicita.'
									</div>
								</div>
								<div style="clear: both;"></div>
							</div>
							<div style="padding: 20px 25px; background-color: #125dab; color: white; text-align: center; font-size: 16px;">
								<b>Para realizar el cambio solicitado ingrese al sistema y vaya al módulo de Remisiones Abiertas, localice la remisión descrita y haga click en Ver/Editar. Ingrese los datos y haga click en Guardar. Al finalizar notifique al usuario del cambio.</b>
							</div>
						</div>
					</table>';

            $mail->Body = $body;

            $sql = 'SELECT SIU_NOMBRE, SIU_EMAIL FROM SISTEMA_USUARIO WHERE SUP_ID = 2';
            $consulta = $this->_conexion->prepare($sql);
			$consulta->execute();
			$administradores = $consulta->fetchAll(PDO::FETCH_ASSOC);

			foreach ($administradores as $admin) {
				$mail->AddAddress($admin['SIU_EMAIL'], $admin['SIU_NOMBRE']);
			}

            $mail->Send();

            //Hacemos una nueva notificación
			//Enviamos en orden: Titulo, Mensaje, URL, Si es URL interna (0) o Externa (1), Icono y Color
			$this->newNotification('<b>Solicitud de Cambio</b>',
								   'Folio: '.$remision['folio'].' <br>Empresa: '.$remision['empresa'].' <br>Cambios: '.$_POST['descripcion'],
								   'rem/abiertas/cambios.php?id='.$_POST['id'],
								   0,
								   'ft-edit',
								   'teal');

            $json['msg'] = 'Los administradores fueron notificados y se comunicarán con usted cuando el cambio solicitado sea aplicado.';

		} else {
			$json['msg'] = 'Favor de ingresar todos los datos.';
			$json['error'] = true;
		}

		echo json_encode($json);
	}

	function cerrar_factura() {
		global $ruta;
		global $url_solicitud;
		$json = array();
		$json['msg'] = '';
		$json['error'] = false;

		if(isset($_POST['id'])) {
			if(!empty($_POST['factura'])) {
				$fecha = date('Y-m-d');

				if(!isset($_SESSION)){
					@session_start();
				}

				$siu_id = $_SESSION["gm_adm"]["userid"];
				$sup_id = $_SESSION["gm_adm"]["userprofile"];

				if($sup_id < 2) {
					$sql = 'UPDATE rem SET 	factura = ?,
											estatus = "Cerrada",
											fecha_cerrada = ?,
											siu_cerrada = ?
							WHERE id = ?';

					$values = array($_POST['factura'],
									$fecha,
									$siu_id,
									$_POST['id']);
					$consulta = $this->_conexion->prepare($sql);
					$consulta->execute($values);

					$json['msg'] = 'Remisión cerrada con éxito. Ahora podrá encontrar la remisión en el módulo de Remisiones - Cerradas.';
				} else {
					//Clave Secreta
					$word = array_merge(range('a', 'z'), range('A', 'Z'));
				    shuffle($word);
				    $clave_secreta = substr(implode($word), 0, 10);

				    //Ponemos como Pendiente la Remisión
				    $sql = 'UPDATE rem SET 	notas2 = ?,
											estatus = "Pendiente",
											factura = ?
							WHERE id = ?';

					$values = array($clave_secreta,
									$_POST['factura'],
									$_POST['id']);
					$consulta = $this->_conexion->prepare($sql);
					$consulta->execute($values);


					//Enviamos correo a los administradores
					require_once($ruta.'/include/class.phpmailer.php'); 
		            require_once($ruta.'/include/class.smtp.php'); 

		            if(!isset($_SESSION)){
						@session_start();
					}

					//Consultamos datos de remisión
					$db = $this->_conexion;
					$values = array($_POST['id']);
					$sql = "SELECT rem.*,
									shortname,
									nombre,
									SIU_NOMBRE
							 FROM rem
							 LEFT JOIN clientes ON rem.cli_id = clientes.id
							 LEFT JOIN contactos ON rem.cont_id = contactos.id
							 LEFT JOIN SISTEMA_USUARIO ON rem.siu_id = SISTEMA_USUARIO.SIU_ID
							 WHERE rem.id = ?";
					
					//Se prepara la consulta de extración de datos
					$consulta = $db->prepare($sql);
						
					$consulta->execute($values);
					
					//Se imprime la tabla
					$remision = $consulta->fetch(PDO::FETCH_ASSOC);

					$solicita = $_SESSION["gm_adm"]["username"];
					$email_solicita = $_SESSION["gm_adm"]["email"];

		            $body = '<table border="0" cellpadding="0" cellspacing="0" width="600">
								<div style="width: 600px; padding: 20px 40px; font-family: sans-serif; font-size: 14px;">
									<div style="background: #ffffff; padding-bottom: 40px;">
										<div style="text-align: center;">
											<img src="https://maxivision.com.mx/demos/remisiones/app-assets/images/logo/logo.png" style="width: 180px;" width="180">
										</div>
										<div style="clear: both;"></div>
									</div>
									<div style="padding: 25px; background-color: #125dab; color: white; text-align: center;">
										<span style="font-size: 25px; font-weight: bold;">Nueva Solicitud para Cerrar Remisión</span><br><br>
										<span>Se recibió la siguiente solicitud para cerrar la remisión:</span><br><br>
										<img src="https://promosgmfmx.com.mx/img/emails/factura.png">
									</div>
									<div style="background-color: #eff3f5; padding: 15px 40px; color: #626366;">
										<div style="width: 47%; float: left;">
											<b style="font-size: 15px; color: #00439e;">Información de Remisión:</b><br> <br>
											<div style="font-size: 12px;">
												<b>Folio: </b> '.$remision['folio'].'<br>
												<b>Empresa: </b> '.$remision['empresa'].'<br>
												<b>Cliente: </b> '.$remision['shortname'].'
											</div>
										</div>
										<div style="width: 47%; float: right;">
											<b style="font-size: 15px; color: #00439e;">Factura:</b><br> <br>
											<div style="font-size: 12px;">
												'.$_POST['factura'].'
											</div>
										</div>
										<div style="width: 47%; float: left; margin-top: 20px;">
											<b style="font-size: 15px; color: #00439e;">Solicita:</b><br> <br>
											<div style="font-size: 12px;">
												<b>'.$solicita.'</b> - '.$email_solicita.'
											</div>
										</div>
										<div style="clear: both;"></div>
									</div>';

			            $sql = 'SELECT SIU_ID, SIU_NOMBRE, SIU_EMAIL FROM SISTEMA_USUARIO WHERE SUP_ID = 2';
			            $consulta = $this->_conexion->prepare($sql);
						$consulta->execute();
						$administradores = $consulta->fetchAll(PDO::FETCH_ASSOC);

						$mail = new PHPMailer(true);

			            $mail->CharSet = $this->CharSet;
			            $mail->From = $this->From;
			            $mail->FromName = $this->FromName;
			            $mail->Host = $this->Host;
			            $mail->SMTPAuth = $this->SMTPAuth;
			            $mail->Username = $this->Username;
			            $mail->Password = $this->Password;
			            $mail->Port = $this->Port;

						$mail->IsHTML(true);
			            $mail->IsSMTP();

			            $mail->Subject = 'Solicitud para Cerrar Remisión';

			            $body_n =$body.'	<div style="padding: 20px 25px; background-color: #125dab; color: white; text-align: center; font-size: 16px;">
										<b>
											Para realizar el cambio solicitado ingrese al siguiente enlace: '.$url_solicitud.'cerrar.php?cs='.$clave_secreta.'&r='.$_POST['id'].'&s='.$admin['SIU_ID'].'<br><br>
											O bien ingrese al sistema y vaya al módulo de Remisiones Pendientes, localice la remisión descrita y haga click en Cerrar Remisión.
										</b>
									</div>
								</div>
							</table>';


			            $mail->Body = $body_n;

						foreach ($administradores as $admin) {

							$mail->AddAddress($admin['SIU_EMAIL'], $admin['SIU_NOMBRE']);
						}

						$mail->Send();

						//Hacemos una nueva notificación
						//Enviamos en orden: Titulo, Mensaje, URL, Si es URL interna (0) o Externa (1), Icono y Color
						$this->newNotification('<b>Pendiente de Cerrar</b>',
											   '',
											   'rem/pendientes',
											   0,
											   'ft-alert-triangle',
											   'pink');


		            $json['msg'] = 'Los administradores fueron notificados. Una vez que el administrador cierre la remisión podrá encontrarla en el módulo de Remisiones Cerradas.';
				}

			} else {
				$json['error'] = true;
				$json['msg'] = 'El campo de factura es obligatorio.';
			}

		} else {
			$json['error'] = true;
			$json['msg'] = 'Error al escoger remisión. Favor de intentar de nuevo más tarde.';
		}

		echo json_encode($json);
	}

	function cerrar_solicitud() {
		global $ruta;
		global $url_solicitud;
		$json = array();
		$json['msg'] = '';
		$json['error'] = false;

		if(isset($_POST['id'])) {
			if(!empty($_POST['motivo'])) {
				//Clave Secreta
				$word = array_merge(range('a', 'z'), range('A', 'Z'));
			    shuffle($word);
			    $clave_secreta = substr(implode($word), 0, 10);

			    //Ponemos como Pendiente la Remisión
			    $sql = 'UPDATE rem SET 	notas2 = ?,
										estatus = "Pendiente",
										motivo_cerrada = ?
						WHERE id = ?';

				$values = array($clave_secreta,
								$_POST['motivo'],
								$_POST['id']);
				$consulta = $this->_conexion->prepare($sql);
				$consulta->execute($values);


				//Enviamos correo a los administradores
				require_once($ruta.'/include/class.phpmailer.php'); 
	            require_once($ruta.'/include/class.smtp.php'); 

	            if(!isset($_SESSION)){
					@session_start();
				}

				//Consultamos datos de remisión
				$db = $this->_conexion;
				$values = array($_POST['id']);
				$sql = "SELECT rem.*,
								shortname,
								nombre,
								SIU_NOMBRE
						 FROM rem
						 LEFT JOIN clientes ON rem.cli_id = clientes.id
						 LEFT JOIN contactos ON rem.cont_id = contactos.id
						 LEFT JOIN SISTEMA_USUARIO ON rem.siu_id = SISTEMA_USUARIO.SIU_ID
						 WHERE rem.id = ?";
				
				//Se prepara la consulta de extración de datos
				$consulta = $db->prepare($sql);
					
				$consulta->execute($values);
				
				//Se imprime la tabla
				$remision = $consulta->fetch(PDO::FETCH_ASSOC);

				$solicita = $_SESSION["gm_adm"]["username"];
				$email_solicita = $_SESSION["gm_adm"]["email"];

	            $body = '<table border="0" cellpadding="0" cellspacing="0" width="600">
							<div style="width: 600px; padding: 20px 40px; font-family: sans-serif; font-size: 14px;">
								<div style="background: #ffffff; padding-bottom: 40px;">
									<div style="text-align: center;">
										<img src="https://maxivision.com.mx/demos/remisiones/app-assets/images/logo/logo.png" style="width: 180px;" width="180">
									</div>
									<div style="clear: both;"></div>
								</div>
								<div style="padding: 25px; background-color: #125dab; color: white; text-align: center;">
									<span style="font-size: 25px; font-weight: bold;">Nueva Solicitud para Cerrar Remisión</span><br><br>
									<span>Se recibió la siguiente solicitud para cerrar la remisión:</span><br><br>
									<img src="https://promosgmfmx.com.mx/img/emails/factura.png">
								</div>
								<div style="background-color: #eff3f5; padding: 15px 40px; color: #626366;">
									<div style="width: 47%; float: left;">
										<b style="font-size: 15px; color: #00439e;">Información de Remisión:</b><br> <br>
										<div style="font-size: 12px;">
											<b>Folio: </b> '.$remision['folio'].'<br>
											<b>Empresa: </b> '.$remision['empresa'].'<br>
											<b>Cliente: </b> '.$remision['shortname'].'
										</div>
									</div>
									<div style="width: 47%; float: right;">
										<b style="font-size: 15px; color: #00439e;">Motivo:</b><br> <br>
										<div style="font-size: 12px;">
											'.$_POST['motivo'].'
										</div>
									</div>
									<div style="width: 47%; float: left; margin-top: 20px;">
										<b style="font-size: 15px; color: #00439e;">Solicita:</b><br> <br>
										<div style="font-size: 12px;">
											<b>'.$solicita.'</b> - '.$email_solicita.'
										</div>
									</div>
									<div style="clear: both;"></div>
								</div>';

		            $sql = 'SELECT SIU_ID, SIU_NOMBRE, SIU_EMAIL FROM SISTEMA_USUARIO WHERE SUP_ID = 2';
		            $consulta = $this->_conexion->prepare($sql);
					$consulta->execute();
					$administradores = $consulta->fetchAll(PDO::FETCH_ASSOC);

					$mail = new PHPMailer(true);

		            $mail->CharSet = $this->CharSet;
		            $mail->From = $this->From;
		            $mail->FromName = $this->FromName;
		            $mail->Host = $this->Host;
		            $mail->SMTPAuth = $this->SMTPAuth;
		            $mail->Username = $this->Username;
		            $mail->Password = $this->Password;
		            $mail->Port = $this->Port;

					$mail->IsHTML(true);
		            $mail->IsSMTP();

		            $mail->Subject = 'Solicitud para Cerrar Remisión';

		            $body_n =$body.'	<div style="padding: 20px 25px; background-color: #125dab; color: white; text-align: center; font-size: 16px;">
									<b>
										Para realizar el cambio solicitado ingrese al siguiente enlace: '.$url_solicitud.'cerrar.php?cs='.$clave_secreta.'&r='.$_POST['id'].'&s='.$admin['SIU_ID'].'<br><br>
										O bien ingrese al sistema y vaya al módulo de Remisiones Pendientes, localice la remisión descrita y haga click en Cerrar Remisión.
									</b>
								</div>
							</div>
						</table>';


		            $mail->Body = $body_n;

					foreach ($administradores as $admin) {

						$mail->AddAddress($admin['SIU_EMAIL'], $admin['SIU_NOMBRE']);

					}

					$mail->Send();

					//Hacemos una nueva notificación
					//Enviamos en orden: Titulo, Mensaje, URL, Si es URL interna (0) o Externa (1), Icono y Color
					$this->newNotification('<b>Pendiente de Cerrar</b>',
										   '',
										   'rem/pendientes',
										   0,
										   'ft-alert-triangle',
										   'pink');


	            $json['msg'] = 'Los administradores fueron notificados. Por lo pronto puede encontrar su remisión en Remisiones Pendientes. Una vez que el administrador cierre la remisión podrá encontrarla en el módulo de Remisiones Cerradas.';

			} else {
				$json['error'] = true;
				$json['msg'] = 'El campo de motivo es obligatorio.';
			}

		} else {
			$json['error'] = true;
			$json['msg'] = 'Error al escoger remisión. Favor de intentar de nuevo más tarde.';
		}

		echo json_encode($json);
	}

	function cerrar() {
		global $ruta;
		$json = array();
		$json['msg'] = '';
		$json['error'] = false;

		if(!isset($_SESSION)){
			@session_start();
		}

		$sup_id = $_SESSION["gm_adm"]["userprofile"];

		if($sup_id < 2) {
			if(isset($_POST['id'])) {
				if(!empty($_POST['motivo'])) {
					$fecha = date('Y-m-d');

					if(!isset($_SESSION)){
						@session_start();
					}

					$siu_id = $_SESSION["gm_adm"]["userid"];

					$sql = 'UPDATE rem SET 	estatus = "Cerrada",
											fecha_cerrada = ?,
											siu_cerrada = ?,
											motivo_cerrada = ?
							WHERE id = ?';

					$values = array($fecha,
									$siu_id,
									$_POST['motivo'],
									$_POST['id']);
					$consulta = $this->_conexion->prepare($sql);
					$consulta->execute($values);

					$json['msg'] = 'Remisión cerrada con éxito. Ahora podrá encontrar la remisión en el módulo de Remisiones - Cerradas.';
				} else {
					$json['error'] = true;
					$json['msg'] = 'Motivo es un campo obligatorio.';
				}

			} else {
				$json['error'] = true;
				$json['msg'] = 'Error al escoger remisión. Favor de intentar de nuevo más tarde.';
			}
		} else {
			$json['error'] = true;
			$json['msg'] = 'Usuario sin permisos para cerrar la remisión sin factura. Inténtelo de nuevo más tarde.';
		}

		echo json_encode($json);
	}

	function getFolio() {
		$json = array();
		$json['error'] = false;
		$json['msg'] = '';

		if(isset($_POST['empresa'])) {
			$db = $this->_conexion;
			$sql = "SELECT folio FROM folios WHERE empresa = ?";
			$values = array($_POST['empresa']);
			$consulta = $db->prepare($sql);
			try {
				$consulta->execute($values);
				$folio = $consulta->fetch(PDO::FETCH_ASSOC);
				$json['folio'] = $folio['folio'];

			} catch(PDOException $e) {
				die($e->getMessage());
			}
		}

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
		case "deleteRecord":
			$libs->deleteRecord();
			break;
		case "showRecord":
			$libs->showRecord();
			break;
		case "saveRecord":
			$libs->saveRecord();
			break;	
		case "getClients":
			$libs->getClients();
			break;	
		case "getEjecutivos":
			$libs->getEjecutivos();
			break;	
		case "getContacto":
			$libs->getContacto();
			break;	
		case "getDelivery":
			$libs->getDelivery();
			break;	
		case "nuevo_cliente":
			$libs->nuevo_cliente();
			break;
		case "nuevo_contacto":
			$libs->nuevo_contacto();
			break;	
		case "solicitud_cambio":
			$libs->solicitud_cambio();
			break;
		case "cerrar_factura":
			$libs->cerrar_factura();
			break;
		case "cerrar_solicitud":
			$libs->cerrar_solicitud();
			break;
		case "cerrar":
			$libs->cerrar();
			break;
		case "getFolio":
			$libs->getFolio();
			break;																		
	}
}

?>