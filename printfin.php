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
    $idaten = $_GET['atencion'];
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
        <link rel="stylesheet" href="css/print.css">   
        <title>Contraseña</title>
    </head>
    <body>
        <div class="fila">
        <?php
            $sql30="SELECT * FROM usuario INNER JOIN bitacora on bitacora.idusuario = usuario.idusuario WHERE bitacora.formbitacora = 'NUEVA CONTRASEÑA Y VENTA ID ".$idaten."'";
            $resultado30 = mysqli_query($conn, $sql30);
            if($row30 = mysqli_fetch_array($resultado30)){ } 
            
            $sql15="SELECT * from tienda WHERE idtienda = '".$tienda."'";
            $resultado15 = mysqli_query($conn, $sql15);
            if($row15 = mysqli_fetch_array($resultado15)){ } 
            
            $sql3="SELECT paciente.*, atencion.fecatencion FROM paciente INNER JOIN atencion
                    ON atencion.idpaciente = paciente.idpaciente WHERE idatencion = '".$idaten."'";
            $resultado3 = mysqli_query($conn, $sql3);            
            if($row3 = mysqli_fetch_array($resultado3)){ $nompa = $row3["nompaciente"]; $telp = $row3["telpaciente"]; $feat = $row3["fecatencion"];}
         
        print"<div class='contenedor'>";
            print"<div class='tabla2'>";            
                print "<table class = 'TablaM2'>";
                print "<tr><td rowspan='3'><img src='".$url."img/logo.png' width='75px' height='75px'></td><td><button class='btn-universal' type='button' onclick='cerrarVentana()'>OPTICA MACARIO</button></td><td rowspan='3'><img src='".$url."img/logo.png' width='75px' height='75px'></td></tr>";
                print "<tr><td align='center'>$row15[1]</td></tr>";
                print "<tr><td align='center'>$row15[2]  Cel $row15[3]</td></tr>";
                print "<tr><td colspan='3'><b>CONTRASEÑA:  </b>$idaten<b>                FECHA: </b>$feat</td></tr>";               
                print "<tr><td colspan='3'>";
                    print "<table class='TablaP'>";
                        print "<tr><td><b>NOMBRE:</b>  $nompa</td></tr>";
                        $sql2="SELECT producto.descproducto FROM producto INNER JOIN ubicacion ON ubicacion.idproducto = producto.idproducto
                                INNER JOIN detalleventa ON detalleventa.idubicacion = ubicacion.idubicacion
                                WHERE detalleventa.idventa = $idaten AND producto.tipoproducto = 'LENTE';";
                        $resultado2 = mysqli_query($conn, $sql2);
                            if ($row2 = mysqli_fetch_row($resultado2)){ }
                        print "<tr><td><b>MATERIAL:</b>  $row2[0]</td></tr>";
                        $sql4="SELECT producto.descproducto FROM producto INNER JOIN ubicacion ON ubicacion.idproducto = producto.idproducto
                                INNER JOIN detalleventa ON detalleventa.idubicacion = ubicacion.idubicacion
                                WHERE detalleventa.idventa = $idaten AND producto.tipoproducto = 'ARO';";
                        $resultado4 = mysqli_query($conn, $sql4);
                            if ($row4 = mysqli_fetch_row($resultado4)){ }
                        print "<tr><td><b>ARO:</b>  $row4[0]</td></tr>";
                        $sql7="SELECT producto.descproducto FROM producto INNER JOIN ubicacion ON ubicacion.idproducto = producto.idproducto
                                INNER JOIN detalleventa ON detalleventa.idubicacion = ubicacion.idubicacion WHERE detalleventa.idventa = $idaten and (producto.tipoproducto ='SERVICIO' OR producto.tipoproducto ='REPARACION')";
                        $resultado7 = mysqli_query($conn, $sql7);
                        print "<tr><td><b>SERVICIOS:</b>  ";
                                while ($row7 = mysqli_fetch_array($resultado7)){ 
                                    print "{$row7['descproducto']}    ";
                                    } 
                        print "</td></tr>";
                        $sql14="SELECT producto.descproducto FROM producto INNER JOIN ubicacion ON ubicacion.idproducto = producto.idproducto
                                INNER JOIN detalleventa ON detalleventa.idubicacion = ubicacion.idubicacion WHERE detalleventa.idventa = $idaten and producto.tipoproducto ='ACCESORIO';";
                        $resultado14 = mysqli_query($conn, $sql14);
                        print "<tr><td><b>ACCESORIOS:</b>  ";
                                while ($row14 = mysqli_fetch_array($resultado14)){ 
                                    print "{$row14['descproducto']}    ";
                                    } 
                        print "</td></tr>";
                        $sql8="SELECT producto.descproducto FROM producto INNER JOIN detallereceta ON detallereceta.idproducto = producto.idproducto WHERE detallereceta.idreceta = '".$idaten."' ";
                        $resultado8 = mysqli_query($conn, $sql8);
                        print "<tr><td><b>MEDICAMENTOS:</b>  ";
                                while ($row8 = mysqli_fetch_array($resultado8)){ 
                                    print "{$row8['descproducto']}    ";
                                    } 
                        print "</td></tr>";
                        $sql5 ="SELECT SUM(pago.cantpago), venta.totalventa, (venta.totalventa - SUM(pago.cantpago)) as pendiente, venta.obsventa  
                        FROM venta INNER JOIN pago ON pago.idventa = venta.idventa
                        WHERE venta.idventa = $idaten";
                        $resultado5 = mysqli_query($conn, $sql5);
                            if ($row5 = mysqli_fetch_row($resultado5)){ }
                        print "<tr><td><b>ANTICIPO:</b>  Q $row5[0]        |  ";
                        print "<b>SALDO:</b>  Q $row5[2]        |  ";
                        print "<b>TOTAL:</b>  Q $row5[1]</td></tr>";
                        $sql6 ="SELECT * FROM entrega WHERE identrega = $idaten";
                        $resultado6 = mysqli_query($conn, $sql6);
                            if ($row6 = mysqli_fetch_row($resultado6)){ }
                            setlocale(LC_TIME, "spanish.utf8");                           
                            $fechacom = strftime("%A, %d de %B de %Y  a partir de las %H:%M horas ", strtotime($row6[2]));
                        print "<tr><td><b>FECHA DE ENTREGA:</b>  $fechacom</td></tr>";
                    print "</table>";                
                print "</td></tr>";
                print "<tr><td colspan='3' align='center'><b>PRESTIGIO - CALIDAD - DISTINCIÓN</b></td></tr>";
                print "<tr><td colspan='3' align='center'><b>No hay devoluciones de Anticipo</b></td></tr>"; 
                print "<tr><td colspan='3' align='center'><b>Tiempo maximo para recoger su producto 30 dias</b></td></tr>";
                print "<tr><td colspan='3' align='center'><br></td></tr>";
                $sql13="SELECT nomtienda, teltienda FROM tienda where idtienda !=10 ";
                $resultado13 = mysqli_query($conn, $sql13);
                    print "<tr><td colspan='3' align='center'><font size=1.5>";
                        while ($row13 = mysqli_fetch_array($resultado13)){ 
                            print "<b>{$row13['nomtienda']}</b>  Cel: {$row13['teltienda']}   ";
                        } 
                    print "</font></td></tr>";
                print "<tr><td colspan='3' align='right'><label class='numf'>$row30[2]</label></td></tr>";
                print "<tr><td colspan='3' align='center'><br></td></tr>";
                print "<tr><td colspan='3'>";                
                    $sql9="SELECT ojograd, esferagrad, cilindrograd, ejegrad, dipgrad, addgrad FROM graduacion WHERE idatencion = '".$idaten."' AND usuariograd = 'ESPECIALISTA'";
                    $resultado9 = mysqli_query($conn, $sql9);
                    print "<label><b>CONTRASEÑA:  </b>".$idaten."   <b>TEL:</b>  $telp</label><br>"; 
                    print "<label><b>PACIENTE:  </b>$nompa</label><br>";
                    print "<table class='TablaB'>";                         
                        print "<tr>";
                        print "<td>OJO</td>";
                        print "<td>ESF</td>";
                        print "<td>CIL</td>"; 
                        print "<td>EJE</td>";
                        print "<td>DIP</td>";
                        print "<td>ADD</td>";
                        print "</tr>";
                            while ($row9 = mysqli_fetch_assoc($resultado9)){
                                print "<tr>";
                                foreach ($row9 as $item9){
                                    print "<td>".($item9!==NULL ?htmlentities($item9):"&nbsp;")."</td>";
                                }
                                print "</tr>";
                            }                  
                        print "</table><table class='TablaP'>";
                            $sql10="SELECT producto.descproducto FROM producto INNER JOIN ubicacion ON ubicacion.idproducto = producto.idproducto
                                INNER JOIN detalleventa ON detalleventa.idubicacion = ubicacion.idubicacion
                                WHERE detalleventa.idventa = '".$idaten."' AND producto.tipoproducto = 'LENTE';";
                            $resultado10 = mysqli_query($conn, $sql10);
                            if ($row10 = mysqli_fetch_row($resultado10)){ }        
                                print "<tr><td> <b>LENTE:</b>";
                                print "  $row10[0]</td></tr>";
                            $sql11="SELECT producto.descproducto, producto.claseproducto FROM producto INNER JOIN ubicacion ON ubicacion.idproducto = producto.idproducto
                                INNER JOIN detalleventa ON detalleventa.idubicacion = ubicacion.idubicacion
                                WHERE detalleventa.idventa = '".$idaten."' AND producto.tipoproducto = 'ARO';";
                            $resultado11 = mysqli_query($conn, $sql11);
                            if ($row11 = mysqli_fetch_row($resultado11)){ }        
                            print "<tr><td> <b>ARO:</b>";
                             print "  $row11[0], $row11[1]</td></tr>";
                            $sql7="SELECT producto.descproducto FROM producto INNER JOIN ubicacion ON ubicacion.idproducto = producto.idproducto
                                INNER JOIN detalleventa ON detalleventa.idubicacion = ubicacion.idubicacion WHERE detalleventa.idventa = $idaten and (producto.tipoproducto ='SERVICIO' OR producto.tipoproducto ='REPARACION')";
                            $resultado7 = mysqli_query($conn, $sql7);
                            print "<tr><td><b>SERVICIOS:</b>  ";
                                while ($row7 = mysqli_fetch_array($resultado7)){ 
                                    print "{$row7['descproducto']}    ";
                                    } 
                            print "<tr><td><b>ACCESORIOS:</b>  ";
                                while ($row14 = mysqli_fetch_array($resultado14)){ 
                                    print "{$row14['descproducto']}    ";
                                    } 
                            print "</td></tr>";
                             $sql16="SELECT fecrent FROM `entrega` WHERE identrega = '".$idaten."'";
                            $resultado16 = mysqli_query($conn, $sql16);
                            if ($row16 = mysqli_fetch_row($resultado16)){ }        
                                print "<tr><td> <b>FECHA ESTIMADA DE ENTREGA:</b>";
                                print "  $row16[0]</td></tr>";
                                print "<tr><td> <b>OBSERVACIONES:</b>";
                                print "  $row5[3]</td></tr>";
                            print "<tr><td> <b>SUCURSAL:</b>  $row15[1]    <b>LUGAR DE ENTREGA:</b>  $row6[5]</td></tr>";
                            print "<tr><td> <b>EMPRESA DE PAQUETERIA:</b>  $row6[6]</td></tr>";
                        print "</table><table class='TablaB'>";
                            print "<tr>";
                                print "<td rowspan='2'>MEDIDA DEL ARO</td>";
                                print "<td>V</td>";
                                print "<td>H</td>"; 
                                print "<td>D</td>";
                                print "<td>P</td>";
                                print "<td>A</td>";
                            print "</tr>";
                            $sql12="SELECT vma, hma, dma, pma, abma FROM maro WHERE idmaro = '".$idaten."'";
                            $resultado12 = mysqli_query($conn, $sql12);
                            while ($row12 = mysqli_fetch_assoc($resultado12)){
                                print "<tr>";
                                foreach ($row12 as $item12){
                                    print "<td>".($item12!==NULL ?htmlentities($item12):"&nbsp;")."</td>";
                                }
                                print "</tr>";
                            }
                        print "</table>";
                        print "<tr><td colspan='3'>  <b>ASESOR DE VENTAS:  </b>$row30[1]</td></tr>";
                   print "</td></tr>";
                print "</table>";   
            print"</div>";
        print"</div>";       
        
            $sql7="SELECT * from tienda WHERE idtienda = '".$tienda."'";
            $resultado7 = mysqli_query($conn, $sql7);
            if($row7 = mysqli_fetch_array($resultado7)){ } 
            
            $sql6="SELECT paciente.nompaciente, paciente.idpaciente FROM paciente INNER JOIN atencion
                    ON atencion.idpaciente = paciente.idpaciente WHERE idatencion = '".$idaten."'";
            $resultado6 = mysqli_query($conn, $sql6);            
            if($row6 = mysqli_fetch_array($resultado6)){ $nompa = $row6["nompaciente"]; $idpac = $row6["idpaciente"];}
            
            $sql1="SELECT * from receta WHERE idreceta = '".$idaten."'";
            $resultado1 = mysqli_query($conn, $sql1);
            if($row1 = mysqli_fetch_array($resultado1)){ } 

            $sql2="SELECT * from cita WHERE idatencion = '".$idaten."'";
            $resultado2 = mysqli_query($conn, $sql2);
            if($row2 = mysqli_fetch_array($resultado2)){ }
            
            $sql4="SELECT * from cuestionario WHERE idatencion = '".$idaten."' and precuest = 'Patologia'";
            $resultado4 = mysqli_query($conn, $sql4);
            if($row4 = mysqli_fetch_array($resultado4)){ }
            
            print"<div class='contenedor'>";
            print"<div class='tabla2'>";            
                print "<table class = 'TablaM2'>";
                print "<tr><td rowspan='3'><img src='".$url."img/logo.png' width='75px' height='75px'></td><td><button class='btn-universal' type='button' onclick='cerrarVentana()'>OPTICA MACARIO</button></td><td rowspan='3'><img src='".$url."img/logo.png' width='75px' height='75px'></td></tr>";
                print "<tr><td align='center'>$row7[1]</td></tr>";
                print "<tr><td align='center'>$row7[2]  Cel $row7[3]</td></tr>";
                print "<tr><td colspan='3' align='right'><br><b>FECHA: </b>$row1[2]</td></tr>";
                print "<tr><td colspan='3'><b>NOMBRE: </b>$nompa</td></tr>";
                print "<tr><td colspan='3'>";
                $sql5="SELECT ojograd, esferagrad, cilindrograd, ejegrad, dipgrad, addgrad FROM graduacion WHERE idatencion = $idaten AND usuariograd = 'ESPECIALISTA'";
                $resultado5 = mysqli_query($conn, $sql5);
                    print "<table class='TablaB'>";                         
                        print "<tr>";
                        print "<td >OJO</td>";
                        print "<td width='15%'>ESF</td>";
                        print "<td width='15%'>CIL</td>"; 
                        print "<td width='15%'>EJE</td>";
                        print "<td width='15%'>DIP</td>";
                        print "<td width='15%'>ADD</td>";
                        print "</tr>";
                            while ($row5 = mysqli_fetch_assoc($resultado5)){
                                print "<tr>";
                                foreach ($row5 as $item5){
                                    print "<td>".($item5!==NULL ?htmlentities($item5):"&nbsp;")."</td>";
                                }
                                print "</tr>";
                            }
                    print "</table>";
                print "</td></tr>";
                print "<tr><td colspan='3'>";
                    print "<table class = 'TablaA'>";
                    print "<tr><td><b>  PATOLOGIA:</b> $row4[3] </td></tr>";
                    print "<tr><td>";
                        $sql3="SELECT detallereceta.cantreceta, producto.descproducto, detallereceta.dosisreceta 
                        FROM detallereceta INNER JOIN producto ON producto.idproducto = detallereceta.idproducto
                        WHERE detallereceta.idreceta =  '".$idaten."'";
                        $resultado3 = mysqli_query($conn, $sql3);
                        while ($row3 = mysqli_fetch_array($resultado3)){
                            print "   <b>{$row3['cantreceta']},  {$row3['descproducto']}</b><br>   {$row3['dosisreceta']}<br><br>";
                        }
                    print "</td></tr>";
                    print "<tr><td>  <b>PROXIMA CITA: </b>   $row2[3]</td></tr>";
                    print "</table>"; 
                print "</td></tr>";
                print "<tr><td colspan='3' align='center'><b>PRESTIGIO - CALIDAD - DISTINCION</b></td></tr>";
                print "<tr><td colspan='3' align='center'><b>¡TIENES MUCHO QUE VER!</b></td></tr>";
                print "<tr><td colspan='3' align='center'>Sugerencias o reclamos cel: 53174181 valido por ocho dias depués de la entrega</td></tr>";
                print "<tr><td> </td></tr>";
                $sql13="SELECT nomtienda, teltienda FROM tienda where idtienda != 10";
                $resultado13 = mysqli_query($conn, $sql13);
                    print "<tr><td colspan='3' align='center'><font size=1>";
                        while ($row13 = mysqli_fetch_array($resultado13)){ 
                            print "<b>{$row13['nomtienda']}</b>  Cel: {$row13['teltienda']}   ";
                        } 
                    print "</font></td></tr>";
                print "</table>";   
            print"</div>";
            print"</div>";
        ?> 
            </div>
        <script>
            function cerrarVentana() {
            // Intenta cerrar en navegadores estándar
                window.open('', '_self', ''); 
                window.close();
    
            // Si lo anterior falla (algunos navegadores móviles), intenta este:
                setTimeout(function() {
                window.history.back(); // Como respaldo, regresa a la página anterior
                }, 500);
            }
        </script>           
    </body>
</html>