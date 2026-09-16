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
        <div class="color-arriba"><h2 class ="subtitulo">INVENTARIO DE LABORATORIO</h2></div>
        <div class="fila">
            <div class="columna">
            <?php 
                $sql1="SELECT lentedes, canlente FROM lente  ORDER BY lente.lentedes ASC";
                $resultado1 = mysqli_query($conn, $sql1);                
                print "<table class='TablaJ'>";                         
                    print "<tr>";
                    print "<td>DESCRIPCIÓN</td>";
                    print "<td>CANTIDAD</td>";                   
                    print "</tr>";
                    while ($row1 = mysqli_fetch_assoc($resultado1)){
                        print "<tr>";
                            foreach ($row1 as $item1){
                                print "<td>".($item1!==NULL ?htmlentities($item1):"&nbsp;")."</td>";
                            }
                            print "</tr>";
                        }
                print "</table>";            
            ?>
            </div>
            <div class="columna">
            <?php 
                 $sql3="SELECT producto.descproducto, ubicacion.cantubicacion
                  FROM producto INNER JOIN ubicacion on ubicacion.idproducto=producto.idproducto
                  WHERE ubicacion.idtienda = 11 AND producto.tipoproducto = 'ACCESORIO' 
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
            print "</table><br>";    
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


