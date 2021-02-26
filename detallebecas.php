<?php
error_reporting(E_ALL);
ini_set("display_errors","On");
ini_set("session.gc_maxlifetime","14400");
session_start();

include ("libs.php");           /* Librerias */
include ("dbconect.php");

// VALIDAR SI YA SE INICIO SESION
IF (isset($_SESSION['login'])&&($_SESSION['login'] == 1)) {
} else {        // Verificar si es la primera vez que envían el login
    $_SESSION['login'] = 0;
    $errores = array();
    if (isset($_POST['username']) && isset($_POST['psw'])) {    // Viene de un login
        $error = Ingresar($_POST['username'], $_POST['psw']); // Validarlo, si es false no existe el usuario
    }
}

headerfull_('Detalle Becas');

/* ---------------- AQUI COMIENZA LA SECCION CENTRAL DE INFORMACION -----------------------*/
if ($_SESSION['login'] == 1) { // realizó login exitoso
    $fecha=0;
    // validar el tipo de usuario
    switch ($_SESSION['Privs']) {
    case 0:     // ALUMNO No tiene nada que hacer aquí
        echo '<h3>No cuentas con los privilegios necesarios</h3>';
        break;
    case 4:     // Becas
        // Validamos que viene la matrícula
        if (isset($_GET['id_alumno']) && $_GET['id_alumno'] != '') { $idAlumno = $_GET['id_alumno']; }
        if (isset($_GET['seccion']) && $_GET['seccion'] != '') { $SeccionAlumno = $_GET['seccion']; }
        if ($_SESSION['Seccion'] > 2) { $CicloAct = CICLOACTS; } else { $CicloAct = CICLOACTA; }
        // hacemos una consulta para verificar si hay datos previos.
        $conexionBD=new alumnos();
        $resultado=$conexionBD->lista_becas_alumno($idAlumno, $CicloAct);
        if (!$resultado) { 
            echo '<p>NO existe un registro</p>';
        } else {    // tomamos los valores
            foreach ($resultado as $registro) {
                $cicloact = $registro['CicloAct'];
                $ciclosig = $registro['CicloSig'];
                $status = $registro['Status'];
                $tipo = $registro['Tipo'];
                $fecha = $registro['Fecha'];
                $observaciones = $registro['Observaciones'];
                $review = $registro['Review'];
                $apellidos = $registro['Apellidos'];
                $nombres = $registro['Nombre'];
                $name = $apellidos.' '.$nombres;
            }
            // Validamos que documentos existen, ponemos un 1 en cada posición si existe el fichero
            $ficheros = array('0','0','0','0', '0');
            $directorio = "becas/".$SeccionAlumno;
            $directorios = ["formato", "boleta", "ingresos", "idoficial", "estudio"];
            //$extensiones = ['pdf','jpg','jpeg','png'];
            for ($i = 0; $i < 5; $i++) {
                $target_file = $directorio . '/'.$directorios[$i]. '/'. $idAlumno.'.pdf';
                if (file_exists($target_file)) { $ficheros[$i] = '1'; }
            }
            
        
        ?>        
        <form id="beca" action="uploadbecas.php" method="post" enctype="multipart/form-data" >
         <div class="row gtr-uniform">
            <div class="col-3 col-12-small">
                Matricula <input id="matricula" name="matricula" type="text" value="<?php echo $idAlumno ?>" readonly /> 
            </div>
            <div class="col-3 col-12-small">
                Sección <input id="seccion" name="seccion" type="text" value="<?php echo corto_seccion() ?>" readonly />
            </div>
            <div class="col-3 col-12-small">
                Ciclo Reinscripción <input id="ciclosig" name="ciclosig" type="text" value="<?php echo $ciclosig ?>" readonly /> 
            </div>
            <div class="col-3 col-12-small">
                Tipo <input id="ciclosig" name="ciclosig" type="text" value="<?php echo $tipo ?>" readonly /> 
            </div>
            <div class="col-12">
                Nombre  <input id="nombre" name="nombre" type="text" value="<?php echo $name ?>" readonly /> 
            </div>
            <div class="col-12">
            <h4>Documentos entregados:</h4></div>
        <?php
            echo '<div class="col-6 col-12-small">'."\n";
            if ($ficheros[0] == '1') {
                $target_file = $directorio . '/'.$directorios[0]. '/'. $idAlumno.'.pdf';
                echo '<input type="checkbox" id="formato_" name="formato_" checked  disabled>'."\n";
                echo '<label for="formato_"><a href="'.$target_file.'" target="_blank">Formato de Solicitud</a></label>'."\n";
            } else { 
                echo '<input type="checkbox" id="formato_" name="formato_" disabled>'."\n".'<label for="formato_">Formato de Solicitud</label>'."\n"; 
            }
            echo '</div>'."\n";
            echo '<div class="col-6 col-12-small">'."\n";
            if ($ficheros[1] == '1') {
                $target_file = $directorio . '/'.$directorios[1]. '/'. $idAlumno.'.pdf';
                echo '<input type="checkbox" id="boleta_" name="boleta_" checked disabled>'."\n";
                echo '<label for="boleta_"><a href="'.$target_file.'" target="_blank">Boleta</a></label>'."\n";
            } else {
                echo '<input type="checkbox" id="boleta_" name="boleta_" disabled>'."\n".'<label for="boleta_">Boleta</label>'."\n";
            }
            echo '</div>'."\n";
            echo '<div class="col-6 col-12-small">'."\n";
            if ($ficheros[2] == '1') {
                $target_file = $directorio . '/'.$directorios[2]. '/'. $idAlumno.'.pdf';
                echo '<input type="checkbox" id="ingresos_" name="ingresos_" checked disabled>'."\n";
                echo '<label for="ingresos_"><a href="'.$target_file.'" target="_blank">Comprobante de Ingresos</a></label>'."\n";
            } else {
                echo '<input type="checkbox" id="ingresos_" name="ingresos_" disabled>'."\n".'<label for="ingresos_">Comprobante de Ingresos</label>'."\n";
            }
            echo '</div>'."\n";
            echo '<div class="col-6 col-12-small">'."\n";
            if ($ficheros[3] == '1') { 
                $target_file = $directorio . '/'.$directorios[3]. '/'. $idAlumno.'.pdf';
                echo '<input type="checkbox" id="idoficial_" name="idoficial_" checked disabled>'."\n";
                echo '<label for="idoficial_"><a href="'.$target_file.'" target="_blank">Identificación Oficial</a></label>'."\n";
            } else {
                echo '<input type="checkbox" id="idoficial_" name="idoficial_" disabled>'."\n".'<label for="idoficial_">Identificación Oficial</label>'."\n";
            }
            echo '</div>'."\n";
            echo '<div class="col-6 col-12-small">'."\n";
            if ($ficheros[4] == '1') { 
                $target_file = $directorio . '/'.$directorios[4]. '/'. $idAlumno.'.pdf';
                echo '<input type="checkbox" id="estudio_" name="estudio_" checked disabled>'."\n";
                echo '<label for="estudio_"><a href="'.$target_file.'" target="_blank">Estudio Socioeconómico</a></label>'."\n";
            } else {
                echo '<input type="checkbox" id="estudio_" name="estudio_" disabled>'."\n".'<label for="estudio_">Estudio Socioeconómico</label>'."\n";
            }
            echo '</div>'."\n";
            echo '<div class="col-12">'."\n";
            echo '<p>Actualizar el registro</p>';
            echo '</div>'."\n";
            echo '<div class="col-6 col-12-small">'."\n";
            echo 'Tipo de Beca <select name="tipo" id="tipo" tabindex="1" required>';
            echo '<option value="int" ';
            if ($tipo == 'int') { echo ' selected'; }
            echo '>Interna</option>'."\n";
            echo '<option value="sep" ';
            if ($tipo == 'sep') { echo ' selected'; }
            echo '>SEP</option>'."\n";
            echo '<option value="hno" ';
            if ($tipo == 'hno') { echo ' selected'; }
            echo '>Hermanos</option>'."\n";
            echo '</select>'."\n";
            echo '</div>'."\n";
            echo '<div class="col-6 col-12-small">'."\n";
            echo 'Estatus <select name="status" id="status" tabindex="2" required>';
            echo '<option value=0 ';
            if ($status == '0') { echo ' selected'; }
            echo '>En Revisión</option>'."\n";
            echo '<option value=1 ';
            if ($status == '1') { echo ' selected'; }
            echo '>No aceptada</option>'."\n";
            echo '<option value=2 ';
            if ($status == '2') { echo ' selected'; }
            echo '>Aceptada</option>'."\n";
            echo '</select>'."\n";
            echo '</div>'."\n";
            echo '<div class="col-12">'."\n";
            echo 'Observaciones para el alumno <textarea id="obs" name="obs" rows="4">';
            echo $observaciones.'</textarea>';
            echo '</div>';
            echo '<div class="col-12">'."\n";
            echo 'Observaciones para Comité (** No visibles para el alumno) <textarea id="review" name="review" rows="4">';
            echo $review.'</textarea>';
            //echo 'Observaciones <input id="observaciones" name="observaciones" type="text" value="'.$observaciones.'"/>';
            echo '</div>'."\n";
            echo '<input id="cicloact" name="cicloact" type="hidden" value="'.$CicloAct.'">'."\n";
            echo '<input id="fecha" name="fecha" type="hidden" value="'.$fecha.'">'."\n";
            echo '<div class="col-12">'."\n";
            echo '<ul class="actions fit">'."\n";
            echo '<li><input type="submit" tabindex="7" value="Actualizar" class="primary" /></li>'."\n";
            echo '</ul>'."\n";
            echo '</div>'."\n";
            
            echo '</div>'."\n";
            echo '</form>'."\n";
        }
    }
} else {
    echo '<header class="major"><h2>Bienvenido al Sistema de Servicios <br>Escolares del Instituto Valladolid.</h2></header>'."\n";
    echo '<p><b>Ingresa con tus credenciales</b></p>'."\n";
    //   if (isset($error) && strlen($error)>2) { echo '<p>'.$error.'</p>'; }
    if (isset($error) && strlen($error)>2) { echo '<script type="text/javascript"> alert ("'.$error.'"); </script> '."\n"; }
}

//echo '<div class="posts"></div>'."\n";
echo '</section>'."\n";

/* ------------------- AQUI TERMINA LA SECCION CENTRAL DE INFORMACION -------------------*/
// comienza el login
//<!-- main -->
footer_();

// Imprime el menú lateral de acuerdo a los datos y al contexto.
sidebar();

/* Scripts */
scripts();

?>
