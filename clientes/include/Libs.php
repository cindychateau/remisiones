<?php

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
$module = 3;

class Libs extends Common {
	/*
	 * @author: Cynthia Castillo 
	 *  
	 * Imprime la tabla de registros de perfil de usuarios EXCEPTUANDO 'daemon'
	 */
	function printTable() {
		global $module;
		global $ruta;

		/*
		 * Query principal
		 */
		$sqlQuery = "SELECT *
					 FROM clientes";
		
		//Se prepara la consulta de extración de datos
		$consulta = $this->_conexion->prepare($sqlQuery);

		//echo $sqlQueryFiltered;

		//Se ejecuta la consulta
		try {
			
			$consulta->execute();
			
			//Se imprime la tabla
			$puntero = $consulta->fetchAll(PDO::FETCH_ASSOC);
			
			/*
			* Salida de Datos
			*/
			$data = array();
			$counter = 0;
			
			foreach ($puntero as $cliente) {
				$counter++;

				//Botones
				$params_editar = array(	"link"		=>	"cambios.php?id=".$cliente['id'],
										"title"		=>	"Ver/Editar");
				$btn_editar = $this->printButton($module, "cambios", $params_editar);
				$params_borrar = array(	"title"		=>	"Borrar",
										"classes"	=>	"borrar",
										"data_id"	=>	$cliente['id'],
										"extras"	=>	"data-name='".$cliente["empresa"]."'");
				$btn_borrar = $this->printButton($module, "baja", $params_borrar);

				$aRow = array($cliente["empresa"], $cliente["shortname"], $btn_editar.$btn_borrar);
				
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
				$consulta = $this->_conexion->prepare("DELETE FROM clientes WHERE cli_id = :valor");
				$consulta->bindParam(':valor', $_POST['id']);
				$consulta->execute();
				if($consulta->rowCount()){
					$json['msg'] = "El cliente fue eliminado con éxito.";
					$json['error'] = false;
				} else{
					$json['error'] = true;
					$json['msg'] = "El cliente elegido no pudo ser eliminado.";
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
		global $ruta;
		$json = array();
		$json['error'] = false;
		$json['msg'] = "Experimentamos fallas técnicas.";
		if(isset($_POST['id'])){
			try{
				$sql = "SELECT *
						FROM clientes
						WHERE id = :valor";
				$db = $this->_conexion;
				$consulta = $db->prepare($sql);
				$consulta->bindParam(':valor', $_POST['id']);
				$consulta->execute();
				$row = $consulta->fetch(PDO::FETCH_ASSOC);

				if ($consulta->rowCount() > 0) {
					$json = array_merge($json, $row);

					//Revisamos las direcciones
					$sql_dir = "SELECT * FROM contactos
								WHERE cli_id = ?";
					$values_dir = array($_POST['id']);
					$consulta_dir = $db->prepare($sql_dir);
					$consulta_dir->execute($values_dir);
					$json['html_contactos'] = '';
					$json['num_contactos'] = 0;

					if ($consulta_dir->rowCount() > 0) {
						$contactos = $consulta_dir->fetchAll(PDO::FETCH_ASSOC);
						foreach ($contactos as $contacto) {
							$json['html_contactos'] .= '<div class="row-contacto contacto_'.$json['num_contactos'].'">
					                                      <div class="row">
							                                <div class="col-sm-12">
							                                  <div class="form-group row">
							                                    <label class="col-sm-2 label-control" for="contacto_nombre_'.$json['num_contactos'].'">Nombre Completo*</label>
							                                    <div class="col-sm-10">
							                                      <input type="text" id="contacto_nombre_'.$json['num_contactos'].'" class="form-control border-primary" name="contacto_nombre['.$json['num_contactos'].']" value="'.$contacto['nombre'].'">
							                                      <input type="hidden" id="contacto_id_'.$json['num_contactos'].'" class="form-control border-primary" name="contacto_id['.$json['num_contactos'].']" value="'.$contacto['id'].'">
							                                    </div>
							                                  </div>
							                                </div>
							                              </div>
							                              <div class="row">
							                                <div class="col-md-12">
							                                  <div class="form-group row">
							                                    <label class="col-sm-2 label-control" for="contacto_area_'.$json['num_contactos'].'">Área</label>
							                                    <div class="col-sm-4">
							                                      <input type="text" id="contacto_area_'.$json['num_contactos'].'" class="form-control border-primary" name="contacto_area['.$json['num_contactos'].']" value="'.$contacto['area'].'">
							                                    </div>
							                                    <label class="col-sm-2 label-control" for="contacto_puesto_'.$json['num_contactos'].'">Puesto</label>
							                                    <div class="col-sm-4">
							                                      <input type="text" id="contacto_puesto_'.$json['num_contactos'].'" class="form-control border-primary" name="contacto_puesto['.$json['num_contactos'].']" value="'.$contacto['puesto'].'">
							                                    </div>
							                                  </div>
							                                </div>
							                              </div>
							                              <div class="row">
							                                <div class="col-md-12">
							                                  <div class="form-group row">
							                                    <label class="col-sm-2 label-control" for="contacto_email_'.$json['num_contactos'].'">E-mail*</label>
							                                    <div class="col-sm-4">
							                                      <input type="text" id="contacto_email_'.$json['num_contactos'].'" class="form-control border-primary" name="contacto_email['.$json['num_contactos'].']" value="'.$contacto['email'].'">
							                                    </div>
							                                    <label class="col-sm-2 label-control" for="contacto_telefono_'.$json['num_contactos'].'">Teléfono*</label>
							                                    <div class="col-sm-4">
							                                      <input type="text" id="contacto_telefono_'.$json['num_contactos'].'" class="form-control border-primary" name="contacto_telefono['.$json['num_contactos'].']" value="'.$contacto['telefono'].'">
							                                    </div>
							                                  </div>
							                                </div>
							                              </div>
							                              <div class="row">
							                                <div class="col-md-12">
							                                  <div class="form-group row">
							                                    <label class="col-sm-2 label-control" for="contacto_celular_'.$json['num_contactos'].'">Celular</label>
							                                    <div class="col-sm-4">
							                                      <input type="text" id="contacto_celular_'.$json['num_contactos'].'" class="form-control border-primary" name="contacto_celular['.$json['num_contactos'].']" value="'.$contacto['celular'].'">
							                                    </div>
							                           		<div class="form-group col-sm-2 offset-4 text-center mt-2">
							                                        <button type="button" class="btn btn-danger eliminar-contacto-exist" data-id="'.$contacto['id'].'">
							                                        	<i class="ft-x"></i> Eliminar <br>Contacto
							                                        </button>
							                                    </div>
							                                  </div>
							                                </div>
							                              </div>
							                              <hr>
							                            </div>';
						    $json['num_contactos']++;                            

						}
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

		$obligatorios = array("empresa",
							  "shortname",
							  "rfc");
		$excepciones = array("contacto_nombre",
						     "contacto_area",
						     "contacto_puesto",
						     "contacto_email",
						     "contacto_telefono",
						     "contacto_celular",
						     "contacto_id",
						     "contacto_elim_id");

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

		/*Valida que el RFC no esté ingresado*/
		$db = $this->_conexion;
		$db->beginTransaction();
		$sql = "SELECT id, empresa FROM clientes WHERE rfc = ? LIMIT 0, 1";
		$params = array($_POST['rfc']);
		$consulta = $db->prepare($sql);
		$consulta->execute($params);
		$cliente_rfc = $consulta->fetch(PDO::FETCH_ASSOC);

		if(!$json["error"] && $consulta->rowCount() > 0 && !isset($_POST['id'])){
			$json["error"] = true;
			$json["focus"] = "rfc";
			$json['msg'] = 'El RFC ingresado ya existe en el sistema y pertenece a: '.$cliente_rfc['empresa'].'. Si este no es el cliente que quiere dar de alta, favor de corregir el RFC; de lo contrario, el cliente fue previamente dado de alta y no es necesario ingresar los datos de nuevo.';
		} else if (!$json["error"] && $consulta->rowCount() > 0 && $_POST['id'] != $cliente_rfc['id']) {
			$json["error"] = true;
			$json["focus"] = "rfc";
			$json['msg'] = 'El RFC ingresado ya existe en el sistema y pertenece a: '.$cliente_rfc['empresa'].'. Si este no es el cliente que quiere dar de alta, favor de corregir el RFC; de lo contrario, el cliente fue previamente dado de alta y no es necesario ingresar los datos de nuevo.';
		}

		if(!$json["error"]) {


			//Eliminamos las direcciones eliminadas
			if(isset($_POST['contacto_elim_id'])) {
				foreach ($_POST['contacto_elim_id'] as $cont_id) {
					try{
						$consulta_del_cont = $db->prepare("DELETE FROM contactos WHERE id = :valor AND cli_id = :cli_id");
						$consulta_del_cont->bindParam(':valor', $cont_id);
						$consulta_del_cont->bindParam(':cli_id', $_POST['id']);
						$consulta_del_cont->execute();
					}catch(PDOException $e){
						$db->rollBack();
						die($e->getMessage());
						$json['msg'] = "Experimentamos Fallas Técnicas. Comuníquese con su proveedor.";
					}
				}
			}

			$values = array($_POST['empresa'],
							$_POST['shortname'],
							$_POST['rfc']);

			if(isset($_POST['id'])) { //UPDATE
				$sql = "UPDATE clientes SET empresa = ?, 
											shortname = ?, 
											rfc = ?
						WHERE id = ?";
				
				
				$values[] = $_POST['id'];

			} else {
				$sql = "INSERT INTO clientes(empresa, 
											 shortname, 
											 rfc) 
						VALUES ( ?, ?, ? )";
			}

			$consulta = $db->prepare($sql);

			try {
				$consulta->execute($values);

				if(isset($_POST['id'])) {
					$last_id = $_POST['id'];

				} else {
					$last_id = $this->last_id();
				}

				if(isset($_POST['contacto_nombre'])) {
					foreach ($_POST['contacto_nombre'] as $key => $value) {

						$values_cont = array($_POST['contacto_nombre'][$key],
											$_POST['contacto_telefono'][$key],
											$_POST['contacto_email'][$key],
											$_POST['contacto_celular'][$key],
											$_POST['contacto_area'][$key],
											$_POST['contacto_puesto'][$key],
											$last_id);

						if(isset($_POST['contacto_id'][$key])) {
							$sql_cont = "UPDATE contactos SET nombre = ?,
															 telefono = ?,
															 email = ?,
															 celular = ?,
															 area = ?,
															 puesto = ?,
															 cli_id = ?
										WHERE id = ?";
							$values_cont[]= $_POST['contacto_id'][$key];
						} else {
							$sql_cont = "INSERT INTO contactos (nombre,
															   telefono,
															   email,
															   celular,
															   area,
															   puesto,
															   cli_id)
										VALUES (?, ?, ?, ?, ?,
												?, ? )";
						}

						$consulta_cont = $db->prepare($sql_cont);

						try {
							$consulta_cont->execute($values_cont);
						} catch (PDOException $e) {
							$json["error"] = true;
							die($e->getMessage());
							$db->rollBack();
						}
															
					}
				}

				$db->commit();
				$json['msg'] = "El Cliente fue guardado con éxito.";

			} catch(PDOException $e) {
				$json["error"] = true;
				die($e->getMessage());
				$db->rollBack();
			}
		}

		echo json_encode($json);
	}

	function to_permalink($str)
	{
		if($str !== mb_convert_encoding( mb_convert_encoding($str, 'UTF-32', 'UTF-8'), 'UTF-8', 'UTF-32') )
			$str = mb_convert_encoding($str, 'UTF-8', mb_detect_encoding($str));
		$str = htmlentities($str, ENT_NOQUOTES, 'UTF-8');
		$str = preg_replace('`&([a-z]{1,2})(acute|uml|circ|grave|ring|cedil|slash|tilde|caron|lig);`i', '\\1', $str);
		$str = html_entity_decode($str, ENT_NOQUOTES, 'UTF-8');
		$str = preg_replace(array('`[^a-z0-9]`i','`[-]+`'), '-', $str);
		$str = strtolower( trim($str, '-') );
		return $str;
	}

	function getMarcas() {
		global $ruta;
		$json = array();
		$json['error'] = false;
		$json['msg'] = '';
		$json['marcas'] = '';

		try{
			$sql = "SELECT *
					FROM marcas
					ORDER BY orden ASC";

			$consulta = $this->_conexion->prepare($sql);
			$consulta->execute();
			$marcas = $consulta->fetchAll(PDO::FETCH_ASSOC);

			if ($consulta->rowCount() > 0) {
				
				foreach ($marcas as $marca) {
					$json['marcas'] .= '<div id="marca_'.$marca['id'].'" class="col-sm-2 text-center">
											<img src="'.$ruta.'/img/marcas/'.$marca['thumb'].'" title="'.$marca['marca'].'">
										</div>';
				}
					
			} else {
				$json['error'] = true;
			}

		}catch(PDOException $e){
			die($e->getMessage());
		}

		echo json_encode($json);

	}

	function saveOrder() {
		$json = array();
		$json['error'] = false;
		$json['msg'] = '';

		if(isset($_POST['marca']) && is_array($_POST['marca'])){

			$db = $this->_conexion;
			$db->beginTransaction();

			foreach ($_POST['marca'] as $key => $marca) {
				
				$sql = "UPDATE marcas SET orden = ?
						WHERE id = ?";

				$values = array($key, $marca);

				$consulta = $db->prepare($sql);

				try {
					$consulta->execute($values);
				} catch(PDOException $e){
					$db->rollBack();
					die($e->getMessage());
				}	

			}

			$db->commit();
			$json['msg'] = 'Orden guardado con éxito.';


		} else {
			$json['error'] = true;
			$json['msg'] = 'Error al escoger el orden de las marcas';
		}

		echo json_encode($json);

	}

	function saveMega() {
		$db = $this->_conexion;
		$sql = 'SELECT * from clientes_mega';
		$consulta = $db->prepare($sql);
		try {
			$consulta->execute();
			$megas = $consulta->fetchAll(PDO::FETCH_ASSOC);

			foreach ($megas as $mega) {
				
				//Consultamos si hay uno que tenga el mismo RFC
				$sql_rfc = "SELECT empresa FROM clientes WHERE rfc = ?";
				$values_rfc = array($mega['rfc']);
				$consulta_rfc = $db->prepare($sql_rfc);
				$consulta_rfc->execute($values_rfc);
				if ($consulta_rfc->rowCount() > 0 && $mega['rfc'] != '') {
					echo '<b>Repetido:</b>'.$mega['rfc'].' - '.$mega['cliente'].'<br>';
				} else {
					$sql_new = 'INSERT INTO clientes(empresa, 
													 rfc) 
								VALUES ( ?, ? )';
					$values_new = array($mega['cliente'], $mega['rfc']);
					$consulta_new = $db->prepare($sql_new);
					$consulta_new->execute($values_new);
					echo '<b>Nuevo:</b>'.$mega['rfc'].' - '.$mega['cliente'].'<br>';
				}

			}

			echo 'FINISH';

		} catch(PDOException $e){
			die($e->getMessage());
		}
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
		case "getMarcas":
			$libs->getMarcas();
			break;	
		case "saveOrder":
			$libs->saveOrder();
			break;
		case "saveMega":
			$libs->saveMega();
			break;				
	}
}

?>