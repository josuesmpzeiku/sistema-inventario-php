<?php
    $http_referer = isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:null;
    $referer = $_SERVER['HTTP_REFERER'];
    if ($referer == "" ) {
        print "<script> alert('TIENES QUE INICIAR SESION'); location.href = 'index.php'; </script>";
    }
    error_reporting(0);
    session_start();
    include ("phpmysql.php"); 
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
        <div class="color-arriba"><h2 class ="subtitulo">REPORTE DE DEPÓSITOS</h2></div> 
        <div class="fila">
            <form name="form1" method="post" action=""> 
                <div id="botones">                   
                    <input class="decorar-input" name="tfecha1" type="date" required id="tfecha1" title="Fecha Inicial">   
                    <input class="decorar-input" name="tfecha2" type="date" required id="tfecha2" title="Fecha Final" >
                    <button type="submit" name="insertu" class="btn-universal" id="insertu"><span class="icon icon-search"></span></button>
                </div>
            </form>
        </div>
       <div class="fila">
            <?php            
            $fecha1 = $_POST['tfecha1'];               
            $fecha2 = $_POST['tfecha2'];   
                $sql1="SELECT tienda.nomtienda, deposito.bancodep, deposito.docdep, deposito.montodep, deposito.fechadep
                    FROM deposito INNER JOIN tienda ON tienda.idtienda = deposito.idtienda
                    WHERE date(deposito.fechadep) BETWEEN '".$fecha1."' AND '".$fecha2."' ORDER BY tienda.nomtienda ASC";
                $resultado1 = mysqli_query($conn, $sql1);
                
                $sql3="SELECT tienda.nomtienda, SUM(deposito.montodep)
                        FROM deposito INNER JOIN tienda ON tienda.idtienda = deposito.idtienda
                        WHERE date(deposito.fechadep) BETWEEN '".$fecha1."' AND '".$fecha2."'
                        GROUP BY tienda.nomtienda ";
                $resultado3 = mysqli_query($conn, $sql3);
                
                print "<div class= 'columna'>";
                print "<label><b>DEPOSITOS DEL $fecha1 AL $fecha2</b></label><br>"; 
                
                print "<table class='TablaJ'>";                         
                    print "<tr>";
                    print "<td>TIENDA</td>";
                    print "<td>TOTAL</td>";                   
                    print "</tr>";
                    while ($row3 = mysqli_fetch_assoc($resultado3)){
                        print "<tr>";
                            foreach ($row3 as $item3){
                                print "<td>".($item3!==NULL ?htmlentities($item3):"&nbsp;")."</td>";
                            }
                            print "</tr>";
                        }
                print "</table><br>";
                print "<table class='TablaJ'>";                         
                    print "<tr>";
                    print "<td>SUCURSAL</td>";
                    print "<td>BANCO</td>";
                    print "<td>REFERENCIA</td>";
                    print "<td>MONTO</td>";
                    print "<td>FECHA</td>";
                    print "</tr>";
                    while ($row1 = mysqli_fetch_assoc($resultado1)){
                        print "<tr>";
                            foreach ($row1 as $item1){
                                print "<td>".($item1!==NULL ?htmlentities($item1):"&nbsp;")."</td>";
                            }
                            print "</tr>";
                        }  
                $sql2="SELECT SUM(deposito.montodep) FROM deposito INNER JOIN tienda ON tienda.idtienda = deposito.idtienda
                        WHERE date(deposito.fechadep) BETWEEN '".$fecha1."' AND '".$fecha2."'";
                $resultado2 = mysqli_query($conn, $sql2);
                if ($row2 = mysqli_fetch_row($resultado2)){ }     
                print "<tr><td colspan = '3'><b>TOTAL</b></td><td><b>$row2[0]</b></td></tr>";
                print "</table>";                
               
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
