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
        <meta http-equiv="refresh" content="30">
    </head>
    <body>
        <div class="color-arriba"><h2 class ="subtitulo">BIENVENIDO <?php echo $nombre ?> </h2></div>   
        <?php
        
            $sql11 ="SELECT atencion.idatencion, atencion.fecatencion, paciente.nompaciente, tienda.nomtienda, entrega.estadoe
                    FROM atencion INNER JOIN paciente ON paciente.idpaciente = atencion.idpaciente
                    INNER JOIN tienda ON tienda.idtienda = atencion.idtienda
                    INNER JOIN entrega on entrega.identrega = atencion.idatencion
                    WHERE entrega.estadoe != 'F'";
            $resultado11 = mysqli_query($conn, $sql11); 
            
            $sql5 ="SELECT * FROM entrega WHERE estadoe = 'E'";
            $resultado5 = mysqli_query($conn, $sql5);
            if ($row5 = mysqli_fetch_row($resultado5)){ }
            
            if ($row5[0] != NULL){         
                print '<audio id="xyz" src="not.mp3" preload="auto"></audio>';
            }
                        
            while ($row11 = mysqli_fetch_array($resultado11)){   
                $sql2 ="SELECT * FROM entrega WHERE estadoe = 'E' and identrega = {$row11['idatencion']}";
                $resultado2 = mysqli_query($conn, $sql2);
                if ($row2 = mysqli_fetch_row($resultado2)){ } 
                if ($row2[0] != NULL){
                    $colorh = 'color: red';
                } 
                else {
                    $colorh = 'color: green';
                } 
                print "<a class='tm-btn-plus' style='$colorh' href='lentes.php?perfil=".$ussera."&atencion=".$row11['idatencion']."&tienda=".$row11['nomtienda']."'>{$row11['idatencion']},  {$row11['nompaciente']}  {$row11['fecatencion']}  {$row11['nomtienda']}</a><br>";                                 
            
            }
        ?> 
        <script>
            document.getElementById('xyz').play();
            alert("Tiene un Nuevo Pedido");
        </script>
	<script type="text/javascript" src="js/jquery-3.6.1.min.js"></script> 
        <script type="text/javascript" src="js/jquery-ui.js"></script>
        <script type="text/javascript" src="js/misfunciones.js"></script>
        <script type="text/javascript" src="js/versection.js"></script> 
        <script type="text/javascript" src="js/funcionlista.js"></script>
    </body>
</html>