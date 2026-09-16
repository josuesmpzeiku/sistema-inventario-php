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
        <div class="color-arriba"><h2 class ="subtitulo">ASIGNACIÓN DE TURNOS</h2></div>       
        <div class="fila">
            <div class="columna">		
                <form name="form1" method="post" action=""> 
                    <input class="decorar-input" name="tnitp" type="text" placeholder="Nit o DPI del Paciente" id="tnitp" title="NIT o DPI del Paciente, puede que¿dar en Blanco">
                    <input class="decorar-input" name="tnombrep" type="text" placeholder="Nombre del Paciente" required   id="tnombrep" title="Nombre Completo del Paciente" >
                    <input class="decorar-input" name="ttelefonop" type="text" placeholder="Número de Teléfono del Paciente" id="ttelefonop" title="Numero de Telefono del Paciente Ocho digitos sin espacio" required> <div><br></div>
                    <div id="botones"><button type="submit" name="insertp" class="btn-universal" id="insertp"><span class="icon icon-checkmark"></span></button></div>
                </form>        
            </div>            
            <div class="columna">
		<form name="form2" method="post" action="">
                    <input class="decorar-input" name="tpaciente" type="text" id="tpaciente" placeholder="Escriba y Elija un Paciente" required>
                    <div><br></div><div id="npaciente" class="contenedor-transparente"></div>                    
                </form>        
            </div>     
        </div>     
        <?php
            $idpa = $_POST['tidpa'];
            $idat = $_POST['tnombreu'];               
            $nitpa = $_POST['tnitp'];        
            $nompa = $_POST['tnombrep'];
            $telpa = $_POST['ttelefonop'];
        
            if (isset ($_POST['insertp'])){  
                
                if ($ussera == NULL or $ussera == 0){
                    print "<script> alert('TIENES QUE INICIAR SESION'); location.href = 'index.php'; </script>";
                }
                else {
                    $sql2 ="insert into paciente values (NULL, '".$nitpa."', '".$nompa."', '', '', '".$telpa."', '', '', '')";
                    $resultado2 = mysqli_query($conn, $sql2);  

                    $sql3="SELECT COUNT(*) as conteo FROM atencion";
                    $resultado3 = mysqli_query($conn, $sql3);
                    if($row3 = mysqli_fetch_array($resultado3)){ $conteo = $row3["conteo"]; }
                    $cuenta = $conteo + 1;               
                    $numero = str_pad($cuenta, 9, "0", STR_PAD_LEFT);                            
                    $idaten = $tienda.$numero; 

                    $sql4 ="SELECT * FROM paciente ORDER BY idpaciente DESC LIMIT 1;";
                    $resultado4 = mysqli_query($conn, $sql4);
                    if ($row4 = mysqli_fetch_row($resultado4)){ } 

                    date_default_timezone_set('America/Guatemala');                          
                    $fechaten = date('Y-m-d');

                    $sql5 ="insert into atencion values ('".$idaten."', '".$row4[0]."', '".$fechaten."', 'RECEPCION', '".$tienda."')";
                    $resultado5 = mysqli_query($conn, $sql5); 

                    $sql6 ="insert into venta values ('".$idaten."', '".$fechaten."', '', '0')";
                    $resultado6 = mysqli_query($conn, $sql6); 

                    $sql1 ="insert into bitacora values ('NULL', '".$ussera."', 'NUEVA CONTRASEÑA Y VENTA ID ".$idaten."', 'INSERT', '".$fechabitacora."')";
                    $resultado1 = mysqli_query($conn, $sql1);

                    print "<div class='columna'>";
                    echo '<div class="color-arriba"><h3>TURNO ASIGNADO CORRECTAMENTE</h3></div>';
                    print "<table class='TablaA'>";                            
                    print "<tr><td width = '35%'>NIT O DPI:</td><td>$row4[1]</td></tr>";
                    print "<tr><td>NOMBRE COMPLETO:</td><td>$row4[2]</td></tr>";
                    print "<tr><td>TELEFONO:</td><td>$row4[5]</td></tr>";               
                    print "</table>";
                    print "</div>";  
                }
            }
            
            if (isset ($_POST['upinaten'])){             
                if ($ussera == NULL or $ussera == 0){
                    print "<script> alert('TIENES QUE INICIAR SESION'); location.href = 'index.php'; </script>";
                }
                else {
                    $sql6 ="update paciente SET nitpaciente = '".$nitpa."', nompaciente = '".$nompa."', telpaciente = '".$telpa."' where idpaciente = '".$idpa."'";
                    $resultado6 = mysqli_query($conn, $sql6); 

                    $sql7="SELECT COUNT(*) as conteo FROM atencion";
                    $resultado7 = mysqli_query($conn, $sql7);
                    if($row7 = mysqli_fetch_array($resultado7)){ $conteo = $row7["conteo"]; }
                    $cuenta = $conteo + 1;               
                    $numero = str_pad($cuenta, 9, "0", STR_PAD_LEFT);                            
                    $idaten = $tienda.$numero; 

                    $sql8 ="SELECT * FROM paciente where idpaciente = '".$idpa."'";
                    $resultado8 = mysqli_query($conn, $sql8);
                    if ($row8 = mysqli_fetch_row($resultado8)){ }                  

                    date_default_timezone_set('America/Guatemala');                          
                    $fechaten = date('Y-m-d'); 

                    $sql9 ="insert into atencion values ('".$idaten."', '".$row8[0]."', '".$fechaten."', 'RECEPCION', '".$tienda."')";
                    $resultado9 = mysqli_query($conn, $sql9);

                    $sql5 ="insert into venta values ('".$idaten."', '".$fechaten."', '', '0')";
                    $resultado5 = mysqli_query($conn, $sql5); 

                    $sql10 ="insert into bitacora values ('NULL', '".$ussera."', 'NUEVA CONTRASEÑA Y VENTA ID ".$idaten."', 'UPDATE', '".$fechabitacora."')";
                    $resultado10 = mysqli_query($conn, $sql10);                 

                    print "<div class='columna'>";
                    echo '<div class="color-arriba"><h3>TURNO ASIGNADO CORRECTAMENTE</h3></div>';
                    print "<table class='TablaA'>";                            
                    print "<tr><td width = '35%'>NIT O DPI:</td><td>$row8[1]</td></tr>";
                    print "<tr><td>NOMBRE COMPLETO:</td><td>$row8[2]</td></tr>";
                    print "<tr><td>TELEFONO:</td><td>$row8[5]</td></tr>";               
                    print "</table>";
                    print "</div>";      
                }
            }
        ?>        
	<script type="text/javascript" src="js/jquery-3.6.1.min.js"></script> 
        <script type="text/javascript" src="js/jquery-ui.js"></script>
        <script type="text/javascript" src="js/misfunciones.js"></script>
        <script type="text/javascript" src="js/versection.js"></script>     
    </body>
</html>
