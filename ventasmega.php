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
        <div class="color-arriba"><h2 class ="subtitulo">VENTAS</h2></div><br>
        <?php                           
            $sql3 ="SELECT SUM(cantpago) FROM `pago` WHERE idtienda = '".$tienda."' AND MONTH(CURDATE())=MONTH(fecpago) AND YEAR(fecpago) = YEAR(CURRENT_DATE());";
            $resultado3 = mysqli_query($conn, $sql3);
                if ($row3 = mysqli_fetch_row($resultado3)){ }
            
            print "<h3>VENTAS DEL MES Q $row3[0]</h3><br>";
            
            $sql1 ="SELECT venta.idventa, paciente.nompaciente, venta.totalventa, venta.fecventa
                    FROM venta INNER JOIN atencion ON atencion.idatencion = venta.idventa
                    INNER JOIN paciente ON paciente.idpaciente = atencion.idpaciente
                    WHERE atencion.idtienda = ".$tienda." AND atencion.estatencion != 'FINALIZADO'";
            $resultado1 = mysqli_query($conn, $sql1);
            
            while ($row1 = mysqli_fetch_array($resultado1)){  
                print "<a class='tm-btn-plus' href='depagomega.php?perfil=".$ussera."&iventa=".$row1['idventa']."'>{$row1['idventa']},   {$row1['nompaciente']},   {$row1['fecventa']},     Q {$row1['totalventa']}</a><br>";                              
            }
        ?>
	<script type="text/javascript" src="js/jquery-3.6.1.min.js"></script> 
        <script type="text/javascript" src="js/jquery-ui.js"></script>
        <script type="text/javascript" src="js/misfunciones.js"></script>
        <script type="text/javascript" src="js/versection.js"></script> 
        <script type="text/javascript" src="js/funcionlista.js"></script>
    </body>
</html>