<?php

include ("dbconect.php");
//include ("libs.php");
$seccion=$_GET['seccion'];
$contexto=$_GET['ctx'];

if ($seccion < 4) {     // Preescolar a Bachillerato, hay que seleccionar el grupo.
    $conexionBD=new titular();
    $resultado=$conexionBD->lista_seccion($seccion);
    echo '<select name="Active" id="Active"><option value="" disabled selected>Elige el Grupo</option>'."\n";
    if (!$resultado) {
        echo '<option value="" disabled selected>NO SE PUDIERON CARGAR LOS GRUPOS...</option>';
    } else {
        foreach ($resultado as $reg) {
            echo '<option value="'.$reg['IdGrupo'].'">'.$reg['IdGrupo'].'</option>'."\n";
        }
    }
    echo '</select>'."\n";
} else {    // universidad
    if ($contexto == '') {  // primera vez que elige carrera, utilizamos el div select1
        echo '<select name="ctx" id="ctx" onchange="showGrupos()"><option value="" disabled selected>Elige la carrera</option>'."\n";
        echo '<option value="ARQ">Arquitectura</option>'."\n";
        echo '<option value="LAV">Animación y Videojuegos</option>'."\n";
        echo '<option value="LDE">Derecho</option>'."\n";
        echo '<option value="LFC">Catequética</option>'."\n";
        echo '<option value="LFR">Fisioterapia y Rehabilitación</option>'."\n";
        echo '<option value="LNI">Negocios Internacionales</option>'."\n";
        echo '<option value="ETO">Traumatología y Ortopedia</option>'."\n";
        echo '<option value="ERD">Rehabilitación Deportiva</option>'."\n";
        echo '<option value="ERN">Rehabilitación Neurológica</option>'."\n";
    } else {                // Ya eligió carrera, hay que mostrar la lista de grupos correspondiente
        echo '<select name="Active" id="Active"><option value="" disabled selected>Elige el grupo</option>'."\n";
        $conexionBD=new titular();
        $resultado=$conexionBD->lista_carrera($contexto);
        if (!$resultado) {
            echo '<option value="" disabled selected>NO SE PUDIERON CARGAR LOS GRUPOS...</option>';
        } else {
            foreach ($resultado as $reg) {
                echo '<option value="'.$reg['IdGrupo'].'"';
                if (isset($_SESSION['Activo']) && $_SESSION['Activo'] == $reg['IdGrupo']) { echo ' selected';}
                echo '>'.$reg['IdGrupo'].'</option>'."\n";
            }
        }
    }
    echo '</select>'."\n";
    } 


?>
