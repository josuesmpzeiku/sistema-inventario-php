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
        <div class="color-arriba"><h2 class ="subtitulo">DEVOLUCIONES</h2></div> 
        <div class="fila">
            <?php
                echo "<form name='form1' method='post' action=''>";                
                    print "<table class = 'TablaH'>";
                        print "<tr><td>TIENDA</td><td>FECHA</td><td>U</td><td>CODIGO</td><td>TIPO</td><td>DESCRIPCIÓN</td><td>MOTIVO</td><td width = '5%'>✔</td><td width = '5%'>♻︎</td></tr>";
                        $sql8 ="SELECT devolucion.*, producto.idproducto, producto.tipoproducto, producto.descproducto, gestionm.fechagestion, tienda.nomtienda, detallegestionm.idubicacion
                                FROM devolucion INNER JOIN detallegestionm ON detallegestionm.iddgestionm = devolucion.iddgestionm
                                INNER JOIN ubicacion ON ubicacion.idubicacion = detallegestionm.idubicacion
                                INNER JOIN producto ON producto.idproducto = ubicacion.idproducto
                                INNER JOIN gestionm ON gestionm.idgestion = detallegestionm.idgestion
                                INNER JOIN tienda ON tienda.idtienda = gestionm.idtienda
                                WHERE devolucion.estadodev = 'ENVIADO'";
                        $resultado8 = mysqli_query($conn, $sql8);
                        while ($row8 = mysqli_fetch_array($resultado8)){ 
                            $fila1 = $row8['iddevo'];
                            print "<tr><td>{$row8['nomtienda']}</td>";
                            print "<td>{$row8['fechagestion']}</td>";
                            print "<td>{$row8['cantdevo']}</td>";
                             print "<td>{$row8['idproducto']}</td>";
                            print "<td>{$row8['tipoproducto']}</td>";
                            print "<td>{$row8['descproducto']}</td>";
                            print "<td>{$row8['comdevo']}</td>";
                            print "<td><button type='submit' name='updev".$fila1."' class='btn-iud'>✔</button></td>"; 
                            print "<td><button type='submit' name='badev".$fila1."' class='btn-iud'>♻</button></td></tr>"; 
                            print "<input name='tidev".$fila1."' type='hidden' value='".$fila1."'>";                            
                            print "<input name='tcd".$fila1."' type='hidden' value='{$row8['cantdevo']}'>";
                            print "<input name='tubi".$fila1."' type='hidden' value='{$row8['idubicacion']}'>";                           
                            $tidev = $_POST['tidev'.$fila1];
                            $tcd = $_POST['tcd'.$fila1];
                            $tubi = $_POST['tubi'.$fila1];
                          
                            
                            if (isset ($_POST['updev'.$fila1])){ 
                                $sql5 ="UPDATE ubicacion SET cantubicacion = (cantubicacion + $tcd) WHERE idubicacion = '".$tubi."'";
                                $resultado5 = mysqli_query($conn, $sql5);
                                
                                $sql3 ="UPDATE devolucion SET estadodev = 'RECIBIDO' WHERE iddevo = '".$tidev."'";
                                $resultado3 = mysqli_query($conn, $sql3); 
                               
                                $sql4 ="insert into bitacora values ('NULL', '".$ussera."', 'FORM DEVOLUCION', 'UPDATE', '".$fechabitacora."')";
                                $resultado4 = mysqli_query($conn, $sql4);

                                echo '<meta http-equiv=refresh content="0">';
                            }
                            if (isset ($_POST['badev'.$fila1])){ 
                                $sql5 ="UPDATE ubicacion SET cantubicacion = (cantubicacion - $tcd) WHERE idubicacion = '".$tubi."'";
                                $resultado5 = mysqli_query($conn, $sql5);
                                
                                $sql2 ="insert into bajas values ('NULL', '{$row8['idproducto']}', {$row8['cantdevo']}, '{$row8['comdevo']}', '".$fechab."')";
                                $resultado2 = mysqli_query($conn, $sql2);
                                
                                $sql3 ="UPDATE devolucion SET estadodev = 'BAJA' WHERE iddevo = '".$tidev."'";
                                $resultado3 = mysqli_query($conn, $sql3); 
                               
                                $sql4 ="insert into bitacora values ('NULL', '".$ussera."', 'FORM BAJA', 'UPDATE', '".$fechabitacora."')";
                                $resultado4 = mysqli_query($conn, $sql4);

                                echo '<meta http-equiv=refresh content="0">';
                            }
                           /* print "<script>function myFunction() { let text = '¿Esta seguro que quiere dar de baja?';
                                    if (confirm(text) == true) {";
                                    $sql2 ="insert into bajas values ('NULL', '{$row8['idproducto']}', {$row8['cantdevo']}, '{$row8['comdevo']}', '".$fechab."')";
                                    $resultado2 = mysqli_query($conn, $sql2); 
                                    
                                    $sql3 ="UPDATE devolucion SET estadodev = 'BAJA' WHERE iddevo = '".$tidev."'";
                                    $resultado3 = mysqli_query($conn, $sql3); 
                               
                                    $sql4 ="insert into bitacora values ('NULL', '".$ussera."', 'FORM DEVOLUCION BAJA', 'UPDATE', '".$fechabitacora."')";
                                    $resultado4 = mysqli_query($conn, $sql4);
                                    echo '<meta http-equiv=refresh content="0">';
                            print "} else { alert('Operacion no realizada');}}</script>";*/   
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