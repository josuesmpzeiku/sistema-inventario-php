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
        <div class="color-arriba"><h2 class ="subtitulo">PAGO DE PACIENTES</h2></div><br> 
        <?php
            $sql1 ="SELECT venta.idventa, paciente.nompaciente, venta.totalventa, venta.fecventa
                    FROM venta INNER JOIN atencion ON atencion.idatencion = venta.idventa
                    INNER JOIN paciente ON paciente.idpaciente = atencion.idpaciente
                    WHERE atencion.idtienda = ".$tienda." AND atencion.estatencion != 'FINALIZADO'";
            $resultado1 = mysqli_query($conn, $sql1);
            while ($row1 = mysqli_fetch_array($resultado1)){                     
                print "<a class='tm-btn-plus' href='opagos.php?perfil=".$ussera."&atencion={$row1['idventa']}'>{$row1['idventa']},   {$row1['nompaciente']},   Q {$row1['totalventa']},    {$row1['fecventa']}</a><br>";                       
            }
        ?>
        <br><br>
        <div class="fila">
            <div class="columna">
                <h3>GASTOS DEL DIA DE HOY</h3>
                <form name="form" method="post" action="">
                    <input name='tdescg' type='text' class='decorar-input' title='Describa el tipo de gasto' placeholder = 'Descripción del Gasto' required>
                    <input name='tperg' type='text' class='decorar-input' title='Nombre de la Persona a quien le dio el efectivo' placeholder = 'Nombre de quién recibe' required>
                    <input name='tpagog' type='text' class='decorar-input' title='Cantidad del efectivo entregado Gasto' placeholder = 'Monto del Gasto' required>
                    <div id='botones'>
                        <button type='submit' name='insertg' class='btn-universal'><span class='icon icon-checkmark'></span></button> 
                    </div>
                 </form>
                <?php
                $descg = $_POST['tdescg'];
                $perg= $_POST['tperg'];
                $pagog = $_POST['tpagog'];
                date_default_timezone_set('America/Guatemala');                          
                $fechag = date('Y-m-d');
                
                if (isset ($_POST['insertg'])){  
                    $sql2 ="insert into gasto values ('NULL', '".$descg."', '".$fechag."', '".$pagog."',  '".$perg."', '".$tienda."')";
                    $resultado2 = mysqli_query($conn, $sql2);                    

                    $sql3 ="insert into bitacora values ('NULL', '".$ussera."', 'FORM GASTO ".$descg."', 'INSERT', '".$fechabitacora."')";
                    $resultado3 = mysqli_query($conn, $sql3);

                    echo '<div class="color-arriba"><h3>GASTO EFECTUADO CORRECTAMENTE</h3></div>';                 
                } 
                ?>
            </div>
            <div class="columna">
                <h3>REPORTE DE CAJA</h3>
                <form name="form2" method="post" action="">
                    <div id="botones">
                        <input class="decorar-input" name="tfechap" type="date" required id="tfechap" title="Elija una fecha para la busqueda">
                        <button type="submit" name="selectpa" class="btn-universal" id="selectpa"><span class="icon icon-search"></span></button> 
                    </div>
                </form>
                <?php
                $fechap = $_POST['tfechap'];
                if (isset ($_POST['selectpa'])){  
                    $sql10="SELECT venta.idventa, pago.tipopago, concat('Q ', pago.cantpago) FROM pago INNER JOIN venta
                            ON venta.idventa = pago.idventa INNER JOIN atencion on atencion.idatencion = venta.idventa 
                            WHERE fecpago = '".$fechap."' AND pago.idtienda = '".$tienda."'";
                    $resultado10 = mysqli_query($conn, $sql10);                
                    print "<table class='TablaH'>";                         
                    print "<tr>";
                    print "<td>CONTRASEÑA</td>";
                    print "<td>TIPO PAGO</td>";
                    print "<td>MONTO</td>";                  
                    print "</tr>";
                    while ($row10 = mysqli_fetch_assoc($resultado10)){
                        print "<tr>";
                            foreach ($row10 as $item10){
                                print "<td>".($item10!==NULL ?htmlentities($item10):"&nbsp;")."</td>";
                            }
                            print "</tr>";
                        }               
                    print "</table><br>"; 
                    $sql11="SELECT pago.tipopago, concat('Q ',SUM(pago.cantpago)) FROM pago  INNER JOIN venta
                            ON venta.idventa = pago.idventa INNER JOIN atencion on atencion.idatencion = venta.idventa 
                            WHERE fecpago = '".$fechap."' AND pago.idtienda = '".$tienda."' GROUP BY pago.tipopago";
                    $resultado11 = mysqli_query($conn, $sql11);                
                    print "<table class='TablaH'>";                         
                    print "<tr>";
                    print "<td>TIPO DE PAGO</td>";
                    print "<td>TOTAL</td>";                
                    print "</tr>";
                    while ($row11 = mysqli_fetch_assoc($resultado11)){
                        print "<tr>";
                            foreach ($row11 as $item11){
                                print "<td>".($item11!==NULL ?htmlentities($item11):"&nbsp;")."</td>";
                            }
                            print "</tr>";
                        }
                    $sql12="SELECT SUM(pago.cantpago) FROM pago INNER JOIN venta
                            ON venta.idventa = pago.idventa INNER JOIN atencion on atencion.idatencion = venta.idventa 
                            WHERE fecpago = '".$fechap."' AND pago.idtienda = '".$tienda."'";
                    $resultado12 = mysqli_query($conn, $sql12);
                    if ($row12 = mysqli_fetch_row($resultado12)){ }
                    print "<tr><td><b>TOTAL</b></td><td><b>Q $row12[0]</b></td></tr>";
                    print "</table>";
                    $sql13="SELECT descg, qrega, concat('Q ', montog) FROM gasto WHERE fecg = '".$fechap."' and idtienda = '".$tienda."'";
                    $resultado13 = mysqli_query($conn, $sql13);                
                    print "<table class='TablaH'>";                         
                    print "<tr>";
                    print "<td>TIPO DE GASTO</td>";
                    print "<td>RECIBE</td>";
                    print "<td>MONTO</td>";
                    print "</tr>";
                    while ($row13 = mysqli_fetch_assoc($resultado13)){
                        print "<tr>";
                            foreach ($row13 as $item13){
                                print "<td>".($item13!==NULL ?htmlentities($item13):"&nbsp;")."</td>";
                            }
                            print "</tr>";
                        }
                    $sql14="SELECT SUM(montog) FROM gasto WHERE fecg = '".$fechap."' and idtienda = '".$tienda."'";
                    $resultado14 = mysqli_query($conn, $sql14);
                    if ($row14 = mysqli_fetch_row($resultado14)){ }
                    print "<tr><td colspan = '2'><b>TOTAL</b></td><td><b>Q $row14[0]</b></td></tr>";
                    print "</table>";
                    $sql15="SELECT SUM(pago.cantpago) FROM pago INNER JOIN venta
                            ON venta.idventa = pago.idventa INNER JOIN atencion on atencion.idatencion = venta.idventa 
                            WHERE pago.fecpago = '".$fechap."' and tipopago = 'Efectivo' AND pago.idtienda = '".$tienda."'";
                    $resultado15 = mysqli_query($conn, $sql15);
                    if ($row15 = mysqli_fetch_row($resultado15)){ }
                    $toc = $row15[0] - $row14[0]; 
                    print "<table class='TablaH'>";                         
                    print "<tr><td>TOTAL DE EFECTIVO EN CAJA Q ".number_format($toc, 2, '.', ',')."</td></tr>";
                    
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
