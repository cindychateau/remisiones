<?php
require_once("Core.php");

class Notices extends Core
{
	public function noticiaVista() {

		$json = array();
		$json['msg'] = "no hizo null";

		try {
			$query = "SELECT * FROM SISTEMA_NOTIFICACIONES WHERE SIN_ESTADO = 0 AND SIN_ID = :valor";//query para user
			$consulta = $this->_conexion->prepare($query);
			$consulta->bindParam(":valor",$_POST['id']);
			$consulta->execute();
			$notificacion = $consulta->fetch(PDO::FETCH_ASSOC);
			if($notificacion['SIN_COLOR'] == 'pink') {
				$query = "UPDATE SISTEMA_NOTIFICACIONES SET SIN_ESTADO = 1
						  WHERE SIN_COLOR = 'pink'";
				$consulta = $this->_conexion->prepare($query);
				$consulta->execute();
			} else {
				$query = "UPDATE SISTEMA_NOTIFICACIONES SET SIN_ESTADO = 1
						  WHERE SIN_ID = :valor";
				$consulta = $this->_conexion->prepare($query);
				$consulta->bindParam(":valor",$notificacion['SIN_ID']);
				$consulta->execute();
			}
			
		} catch(PDOException $e) {
			die($e->getMessage());
		}

		echo json_encode($json);
	}
	/*
	public function noticiaVista() {
		$json = array();
		$json['msg'] = "";
		try {
			$query = "UPDATE SISTEMA_MENSAJE SET SIM_ESTADO = 1 WHERE SIM_TIPO = :valor";
			$consulta = $this->_conexion->prepare($query);
				$consulta->bindParam(":valor",$_POST['id_t']);
				$consulta->execute();
				$json['msg'] = "Se realizo";
				
		} catch(PDOException $e) {
			die($e->getMessage());
		}		
		echo json_encode($json);
	}*/
}
if (isset($_GET['accion'])) {
	$notices = new Notices();
	switch ($_GET['accion']) {
		case 'vista':
			$notices->noticiaVista();
			break;
		default:
			die("Acción no definida");
			break;
	}
}
?>