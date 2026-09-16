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
        <div class="color-arriba"><h2 class ="subtitulo">BODEGA LOVA</h2></div>
        <div class="fila"> 
            <div class="columna">
                <form name="form7" method="post" action="">
                    <h3>INSERTAR NUEVO ACCESORIOS</h3>                        
                    <input class="decorar-input" name="tdesac" type="text" placeholder="Descripción del Accesorio" required>
                    <input class="decorar-input" name="tpreac" type="number" required placeholder="Precio al Publico" step="0.01" title="Puede ser entero o decimal">
                    <input class="decorar-input" name="tobsac" type="text" placeholder="Observación para el Accesorio">
                    <div id="botones"><button type="submit" name="insertpac" class="btn-universal"><span class="icon icon-floppy-disk"></span></button></div>
                </form>
                 <?php
                 $idac = $_POST['tidac'];  $desac = $_POST['tdesac'];  $preac = $_POST['tpreac'];  $obsac = $_POST['tobsac'];
                
            if (isset ($_POST['insertpac'])){
                $sql="SELECT COUNT(*) AS conteo FROM producto WHERE tipoproducto = 'ACCESORIO'";
                $resultado = mysqli_query($conn, $sql);
                if($row = mysqli_fetch_array($resultado)){ $conteo = $row["conteo"]; }
                $cuenta = $conteo + 1;               
                $numero = str_pad($cuenta, 3, "0", STR_PAD_LEFT);                            
                $idac = 'AC'.$numero;
                
                $sql1 ="insert into producto values ('".$idac."', 'ACCESORIO', 'NO APLICA', '".$desac."', '', '".$preac."', '".$obsac."')";
                $resultado1 = mysqli_query($conn, $sql1); 

                $sql2 ="insert into bitacora values ('NULL', '".$ussera."', 'FORM PRODUCTO ACCESORIO ".$idac."', 'INSERT', '".$fechabitacora."')";
                $resultado2 = mysqli_query($conn, $sql2);

                $sql3 ="SELECT * FROM producto where idproducto = '".$idac."'";
                $resultado3 = mysqli_query($conn, $sql3);
                if ($row3 = mysqli_fetch_row($resultado3)){ }
                print "<div class='columna'>";
                echo '<div class="color-arriba"><h3>ACCESORIO GUARDADO CORRECTAMENTE</h3></div>';
                print "<table class='TablaA'>";                            
                print "<tr><td width = '35%'>ID:</td><td>$row3[0]</td></tr>";
                print "<tr><td>DESCRIPCIÓN:</td><td>$row3[3]</td></tr>";
                print "<tr><td>PRECIO:</td><td>$row3[5]</td></tr>";
                print "<tr><td>OBSERVACIÓN:</td><td>$row3[6]</td></tr>";
                print "</table>";
                print "</div>";                             
            } 
        ?>
            </div>
            <div class="columna">
                <form name="form2" method="post" action="">
                    <h3>MODIFICACIÓN DE INFORMACION DE ACCSESORIO</h3>
                    <input class="decorar-input" name="tacm" id="tacm" type="text" placeholder="Escriba y Elija un Accesorio" required>
                    <div><br></div><div id="nacm" class="contenedor-transparente"></div>                    
                </form>
                 <?php           
            if (isset ($_POST['updac'])){
                $sql4 ="insert into bitacora values ('NULL', '".$ussera."', 'FORM PRODUCTO ACCESORIO ".$idac."', 'UPDATE', '".$fechabitacora."')";
                $resultado4 = mysqli_query($conn, $sql4); 
                
                $sql5 ="update producto SET descproducto = '".$desac."', prevproducto = '".$preac."', obsproducto = '".$obsac."' where idproducto = '".$idac."' ";
                $resultado5 = mysqli_query($conn, $sql5); 
                                          
                $sql3 ="SELECT * FROM producto where idproducto = '".$idac."'";
                $resultado3 = mysqli_query($conn, $sql3);
                if ($row3 = mysqli_fetch_row($resultado3)){ }
                print "<div class='columna'>";
                echo '<div class="color-arriba"><h3>ACCESORIO GUARDADO CORRECTAMENTE</h3></div>';
                print "<table class='TablaA'>";                            
                print "<tr><td width = '35%'>ID:</td><td>$row3[0]</td></tr>";
                print "<tr><td>DESCRIPCIÓN:</td><td>$row3[3]</td></tr>";
                print "<tr><td>PRECIO:</td><td>$row3[5]</td></tr>";
                print "<tr><td>OBSERVACIÓN:</td><td>$row3[6]</td></tr>";
                print "</table>";
                print "</div>";                      
            }  
       
            if (isset ($_POST['delac'])){                 
                $sql7 ="delete from producto where idproducto = '".$idac."'";
                $resultado7 = mysqli_query($conn, $sql7); 
                echo '<div class="color-arriba"><h3>EL PRODUCTO -'.$desac.'- HA SIDO ELIMINADO CORRECTAMENTE</h3></div>';   
                
                $sql8 ="insert into bitacora values ('NULL', '".$ussera."', 'FORM PRODUCTO ACCESORIO ".$idac."', 'DELETE', '".$fechabitacora."')";
                $resultado8 = mysqli_query($conn, $sql8);
            }            
            ?>
            </div>           
        </div>
        <div class="fila"> 
            <div class="columna">
                <form name="form1" method="post" action=""> 
                    <h3>INGRESO DE PRODUCTOS A BODEGA</h3>
                    <div id="botones"><button type="submit" name="insertg" class="btn-universal"><span class="icon icon-spell-check"></span></button></div>
                </form>
                <form name="form2" method="post" action="">         
                    <table class="TablaE">                        
                        <tr>
                            <td><input class="input-iud" name="tac" type="text" id="tac"  placeholder="Escriba y Elija un Accesorio"><div id="nac"></div></td>
                            <td><input class="input-iud" name="pacs" type="text"  placeholder="#"></td>          
                            <td><button type="submit" name="insertac" class="btn-iud" id="insertac">✔</button></td>
                        </tr>
                    </table>
                </form>
                <form name="form3" method="post" action=""> 
                    <table width = '100%'>
                        <tr><td colspan="2"><h3>ELIJE UNA FECHA Y LUEGO EN EL BOTON PARA VER</h3></td></tr>
                        <tr>                           
                            <td width = '80%'><input type="date" name="fbus" class="input-iud" id="fbus" required></td>
                            <td><button type="submit" name="selectb" class="btn-universal" id="selectb"><span class="icon icon-search"></span></button></td>
                        </tr>
                    </table>
                    <?php
                    $fbus = $_POST['fbus'];
                    if (isset ($_POST['selectb'])){                
                
                $sql3="SELECT producto.descproducto, detallegestionm.idcantidad FROM gestionm
                        INNER JOIN detallegestionm ON detallegestionm.idgestion = gestionm.idgestion
                        INNER JOIN ubicacion ON ubicacion.idubicacion = detallegestionm.idubicacion
                        INNER JOIN producto ON producto.idproducto = ubicacion.idproducto
                        WHERE gestionm.idtienda = 11 AND producto.tipoproducto = 'ACCESORIO' AND gestionm.fechagestion = '".$fbus."'                         
                        ORDER BY `producto`.`descproducto` ASC";
                $resultado3 = mysqli_query($conn, $sql3);                
                print "<table class='TablaJ'>";                         
                    print "<tr>";                    
                    print "<td>ACCESORIO</td>";                    
                    print "<td>TOTAL</td>"; 
                    print "</tr>";
                    while ($row3 = mysqli_fetch_assoc($resultado3)){
                        print "<tr>";
                            foreach ($row3 as $item3){
                                print "<td>".($item3!==NULL ?htmlentities($item3):"&nbsp;")."</td>";
                            }
                            print "</tr>";
                        }
                print "</table>";
            }
            ?> 
                </form>
            </div>  
        <?php 
            date_default_timezone_set('America/Guatemala');                          
            $fechag = date('Y-m-d');
            
            $sql3 ="SELECT * FROM gestionm where idtienda = 11 and fechagestion = '".$fechag."'";
            $resultado3 = mysqli_query($conn, $sql3);
                if ($row3 = mysqli_fetch_row($resultado3)){ }
                    print "<div class='columna'>";               
                        print "<table class='TablaA'>";
                            print "<tr><td colspan = '2'>LISTADO DE PRODUCTOS INGRESADOS A BODEGA</td></tr>";
                            print "<tr><td width='35%'>FECHA:</td><td>$row3[2]</td></tr>";               
                        print "</table>";
                        
            if (isset ($_POST['insertg'])){ 
                if ($row3[0] == NULL){
                    $sql1 ="insert into gestionm values (NULL, 11, '".$fechag."', '')";
                    $resultado1 = mysqli_query($conn, $sql1);

                    $sql2 ="insert into bitacora values ('NULL', '".$ussera."', 'FORM BODEGA', 'INSERT', '".$fechabitacora."')";
                    $resultado2 = mysqli_query($conn, $sql2); 
                    
                    echo '<meta http-equiv=refresh content="0">';
                }
                else{
                   print "<script> alert('YA HAY UN LISTADO CON ESA FECHA');</script>"; 
                }
            }  
            
            $idac = $_POST['tidpro5'];           
            $pacs = $_POST['pacs']; 
            if (isset ($_POST['insertac'])){
                if ($row3[0] != NULL){
                    if ($idac != NULL) { 
                        $sql11="SELECT * FROM ubicacion WHERE idproducto = '".$idac."' AND idtienda = 11";
                        $resultado11 = mysqli_query($conn, $sql11);
                            if($row11 = mysqli_fetch_array($resultado11)){ $idubicacion = $row11["idubicacion"];}

                        if ($idubicacion != NULL){                        
                            $sql2 ="UPDATE ubicacion SET cantubicacion = (cantubicacion + $pacs) WHERE idubicacion = '".$idubicacion."'";
                            $resultado2 = mysqli_query($conn, $sql2);                        
                        }
                        else {
                          $sql2 ="insert into ubicacion values ('NULL', '".$idac."', 11, '".$pacs."')";
                          $resultado2 = mysqli_query($conn, $sql2);                       
                        }
                        $sql11="SELECT * FROM ubicacion WHERE idproducto = '".$idac."' AND idtienda = 11";
                        $resultado11 = mysqli_query($conn, $sql11);
                            if($row11 = mysqli_fetch_array($resultado11)){ $idubicacion = $row11["idubicacion"];} 

                        $sql3 ="insert into detallegestionm values ('NULL', '".$row3[0]."', '".$idubicacion."', '".$pacs."', '', 'RECIBIDO')";
                        $resultado3 = mysqli_query($conn, $sql3);

                        $sql4 ="insert into bitacora values ('NULL', '".$ussera."', 'FORM DETALLEGESTION PARA ".$idac.", INSERT ACCESORIO', 'INSERT', '".$fechabitacora."')";
                        $resultado4 = mysqli_query($conn, $sql4);                    
                    }
                    else{
                         print "<script> alert('DEBE ELEGIR UN ACCESORIO');</script>";
                    }
                }
                else {
                     print "<script> alert('PRODUCTO NO INGRESADO, ANTES DEBE DAR CLICK EN NUEVO INGRESO');</script>";
                }
            } 

                    echo "<form name='form' method='post' action=''>";
                        print "<table class = 'TablaC'>";
                            print "<tr><td>CANT</td><td>PRODUCTO</td><td>ELIMINAR</td></tr>";
                            $sql4 ="SELECT detallegestionm.*, producto.*, ubicacion.*
                                    FROM detallegestionm INNER JOIN ubicacion ON ubicacion.idubicacion = detallegestionm.idubicacion
                                    INNER JOIN producto ON producto.idproducto = ubicacion.idproducto
                                    WHERE detallegestionm.idgestion = '".$row3[0]."'";
                            $resultado4 = mysqli_query($conn, $sql4);
                            while ($row4 = mysqli_fetch_array($resultado4)){ 
                                $fila = $row4['iddgestionm'];
                                print "<tr><td>{$row4['idcantidad']}</td><td>{$row4['descproducto']}</td>";
                                print "<td><button type='submit' name='deldg".$fila."' class='btn-iud'>✕</button>";
                                print "<input name='tiddg".$fila."' type='hidden' value='".$fila."'>";
                                print "<input name='tcanu".$fila."' type='hidden' value='{$row4['idcantidad']}'>";
                                print "<input name='idub".$fila."' type='hidden' value='{$row4['idubicacion']}'></td></tr>";
                                $tid = $_POST['tiddg'.$fila];
                                $idub = $_POST['idub'.$fila];
                                $tcanu = $_POST['tcanu'.$fila];
                                
                                if (isset ($_POST['deldg'.$fila])){                         
                                    
                                    $sql6 ="update ubicacion SET cantubicacion = (cantubicacion - $tcanu) where idubicacion = $idub";
                                    $resultado6 = mysqli_query($conn, $sql6);
                                    
                                    $sql5 ="delete from detallegestionm where iddgestionm = '".$tid."'";
                                    $resultado5 = mysqli_query($conn, $sql5);                                   

                                    $sql7 ="insert into bitacora values ('NULL', '".$ussera."', 'FORM BODEGA', 'DELETE', '".$fechabitacora."')";
                                    $resultado7 = mysqli_query($conn, $sql7);

                                    echo '<meta http-equiv=refresh content="0">';
                                }
                            }
                        print "</table>";
                    echo "</form>";
                print "</div>";           
        ?>
        </div>
	<script type="text/javascript" src="js/jquery-3.6.1.min.js"></script> 
        <script type="text/javascript" src="js/jquery-ui.js"></script>
        <script type="text/javascript" src="js/misfunciones.js"></script>
        <script type="text/javascript" src="js/versection.js"></script> 
        <script type="text/javascript" src="js/funcionlista.js"></script>
    </body>
</html>