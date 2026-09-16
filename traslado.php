<?php
    $http_referer = isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:null;
    $referer = $_SERVER['HTTP_REFERER'];
    if ($referer == "" ) {
        print "<script> alert('TIENES QUE INICIAR SESION'); location.href = 'index.php'; </script>";
    }
    error_reporting(0);
    session_start();
    include ("phpmysql.php");  
    $ussera = $_GET['perfil'];
    $tidti = $_GET['idt'];
    $sql="Select * from usuario  WHERE idusuario = '".$ussera."'";
    $resultado = mysqli_query($conn, $sql);
    if($row = mysqli_fetch_array($resultado)){ $tipou = $row["puestousuario"]; $nombre =$row["nomusuario"]; $tienda = $row["idtienda"];}   
?>      
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">         
        <link type="text/css" rel="stylesheet" href="css/jquery-ui.css">       
        <link rel="stylesheet" href="css/miestilo.css">      
        <link rel="stylesheet" href="css/bootstrap.min.css">
	<link rel="stylesheet" href="css/tooplate-style.css">
        <link rel="stylesheet" href="css/demo.css"> 
    </head>
    <body>
        <div class="color-arriba"><h2 class ="subtitulo">TRASLADO DE PRODUCTOS O PACIENTES</h2></div> 
        <div class="fila"> 
            <div class="columna">
                <h3>Traslado de Producto</h3>
                <form name="form1" method="post" action="">
                    <input class="decorar-input" name="ttienda" type="text" id="ttienda" placeholder="Escriba y Elija una Tienda" required><div id="ntienda"></div> 
                    <input class="decorar-input" name="tbuscarp2" id="tbuscarp2" type="text" placeholder="Escriba un Código o Descripcion de Producto para Búsqueda" required><div id="bpro2"></div>
                    <input class="decorar-input" name="ttrans" id="ttrans" type="text" placeholder="Escriba un Trasnporte" required>                   
                    <div id="botones"><button type="submit" name="insertt" class="btn-universal" id="insertt"><span class="icon icon-tab"></span></button></div>                
                </form>               
                     
        <?php 
            $idti = $_POST['idti'];            
            $idpro = $_POST['idp2'];
            $trans = $_POST['ttrans']; 
            
            date_default_timezone_set('America/Guatemala');                          
            $fechat = date('Y-m-d h:i:s');
                        
            if (isset ($_POST['insertt'])){
                if ($idti != NULL) { 
                    if ($idpro != NULL) { 
                        $sql11="SELECT * FROM ubicacion WHERE idproducto = '".$idpro."' AND idtienda = '".$tienda."'";
                        $resultado11 = mysqli_query($conn, $sql11);
                            if($row11 = mysqli_fetch_array($resultado11)){ $idubicacion = $row11["idubicacion"]; $canub = $row11["cantubicacion"];}
                                               
                        if ($canub != 0){                            
                                $sql2 ="UPDATE ubicacion SET cantubicacion = (cantubicacion - 1) WHERE idubicacion = '".$idubicacion."'";
                                $resultado2 = mysqli_query($conn, $sql2);

                                $sql3 ="insert into traslado values ('NULL', '".$idpro."', '".$tienda."', '".$idti."', 1, '".$fechat."','','ENVIADO', '".$trans."')";
                                $resultado3 = mysqli_query($conn, $sql3);

                                $sql4 ="insert into bitacora values ('NULL', '".$ussera."', 'FORM TRASLADO PARA ".$idmed.", A TIENDA ".$idti."', 'INSERT', '".$fechabitacora."')";
                                $resultado4 = mysqli_query($conn, $sql4);                              
                        }
                        else {
                            print "<script> alert('CANTIDAD INSUFICIENTE EN SUCURSAL PARA ENVIAR');</script>";
                        }               
                    }
                    else{
                         print "<script> alert('DEBE ELEGIR UN MEDICAMENTO DE LA LISTA');</script>";
                    }
                }
                else {
                    print "<script> alert('DEBES ELEGIR UNA TIENDA DE LA LISTA');</script>";
                }
            }            
            $sql5="SELECT producto.idproducto, producto.descproducto, tienda.nomtienda, traslado.fetrasen, traslado.transtras
                    FROM producto INNER JOIN traslado ON traslado.idproducto = producto.idproducto
                    INNER JOIN tienda ON tienda.idtienda = traslado.idtr
                    WHERE traslado.idte = $tienda and traslado.estadotras = 'ENVIADO'";
            $resultado5 = mysqli_query($conn, $sql5);
            print "<h3>PRODUCTOS ENVIADOS</h3>";
            print "<table class='TablaH'>";                         
                print "<tr>";
                print "<td>ID</td>";
                print "<td>PRODUCTO</td>";
                print "<td>TIENDA</td>";
                print "<td>FECHA</td>";
                print "<td>TRANSPORTE</td>";
                print "</tr>";
                while ($row5 = mysqli_fetch_assoc($resultado5)){
                    print "<tr>";
                        foreach ($row5 as $item5){
                            print "<td>".($item5!==NULL ?htmlentities($item5):"&nbsp;")."</td>";
                        }
                        print "</tr>";
                    }
            print "</table>";            
        ?>                
            </div> 
            <div class="columna">
                <?php
                    echo "<form name='form' method='post' action=''>";
                    print "<h3>PRODUCTOS POR RECIBIR</h3>";
                    print "<table class = 'TablaH'>";                    
                        print "<tr><td>ID</td><td>PRODUCTO</td><td>TIENDA</td><td>FECHA</td><td>TRANSPORTE</td><td>✔</td></tr>";
                        $sql4 ="SELECT producto.idproducto, producto.descproducto, tienda.nomtienda, traslado.*
                                FROM producto INNER JOIN traslado ON traslado.idproducto = producto.idproducto
                                INNER JOIN tienda ON tienda.idtienda = traslado.idte
                                WHERE traslado.idtr = $tienda and traslado.estadotras = 'ENVIADO'";
                        $resultado4 = mysqli_query($conn, $sql4);
                        while ($row4 = mysqli_fetch_array($resultado4)){ 
                            $fila = $row4['idtraslado'];
                            print "<tr><td>{$row4['idproducto']}</td>"; 
                            print "<td>{$row4['descproducto']}</td>";                            
                            print "<td>{$row4['nomtienda']}</td>";
                            print "<td>{$row4['fetrasen']}</td>";
                            print "<td>{$row4['transtras']}</td>";
                            print "<td><button type='submit' name='updt".$fila."' class='btn-iud'>✔</button></td></tr>"; 
                            print "<input name='ttra".$fila."' type='hidden' value='".$fila."'>";
                            print "<input name='tidp2".$fila."' type='hidden' value='{$row4['idproducto']}'>";
                            $ttra= $_POST['ttra'.$fila];
                            $tidp2 = $_POST['tidp2'.$fila];
                            
                            if (isset ($_POST['updt'.$fila])){ 
                                $sql2="SELECT * FROM ubicacion WHERE idproducto = '".$tidp2."' AND idtienda = $tienda ";
                                $resultado2 = mysqli_query($conn, $sql2);
                                if($row2 = mysqli_fetch_array($resultado2)){ $idubicacion = $row2["idubicacion"];}                                
                                
                                if ($idubicacion != NULL){                        
                                    $sql3 ="UPDATE ubicacion SET cantubicacion = (cantubicacion + 1) WHERE idubicacion = '".$idubicacion."'";
                                    $resultado3 = mysqli_query($conn, $sql3); 
                                }
                                else {
                                    $sql3 ="insert into ubicacion values ('NULL', '".$tidp2."', $tienda, 1)";
                                    $resultado3 = mysqli_query($conn, $sql3);
                                } 
                                
                                $sql5 ="UPDATE traslado SET estadotras = 'RECIBIDO', fetrasre = '".$fechat."' WHERE idtraslado = '".$ttra."'";
                                $resultado5 = mysqli_query($conn, $sql5);  

                                $sql6 ="insert into bitacora values ('NULL', '".$ussera."', 'FORM TRASLADO RECIBIDO', 'UPDATE', '".$fechabitacora."')";
                                $resultado6 = mysqli_query($conn, $sql6);

                                echo '<meta http-equiv=refresh content="0">';
                            }
                        }
                    print "</table>";
                echo "</form>";
                ?>
            </div>
        </div>
	<script type="text/javascript" src="js/jquery-3.6.1.min.js"></script> 
        <script type="text/javascript" src="js/jquery-ui.js"></script>
        <script type="text/javascript" src="js/misfunciones.js"></script>
        <script type="text/javascript" src="js/versection.js"></script> 
        <script type="text/javascript" src="js/funcionlista.js"></script>
    </body>
</html>
