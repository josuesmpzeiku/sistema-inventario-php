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
        <div class="color-arriba"><h2 class ="subtitulo">REPORTE DE PRODUCTO INGRESADO POR FECHA</h2></div> 
        <div class="fila">
            <div class="columna">
                <form name="form1" method="post" action=""> 
                    <input class="decorar-input" name="tfecha1" type="date" required id="tfecha1" title="Fecha para la búsqueda">                    
                    <div id="botones"> 
                        <button type="submit" name="insertu" class="btn-universal" id="insertu"><span class="icon icon-search"></span></button>
                    </div>
                </form>
            </div>
        </div>
        <div class="fila">
            <?php            
            $fecha1 = $_POST['tfecha1'];               
            if (isset ($_POST['insertu'])){                 
                $sql1="SELECT lente.lentedes, inlab.cantinlab FROM inlab INNER JOIN lente ON lente.idlente = inlab.idlente WHERE inlab.fecinlab = '".$fecha1."'";
                $resultado1 = mysqli_query($conn, $sql1); 
                print "<div class'columna'>";
                print "<h3>PRODUCTOS INGREDADOS EN LA FECHA  $fecha1</h3><br>";
                print "<table class='TablaJ'>";                         
                    print "<tr>";
                    print "<td>PRODUCTO</td>";
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
