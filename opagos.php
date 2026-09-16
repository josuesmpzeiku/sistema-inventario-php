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
    
    $sql1 ="SELECT venta.idventa, paciente.nompaciente, venta.totalventa, venta.fecventa
            FROM venta INNER JOIN atencion ON atencion.idatencion = venta.idventa
            INNER JOIN paciente ON paciente.idpaciente = atencion.idpaciente
            WHERE venta.idventa = '".$idaten."'";
    $resultado1 = mysqli_query($conn, $sql1);
    if ($row1 = mysqli_fetch_row($resultado1)){ }
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
        <div class="color-arriba"><h2 class ="subtitulo">PAGO DE <?php echo $row1[1];?> </h2></div> 
        <div class='fila'>
        <?php         
            print "<div class='columna'>";
                print "<table class = 'TablaH'>";
                    print "<tr><td>Cant</td><td>PRODUCTO</td><td>Desc</td><td> SUBTOTAL </td></tr>";
                    $sql4 ="SELECT detalleventa.*, producto.*
                    FROM detalleventa INNER JOIN ubicacion ON ubicacion.idubicacion = detalleventa.idubicacion
                    INNER JOIN producto ON producto.idproducto = ubicacion.idproducto
                    WHERE detalleventa.idventa = '".$idaten."'";
                    $resultado4 = mysqli_query($conn, $sql4);
                    while ($row4 = mysqli_fetch_array($resultado4)){                                
                        print "<tr><td>{$row4['cantdventa']}</td><td>{$row4['descproducto']}</td><td>{$row4['descdventa']}%</td><td>Q {$row4['subtdventa']}</td></tr>";
                    }
                    print "<tr><td colspan = '3'>TOTAL</td><td>Q {$row1['2']}</td></tr>";
                print "</table><br>";
                        
                $sql5 ="SELECT SUM(pago.cantpago), venta.totalventa, (venta.totalventa - SUM(pago.cantpago)) as pendiente 
                FROM venta INNER JOIN pago ON pago.idventa = venta.idventa
                WHERE venta.idventa = '".$idaten."'";
                $resultado5 = mysqli_query($conn, $sql5);
                if ($row5 = mysqli_fetch_row($resultado5)){ } 

                $sql6 ="SELECT * FROM entrega WHERE identrega = '".$idaten."'";
                $resultado6 = mysqli_query($conn, $sql6);
                if ($row6 = mysqli_fetch_row($resultado6)){ }
                setlocale(LC_TIME, "spanish.utf8");               
                $fechacom = strftime("%A, %d de %B de %Y  a partir de las %H:%M horas ", strtotime($row6[2]));                 
                        
                print "<h4>TOTAL: Q $row5[1]   ANTICIPO: Q $row5[0]   PENDIENTE: Q $row5[2]</h4>";
                print "<label> ENTREGA:   $fechacom</label>";
                print "<div id='botones'>";
                print "<a class='btn-universal' href='deacliente.php?perfil=$ussera&atencion={$row1[0]}' target='_blank'><span class='icon icon-file-text'></span></a>";
                print "<a class='btn-universal' href='printpass.php?perfil=$ussera&atencion={$row1[0]}' target='_blank'><span class='icon icon-key2'></span></a>";               
                print "<a class='btn-universal' href='printreceta.php?perfil=$ussera&atencion={$row1[0]}' target='_blank'><span class='icon icon-plus'></span></a>";
                print "<a class='btn-universal' href='printfin.php?perfil=$ussera&atencion={$row1[0]}' target='_blank'><span class='icon icon-printer'></span></a>";
                print "</div>";                        
            print "</div>"
            ?>    
            <div class='columna'>
                <h3>Efectuar Pago</h3>
                <form name='form' method='post' action=''>
                    <select name='ttpa' class='decorar-input' required>
                        <option value='' disabled selected>Elija un Tipo de Pago</option>
                        <option>Efectivo</option>
                        <option>Visacuotas</option>
                        <option>Tarjeta Credito</option>
                        <option>Tarjeta Debito</option> 
                        <option>Deposito</option>  
                        <option>Transferencia</option>                        
                    </select>
                    <input name='tcantp' type='number'  step="0.01" class='decorar-input' title='Cantidad en Numeros' placeholder = 'Cantidad del Pago a Realizar' required>
                    <input name='tdoc' type='text' class='decorar-input' title='Numero de Boleta, Cheque, Transaccion' placeholder = 'Numero de Documento' >
                    <input name='tfecp' type='date' class='decorar-input' required >
                    <div id='botones'>
                        <button type='submit' name='insp' class='btn-universal'><span class='icon icon-coin-euro'></span></button> 
                        <button type='submit' name='updat' class='btn-universal' formnovalidate><span class='icon icon-cloud-check'></span></button>
                    </div>                   
                </form> 
                <br><h3>Cambiar Estación del Paciente</h3>
                <form name='form2' method='post' action=''> 
                    <select name='testadoa' class='decorar-input' required=''>
                        <option value='' disabled selected>Elija una Estacion</option>                        
                        <option value='RECEPCION'>ASESOR</option>
                        <option value='ASESOR'>ESPECIALISTA</option> 
                        <option value='LABORATORIO'>LABORATORIO</option>
                    </select>
                    <div id='botones'>
                        <button type='submit' name='upda' class='btn-universal' formnovalidate ><span class='icon icon-pencil'></span></button> 
                    </div>
                </form>
            <?php     
            $doc= $_POST['tdoc'];
            $tcantp = $_POST['tcantp'];
            $ttpa = $_POST['ttpa'];
            $tfecp = $_POST['tfecp'];
            $esta= $_POST['testadoa'];

            if (isset ($_POST['insp'])){ 
                if ($ussera == NULL or $ussera == 0){
                         print "<script> alert('TIENES QUE INICIAR SESION'); location.href = 'index.php'; </script>";
                    }
                else {
                    $sql ="SELECT venta.totalventa FROM venta WHERE venta.idventa = '".$idaten."'";
                    $resultado = mysqli_query($conn, $sql);
                    if ($row = mysqli_fetch_row($resultado)){ }
                    if ($row5[0] == NULL ){
                        $anticipo = 0; 
                        $pendiente = $row[0];
                    }
                    else {
                        $anticipo = $row5[0]; 
                        $pendiente = $row5[2];
                    }
                     if ($anticipo != $row[0]){

                        if($tcantp <= $pendiente){
                           $sql2 ="insert into pago values ('NULL', '".$idaten."', '".$ttpa."', '".$tcantp."',  '".$tfecp."', '".$doc."', '".$tienda."')";
                            $resultado2 = mysqli_query($conn, $sql2);                    

                            $sql3 ="insert into bitacora values ('NULL', '".$ussera."', 'GERENTE INGRESO PAGO DE Q".$tcantp.".00  A CONTRASEÑA ".$idaten."', 'INSERT', '".$fechabitacora."')";
                            $resultado3 = mysqli_query($conn, $sql3);

                            echo '<div class="color-arriba"><h3>PAGO AL ID VENTA -'.$idaten.'- REALIZADO CORRECTAMENTE</h3></div>'; 
                            echo '<meta http-equiv=refresh content="1">';
                        }
                        else{
                            print "<script> alert('EL PAGO EXCEDE EL TOTAL PENDIENTE DE LA VENTA'); </script>";
                        }
                    }
                    else{
                            print "<script> alert('EL TOTAL DE LA VENTA YA FUE CANCELADO'); </script>";
                    }
                }

            }
            if (isset ($_POST['updat'])){ 
                if ($ussera == NULL or $ussera == 0){
                         print "<script> alert('TIENES QUE INICIAR SESION'); location.href = 'index.php'; </script>";
                }
                else {
                $sql9 ="SELECT venta.totalventa, SUM(pago.cantpago) FROM venta INNER JOIN pago ON pago.idventa = venta.idventa WHERE venta.idventa = '".$idaten."'";
                $resultado9 = mysqli_query($conn, $sql9);
                if($row9 = mysqli_fetch_array($resultado9)){ $tpa = $row9["SUM(pago.cantpago)"]; $tve =$row9["totalventa"];} 

                    if($tpa != $tve or $tpa == NULL){                        
                      print "<script> alert('NO PUEDE FINALIZAR EL PROCESO HASTA QUE EL COSTO TOTAL SEA CANCELADO');</script>";  
                    }
                    else {
                        $sql7 ="update atencion set estatencion = 'FINALIZADO' where idatencion = '".$idaten."'";
                        $resultado7 = mysqli_query($conn, $sql7);                    

                        $sql8 ="insert into bitacora values ('NULL', '".$ussera."', 'GERENTE FINALIZÓ CONTRASEÑA ".$idaten." EN PAGOS', 'UPDATE', '".$fechabitacora."')";
                        $resultado8 = mysqli_query($conn, $sql8);
                        print "<script> alert('EL PROCESO CON LA CONTRASEÑA $idaten FUE FINALIZADO');</script>";
                    }
                }
            }
            if (isset ($_POST['upda'])){ 
                if ($ussera == NULL or $ussera == 0){
                         print "<script> alert('TIENES QUE INICIAR SESION'); location.href = 'index.php'; </script>";
                }
                else {
                    $sql5 ="update atencion SET estatencion = '".$esta."' where idatencion = '".$idaten."' ";
                    $resultado5 = mysqli_query($conn, $sql5); 

                    $sql4 ="insert into bitacora values ('NULL', '".$ussera."', 'GERENTE CAMBIO ESTACIÓN CONTRASEÑA ".$idaten." EN PAGOS', 'UPDATE', CURRENT_TIMESTAMP)";
                    $resultado4 = mysqli_query($conn, $sql4);
                    echo '<div class="color-arriba"><h3>LA ESTACION FUE MODIFICADA</h3></div>';     
                }
            }            
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
