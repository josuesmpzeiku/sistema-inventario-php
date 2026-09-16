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
        <div class="color-arriba"><h2 class ="subtitulo">REPOSICION DE MATERIAL PARA LENTES</h2></div>
        <div class="fila">
            <div class="columna">                
                <form name="form1" method="post" action="">  
                    <input class="decorar-input" name="incontras" type="text" placeholder="CONTRASEÑA">
                    <input class="decorar-input" name="lder" type="text" id="lder" placeholder="Escriba y Elija un Lente o Base para el LENTE DERECHO"><div id="nder"></div> 
                    <input class="decorar-input" name="lizq" type="text" id="lizq" placeholder="Escriba y Elija un Lente o Base para el LENTE IZQUIERDO"><div id="nizq"></div> 
                    <div id="botones"><button type="submit" name="insertrepo" class="btn-universal" id="insertrepo"><span class="icon icon-floppy-disk"></span></button></div> 
                </form>
            </div>
            <div class="columna">
               
            </div>
        </div>        
        <?php
            $idcontraseña= $_POST['incontras'];                     
            $lder = $_POST['idled'];        
            $lizq = $_POST['idlei'];
            date_default_timezone_set('America/Guatemala');                          
            $fechag = date('Y-m-d');
        
            if (isset ($_POST['insertrepo'])){                 
                if ($lder == NULL OR $lizq == NULL){
                    print "<script> alert('TIENES QUE ELEGIR UN LENTE O MATERIAL DE LA LISTA');</script>";
                }
                else {
                    $sql4 ="insert into repolente values ('NULL', '".$idcontraseña."', '".$fechag."', '".$lder."', '".$lizq."')";
                    $resultado4 = mysqli_query($conn, $sql4);  

                    $sql5 ="insert into bitacora values ('NULL', '".$ussera."', 'FORM REPOSICION ".$idcontraseña."', 'INSERT', '".$fechabitacora."')";
                    $resultado5 = mysqli_query($conn, $sql5);

                    $sql6 ="UPDATE lente SET canlente=(canlente-1) WHERE idlente='".$lder."'";
                    $resultado6 = mysqli_query($conn, $sql6);
                    $sql7 ="UPDATE lente SET canlente=(canlente-1) WHERE idlente='".$lizq."' ";
                    $resultado7 = mysqli_query($conn, $sql7);

                    echo '<div class="color-arriba"><h3>GUARDADO CORRECTAMENTE</h3></div>'; 
                }                
            }
        ?>        
	<script type="text/javascript" src="js/jquery-3.6.1.min.js"></script> 
        <script type="text/javascript" src="js/jquery-ui.js"></script>
        <script type="text/javascript" src="js/misfunciones.js"></script>
        <script type="text/javascript" src="js/versection.js"></script> 
        <script type="text/javascript" src="js/funcionlista.js"></script>
    </body>
</html>

