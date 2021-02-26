
function acumular() {    
    var valor = parseInt(document.getElementById("flagdata").value);
    valor = isNaN(valor) ? 0 : valor;
    document.getElementById("flagdata").value = valor +1;
}

// para el listado de informacion (informacion.php)
function showUser(str) {
  if (str == "") {
    document.getElementById("info").innerHTML = "";
    return;
  } else {
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
        document.getElementById("info").innerHTML = this.responseText;
        document.getElementById("info").style.display='block';
      }
    };
    xmlhttp.open("GET","getuser.php?q="+str,true);
    xmlhttp.send();
  }
}

// Para el formulario de Administrador (index.php)
function showGrupos() {
    var str = document.getElementById("seccion").value;
    var divcrr = document.getElementById("ctx");
    var crr = "";
    if (divcrr) {
        crr = divcrr.value;
        if (crr == undefined) {
            crr = "";
        }
    }
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
        if (str < '4') {
            document.getElementById("select1").innerHTML = this.responseText;
        } else {
            if (str == '4' && crr == "") {
                document.getElementById("select1").innerHTML = this.responseText;
            } else {
                document.getElementById("select2").innerHTML = this.responseText;
            }
        }
      }
    };
    
    var str1 = "datos.php?seccion=";
    var str2 = "&ctx=";
    var url = str1.concat(str, str2, crr);
    xmlhttp.open("GET",url,true);
    xmlhttp.send(); 
}

// para el aviso de bloqueo/desbloqueo de boleta (informacion.php)
 function unlock(str) {
  if (str == "") {
    document.getElementById("info").innerHTML = "";
    return;
  } else {
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
        document.getElementById("info").innerHTML = this.responseText;
        document.getElementById("info").style.display='block';
      }
    };
    xmlhttp.open("GET","unlock.php?id="+str,true);
    xmlhttp.send();
  }
}
