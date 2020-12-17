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
$module = 15;

class Libs extends Common {

	function showRecord() {
		global $ruta;
		$json = array();
		$json['error'] = false;
		$json['msg'] = "Experimentamos fallas técnicas.";
		try{
			$sql = "SELECT *
					FROM folios";

			$consulta = $this->_conexion->prepare($sql);
			$consulta->execute();
			$folios = $consulta->fetchAll(PDO::FETCH_ASSOC);

			if ($consulta->rowCount() > 0) {
				$json['folio_1'] = $folios[0]['folio'];
				$json['folio_2'] = $folios[1]['folio'];
				$json['folio_3'] = $folios[2]['folio'];
					
			} else {
				$json['error'] = true;
			}

		}catch(PDOException $e){
			die($e->getMessage());
		}
		echo json_encode($json);
	}

	function saveRecord() {
		global $ruta;
		if(!isset($_SESSION)){
			@session_start();
		}

		$json = array();
		$json["msg"] = "Todos los campos son obligatorios.";
		$json["error"] = false;
		$json["focus"] = "";

		$obligatorios = array("folio-1", "folio-2", "folio-3");

		//VALIDACIÓN
		foreach($_POST as $clave=>$valor){
			if(!$json["error"]){
				if($this->is_empty(trim($valor)) && in_array($clave, $obligatorios)) {
					$json["error"] = true;
					$json["focus"] = $clave;
					$json['msg'] = "El campo ". lcfirst($clave)." es obligatorio.";	
				}
			}
		}

		if(!$json["error"]) {
			$db = $this->_conexion;
			$db->beginTransaction();

			$sql = "UPDATE folios SET folio = ?
					WHERE id = 1";

			$values = array($_POST['folio-1']);	

			$consulta = $db->prepare($sql);

			try {
				$consulta->execute($values);

			} catch(PDOException $e) {
				$db->rollBack();
				die($e->getMessage());
			}

			$sql = "UPDATE folios SET folio = ?
					WHERE id = 2";

			$values = array($_POST['folio-2']);	

			$consulta = $db->prepare($sql);

			try {
				$consulta->execute($values);

			} catch(PDOException $e) {
				$db->rollBack();
				die($e->getMessage());
			}

			$sql = "UPDATE folios SET folio = ?
					WHERE id = 3";

			$values = array($_POST['folio-3']);	

			$consulta = $db->prepare($sql);

			try {
				$consulta->execute($values);

			} catch(PDOException $e) {
				$db->rollBack();
				die($e->getMessage());
			}

			$db->commit();
			$json['msg'] = 'Folios cambiados con éxito.';
		}

		echo json_encode($json);
	}

}

if(isset($_REQUEST['accion'])){
	//Se inicializa la clase
	$libs = new Libs;
	switch($_REQUEST['accion']){
		case "saveRecord":
			$libs->saveRecord();
			break;	
		case "showRecord":
			$libs->showRecord();
			break;			
	}
}

?>