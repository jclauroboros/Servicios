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

headerfull_('Solicitud / Renovación de Becas');

/* ---------------- AQUI COMIENZA LA SECCION CENTRAL DE INFORMACION -----------------------*/
if ($_SESSION['login'] == 1) { // realizó login exitoso
    $fecha=0;
    // validar el tipo de usuario
    switch ($_SESSION['Type']) {
    case 0:     // ALUMNO
        // INICIALIZAMOS LOS VALORES DE REINSCRIPCION
        $GradoSig = $_SESSION['Grado']+1;
        if ($_SESSION['Seccion'] > 2) { $CicloSig = CICLOSIGS; } else { $CicloSig = CICLOSIGA; }
        if ($_SESSION['Seccion'] > 2) { $CicloAct = CICLOACTS; } else { $CicloAct = CICLOACTA; }
        $tipo = '';
        // hacemos una consulta para verificar si hay datos previos.
        $conexionBD=new alumnos();
        $resultado=$conexionBD->lista_becas($_SESSION['Id'], $CicloAct);
        if (!$resultado) { 
            $flagfile = 0; // Es la primera vez que sube datos

        } else {    // tomamos los valores
            $flagfile = 1;
            foreach ($resultado as $registro) {
                $cicloact = $registro['CicloAct'];
                $ciclosig = $registro['CicloSig'];
                $status = $registro['Status'];
                $tipo = $registro['Tipo'];
                $fecha = $registro['Fecha'];
                $observaciones = $registro['Observaciones'];
            }
            // Validamos que documentos existen, ponemos un 1 en cada posición si existe el fichero
            $ficheros = array('0','0','0','0', '0');
            $directorio = "becas/".$_SESSION['Seccion'];
            $directorios = ["formato", "boleta", "ingresos", "idoficial", "estudio"];
            //$extensiones = ['pdf','jpg','jpeg','png'];
            for ($i = 0; $i < 5; $i++) {
                $target_file = $directorio . '/'.$directorios[$i]. '/'. $_SESSION['Id'].'.pdf';
                    if (file_exists($target_file)) { $ficheros[$i] = '1'; }
                }
        }
        
        if (isset($status)) {
            // Existe un registro, ¿Cómo va el proceso?
            switch ($status) {
                case 0:     // En proceso
                    echo '<h3>TRÁMITE EN PROCESO DE REVISIÓN</h3>';
                    break;
                case 1:     // Denegado
                    echo '<h3>TRÁMITE CON RESULTADO NEGATIVO</h3>';
                    break;
                case 2:     // Aceptado
                    echo '<h3>TRÁMITE CON RESULTADO FAVORABLE</h3>';
                    break;
            }
            echo '<div class="col-12">'.$observaciones.'<hr/></div>';
            
        } else {
            texto_becas($_SESSION['Seccion']);
            echo '<h4>Revisa la información y actualiza los datos necesarios</h4>';
        }
        ?>
        
        <form id="beca" action="uploadbecas.php" method="post" enctype="multipart/form-data" >
         <div class="row gtr-uniform">
            <div class="col-3 col-12-small">
                Matricula <input id="matricula" name="matricula" type="text" value="<?php echo $_SESSION['Id'] ?>" readonly /> 
            </div>
            <div class="col-3 col-12-small">
                Sección <input id="seccion" name="seccion" type="text" value="<?php echo corto_seccion() ?>" readonly />
            </div>
            <div class="col-3 col-12-small">
                Ciclo Reinscripción <input id="ciclosig" name="ciclosig" type="text" value="<?php echo $CicloSig ?>" readonly /> 
            </div>
            <div class="col-3 col-12-small">
                Grado <input id="gradosig" name="gradosig" type="text" value="<?php echo $GradoSig ?>" readonly /> 
            </div>

            <div class="col-12">
                <input id="nombre" name="nombre" type="text" value="<?php echo $_SESSION['Nombres'] ?>" readonly /> 
            </div>
            <div class="col-12"><hr></div>
             <?php
        if ($flagfile == 1) {       // Ya existen archivos de este alumno.
            echo '<div class="col-12">'."\n";
            echo '<h4>Documentos Entregados anteriormente:</h4>'."\n";
            echo '</div>'."\n";
            echo '<div class="col-6 col-12-small">'."\n";
            if ($ficheros[0] == '1') { 
                echo '<input type="checkbox" id="formato_" name="formato_" checked  disabled>'."\n".'<label for="formato_">Formato de Solicitud</label>'."\n";
            } else { 
                echo '<input type="checkbox" id="formato_" name="formato_" disabled>'."\n".'<label for="formato_">Formato de Solicitud</label>'."\n"; 
            }
            echo '</div>'."\n";
            echo '<div class="col-6 col-12-small">'."\n";
            if ($ficheros[1] == '1') { 
                echo '<input type="checkbox" id="boleta_" name="boleta_" checked disabled>'."\n".'<label for="boleta_">Boleta</label>'."\n";
            } else {
                echo '<input type="checkbox" id="boleta_" name="boleta_" disabled>'."\n".'<label for="boleta_">Boleta</label>'."\n";
            }
            echo '</div>'."\n";
            echo '<div class="col-6 col-12-small">'."\n";
            if ($ficheros[2] == '1') { 
                echo '<input type="checkbox" id="ingresos_" name="ingresos_" checked disabled>'."\n".'<label for="ingresos_">Comprobante de Ingresos</label>'."\n";
            } else {
                echo '<input type="checkbox" id="ingresos_" name="ingresos_" disabled>'."\n".'<label for="ingresos_">Comprobante de Ingresos</label>'."\n";
            }
            echo '</div>'."\n";
            echo '<div class="col-6 col-12-small">'."\n";
            if ($ficheros[3] == '1') { 
                echo '<input type="checkbox" id="idoficial_" name="idoficial_" checked disabled>'."\n".'<label for="idoficial_">Identificación Oficial</label>'."\n";
            } else {
                echo '<input type="checkbox" id="idoficial_" name="idoficial_" disabled>'."\n".'<label for="idoficial_">Identificación Oficial</label>'."\n";
            }
            echo '</div>'."\n";
            echo '<div class="col-6 col-12-small">'."\n";
            if ($ficheros[4] == '1') { 
                echo '<input type="checkbox" id="estudio_" name="estudio_" checked disabled>'."\n".'<label for="estudio_">Estudio Socioeconómico</label>'."\n";
            } else {
                echo '<input type="checkbox" id="estudio_" name="estudio_" disabled>'."\n".'<label for="estudio_">Estudio Socioeconómico</label>'."\n";
            }
            echo '</div>'."\n";
            if (isset($status) && $status == 0) {
                echo '<h4>Si lo requiere, suba los documentos solicitados en formato PDF, no mayores de 2 Mb</h4>'."\n";
            }
        } else  {       // Aún no ha subido ningun archivo, hay que solicitarlos
            echo '<h4>A continuación suba los documentos solicitados en formato PDF, no mayores de 2 Mb</h4>'."\n";
        }            
            
            if (isset($status) && $status != 0) {
                echo '<div class="col-6 col-12-small">'."\n";
                echo '<input id="tipo" name="tipo" type="text" value="'.$tipo.'" readonly />';
            } else {
                echo '<div class="col-12">'."\n";
                echo '<select name="tipo" id="tipo" tabindex="1" required>';
                echo '<option value="" disabled';
                if ($tipo=='') { echo ' selected'; }
                echo '>Tipo de Beca solicitada</option>'."\n";
                echo '<option value="int" ';
                if ($tipo == 'INT') { echo ' selected'; }
                echo '>Interna</option>'."\n";
                echo '<option value="sep" ';
                if ($tipo == 'SEP') { echo ' selected'; }
                echo '>SEP</option>'."\n";
                echo '<option value="hno" ';
                if ($tipo == 'HNO') { echo ' selected'; }
                echo '>Hermanos</option>'."\n";
                echo '</select>'."\n";
            }
            echo '</div>'."\n";
            if (!isset($status) || $status == 0) {
            ?>
            <div class="col-6 col-12-small">
            <label>Formato de Solicitud</label>
            <input placeholder="Formato de Solicitud" id="formato" name="formato" accept=".pdf" tabindex="2" type="file" <?php if ($flagfile == 0) {echo 'required';} ?>/> 
            <hr/></div>
            <div class="col-6 col-12-small">
            <label>Boleta de Calificaciones</label>
            <input placeholder="Boleta de Calificaciones" id="boleta" name="boleta" accept=".pdf" tabindex="3" type="file" <?php if ($flagfile == 0) {echo 'required';} ?>/>  
            <hr/></div>
            <div class="col-6 col-12-small">
            <label>Comprobantes de Ingresos</label>
            <input placeholder="Comprobantes de Ingresos" id="ingresos" name="ingresos" accept=".pdf" tabindex="4" type="file" <?php if ($flagfile == 0) {echo 'required';} ?>/>  
            <hr/></div>
            <div class="col-6 col-12-small">
            <label>Identificación Oficial</label>
            <input placeholder="Identificación" id="idoficial" name="idoficial" accept=".pdf" tabindex="5" type="file" <?php if ($flagfile == 0) {echo 'required';} ?>/> 
            </div>
            <div class="col-6 col-12-small">
            <label>Pago Estudio Socioeconómico</label>
            <input placeholder="Pago Estudio Socioeconómico" id="estudio" name="estudio" accept=".pdf" tabindex="6" type="file" class="primary"/> 
            </div>
            <input id="Flag" name="Flag" type="hidden" value="<?php echo $flagfile; ?>">
            <input id="cicloact" name="cicloact" type="hidden" value="<?php echo $CicloAct; ?>">
            <input id="fecha" name="fecha" type="hidden" value="<?php echo $fecha; ?>">
            <div class="col-12"><hr></div>
            <div class="col-12">
                <ul class="actions fit">
            <?php
            if ($flagfile == 0) {
                echo '<li><input type="submit" tabindex="7" value="Solicitar" class="primary" /></li>'."\n";
            } else {
                echo '<li><input type="submit" tabindex="7" value="Actualizar Solicitud" class="primary" /></li>'."\n";
            }
            ?>
                </ul>
            </div>
            <?php
            }
            echo '</div>'."\n";
            echo '</form>'."\n";
        break;
    case 1:     // USUARIO - Validar el tipo de usuario
        echo '<h3>Bienvenido</h3>'."\n";
        echo '<p>No deberías estar aquí</p>'."\n";
        break;
    }
} else {
    echo '<header class="major"><h2>Bienvenido al Sistema de Servicios <br>Escolares del Instituto Valladolid.</h2></header>'."\n";
    echo '<p><b>Ingresa con tus credenciales</b></p>'."\n";
    if (isset($error) && strlen($error)>2) { echo '<script type="text/javascript"> alert ("'.$error.'"); </script> '."\n"; }
}

echo '<div class="posts"></div>'."\n";
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
