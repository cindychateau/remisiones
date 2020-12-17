<?php

$params = array(
				'dbms' => "mysql",
				'host' => 'localhost',
				'name' => 'tirasled_tiras',
				'user' => 'tirasled_tiras',
				'password' => 'TirasLeds2018',
				'encoding' => 'utf8',
				'port' => '',
				'persistant' => false,
			);

$dsn = "mysql:host=".$params["host"].";dbname=".$params["name"];
$dsn .= (strlen(trim($params["encoding"])) == 0) ? "" : ";charset=".$params["encoding"];
$dsn .= (strlen(trim($params["port"])) == 0) ? "" : ";port=".$params["port"];
$db = new PDO($dsn,$params["user"],$params["password"],array(PDO::ATTR_PERSISTENT => $params["persistant"]));
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

//Revisamos todos los productos
$sql = "SELECT * FROM `wp_baqhs0mzrr_posts` WHERE `post_type` LIKE 'product'";
$consulta = $db->prepare($sql);
try {
	$consulta->execute();
	$result = $consulta->fetchAll(PDO::FETCH_ASSOC);
	foreach ($result as $row) {
		//Buscamos su equivalente wpsc
		$sql_wpsc = "SELECT * FROM `wp_baqhs0mzrr_posts` WHERE `post_title` LIKE ? AND `post_type` LIKE 'wpsc_product'";
		$values_wpsc = array($row['post_title']);
		$consulta_wpsc = $db->prepare($sql_wpsc);
		try {
			
			$consulta_wpsc->execute($values_wpsc);
			$row_wpsc = $consulta_wpsc->fetch(PDO::FETCH_ASSOC);

			$sql_up = "UPDATE `wp_baqhs0mzrr_postmeta` SET `post_id`= ? WHERE post_id = ? AND meta_key LIKE '%yoast%'";
			$values_up = array($row['ID'],
							   $row_wpsc['ID']);
			$consulta_up = $db->prepare($sql_up);
			try {
				$consulta_up->execute($values_up);
				echo $row['ID']." - ".$row_wpsc['ID'].'<br>';
			} catch (PDOException $e) {
				die($e->getMessage());
			}

		} catch (PDOException $e) {
			die($e->getMessage());
		}


	}

} catch (PDOException $e) {
	die($e->getMessage());
}



?>