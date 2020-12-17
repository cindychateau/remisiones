<?php
    $url = explode("/remisiones", $_SERVER["REQUEST_URI"]);
	$url = explode("/", $url[1]);

	//$url = explode("/", $_SERVER["REQUEST_URI"]);

	$ruta = "";
	$file=$url[count($url)-1];
	for ($i=1; $i < (count($url) - 1); $i++){
		$ruta .= "../";
	}
    if(isset($_GET['id'])) {

    	include_once($ruta.'include/rotation.php');
        include($ruta.'include/Common.php');

        $db = $common->_conexion;
		$sql = "SELECT rem.*,
							clientes.empresa as cliente_empresa,
							shortname,
							nombre,
							SIU_NOMBRE,
							telefono,
							email
					 FROM rem
					 LEFT JOIN clientes ON rem.cli_id = clientes.id
					 LEFT JOIN contactos ON rem.cont_id = contactos.id
					 LEFT JOIN SISTEMA_USUARIO ON rem.siu_id = SISTEMA_USUARIO.SIU_ID
					 WHERE rem.id = ? ";
		
		$values = array($_GET['id']);

		//Se prepara la consulta de extración de datos
		$consulta = $db->prepare($sql);
		$consulta->execute($values);
		$remision = $consulta->fetch(PDO::FETCH_ASSOC);

		$sql_info = 'SELECT * FROM rem_info WHERE rem_id = ?';
		$values_info = array($_GET['id']);
		$consulta_info = $db->prepare($sql_info);
		$consulta_info->execute($values_info);
		$infos = $consulta_info->fetchAll(PDO::FETCH_ASSOC);

        class PDF extends PDF_Rotate{
			/*Funciones y vars para Tabla*/
			var $widths;
			var $aligns;
			var $num_hoja = 0;

			function SetWidths($w)
			{
			    //Set the array of column widths
			    $this->widths=$w;
			}

			function SetAligns($a)
			{
			    //Set the array of column alignments
			    $this->aligns=$a;
			}

			function Row($data)
			{
			    //Calculate the height of the row
			    $nb=0;
			    for($i=0;$i<count($data);$i++)
			        $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
			    $h=6*$nb;
			    //Issue a page break first if needed
			    $this->CheckPageBreak($h);
			    //Draw the cells of the row
			    for($i=0;$i<count($data);$i++)
			    {
			        $w=$this->widths[$i];
			        $a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'C';
			        //Save the current position
			        $x=$this->GetX();
			        $y=$this->GetY();
			        //Draw the border
			        $this->Rect($x,$y,$w,$h, "FD");
			        //Print the text
			        $this->MultiCell($w,6,$data[$i],0,$a);
			        //Put the position to the right of the cell
			        $this->SetXY($x+$w,$y);
			    }
			    //Go to the next line
			    $this->Ln($h);
			}

			function CheckPageBreak($h)
			{
			    //If the height h would cause an overflow, add a new page immediately
			    if($this->GetY()+$h>$this->PageBreakTrigger)
			        $this->AddPage($this->CurOrientation);
			}

			function NbLines($w,$txt)
			{
			    //Computes the number of lines a MultiCell of width w will take
			    $cw=&$this->CurrentFont['cw'];
			    if($w==0)
			        $w=$this->w-$this->rMargin-$this->x;
			    $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
			    $s=str_replace("\r",'',$txt);
			    $nb=strlen($s);
			    if($nb>0 and $s[$nb-1]=="\n")
			        $nb--;
			    $sep=-1;
			    $i=0;
			    $j=0;
			    $l=0;
			    $nl=1;
			    while($i<$nb)
			    {
			        $c=$s[$i];
			        if($c=="\n")
			        {
			            $i++;
			            $sep=-1;
			            $j=$i;
			            $l=0;
			            $nl++;
			            continue;
			        }
			        if($c==' ')
			            $sep=$i;
			        $l+=$cw[$c];
			        if($l>$wmax)
			        {
			            if($sep==-1)
			            {
			                if($i==$j)
			                    $i++;
			            }
			            else
			                $i=$sep+1;
			            $sep=-1;
			            $j=$i;
			            $l=0;
			            $nl++;
			        }
			        else
			            $i++;
			    }
			    return $nl;
			}

			function mes($m) {
				$mes = "Enero";
				switch ($m) {
					case 1:
						$mes = "Enero";
						break;
					case 2:
						$mes = "Febrero";
						break;
					case 3:
						$mes = "Marzo";
						break;
					case 4:
						$mes = "Abril";
						break;
					case 5:
						$mes = "Mayo";
						break;
					case 6:
						$mes = "Junio";
						break;
					case 7:
						$mes = "Julio";
						break;
					case 8:
						$mes = "Agosto";
						break;
					case 9:
						$mes = "Septiembre";
						break;
					case 10:
						$mes = "Octubre";
						break;
					case 11:
						$mes = "Noviembre";
						break;
					case 12:
						$mes = "Diciembre";
						break;													
				}

				return strtoupper($mes);
			}

			/*
			 * @author: Cynthia Castillo <cynthia.castillo@metodika.mx>
			 * @version: 0.1 2015-02-12
			 * 
			 * @param '$date'	string. Fecha
			 *
			 * @return 			boolean.True -> Si es Domingo; False -> Es otro día
			 * 
			 * Regresa si es Domingo la fecha que se le da
			 */
			function isWeekend($date) {
				$date_required = str_replace('/', '-', $date);
				$day = date('l', strtotime($date_required));
				if ($day == 'Sunday') {
					return true;
				} else {
					return false;
				}
			}

			/*
			 * @author: Cynthia Castillo <cynthia.castillo@metodika.mx>
			 * @version: 0.1 2015-02-13
			 * 
			 * @param '$date_str'	string. 	Fecha
			 * @param '$months'		int.		Meses que se van a sumar
			 *
			 * @return '$date'		DateTime.	Fecha con un mes más
			 * 
			 * Método que aumenta un mes a la fecha dada
			 */
			function addMonth($date_str, $new_date ,$months) {
			    $date = new DateTime($date_str);
			    $start_day = $date->format('j');
			    //Si el día que te dieron es el último día
			    if($start_day == $date->format('t')) {
			    	$date = new DateTime($new_date);
			    	$start_day = $date->format('j');
			    	if($start_day == $date->format('t')) {
			    		$date->modify('last day of next month');
			    	} else {
			    		$date->modify('last day of this month');
			    	}
			    	
			    } else {
			    	//Si no, solo agrega los meses
			    	$date->modify("+{$months} month");
				    $end_day = $date->format('j');

				    if ($start_day != $end_day)
				        $date->modify('last day of last month');
			    }

			    return $date;
			}

			/*
			 * @author: Cynthia Castillo <cynthia.castillo@metodika.mx>
			 * @version: 0.1 2015-02-13
			 * 
			 * @param '$date_str'	string. 	Fecha
			 *
			 * @return '$date'		DateTime.	Fecha de la siguiente quincena
			 * 
			 * Método que aumenta una quincena a la fecha dada
			 * 3 OPCIONES
			 * 1.- Si la fecha es igual al último día del mes -> date debe ser el día 15 del prox mes
			 * 2.- Si la fecha es menor a 13 -> date debe ser el día 15 de ese mes
			 * 3.- Si la fecha es mayor o igual a 15 -> date debe ser el último día de ese mes 
			 */
			function addFortnight($date_str) {
				$date = new DateTime($date_str);
				$start_day = $date->format('j');
				//Si el día que te dieron es el último día
				if($start_day == $date->format('t')) {
					$date->modify("first day of next month");
					$date->modify("+14 days");
				} else if($start_day < 13) {
					$date->modify("first day of this month");
					$date->modify("+14 days");
				} else if($start_day >= 15) {
					$date->modify('last day of this month');
				}

				return $date;
			}

			/*
			 * @author: Cynthia Castillo <cynthia.castillo@metodika.mx>
			 * @version: 0.1 2015-02-13
			 * 
			 * Agrega marca de agua
			 */
			function watermark() {
			    //Put the watermark
			    $this->SetFont('Arial','B',50);
			    $this->SetTextColor(255,192,203);
			    $this->RotatedText(80,80,'C O P I A',45);
			     $this->RotatedText(80,168,'C O P I A',45);
			      $this->RotatedText(80,256,'C O P I A',45);
			}

			/*
			 * @author: Cynthia Castillo <cynthia.castillo@metodika.mx>
			 * @version: 0.1 2015-02-13
			 * 
			 * Rota texto de marca de agua
			 */
			function RotatedText($x, $y, $txt, $angle) {
			    //Text rotated around its origin
			    $this->Rotate($angle,$x,$y);
			    $this->Text($x,$y,$txt);
			    $this->Rotate(0);
			}

			function Header() {

			}

			function Footer() {
				if ($this->foot == 1) {
					// Go to 1.5 cm from bottom
				    $this->SetY(-15);
				    // Select Arial italic 8
				    $this->SetFont('Arial','',8);
				    // Print centered page number
				    $this->Cell(0,10,$this->PageNo(),0,0,'R');
				}
			}

			//------    CONVERTIR NUMEROS A LETRAS         ---------------
			//------    Máxima cifra soportada: 18 dígitos con 2 decimales
			//------    999,999,999,999,999,999.99
			// NOVECIENTOS NOVENTA Y NUEVE MIL NOVECIENTOS NOVENTA Y NUEVE BILLONES
			// NOVECIENTOS NOVENTA Y NUEVE MIL NOVECIENTOS NOVENTA Y NUEVE MILLONES
			// NOVECIENTOS NOVENTA Y NUEVE MIL NOVECIENTOS NOVENTA Y NUEVE PESOS 99/100 M.N.
			//------    Creada por:                        ---------------
			//------             ULTIMINIO RAMOS GALÁN     ---------------
			//------            uramos@gmail.com           ---------------
			//------    10 de junio de 2009. México, D.F.  ---------------
			//------    PHP Version 4.3.1 o mayores (aunque podría funcionar en versiones anteriores, tendrías que probar)
			function numtoletras($xcifra)
			{
			    $xarray = array(0 => "Cero",
			        1 => "UN", "DOS", "TRES", "CUATRO", "CINCO", "SEIS", "SIETE", "OCHO", "NUEVE",
			        "DIEZ", "ONCE", "DOCE", "TRECE", "CATORCE", "QUINCE", "DIECISEIS", "DIECISIETE", "DIECIOCHO", "DIECINUEVE",
			        "VEINTI", 30 => "TREINTA", 40 => "CUARENTA", 50 => "CINCUENTA", 60 => "SESENTA", 70 => "SETENTA", 80 => "OCHENTA", 90 => "NOVENTA",
			        100 => "CIENTO", 200 => "DOSCIENTOS", 300 => "TRESCIENTOS", 400 => "CUATROCIENTOS", 500 => "QUINIENTOS", 600 => "SEISCIENTOS", 700 => "SETECIENTOS", 800 => "OCHOCIENTOS", 900 => "NOVECIENTOS"
			    );
			//
			    $xcifra = trim($xcifra);
			    $xlength = strlen($xcifra);
			    $xpos_punto = strpos($xcifra, ".");
			    $xaux_int = $xcifra;
			    $xdecimales = "00";
			    if (!($xpos_punto === false)) {
			        if ($xpos_punto == 0) {
			            $xcifra = "0" . $xcifra;
			            $xpos_punto = strpos($xcifra, ".");
			        }
			        $xaux_int = substr($xcifra, 0, $xpos_punto); // obtengo el entero de la cifra a covertir
			        $xdecimales = substr($xcifra . "00", $xpos_punto + 1, 2); // obtengo los valores decimales
			    }
			 
			    $XAUX = str_pad($xaux_int, 18, " ", STR_PAD_LEFT); // ajusto la longitud de la cifra, para que sea divisible por centenas de miles (grupos de 6)
			    $xcadena = "";
			    for ($xz = 0; $xz < 3; $xz++) {
			        $xaux = substr($XAUX, $xz * 6, 6);
			        $xi = 0;
			        $xlimite = 6; // inicializo el contador de centenas xi y establezco el límite a 6 dígitos en la parte entera
			        $xexit = true; // bandera para controlar el ciclo del While
			        while ($xexit) {
			            if ($xi == $xlimite) { // si ya llegó al límite máximo de enteros
			                break; // termina el ciclo
			            }
			 
			            $x3digitos = ($xlimite - $xi) * -1; // comienzo con los tres primeros digitos de la cifra, comenzando por la izquierda
			            $xaux = substr($xaux, $x3digitos, abs($x3digitos)); // obtengo la centena (los tres dígitos)
			            for ($xy = 1; $xy < 4; $xy++) { // ciclo para revisar centenas, decenas y unidades, en ese orden
			                switch ($xy) {
			                    case 1: // checa las centenas
			                        if (substr($xaux, 0, 3) < 100) { // si el grupo de tres dígitos es menor a una centena ( < 99) no hace nada y pasa a revisar las decenas
			                             
			                        } else {
			                            $key = (int) substr($xaux, 0, 3);
			                            if (TRUE === array_key_exists($key, $xarray)){  // busco si la centena es número redondo (100, 200, 300, 400, etc..)
			                                $xseek = $xarray[$key];
			                                $xsub = $this->subfijo($xaux); // devuelve el subfijo correspondiente (Millón, Millones, Mil o nada)
			                                if (substr($xaux, 0, 3) == 100)
			                                    $xcadena = " " . $xcadena . " CIEN " . $xsub;
			                                else
			                                    $xcadena = " " . $xcadena . " " . $xseek . " " . $xsub;
			                                $xy = 3; // la centena fue redonda, entonces termino el ciclo del for y ya no reviso decenas ni unidades
			                            }
			                            else { // entra aquí si la centena no fue numero redondo (101, 253, 120, 980, etc.)
			                                $key = (int) substr($xaux, 0, 1) * 100;
			                                $xseek = $xarray[$key]; // toma el primer caracter de la centena y lo multiplica por cien y lo busca en el arreglo (para que busque 100,200,300, etc)
			                                $xcadena = " " . $xcadena . " " . $xseek;
			                            } // ENDIF ($xseek)
			                        } // ENDIF (substr($xaux, 0, 3) < 100)
			                        break;
			                    case 2: // checa las decenas (con la misma lógica que las centenas)
			                        if (substr($xaux, 1, 2) < 10) {
			                             
			                        } else {
			                            $key = (int) substr($xaux, 1, 2);
			                            if (TRUE === array_key_exists($key, $xarray)) {
			                                $xseek = $xarray[$key];
			                                $xsub = $this->subfijo($xaux);
			                                if (substr($xaux, 1, 2) == 20)
			                                    $xcadena = " " . $xcadena . " VEINTE " . $xsub;
			                                else
			                                    $xcadena = " " . $xcadena . " " . $xseek . " " . $xsub;
			                                $xy = 3;
			                            }
			                            else {
			                                $key = (int) substr($xaux, 1, 1) * 10;
			                                $xseek = $xarray[$key];
			                                if (20 == substr($xaux, 1, 1) * 10)
			                                    $xcadena = " " . $xcadena . " " . $xseek;
			                                else
			                                    $xcadena = " " . $xcadena . " " . $xseek . " Y ";
			                            } // ENDIF ($xseek)
			                        } // ENDIF (substr($xaux, 1, 2) < 10)
			                        break;
			                    case 3: // checa las unidades
			                        if (substr($xaux, 2, 1) < 1) { // si la unidad es cero, ya no hace nada
			                             
			                        } else {
			                            $key = (int) substr($xaux, 2, 1);
			                            $xseek = $xarray[$key]; // obtengo directamente el valor de la unidad (del uno al nueve)
			                            $xsub = $this->subfijo($xaux);
			                            $xcadena = " " . $xcadena . " " . $xseek . " " . $xsub;
			                        } // ENDIF (substr($xaux, 2, 1) < 1)
			                        break;
			                } // END SWITCH
			            } // END FOR
			            $xi = $xi + 3;
			        } // ENDDO
			 
			        if (substr(trim($xcadena), -5, 5) == "ILLON") // si la cadena obtenida termina en MILLON o BILLON, entonces le agrega al final la conjuncion DE
			            $xcadena.= " DE";
			 
			        if (substr(trim($xcadena), -7, 7) == "ILLONES") // si la cadena obtenida en MILLONES o BILLONES, entoncea le agrega al final la conjuncion DE
			            $xcadena.= " DE";
			 
			        // ----------- esta línea la puedes cambiar de acuerdo a tus necesidades o a tu país -------
			        if (trim($xaux) != "") {
			            switch ($xz) {
			                case 0:
			                    if (trim(substr($XAUX, $xz * 6, 6)) == "1")
			                        $xcadena.= "UN BILLON ";
			                    else
			                        $xcadena.= " BILLONES ";
			                    break;
			                case 1:
			                    if (trim(substr($XAUX, $xz * 6, 6)) == "1")
			                        $xcadena.= "UN MILLON ";
			                    else
			                        $xcadena.= " MILLONES ";
			                    break;
			                case 2:
			                    if ($xcifra < 1) {
			                        $xcadena = "CERO PESOS $xdecimales/100 Moneda de curso legal de los Estados Unidos Mexicanos ";
			                    }
			                    if ($xcifra >= 1 && $xcifra < 2) {
			                        $xcadena = "UN PESO $xdecimales/100 Moneda de curso legal de los Estados Unidos Mexicanos ";
			                    }
			                    if ($xcifra >= 2) {
			                        $xcadena.= " PESOS $xdecimales/100 Moneda de curso legal de los Estados Unidos Mexicanos "; //
			                    }
			                    break;
			            } // endswitch ($xz)
			        } // ENDIF (trim($xaux) != "")
			        // ------------------      en este caso, para México se usa esta leyenda     ----------------
			        $xcadena = str_replace("VEINTI ", "VEINTI", $xcadena); // quito el espacio para el VEINTI, para que quede: VEINTICUATRO, VEINTIUN, VEINTIDOS, etc
			        $xcadena = str_replace("  ", " ", $xcadena); // quito espacios dobles
			        $xcadena = str_replace("UN UN", "UN", $xcadena); // quito la duplicidad
			        $xcadena = str_replace("  ", " ", $xcadena); // quito espacios dobles
			        $xcadena = str_replace("BILLON DE MILLONES", "BILLON DE", $xcadena); // corrigo la leyenda
			        $xcadena = str_replace("BILLONES DE MILLONES", "BILLONES DE", $xcadena); // corrigo la leyenda
			        $xcadena = str_replace("DE UN", "UN", $xcadena); // corrigo la leyenda
			    } // ENDFOR ($xz)
			    return trim($xcadena);
			}
			 
			// END FUNCTION
			 
			function subfijo($xx)
			{ // esta función regresa un subfijo para la cifra
			    $xx = trim($xx);
			    $xstrlen = strlen($xx);
			    if ($xstrlen == 1 || $xstrlen == 2 || $xstrlen == 3)
			        $xsub = "";
			    //
			    if ($xstrlen == 4 || $xstrlen == 5 || $xstrlen == 6)
			        $xsub = "MIL";
			    //
			    return $xsub;
			}
			 
			// END FUNCTION

			function diaLetras($fecha) {
				$dw = date("w", strtotime($fecha));
				$dia = "Viernes";
				switch ($dw) {
					case 0:
						$dia = "Domingo";
						break;
					case 1:
						$dia = "Lunes";
						break;
					case 2:
						$dia = "Martes";
						break;
					case 3:
						$dia = "Miércoles";
						break;
					case 4:
						$dia = "Jueves";
						break;
					case 5:
						$dia = "Viernes";
						break;
					case 6:
						$dia = "Sábado";
						break;							
				}

				return $dia;
			}

		}

		$pdf = new PDF();

		// Agrega la pagina
		$pdf->foot = 2;
		$pdf->AddPage();

		switch ($remision['empresa']) {
			case '3G Consulting y Asesoría':
				$pdf->Image('3G.png', 0, 0, $pdf->w, $pdf->h);
				break;
			case 'Sky Consulting Partners':
				$pdf->Image('Sky.png', 0, 0, $pdf->w, $pdf->h);
				break;	
			case 'Alliance Soluciones':
				$pdf->Image('Alliance.png', 0, 0, $pdf->w, $pdf->h);
				break;		
		}

		$pdf->SetFont('Arial','',10);
		$pdf->SetTextColor(0,0,0);

		if($remision['empresa'] == 'Sky Consulting Partners') {
			/*Remisión 
			como SKY*/

			//FECHA
			$pdf->SetXY(162, 29);
			$remision['fecha'] = date("d/m/Y",strtotime($remision['fecha']));
			$pdf->Cell(39,4,$remision['fecha'],0,0,'C');

			//Cliente
			$pdf->SetXY(10, 49);
			$pdf->Cell(114,4,utf8_decode($remision['cliente_empresa']),0,0,'L');

			//Teléfono
			$pdf->Cell(3,4,'',0,0,'L');
			$pdf->Cell(73,4,$remision['telefono'],0,0,'L');

			//Dirección
			$pdf->SetXY(10, 58);
			$pdf->MultiCell(189,4,utf8_decode($remision['direccion']),0,"L");

			//Contacto
			$pdf->SetXY(10, 69);
			$pdf->Cell(114,4,utf8_decode($remision['nombre']),0,0,'L');

			//Email
			$pdf->Cell(3,4,'',0,0,'L');
			$pdf->Cell(73,4,$remision['email'],0,0,'L');

			//Info
			$pdf->SetXY(30.7, 81);
			$n = 0;
			foreach ($infos as $info) {

				$pdf->SetX(30.7);

				if($n == 5) {
					$pdf->Cell(92,6,'',0,0,'L');
					$pdf->Ln(7.3);
					$pdf->SetX(30.7);
					$n = 0;
				}

				$this_info = utf8_decode($info['cantidad'].' '.$info['descripcion']);

				$pdf->Cell(92,6.4,$this_info,0,0,'L');
				$pdf->Ln(7.3);
				$n++;
			}

			//Notas
			$pdf->SetXY(30.7, 225);
			$pdf->MultiCell(170,6.4,utf8_decode($remision['notas']),0,'L');

		} else if($remision['empresa'] == 'Alliance Soluciones'){
			/*Remisión 
			como ALLIANCE*/
			//FECHA
			$pdf->SetXY(163, 27);
			$remision['fecha'] = date("d/m/Y",strtotime($remision['fecha']));
			$pdf->Cell(39,4,$remision['fecha'],0,0,'C');

			//Cliente
			$pdf->SetXY(46, 44.5);
			$pdf->Cell(89,4,utf8_decode($remision['cliente_empresa']),0,0,'L');

			//Teléfono
			$pdf->Cell(18,4,'',0,0,'L');
			$pdf->Cell(49,4,$remision['telefono'],0,0,'L');

			//Dirección
			$pdf->SetXY(29, 54);
			$pdf->MultiCell(170,4,utf8_decode($remision['direccion']),0,"L");

			//Contacto
			$pdf->SetXY(50, 64);
			$pdf->Cell(85,4,$remision['nombre'],0,0,'L');

			//Email
			$pdf->Cell(13,4,'',0,0,'L');
			$pdf->Cell(54,4,$remision['email'],0,0,'L');

			//Info
			$pdf->SetXY(29, 78);
			$n = 0;
			foreach ($infos as $info) {

				$pdf->SetX(29);

				if($n == 5) {
					$pdf->Cell(92,6,'',0,0,'L');
					$pdf->Ln(7.3);
					$pdf->SetX(29);
					$n = 0;
				}

				$this_info = utf8_decode($info['cantidad'].' '.$info['descripcion']);

				$pdf->Cell(92,6.4,$this_info,0,0,'L');
				$pdf->Ln(7.3);
				$n++;
			}

			//Notas
			$pdf->SetXY(29, 221);
			$pdf->MultiCell(170,6.4,utf8_decode($remision['notas']),0,'L');


		}  else if($remision['empresa'] == '3G Consulting y Asesoría') {
			/*Remisión 
			como 3G*/
			//FECHA
			$pdf->SetXY(159, 23);
			$remision['fecha'] = date("d/m/Y",strtotime($remision['fecha']));
			$pdf->Cell(37,4,$remision['fecha'],0,0,'C');

			//Cliente
			$pdf->SetXY(28, 40);
			$pdf->Cell(97,4,utf8_decode($remision['cliente_empresa']),0,0,'L');

			//Teléfono
			$pdf->Cell(23,4,'',0,0,'L');
			$pdf->Cell(47,4,$remision['telefono'],0,0,'L');

			//Dirección
			$pdf->SetXY(32, 47);
			$pdf->MultiCell(165,4,utf8_decode($remision['direccion']),0,"L");

			//Contacto
			$pdf->SetXY(32, 52.5);
			$pdf->Cell(94,4,$remision['nombre'],0,0,'L');

			//Email
			$pdf->Cell(13,4,'',0,0,'L');
			$pdf->Cell(54,4,$remision['email'],0,0,'L');

			//Info
			$pdf->SetXY(12, 68.7);
			$n = 0;
			$pdf->SetLineWidth(.5);
			foreach ($infos as $info) {

				$pdf->SetX(12);
				$pdf->Cell(19.5,8,$info['cantidad'],0,0,'C');
				$pdf->Cell(19.5,8,$info['importe'],0,0,'C');

				$pdf->Cell(145,8,$info['descripcion'],0,0,'L');
				$pdf->Ln(8.1);
				$n++;
			}

		}


		$archivo = 'remision_'.$_GET['id'].'.pdf';
		$pdf->Output();

    } else {
        header("Location:../index.php");
    }
?>