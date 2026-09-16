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
        <div class="color-arriba"><h2 class ="subtitulo">REPORTE POR SUCURSAL</h2></div> 
        <div class="fila">
            <form name="form1" method="post" action=""> 
                <div id="botones">
                    <input class="decorar-input" name="ttienda" type="text" id="ttienda" placeholder="Escriba y Elija una Tienda" required><div id="ntienda"></div>   
                    <input class="decorar-input" name="tfecha1" type="date" required id="tfecha1" title="Fecha Inicial">   
                    <input class="decorar-input" name="tfecha2" type="date" required id="tfecha2" title="Fecha Final" >
                    <button type="submit" name="insertu" class="btn-universal" id="insertu"><span class="icon icon-search"></span></button>
                </div>
            </form>
        </div>
       <div class="fila">
            <?php
            $idtus = $_POST['idti'];
            $fecha1 = $_POST['tfecha1'];               
            $fecha2 = $_POST['tfecha2'];   
               $sql1="SELECT idarm, fecharm, tipoarm, prearm1, tipolarm, talladoarm, materialarm, gradarm, addarm, prearm2 FROM armado INNER JOIN atencion ON atencion.idatencion = armado.idarm
                      WHERE date(armado.fecharm) BETWEEN '".$fecha1."' AND '".$fecha2."' and atencion.idtienda = '".$idtus."'";
                $resultado1 = mysqli_query($conn, $sql1);                
                print "<table class='TablaI'>";                         
                    print "<tr>";
                    print "<td>CONTRASEÑA</td>";
                    print "<td>FECHA</td>";
                    print "<td>T ARO</td>";
                    print "<td>COSTO 1</td>";
                    print "<td>T LENTE</td>";
                    print "<td>T DIGITAL</td>";
                    print "<td>MATERIAL</td>";
                    print "<td>GRAD</td>";
                    print "<td>ADD</td>";
                    print "<td>COSTOS</td>";
                    print "</tr>";
                    while ($row1 = mysqli_fetch_assoc($resultado1)){
                        print "<tr>";
                            foreach ($row1 as $item1){
                                print "<td>".($item1!==NULL ?htmlentities($item1):"&nbsp;")."</td>";
                            }
                            print "</tr>";
                        }
                $sql2="SELECT COUNT(*), SUM(prearm1), SUM(prearm2) FROM armado INNER JOIN atencion ON atencion.idatencion = armado.idarm
                       WHERE date(armado.fecharm) BETWEEN '".$fecha1."' AND '".$fecha2."' and atencion.idtienda = '".$idtus."'";
                $resultado2 = mysqli_query($conn, $sql2);
                if ($row2 = mysqli_fetch_row($resultado2)){ }
                print "<tr><td colspan=2><b>TOTAL:</b>   $row2[0]</td>";
                print "<td colspan=2><b>TOTAL COSTO 1:</b>   Q $row2[1]</td>";
                print "<td colspan=3></td>";
                print "<td colspan=3><b>TOTAL OTROS:</b>   Q $row2[2]</td>";
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