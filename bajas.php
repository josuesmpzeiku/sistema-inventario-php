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
        <div class="color-arriba"><h2 class ="subtitulo">BAJAS</h2></div>       
        <div class="fila">
            <div class="columna">
                <h3>DAR DE BAJA</h3>
                <form name="form1" method="post" action=""> 
                    <input class="decorar-input" name="tbuscarp2" type="text" id="tbuscarp2" placeholder="Escriba y Elija un PRODUCTO" required><div id="bpro2"></div> 
                    <input class="decorar-input" name="tcanb" type="text" placeholder="Cantidad" id="tcanb" title="Unidades del Producto para BAJAS">
                    <input class="decorar-input" name="tdesb" type="text" placeholder="Descripción de la Baja" required   id="tdesb" title="Describa una razon para la BAJA" >
                    <div id="botones"><button type="submit" name="insertb" class="btn-universal" id="insertb"><span class="icon icon-bin"></span></button></div>
                </form>        
            </div>            
            <div class="columna">
                <h3>REPORTE DE BAJAS</h3>
		<form name="form2" method="post" action="">
                    <div id="botones">
                        <input class="decorar-input" name="tfecha1" type="date" required id="tfecha1" title="Fecha Inicial">   
                        <input class="decorar-input" name="tfecha2" type="date" required id="tfecha2" title="Fecha Final" >
                        <button type="submit" name="selectb" class="btn-universal" id="selectb"><span class="icon icon-search"></span></button> 
                    </div>
                </form>        
            </div>     
        </div>     
        <?php
            $idba = $_POST['idp2'];
            $canb = $_POST['tcanb'];               
            $desb = $_POST['tdesb'];        
            $fecha1 = $_POST['tfecha1'];               
            $fecha2 = $_POST['tfecha2'];  
        
            if (isset ($_POST['insertb'])){   
                date_default_timezone_set('America/Guatemala');                          
                $fechab = date('Y-m-d'); 
                
                $sql1="SELECT * FROM ubicacion WHERE idproducto = '".$idba."' AND idtienda = 10";
                $resultado1 = mysqli_query($conn, $sql1);
                if($row1 = mysqli_fetch_array($resultado1)){ $idubicacion = $row1["idubicacion"];} 
                                 
                $sql2 ="insert into bajas values ('NULL', '".$idba."', '".$canb."', '".$desb."', '".$fechab."')";
                $resultado2 = mysqli_query($conn, $sql2); 
                
                $sql3 ="UPDATE ubicacion SET cantubicacion = (cantubicacion - $canb) WHERE idubicacion = '".$idubicacion."'";
                $resultado3 = mysqli_query($conn, $sql3);
               
                $sql4 ="insert into bitacora values ('NULL', '".$ussera."', 'FORM BAJA AL PRODUCTO ".$idba."', 'INSERT', '".$fechabitacora."')";
                $resultado4 = mysqli_query($conn, $sql4);               

                echo "<div class='color-arriba'><h3>$canb   UNIDADES DEL PRODUCTO $idba DADO DE BAJA CORRECTAMENTE</h3></div>";                                  
            }
            
            if (isset ($_POST['selectb'])){ 
                
                $sql5="SELECT bajas.canbaja, producto.descproducto, bajas.desbaja 
                        FROM bajas INNER JOIN producto ON producto.idproducto = bajas.idproducto
                        WHERE date(bajas.febaja) BETWEEN '".$fecha1."' AND '".$fecha2."'";
                $resultado5 = mysqli_query($conn, $sql5);                
                print "<table class='TablaH'>";                         
                    print "<tr>";
                    print "<td>CANT</td>";
                    print "<td>PRODUCTO</td>";
                    print "<td>DESCRIPCIÓN</td>";                  
                    print "</tr>";
                    while ($row5 = mysqli_fetch_assoc($resultado5)){
                        print "<tr>";
                            foreach ($row5 as $item5){
                                print "<td>".($item5!==NULL ?htmlentities($item5):"&nbsp;")."</td>";
                            }
                            print "</tr>";
                        }               
                print "</table>";           
            }
        ?>        
	<script type="text/javascript" src="js/jquery-3.6.1.min.js"></script> 
        <script type="text/javascript" src="js/jquery-ui.js"></script>
        <script type="text/javascript" src="js/misfunciones.js"></script>
        <script type="text/javascript" src="js/versection.js"></script>     
    </body>
</html>

