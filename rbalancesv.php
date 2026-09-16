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
        <div class="color-arriba"><h2 class ="subtitulo">REPORTE DE GASTOS Y VENTAS</h2></div>       
        <div class="fila">
            <form name="form1" method="post" action="">
                <table width = '100%'>
                    <tr>
                        <td><input class="decorar-input" name="ttienda" type="text" id="ttienda" placeholder="Escriba y Elija una Tienda" required><div id="ntienda" size="50"></div></td>
                        <td><input class="decorar-input" name="tfecha1" type="date" required id="tfecha1" title="Fecha Inicial"></td>    
                        <td><input class="decorar-input" name="tfecha2" type="date" required id="tfecha2" title="Fecha Final" ></td> 
                        <td><button type="submit" name="selectbv" class="btn-universal" id="selectbv"><span class="icon icon-search"></span></button></td>  
                    </tr>
                </table>  
            </form>
        </div>        
        <?php       
            $idt = $_POST['idti'];
            $fecha1 = $_POST['tfecha1'];               
            $fecha2 = $_POST['tfecha2'];  
        
            if (isset ($_POST['selectbv'])){ 
                $sql5 ="SELECT SUM(montog) FROM gasto WHERE fecg BETWEEN '".$fecha1."' AND '".$fecha2."' AND idtienda = '".$idt."'";
                $resultado5 = mysqli_query($conn, $sql5);
                if ($row5 = mysqli_fetch_row($resultado5)){ }
                $sql6 ="SELECT SUM(cantpago) FROM pago WHERE fecpago BETWEEN '".$fecha1."' AND '".$fecha2."' AND idtienda = '".$idt."'";
                $resultado6 = mysqli_query($conn, $sql6);
                if ($row6 = mysqli_fetch_row($resultado6)){ }                
                $diferencia = $row6[0] - $row5[0];
                print "<div class='fila'>";
                    print "<div class='columna'>";
                    print "<h3>TOTAL DEL $fecha1 AL $fecha2</h3>";
                        print "<table class='TablaJ'>";                         
                            print "<tr><td>VENTAS</td><td>PAGOS</td><td>DIFERENCIA</td></tr>";
                            Print "<tr><td>Q $row6[0]</td><td>Q $row5[0]</td><td>Q $diferencia</td></tr>";
                        print "</table><br>";
                        $sql9="SELECT tipopago, concat('Q ',SUM(cantpago)) FROM pago  
                                WHERE fecpago BETWEEN '".$fecha1."' AND '".$fecha2."' AND idtienda = '".$idt."' GROUP BY tipopago";
                        $resultado9 = mysqli_query($conn, $sql9);                
                        print "<table class='TablaJ'>";                         
                        print "<tr>";
                        print "<td>TIPO DE PAGO</td>";
                        print "<td>TOTAL</td>";                
                        print "</tr>";
                        while ($row9 = mysqli_fetch_assoc($resultado9)){
                        print "<tr>";
                            foreach ($row9 as $item9){
                                print "<td>".($item9!==NULL ?htmlentities($item9):"&nbsp;")."</td>";
                            }
                            print "</tr>";
                        }
                        print "</table>";
                    print "</div>"; 
                print "</div>";
                $sql ="SELECT ROW_NUMBER() OVER () AS numf, fecg, descg, qrega, CONCAT('Q ', montog) FROM `gasto` WHERE idtienda = '".$idt."' AND fecg BETWEEN '".$fecha1."' AND '".$fecha2."'";
                $resultado = mysqli_query($conn, $sql);               
                print "<div class='fila'>";                
                    print "<h3>GASTOS DEL $fecha1 AL $fecha2</h3><br>";
                print "</div>";
                print "<div class='fila'>";
                    print "<table class='TablaJ'>";                         
                    print "<tr>";
                    print "<td>No.</td>";
                    print "<td width = '10%'>FECHA</td>";
                    print "<td width = '45%'>DESCRIPCIÓN</td>";  
                    print "<td width = '35%'>QUIEN RECIBIÓ</td>"; 
                    print "<td>MONTO</td>"; 
                    print "</tr>";
                    while ($row = mysqli_fetch_assoc($resultado)){
                    print "<tr>";
                        foreach ($row as $item){
                            print "<td>".($item!==NULL ?htmlentities($item):"&nbsp;")."</td>";
                        }
                        print "</tr>";
                    }
                    print "<tr><td colspan='3'><b>TOTAL</b><td><b>Q $row5[0]</b></td><tr>";
                    print "</table>";                     
                print "</div>";
                $sql1 ="SELECT ROW_NUMBER() OVER () AS numf, fecpago, idventa, CONCAT('Q ',cantpago) FROM pago WHERE tipopago = 'Efectivo' and idtienda = '".$idt."'  AND fecpago BETWEEN  '".$fecha1."' AND '".$fecha2."'";
                $resultado1 = mysqli_query($conn, $sql1); 
                print "<div class='fila'>";  
                    print "<div class='columna'>";
                    print "<h3>VENTAS EN EFECTIVO DEL $fecha1 AL $fecha2</h3><br>";
                        print "<table class='TablaJ'>";                         
                        print "<tr>";
                        print "<td>No.</td>";
                        print "<td width = '33%'>FECHA</td>";
                        print "<td width = '33%'>CONTRASEÑA</td>";  
                        print "<td>MONTO</td>";                        
                        print "</tr>";
                        while ($row1 = mysqli_fetch_assoc($resultado1)){
                        print "<tr>";
                            foreach ($row1 as $item1){
                                print "<td>".($item1!==NULL ?htmlentities($item1):"&nbsp;")."</td>";
                            }
                            print "</tr>";
                        }                       
                        print "</table>";  
                    print "</div>";
                print "</div>";               
            }   
        ?>  
        </div>
	<script type="text/javascript" src="js/jquery-3.6.1.min.js"></script> 
        <script type="text/javascript" src="js/jquery-ui.js"></script>
        <script type="text/javascript" src="js/misfunciones.js"></script>
        <script type="text/javascript" src="js/versection.js"></script> 
        <script type="text/javascript" src="js/funcionlista.js"></script> 
    </body>
</html>