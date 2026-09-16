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
    </head>
    <body>
        <div class="color-arriba"><h2 class ="subtitulo">PACIENTES PARA EVALUACIÓN</h2></div> 
        <br>  
        <?php
            $sql1 ="SELECT atencion.idatencion, paciente.*
                    FROM atencion INNER JOIN paciente ON paciente.idpaciente = atencion.idpaciente
                    WHERE atencion.idtienda = ".$tienda." AND atencion.estatencion = 'ASESOR'";
            $resultado1 = mysqli_query($conn, $sql1);
            while ($row1 = mysqli_fetch_array($resultado1)){                     
                print "<a class='tm-btn-plus' href='fichap.php?perfil=".$ussera."&atencion=".$row1['idatencion']."'>{$row1['idatencion']},  {$row1['nompaciente']}</a><br>";                                 
            
            }
        ?>	
    </body>
</html>
