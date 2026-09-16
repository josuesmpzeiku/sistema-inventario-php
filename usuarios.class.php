<?php
class Tienda{     
    public function buscarT($tienda){
        include ("phpmysql.php");   
        $datos = array();
        $sql = "SELECT * FROM tienda
                        WHERE  idtienda LIKE '%$tienda%' or nomtienda LIKE '%$tienda%'"; 
        $resultado = mysqli_query($conn, $sql);        
        while ($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)){
          $datos[] = array("value" => $row['idtienda'].'   '.
                                      $row['nomtienda'],
                           "idt" =>   $row['idtienda']);                                                   
        }
        return $datos;       
    }
}

class Usuariop{     
    public function buscarU($idus){
        include ("phpmysql.php");   
        $datos = array();
        $sql = "SELECT * FROM usuario
                        WHERE  idusuario LIKE '%$idus%' or nomusuario LIKE '%$idus%'"; 
        $resultado = mysqli_query($conn, $sql);        
        while ($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)){
          $datos[] = array("value" => $row['idusuario'].'   '.
                                      $row['nomusuario'],
                           "nomu" =>   $row['nomusuario'],
                           "useru" =>   $row['userusuario'],
                           "passu" =>   $row['passusuario'],
                           "tipou" =>   $row['puestousuario'],
                           "tieu" =>   $row['idtienda'],
                           "idu" =>   $row['idusuario']);                                                   
        }
        return $datos;       
    }
}

class Paciente{     
    public function buscarPa($paciente){
        include ("phpmysql.php");   
        $datos = array();
        $sql = "SELECT paciente.*, TIMESTAMPDIFF(YEAR,fecnpaciente,CURDATE()) AS edad FROM paciente
                        WHERE  nitpaciente LIKE '%$paciente%' or nompaciente LIKE '%$paciente%'"; 
        $resultado = mysqli_query($conn, $sql);        
        while ($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)){
          $datos[] = array("value" => $row['nitpaciente'].'   '.
                                      $row['nompaciente'].'   EDAD '.
                                      $row['edad'].' años',
                           "nompa" =>   $row['nompaciente'],
                           "telpa" =>   $row['telpaciente'],
                           "nitpa" =>   $row['nitpaciente'],
                           "idpa" =>   $row['idpaciente']);                                                   
        }
        return $datos;       
    }
}

class Lente{     
    public function buscarLen($lente){
        include ("phpmysql.php");   
        $datos = array();
        $sql = "SELECT * FROM producto
                WHERE  descproducto LIKE '%$lente%' and tipoproducto = 'LENTE'"; 
        $resultado = mysqli_query($conn, $sql);        
        while ($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)){
          $datos[] = array("value" =>   $row['descproducto'],
                            "descp" =>   $row['descproducto'],
                            "clap" =>   $row['claseproducto'],
                            "obsp" =>   $row['obsproducto'],
                            "preciop" =>   $row['prevproducto'],                          
                            "idpro" =>   $row['idproducto']);                                                   
        }
        return $datos;       
    }
}

class Medic{     
    public function buscarMe($med){
        include ("phpmysql.php");   
        $datos = array();
        $sql = "SELECT * FROM producto
                WHERE  (descproducto LIKE '%$med%' or idproducto LIKE '%$med%') and tipoproducto = 'MEDICAMENTO'"; 
        $resultado = mysqli_query($conn, $sql);        
        while ($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)){
          $datos[] = array("value" =>   $row['idproducto'].'   '.
                                        $row['descproducto'],
                            "descp2" =>   $row['descproducto'],
                            "obsp2" =>   $row['obsproducto'], 
                            "venp2" =>   $row['fevenproducto'],
                            "preciop2" =>   $row['prevproducto'],                          
                            "idpro2" =>   $row['idproducto']);                                                   
        }
        return $datos;       
    }
}

class Aro{     
    public function buscarA($aro){
        include ("phpmysql.php");   
        $datos = array();
        $sql = "SELECT * FROM producto
                WHERE  (descproducto LIKE '%$aro%' or idproducto LIKE '%$aro%') and tipoproducto = 'ARO'"; 
        $resultado = mysqli_query($conn, $sql);        
        while ($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)){
          $datos[] = array("value" =>   $row['idproducto'].'   '.
                                        $row['descproducto'],
                           "descp3" =>   $row['descproducto'],
                           "clap3" =>   $row['claseproducto'],
                           "preciop3" =>   $row['prevproducto'],
                           "obsp3" =>   $row['obsproducto'],                          
                           "idpro3" =>   $row['idproducto']);                                                   
        }
        return $datos;       
    }
}

class Servicio{     
    public function buscarSe($serv){
        include ("phpmysql.php");   
        $datos = array();
        $sql = "SELECT * FROM producto
                WHERE  (descproducto LIKE '%$serv%' or idproducto LIKE '%$serv%') and tipoproducto = 'SERVICIO'"; 
        $resultado = mysqli_query($conn, $sql);        
        while ($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)){
          $datos[] = array("value" =>   $row['idproducto'].'   '.
                                        $row['descproducto'],
                            "descp4" =>   $row['descproducto'],
                            "obsp4" =>   $row['obsproducto'],
                            "preciop4" =>   $row['prevproducto'],                          
                            "idpro4" =>   $row['idproducto']);                                                   
        }
        return $datos;       
    }
}

