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
        <div class="color-arriba"><h2 class ="subtitulo">REPORTE LIBRO DIARIO</h2></div>       
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
                                WHERE fecpago BETWEEN '".$fecha1."' AND '".$fecha2."' AND idtienda = $idt GROUP BY tipopago";
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
                $sql11 ="SELECT * FROM pago WHERE fecpago BETWEEN '".$fecha1."' AND '".$fecha2."' AND idtienda = '".$idt."' GROUP BY fecpago";
                $resultado11 = mysqli_query($conn, $sql11);
                while ($row11 = mysqli_fetch_array($resultado11)){  
                print "<button class='btn-listado'>{$row11['fecpago']}</button>";                                 
                print "<div class='contenedor-lista'>";
                    print "<div class='fila'>";                      
                        print "<div>";                        
                        $sql2="SELECT idventa, tipopago, cantpago FROM pago WHERE fecpago = '{$row11['fecpago']}' AND idtienda = '".$idt."' order by idventa asc";
                        $resultado2 = mysqli_query($conn, $sql2);
                        $sql7="SELECT SUM(cantpago) FROM pago WHERE fecpago = '{$row11['fecpago']}' AND idtienda = '".$idt."' ";
                        $resultado7 = mysqli_query($conn, $sql7);
                        if ($row7 = mysqli_fetch_row($resultado7)){ }
                        print "<h3>VENTAS</h3>"; 
                        print "<table class='TablaJ'>";                         
                            print "<tr>";                    
                            print "<td>CONTRASEÑA</td>";                    
                            print "<td>TIPO</td>";
                            print "<td>CANTIDAD</td>";
                            print "</tr>";
                            while ($row2 = mysqli_fetch_assoc($resultado2)){
                                print "<tr>";
                                    foreach ($row2 as $item2){
                                        print "<td>".($item2!==NULL ?htmlentities($item2):"&nbsp;")."</td>";
                                    }
                                    print "</tr>";
                                }
                            print "<tr><td colspan= '2'><b>TOTAL VENTAS</b></td><td><b>$row7[0]</b></td></tr>";
                        print "</table> <br>";
                        $sql8="SELECT tipopago, concat('Q ',SUM(cantpago)) FROM pago  
                                WHERE fecpago = '{$row11['fecpago']}' AND idtienda = $idt GROUP BY tipopago";
                        $resultado8 = mysqli_query($conn, $sql8);                
                        print "<table class='TablaJ'>";                         
                        print "<tr>";
                        print "<td>TIPO DE PAGO</td>";
                        print "<td>TOTAL</td>";                
                        print "</tr>";
                        while ($row8 = mysqli_fetch_assoc($resultado8)){
                        print "<tr>";
                            foreach ($row8 as $item8){
                                print "<td>".($item8!==NULL ?htmlentities($item8):"&nbsp;")."</td>";
                            }
                            print "</tr>";
                        }
                        print "</table>";
                        print "</div>"; 
                        print "<div>"; 
                        $sql3="SELECT qrega, descg,montog FROM gasto WHERE fecg = '{$row11['fecpago']}' and idtienda = '".$idt."'";
                        $resultado3 = mysqli_query($conn, $sql3); 
                        $sql4 ="SELECT SUM(montog) AS TG FROM gasto WHERE fecg = '{$row11['fecpago']}' and idtienda = '".$idt."'";
                        $resultado4 = mysqli_query($conn, $sql4);
                        if ($row4 = mysqli_fetch_row($resultado4)){ }
                        print "<h3>GASTOS</h3>"; 
                        print "<table class='TablaJ'>";                         
                            print "<tr>";                    
                            print "<td>RECIBIÓ</td>";                    
                            print "<td>DESCRIPCIÓN</td>";
                            print "<td>SUBTOTAL</td>";                            
                            print "</tr>";
                            while ($row3 = mysqli_fetch_assoc($resultado3)){
                                print "<tr>";
                                    foreach ($row3 as $item3){
                                        print "<td>".($item3!==NULL ?htmlentities($item3):"&nbsp;")."</td>";
                                    }
                                    print "</tr>";
                                }
                            print "<tr><td colspan= '2'><b>TOTAL GASTOS</b></td><td><b>$row4[0]</b></td></tr>";
                        print "</table>";                                               
                        print "</div>"; 
                    print "</div>";                   
                    $difdia = $row7[0]-$row4[0];
                    print "<h3>Total Ventas: Q $row7[0]    Total Gastos: Q $row4[0]   Diferencia: Q $difdia</h3>";
                print "</div><br>";
                }
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