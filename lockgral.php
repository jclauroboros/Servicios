<?php

//############################################################################
//## Busca un archivo de texto llamado lock.txt que contiene por líneas 	##
//## las matriculas de los alumnos a bloquear.		   						##
//## Se basa en la premisa de que las boletas tienen un guion bajo al		##
//## inicio del nombre, por lo que solamente hay que renombrarlas.			##
//## Una vez encontrado el archivo, va leyendo linea a linea y renombrando.	##
//## Si no encuentra un archivo lo registra en una bitacora:				##
//## "errorlock.txt"														##
//##																		##
//## 9 de octubre 2019														##
//## 14 de diciembre 2020 Se incorpora Preescolar, LDE y se retira IND		##
//############################################################################
	
	$filesnumber = 0;		//Numero de archivos procesados
	$errornumber = 0;		//Numero de archivos con error (no encontrados?)
	$flag = 0;				//si es encabezado se brinca la línea
	$errores = array();		//guarda las matriculas con error
	
	echo "<h2>Bloqueando boletas</h2>";
	//buscar el archivo lock.txt
	$lista = fopen("lock.txt","r");
	

	
	if (! $lista) {	
		echo "<br />Error al abrir el archivo: ".$php_errormsg."<br />"; 
	} else {
		
		// preparando archivo para logs de errores
		$errorlog = fopen("errorlock.txt","w");
		$linea = "-------- ".date('d/m/y') ."---------\n";
		fwrite($errorlog, $linea );
		
		// leyendo archivo de matriculas
		while (!feof($lista)) {
			$file=fgets($lista);
			
			//revisar que no sea encabezado
			switch(trim($file)) {
				case '**K**':
					echo "<br />Comenzando Preescolar - ".$filesnumber;
					$ruta = "preescolar/boletas/";
					$flag = 1;
					break;
				case '**P**':
					echo "<br />Comenzando Primaria - ".$filesnumber;
					$ruta = "primaria/boletas/";
					$flag = 1;
					break;
				case '**S**':
					echo "<br />Comenzando Secundaria - ".$filesnumber;
					$ruta = "secundaria/boletas/";
					$flag = 1;
					break;		
				case '**B**':
					echo "<br />Comenzando Preparatoria - ".$filesnumber;
					$ruta = "preparatoria/pagos/reinscripcion/";
					$flag = 1;
					break;
				case '**ARQ**':
					echo "<br />Comenzando Arquitectura - ".$filesnumber;
					$ruta = "universidad/ARQ/boletas/";
					$flag = 1;
					break;
				case '**LDE**':
					echo "<br />Comenzando Derecho - ".$filesnumber;
					$ruta = "universidad/LDE/boletas/";
					$flag = 1;
					break;
				case '**LAV**':
					echo "<br />Comenzando Animación - ".$filesnumber;
					$ruta = "universidad/LAV/boletas/";
					$flag = 1;
					break;
				case '**LFR**':
					echo "<br />Comenzando Fisioterapia - ".$filesnumber;
					$ruta = "universidad/LFR/boletas/";
					$flag = 1;
					break;
				case '**LNI**':
					echo "<br />Comenzando Negocios - ".$filesnumber;
					$ruta = "universidad/LNI/boletas/";
					$flag = 1;
					break;
				default:
					// no es encabezado
					$flag = 0;
			}
			
			if ($flag == 0) {
				// preparando nombres
				$filename = $ruta.trim($file).".pdf";
				$newfilename = $ruta."_".trim($file).".pdf";

				
				if (@rename($filename, $newfilename)) {
					// no hay errores
					$filesnumber += 1;
				} else {
					// incrementa el contador y registralo en logs
					$errores[$errornumber] = $filename;
					$errornumber +=1;
					fwrite($errorlog, "$filename \n");
				}
			}
		}
		
		// cierra archivos
		fclose($lista);
		fclose($errorlog);
		
		// imprime informe
		echo "<br /> <br /> <h2>".$filesnumber." archivos bloqueados</h2>";
		if ($errornumber > 0) {
			echo "<br />*************** ERRORES *****************";
			$i = 0;
			while ($i < $errornumber) {
				echo "<br />**".$errores[$i];
				$i += 1;
			}

			echo "<h2>".$errornumber." archivos no encontrados</h2>";
			echo "<br />Revisar errorlock.txt para mas detalles";
		}
			
	}
?>