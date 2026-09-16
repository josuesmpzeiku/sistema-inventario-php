<?php
    $http_referer = isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:null;
    $referer = $_SERVER['HTTP_REFERER'];
    if ($referer == "" ) {
        print "<script> alert('TIENES QUE INICIAR SESION'); location.href = 'index.php'; </script>";
    }
    error_reporting(0);
    session_start();
    $ussera = $_SESSION["usuario"];
    include ("phpmysql.php"); 
    $sql="Select * from usuario  WHERE idusuario = '".$ussera."'";
    $resultado = mysqli_query($conn, $sql);
    if($row = mysqli_fetch_array($resultado)){ $tipou = $row["puestousuario"]; $nombre =$row["userusuario"]; $tienda = $row["idtienda"];}

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
        <div class="color-arriba"><h2 class ="subtitulo">REPORTE DE PACIENTES</h2></div>       
        <?php 
                       
            $sql4="SELECT paciente.idpaciente, paciente.nompaciente, paciente.telpaciente 
            from paciente INNER JOIN atencion on atencion.idpaciente = paciente.idpaciente
            WHERE atencion.idtienda = '".$tienda."'
            GROUP BY paciente.nompaciente
            ORDER BY paciente.nompaciente asc;";
            $resultado4 = mysqli_query($conn, $sql4); 
            print "<div class= 'fila'>";
            print "<div class= 'columna'>";
            print "<table class='TablaB'>"; 
            print "<tr>";                    
                print "<td>NOMBRE DEL PACIENTE</td>";                    
                print "<td>TELEFONO</td>"; 
            print "</tr>";
            while ($row4 = mysqli_fetch_array($resultado4)){                         
                print "<tr><td><a class='tm-btn-plus' href='infopac.php?perfil=$ussera&atencion={$row4[0]}' target='_blank'>{$row4[1]}</a></td>";
                print "<td>{$row4['telpaciente']}</td>";  
            }
            

        ?>
	<script type="text/javascript" src="js/jquery-3.6.1.min.js"></script> 
        <script type="text/javascript" src="js/jquery-ui.js"></script>
        <script type="text/javascript" src="js/misfunciones.js"></script>
        <script type="text/javascript" src="js/versection.js"></script> 
        <script type="text/javascript" src="js/funcionlista.js"></script>
    </body>
</html>
