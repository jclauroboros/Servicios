<?php
class Conexion extends PDO {
  private $tipo_de_base = 'mysql';
  private $host = 'localhost';
    /*DATOS SERVIDOR LOCAL*/
  private $usuario = 'servicios';
  private $password = 'servicios';
  private $nombre_de_base = 'Servicios';
  //private $usuario = 'id15894937_servicios_user';
  //private $password = '@T(/GQ*~eSf[7IvN';
  //private $nombre_de_base = 'id15894937_servicios';
  /* DATOS SERVIDOR HOSTGATOR*/
  // private $usuario='umvallae_uniuser';// aqui debes ingresar el nombre de usuario bd
  // private $password='45,w.;kxcVPE'; // password de acceso para el usuario de la bd

public function __construct() {
  try{
    parent::__construct($this->tipo_de_base.':host='.$this->host.';dbname='.$this->nombre_de_base, $this->usuario, $this->password);
  }catch(PDOException $e){
    echo 'Ha surgido un error y no se puede conectar a la base de datos. Detalle: ' . $e->getMessage();
    exit;
  }
}

}

?>
