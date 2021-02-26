<?php 
// Para no permitir que se descarguen archivos que no correspondan al usuario activo, ni que se vean las rutas
error_reporting(E_ALL);
//ini_set("display_errors","On");
//ini_set("session.gc_maxlifetime","14400");
session_start();


include ("libs.php");           /* Librerias */
include ("dbconect.php");


echo '<form class="modal-content animate">'."\n";
echo '<div class="imgcontainer"><span onclick="document.getElementById(\'info\').style.display=\'none\'" class="close" title="Close Modal">&times;</span></div>'."\n";
echo '<div class="container">'."\n";
//echo '<div class="col-6 col-12-small">';
//echo '<input id="obs" name="obs" placeholder="Observaciones" type="text" onchange="leerformulario()" required/> ';
//echo '</div>';
//Validamos variables por get
if(isset($_GET['id'])) {
    $matricula=$_GET['id'];
} else {
    echo '<h3>Error al obtener la matrícula: '.$matricula.'</h3>';
}
//Validamos que solo un administrador pueda realizar el desbloqueo
if (isset($_SESSION['Privs']) && $_SESSION['Privs'] == 5) {
    //Recuperamos Datos del alumno
    $ruta = "boletas/".$_SESSION['Seccion']."/";   // Ruta de la boleta
    $nombre = $matricula.".pdf";
    $archivo = $ruta.$nombre;
    if (file_exists($archivo)) 	{   //La boleta no está bloqueada, hay que bloquear
        rename ($archivo, $ruta."_".$nombre);
		$log = fopen("locks.log","a");
		$linea = '- ' . date('d/m/y') . ' - ' . $matricula . ' - ' . $_SESSION['Id'] . " - Bloqueo\n";
		fwrite($log, $linea );
		fclose($log);
        echo '<center><h3>Boleta Bloqueada...</h4></center>';
    } else 	{
        $archivo = $ruta."_".$nombre;
        if (file_exists($archivo)) 	{ // Validar que exista y entonces renombrarlo
            rename ($archivo, $ruta.$nombre);
            $log = fopen("locks.log","a");
            $linea = '- ' . date('d/m/y') . ' - ' . $matricula . ' - ' . $_SESSION['Id'] . " - Desbloqueo\n";
            fwrite($log, $linea );
            fclose($log);

            echo '<center><h3>Boleta Desbloqueada...</h3></center>';
        } else { // No se encuentra el archivo, solicitarlo impreso 
                    echo '<center><h3>Error al buscar la boleta</h3>';
                    echo '<p>'.$archivo.'</p>';
                    echo '<p>Comunicate con el departamento de Sistemas</p></center>';
                }
    }
} else {
	echo "<center><h3>Error de ejecución</h3><p>Inicia sesión o revisa los privilegios con los que cuentas.</hp></center>";
}
echo '</div>'."\n";
echo '</form>'."\n";
?>
