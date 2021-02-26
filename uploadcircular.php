<?php
error_reporting(E_ALL);
ini_set("display_errors","On");
ini_set("session.gc_maxlifetime","14400");
session_start();

include ("libs.php");           /* Librerias */
include ("dbconect.php");

$errorflag = 0;         // Cuenta los errores encontrados
$flagOK = 0;            // Suma los archivos subidos, debe ser igual a 4 para que se considere completo el proceso
$uploadOk = 0;          // 0 si existe algún errro, 1 si es posible subir el archivo. Se inicializa en 1 al comenzar cada validación. Esta relacionada con la variable anterior
$errores = array();     // Acumula en un arreglo los errores para mostrarlos al final
$Valida_POST = FALSE;   // Bandera que revisa que los datos en el formulario no estén vacios.
$ValidaF1 = 0;          // Se envío Circular
$circular='';
$ruta = 'circulares/';

// VALIDAR SI YA SE INICIO SESION
if (isset($_SESSION['login'])&&($_SESSION['login'] != 1)) {
    $_SESSION['login'] = 0;
}

headerfull_('Comunicación');

/* ---------------- AQUI COMIENZA LA SECCION CENTRAL DE INFORMACION -----------------------*/
if ($_SESSION['login'] == 1) { // realizó login exitoso
    switch ($_SESSION['Type']) {
        case 0:     // alumno
            echo '<p>¿Que haces aquí?</p>';
            break;
        case 1:     // usuario, validar los privs
            switch ($_SESSION['Privs']) {
                case 2: // Titular
                    echo '<p>Eeeepaaa</p>';
                    break;
                case 4:     // Becas
                case 5:     // Administrador
                    $circular = basename($_FILES['circular']['name']);
                    $Valida_POST = (isset($_POST['seccion']) && !empty($_POST['seccion']));
                    if (is_uploaded_file($_FILES['circular']['tmp_name'])) { $ValidaF1 = 1; }
                    if ($Valida_POST == FALSE || $ValidaF1 == 0) {
                        echo '<script type="text/javascript">'."\n";
                        echo 'alert("Los datos están incompletos, por favor revisa el formulario nuevamente");'."\n"; 
                        echo 'window.location = "comunicacion.php"'."\n"; 
                        echo '</script>'."\n";
                        //header("Location: comunicacion.php");
                    } else  {
                        if (isset($_POST['ctx'])) {
                            $ctx = $_POST['ctx'];
                        } else { 
                            $ctx = '';
                        }
                        if (isset($_POST['Active'])) {
                            $grupo = $_POST['Active'];
                        } else {
                            $grupo = '';
                        }
                        $ruta = $ruta.$_POST['seccion'];         // agrega el numero de la sección
                        
                        if ($_POST['seccion'] > 2) {    // es semestral 
                            $ciclo = CICLOACTS;
                            $circular = seccion_nombre($_POST['seccion'], $ctx).'_'.$circular;     // Si es carrera agrega prefijo
                        } else  { 
                            $ciclo = CICLOACTA;
                        }  //  definir el ciclo actual para la circular
                        $seccion = seccion_nombre($_POST['seccion'], $ctx);     // Convierte la sección en las iniciales
                        $descripcion = $_POST['descripcion'];
                        if ($_FILES["circular"]["error"] == 0 && $ValidaF1 == 1) {  // Probamos a subir el archivo
                            $uploadOk = 1;  // inicializamos banderas de subida
                            $target_file = $ruta."/".$circular;
                            // Revisar si el archivo existe
                            if (file_exists($target_file)) {
                                $errorflag += 1;
                                array_push ($errores, "Archivo: Ya existe, por favor revisa los datos de la circular");
                                //unlink($target_file);
                            }
                            // Revisar el tipo de archivo
                            $FileType1 = strtolower(pathinfo($circular,PATHINFO_EXTENSION));
                            if($FileType1 != "pdf" ) {
                                $errorflag += 1;
                                array_push ($errores, "Formato: Solo se aceptan archivos pdf");
                                $uploadOk = 0;
                            }
                            // Si cumple con todas las condiciones anteriores, es momento de subirlo
                            if ($uploadOk == 1) {
                                if (move_uploaded_file($_FILES["circular"]["tmp_name"], $target_file)) {
                                    $flagOK = 1;
                                } else {
                                    $errorflag += 1;
                                    array_push ($errores, "Circular: Error al cargar archivo");
                                }
                            }
                        }
                
                        // Validar si los archivos se subieron correctamente y no hay errores, entonces intentar actualizar la BD
                        if ($flagOK == 1 && $errorflag == 0) {
                            //Subir los datos del formulario
                            $conexionBD=new Circular();
                            $result=$conexionBD->insert_circular($seccion, $grupo, $descripcion, $target_file, $ciclo);
                            if (!$result) {
                                $errorflag += 1;
                                array_push ($errores, "Base de Datos: Error al actualizar los datos");
                            }
                        }
                        // Validar si se actualizó la BD y se subió el archivo
                        echo '<table id="errores">'."\n";
                        echo '<tr><td>Seccion</td><td>'.$seccion.'</td></tr>';
                        echo '<tr><td>Ciclo</td><td>'.$ciclo.'</td></tr>';
                        echo '<tr><td>Grupo</td><td>'.$grupo.'</td></tr>';
                        echo '<tr><td>Descripción</td><td>'.$descripcion.'</td></tr>';
                        echo '<tr><td>Archivo</td><td>'.$target_file.'</td></tr>';
                        if ($errorflag > 0) { // o no se subieron los archivos o no se actualizó la BD
                            echo '<tr><th colspan=2><h4>ERRORES ENCONTRADOS</h4> No se pudo subir la circular...</th></tr>'."\n";

                            $max = sizeof($errores);
                            for($i = 0; $i < $max;$i++) {
                                $j = $i+1;
                                echo '<tr><td>'. $j .'</td><td>'.$errores[$i].'</td></tr>'."\n";
                            }
                        }
                        echo '</table><br>'."\n"; 
                        echo '<a href='.$_SERVER['HTTP_REFERER'].'>Regresar a formulario</a>'."\n";
                    }
                    break;
                default:
                    echo '<p>¿Qué haces aquí?</p>';
                }   // termina case privs
            
            }           // Fin del Switch type
    } // fin del else de validación


//echo '<div class="posts"></div>'."\n";
//echo '</section>'."\n";
/* ------------------- AQUI TERMINA LA SECCION CENTRAL DE INFORMACION -------------------*/
// comienza el login
//<!-- main -->
footer_();

// Imprime el menú lateral de acuerdo a los datos y al contexto.
sidebar();

/* Scripts */
scripts();

?>
