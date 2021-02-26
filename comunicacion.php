<?php
error_reporting(E_ALL);
ini_set("display_errors","On");
ini_set("session.gc_maxlifetime","14400");
session_start();

include ("libs.php");           /* Librerias */
include ("dbconect.php");

// VALIDAR SI YA SE INICIO SESION
IF (isset($_SESSION['login'])&&($_SESSION['login'] == 1)) {
    // sesion iniciada
} else {
    // Verificar si es la primera vez que envían el login
    $_SESSION['login'] = 0;
    $errores = array();
    if (isset($_POST['username']) && isset($_POST['psw'])) {
        // Viene de un login
        $error = Ingresar($_POST['username'], $_POST['psw']);
    }
}

headerfull_('Comunicación');

/* ---------------- AQUI COMIENZA LA SECCION CENTRAL DE INFORMACION -----------------------*/
if ($_SESSION['login'] == 1) { // realizó login exitoso
    // validar el tipo de usuario
    switch ($_SESSION['Type']) {
    case 0:     // ALUMNO
        // ¿Existen avisos?
 
        break;
    case 1:     // USUARIO - Validar el tipo de usuario----------------------------------
        switch ($_SESSION['Privs']) {
            case 2:     // Titular
           
                break;
            case 4: // BECAS-----------------------------------------
            case 5: // Administrador---------------------------------
                // Validar si hay una selección de Grupo o de carrera o de sección
                $ValidaSeleccion = isset($_SESSION['Activo']) || isset($_SESSION['Carrera']) && $_SESSION['Carrera'] != 'NO' || isset($_SESSION['Seccion']) && $_SESSION['Seccion'] != '10';
                if ($ValidaSeleccion) {
                    // Mostrar seccion o grupo activo
                    echo '<h4>Selección: '.secciones();
                    if (isset($_SESSION['Activo']) && $_SESSION['Activo'] != '') {
                        echo ' - Grupo: '.$_SESSION['Activo'];
                    }
                    echo '</h4>';   
                }
                // PRIMER FORMULARIO-------------------------
                echo '<h4>Subir Circulares</h4>'."\n";
                echo '<form method="post" action="uploadcircular.php" enctype="multipart/form-data">'."\n";
                echo '<div class="row gtr-uniform">'."\n";
                if (!$ValidaSeleccion) {
                    echo '<div id="ListaSeccion" name="ListaSeccion" class="col-4 col-12-xsmall">'."\n";
                    echo '<select id="seccion" name="seccion" onchange="showGrupos()" tabindex="1" required>'."\n";
                    echo '<option value="" disabled selected>Elige la sección</option>'."\n";
                    echo '<option value="0">Preescolar</option>'."\n";
                    echo '<option value="1">Primaria</option>'."\n";
                    echo '<option value="2">Secundaria</option>'."\n";
                    echo '<option value="3">Bachillerato</option>'."\n";
                    echo '<option value="4">Universidad</option>'."\n";
                    echo '</select>'."\n";
                    echo '</div>'."\n";
                    echo '<div id="select1" name="select1" class="col-4 col-12-xsmall">'."\n";
                    echo '<select id="Active" name="Active" tabindex="2">'."\n";
                    echo '<option value="" disabled selected>Elige el Grupo</option>'."\n";
                    echo '</select>'."\n";
                    echo '</div>'."\n";
                    echo '<div id="select2" name="select2" class="col-4 col-12-xsmall">'."\n";
                    echo '</div>'."\n";
                } else {
                    echo '<input id="seccion" name="seccion" type="hidden" value="'.$_SESSION['Seccion'].'">';
                    echo '<input id="ctx" name="ctx" type="hidden" value="'.$_SESSION['Carrera'].'">';
                    echo '<input id="Active" name="Active" type="hidden" value="'.$_SESSION['Activo'].'">';
                }
                echo '<div id="inputDescripcion" name="inputDescripcion" class="col-12">'."\n";
                echo '<label>Descripción de la circular</label>'."\n";
                echo '<input id="descripcion" name="descripcion" type="text" tabindex="4" placeholder="Descripción" required>'."\n";
                echo '</div>'."\n";
                echo '<div id="inputArchivo" name="inputArchivo" class="col-6 col-12-xsmall">'."\n";
                echo '<label>Archivo</label>'."\n";
                echo '<input id="circular" name="circular"  accept=".pdf" tabindex="5" type="file" required>'."\n";
                echo '</div>'."\n";
                echo '<div class="col-6 col-12-xsmall"><br>'."\n";
                echo '<ul class="actions">'."\n";
                echo '<li><input type="submit" value="Subir Circular" class="primary" tabindex="6"></li>'."\n";
                echo '</ul>'."\n";
                echo '</div>'."\n";
                echo '</div>'."\n";
                echo '</form>'."\n";
            
                // MOSTRAR CIRCULARES ACTUALES-------------------------------------------------------
                if ($ValidaSeleccion) {
                    lista_circulares();
                }
                break;
            }       // switch privs
        break;
    }       // switch type
} else {
    echo '<h2>Bienvenido al Sistema de Servicios Escolares del Instituto Valladolid.</h2>'."\n";
    //echo '<header class="major"><h2>Bienvenido al Sistema de Servicios Escolares del Instituto Valladolid.</h2></header>'."\n";
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