class Accesorio{     
    public function buscarAc($acs){
        include ("phpmysql.php");   
        $datos = array();
        $sql = "SELECT * FROM producto
                WHERE (descproducto LIKE '%$acs%' or idproducto LIKE '%$acs%') and tipoproducto = 'ACCESORIO'"; 
        $resultado = mysqli_query($conn, $sql);        
        while ($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)){
          $datos[] = array("value" =>   $row['idproducto'].'   '.
                                        $row['descproducto'],
                            "descp5" => $row['descproducto'],
                            "obsp5" =>  $row['obsproducto'],
                            "preciop5" =>$row['prevproducto'],                          
                            "idpro5" =>  $row['idproducto']);                                                   
        }
        return $datos;       
    }
}
class Reparacion{     
    public function buscarR($rep){
        include ("phpmysql.php");   
        $datos = array();
        $sql = "SELECT * FROM producto
                WHERE (descproducto LIKE '%$rep%' or idproducto LIKE '%$rep%') and tipoproducto = 'REPARACION'"; 
        $resultado = mysqli_query($conn, $sql);        
        while ($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)){
          $datos[] = array("value" =>   $row['idproducto'].'   '.
                                        $row['descproducto'],
                            "descp6" => $row['descproducto'],
                            "obsp6" =>  $row['obsproducto'],
                            "preciop6" =>$row['prevproducto'],                          
                            "idpro6" =>  $row['idproducto']);                                                   
        }
        return $datos;       
    }
}

class Producto{
     
    public function buscarP($producto){
        include ("phpmysql.php");
        $datos = array();
        $sql = "SELECT producto.*, MAX(CASE WHEN ubicacion.idtienda = 1 THEN ubicacion.cantubicacion end) as tienda1,
                MAX(CASE WHEN ubicacion.idtienda = 2 THEN ubicacion.cantubicacion end) as tienda2,
                MAX(CASE WHEN ubicacion.idtienda = 3 THEN ubicacion.cantubicacion end) as tienda3,
                MAX(CASE WHEN ubicacion.idtienda = 4 THEN ubicacion.cantubicacion end) as tienda4,
                MAX(CASE WHEN ubicacion.idtienda = 5 THEN ubicacion.cantubicacion end) as tienda5,
                MAX(CASE WHEN ubicacion.idtienda = 6 THEN ubicacion.cantubicacion end) as tienda6,
                MAX(CASE WHEN ubicacion.idtienda = 7 THEN ubicacion.cantubicacion end) as tienda7,
                MAX(CASE WHEN ubicacion.idtienda = 8 THEN ubicacion.cantubicacion end) as tienda8,
                max(CASE WHEN ubicacion.idtienda = 10 THEN ubicacion.cantubicacion end) as bodega
                FROM producto INNER JOIN ubicacion ON ubicacion.idproducto = producto.idproducto               
                WHERE producto.idproducto LIKE '%$producto%'  OR  producto.descproducto LIKE '%$producto%'
                GROUP by ubicacion.idproducto"; 
        $resultado = mysqli_query($conn, $sql);        
        while ($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)){
          $datos[] = array("value" => $row['idproducto'].'   '.
                                      $row['descproducto'],
                            "idp" => $row['idproducto'],
                            "tipo" => $row['tipoproducto'],
                            "clase" => $row['claseproducto'],
                            "desc" => $row['descproducto'],
                            "fven" => $row['fevenproducto'],
                            "fven" => $row['fevenproducto'],
                            "precio" => $row['prevproducto'],
                            "obs" => $row['obsproducto'],
                            "t1" => $row['tienda1'],
                            "t2" => $row['tienda2'],
                            "t3" => $row['tienda3'],
                            "t4" => $row['tienda4'],
                            "t5" => $row['tienda5'],
                            "t6" => $row['tienda6'],
                            "t7" => $row['tienda7'],
                            "t8" => $row['tienda8'],              
                            "bod" => $row['bodega']);                                                   
        }
        
        return $datos;       
    }
    
}

class mlen{     
    public function buscarml($ml){
        include ("phpmysql.php");   
        $datos = array();
        $sql = "SELECT * FROM lente
                WHERE  lentedes LIKE '%$ml%' and canlente != 0 "; 
        $resultado = mysqli_query($conn, $sql);        
        while ($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)){
          $datos[] = array("value" =>   $row['lentedes'],
                            "idl" =>   $row['idlente'],
                            "desl" =>   $row['lentedes'],
                            "prel" =>   $row['canlente']);                                                   
        }
        return $datos;       
    }
}

class nlen{     
    public function buscarnl($nl){
        include ("phpmysql.php");   
        $datos = array();
        $sql = "SELECT * FROM lente WHERE  lentedes LIKE '%$nl%'"; 
        $resultado = mysqli_query($conn, $sql);        
        while ($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)){
          $datos[] = array("value" =>   $row['lentedes'],
                            "idln" =>   $row['idlente'],
                            "desln" =>   $row['lentedes'],
                            "preln" =>   $row['canlente']);                                                   
        }
        return $datos;       
    }
}
class Atencion{     
    public function buscarAt($aten){
        include ("phpmysql.php");   
        $datos = array();
        $sql = "SELECT atencion.*, paciente.*, venta.* FROM paciente INNER JOIN atencion ON atencion.idpaciente = paciente.idpaciente
                INNER JOIN venta ON venta.idventa = atencion.idatencion
                WHERE atencion.idatencion LIKE '%$aten%' or paciente.nompaciente LIKE '%$aten%' or atencion.fecatencion LIKE '%$aten%' "; 
        $resultado = mysqli_query($conn, $sql);        
        while ($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)){
          $datos[] = array("value" => $row['idatencion'].'   '.
                                      $row['nompaciente'].'   '.
                                      $row['fecatencion'],
                           "ida" =>   $row['idatencion'],
                           "obsv" =>   $row['obsventa']);                                             
        }
        return $datos;       
    }
}