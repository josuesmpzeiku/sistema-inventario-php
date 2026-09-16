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
        <div class="color-arriba"><h2 class ="subtitulo">ENTRADAS EN SUCURSAL</h2></div> 
        <div class="fila">  
        <?php 
            $sql1 ="SELECT gestionm.*, tienda.* FROM gestionm INNER JOIN tienda ON tienda.idtienda = gestionm.idtienda where gestionm.idtienda  = '".$tienda."' ORDER by gestionm.idgestion desc limit 1";
            $resultado1 = mysqli_query($conn, $sql1);
            if ($row1 = mysqli_fetch_row($resultado1)){ }                        
            print "<div class='columna'>";
                echo "<form name='form' method='post' action=''>";
                    print "<table class = 'TablaH'>";
                        print "<tr><td width = '5%'>BO</td><td width = '5%'>SU</td><td width = '10%'>TIPO</td><td '10%'>CÓDIGO</td><td>DESCRIPCIÓN</td><td '10%'>PUNTO</td><td width = '5%'>✔</td></tr>";
                        $sql4 ="SELECT detallegestionm.*, producto.*, ubicacion.*
                                FROM detallegestionm INNER JOIN ubicacion ON ubicacion.idubicacion = detallegestionm.idubicacion
                                INNER JOIN producto ON producto.idproducto = ubicacion.idproducto
                                WHERE detallegestionm.idgestion = '".$row1[0]."' AND detallegestionm.estadogestio = 'ENVIADO'";
                        $resultado4 = mysqli_query($conn, $sql4);
                        while ($row4 = mysqli_fetch_array($resultado4)){ 
                            $fila = $row4['iddgestionm'];
                            print "<tr><td>{$row4['idcantidad']}</td>";                            
                            print "<td><input name='tcans".$fila."' type='text' value='{$row4['cans']}' class='input-iud' size='1'></td>";
                            print "<td>{$row4['tipoproducto']}</td>";
                            print "<td>{$row4['idproducto']}</td>";
                            print "<td>{$row4['descproducto']}</td>";
                            print "<td>{$row4['obsproducto']}</td>";
                            print "<td><button type='submit' name='updcs".$fila."' class='btn-iud'>✔</button></td></tr>"; 
                            print "<input name='tiddg".$fila."' type='hidden' value='".$fila."'>";
                            print "<input name='tcanbo".$fila."' type='hidden' value='{$row4['idcantidad']}'>";                            
                            print "<input name='idp".$fila."' type='hidden' value='{$row4['idproducto']}'></td></tr>";
                            $tid = $_POST['tiddg'.$fila];
                            $idp = $_POST['idp'.$fila];
                            $tcanbo = $_POST['tcanbo'.$fila];
                            $tcans = $_POST['tcans'.$fila];

                            if (isset ($_POST['updcs'.$fila])){ 
                                $sql2="SELECT * FROM ubicacion WHERE idproducto = '".$idp."' AND idtienda = $tienda ";
                                $resultado2 = mysqli_query($conn, $sql2);
                                if($row2 = mysqli_fetch_array($resultado2)){ $idubicacion = $row2["idubicacion"];}                                
                                
                                if ($idubicacion != NULL){                        
                                    $sql3 ="UPDATE ubicacion SET cantubicacion = (cantubicacion + $tcans) WHERE idubicacion = '".$idubicacion."'";
                                    $resultado3 = mysqli_query($conn, $sql3); 
                                }
                                else {
                                    $sql3 ="insert into ubicacion values ('NULL', '".$idp."', $tienda, '".$tcans."')";
                                    $resultado3 = mysqli_query($conn, $sql3);
                                }
                                
                                if ($tcanbo != $tcans){ 
                                    $tcand = $tcanbo - $tcans;
                                    $sql4 ="insert into devolucion values ('NULL', '".$tid."', '".$tcand."', '', 'GESTION')";
                                    $resultado4 = mysqli_query($conn, $sql4);
                                }
                                
                                $sql5 ="UPDATE detallegestionm SET estadogestio = 'RECIBIDO', cans = $tcans WHERE iddgestionm = '".$tid."'";
                                $resultado5 = mysqli_query($conn, $sql5);  

                                $sql6 ="insert into bitacora values ('NULL', '".$ussera."', 'FORM SUCURSAL RECIBIDO', 'UPDATE', '".$fechabitacora."')";
                                $resultado6 = mysqli_query($conn, $sql6);

                                echo '<meta http-equiv=refresh content="0">';
                            }
                        }
                    print "</table>";
                echo "</form>";                
            print "</div>";
            print "<div class='columna'>";
                $sql7="SELECT detallegestionm.cans, producto.tipoproducto, producto.idproducto, producto.descproducto, producto.obsproducto
                        FROM detallegestionm INNER JOIN ubicacion ON ubicacion.idubicacion = detallegestionm.idubicacion
                        INNER JOIN producto ON producto.idproducto = ubicacion.idproducto
                        WHERE detallegestionm.idgestion = '".$row1[0]."' AND detallegestionm.estadogestio = 'RECIBIDO'";
                $resultado7 = mysqli_query($conn, $sql7);
                print "<h3>PRODUCTOS RECIBIDOS</h3>";
                print "<table class='TablaH'>";                         
                    print "<tr>";
                    print "<td>U</td>";
                    print "<td>TIPO</td>";
                    print "<td>CODIGO</td>";
                    print "<td>DESCRIPCIÓN</td>";
                    print "<td>PUNTO</td>";
                    print "</tr>";
                    while ($row7 = mysqli_fetch_assoc($resultado7)){
                        print "<tr>";
                            foreach ($row7 as $item7){
                                print "<td>".($item7!==NULL ?htmlentities($item7):"&nbsp;")."</td>";
                            }
                            print "</tr>";
                        }
                print "</table>";
            print "</div>";
        ?>
        </div><br><br>
        <div class="fila">
            <?php
                echo "<form name='form1' method='post' action=''>";
                print "<h3>GESTIONAR DEVOLUCIONES</h3>";
                    print "<table class = 'TablaH'>";
                        print "<tr><td width = '5%'>U</td><td>TIPO</td><td>CÓDIGO</td><td>DESCRIPCIÓN</td><td>PUNTO</td><td width = '30%'>MOTIVO</td><td width = '5%'>✔</td></tr>";
                        $sql8 ="SELECT devolucion.*, producto.*
                                FROM devolucion INNER JOIN detallegestionm ON detallegestionm.iddgestionm = devolucion.iddgestionm
                                INNER JOIN ubicacion ON ubicacion.idubicacion = detallegestionm.idubicacion
                                INNER JOIN producto ON producto.idproducto = ubicacion.idproducto
                                WHERE detallegestionm.idgestion = '".$row1[0]."' and devolucion.estadodev = 'GESTION'";
                        $resultado8 = mysqli_query($conn, $sql8);
                        while ($row8 = mysqli_fetch_array($resultado8)){ 
                            $fila1 = $row8['iddevo'];
                            print "<tr><td>{$row8['cantdevo']}</td>";                            
                            print "<td>{$row8['tipoproducto']}</td>";
                            print "<td>{$row8['idproducto']}</td>";
                            print "<td>{$row8['descproducto']}</td>";
                            print "<td>{$row8['obsproducto']}</td>";
                            print "<td><input name='tcom".$fila1."' type='text' value='{$row8['comdevo']}' class='decorar-input'></td>";                            
                            print "<td><button type='submit' name='updev".$fila1."' class='btn-iud'>✔</button></td></tr>"; 
                            print "<input name='tidev".$fila1."' type='hidden' value='".$fila1."'>";
                            $tidev = $_POST['tidev'.$fila1];
                            $tcom = $_POST['tcom'.$fila1];
                            
                            if (isset ($_POST['updev'.$fila1])){ 
                                if ($tcom != NULL){                        
                                    $sql3 ="UPDATE devolucion SET comdevo = '".$tcom."', estadodev = 'ENVIADO' WHERE iddevo = '".$tidev."'";
                                    $resultado3 = mysqli_query($conn, $sql3); 
                                }
                                else {
                                    print "<script> alert('TIENES QUE ESCRIBIR UN MOTIVO');</script>";
                                }
                                $sql4 ="insert into bitacora values ('NULL', '".$ussera."', 'FORM SUCURSAL UPDATE DEVOLUCION', 'UPDATE', '".$fechabitacora."')";
                                $resultado4 = mysqli_query($conn, $sql4);

                                echo '<meta http-equiv=refresh content="0">';
                            }
                        }
                    print "</table>";
                echo "</form>";
            ?>
        </div>
	<script type="text/javascript" src="js/jquery-3.6.1.min.js"></script> 
        <script type="text/javascript" src="js/jquery-ui.js"></script>
        <script type="text/javascript" src="js/misfunciones.js"></script>
        <script type="text/javascript" src="js/versection.js"></script> 
        <script type="text/javascript" src="js/funcionlista.js"></script>
    </body>
</html>

