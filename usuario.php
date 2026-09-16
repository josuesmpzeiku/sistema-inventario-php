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
    if($row = mysqli_fetch_array($resultado)){ $tipou = $row["puestousuario"];}  
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
        <div class="color-arriba"><h2 class ="subtitulo">INFORMACIÓN DE USUARIOS</h2></div>
        <div class="fila">
            <div class="columna">		
                <form name="form1" method="post" action=""> 
                    <input class="decorar-input" name="tnombreu" type="text" placeholder="Nombre de Usuario" required   id="tnombreu" title="Nombre Completo del Usuario" >
                    <input class="decorar-input" name="tuseru" type="text" placeholder="Usuario"  required id="tuseru" title="Usuario">
                    <input class="decorar-input" name="tpassu" type="text"  placeholder="Contraseña" id="tpassu" title="Contraseña" >
                    <select name="tpusuario" class="decorar-input" required="">
                        <option value="" disabled selected>Elija un Puesto</option>                        
                        <option value="B">B Director</option> 
                        <option value="C">C Gerente</option>
                        <option value="D">D Asesor</option> 
                        <option value="E">E Laboratorio</option> 
                        <option value="F">F Gerente Mega</option> 
                        <option value="G">G Asesor Mega</option> 
                    </select>
                    <input class="decorar-input" name="ttienda" type="text" id="ttienda" placeholder="Escriba y Elija una Tienda" required>
                    <div><br></div><div id="ntienda" class="contenedor-transparente"></div> 
                    <div id="botones"><button type="submit" name="insertu" class="btn-universal" id="insertu"><span class="icon icon-floppy-disk"></span></button></div>
                </form>        
            </div>            
            <div class="columna">
		<form name="form2" method="post" action="">
                    <input class="decorar-input" name="tusuario" type="text" id="tusuario" placeholder="Escriba y Elija un Usuario" required>
                    <div><br></div><div id="nusuario" class="contenedor-transparente"></div>                    
                </form>        
            </div>     
        </div>     
        <?php 
            $idus = $_POST['tidus'];
            $nomus = $_POST['tnombreu'];               
            $userus = $_POST['tuseru'];        
            $passus = $_POST['tpassu'];
            $puesus = $_POST['tpusuario'];
            $idtus = $_POST['idti'];
        
            if (isset ($_POST['insertu'])){
                
                if ($idtus == NULL){
                    print "<script> alert('TIENES QUE ELEGIR UNA TIENDA DE LA LISTA');</script>";
                }
                else {
                    $sql1 ="insert into usuario values (NULL, '".$nomus."', '".$userus."', '".$passus."', '".$puesus."', '".$idtus."')";
                    $resultado1 = mysqli_query($conn, $sql1); 
                    
                    $sql2 ="insert into bitacora values ('NULL', '".$ussera."', 'FORM USUARIO ".$nomus."', 'INSERT', '".$fechabitacora."')";
                    $resultado2 = mysqli_query($conn, $sql2);
                }
                                  
                    $sql3 ="SELECT * FROM usuario ORDER BY idusuario DESC LIMIT 1;";
                    $resultado3 = mysqli_query($conn, $sql3);
                    if ($row3 = mysqli_fetch_row($resultado3)){ }
                    print "<div class='columna'>";
                    echo '<div class="color-arriba"><h3>USUARIO GUARDADO CORRECTAMENTE</h3></div>';
                    print "<table class='TablaA'>";                            
                    print "<tr><td width = '35%'>ID:</td><td>$row3[0]</td></tr>";
                    print "<tr><td>NOMBRE COMPLETO:</td><td>$row3[1]</td></tr>";
                    print "<tr><td>USUARIO:</td><td>$row3[2]</td></tr>";
                    print "<tr><td>CONTRASEÑA:</td><td>$row3[3]</td></tr>";
                    print "<tr><td>TIPO DE USUARIO:</td><td>$row3[4]</td></tr>";
                    print "<tr><td>TIENDA:</td><td>$row3[5]</td></tr>";
                    print "</table>";
                    print "</div>";                             
            }
        
            if (isset ($_POST['updateu'])){
                $sql4 ="insert into bitacora values ('NULL', '".$ussera."', 'FORM USUARIO ".$nomus."', 'UPDATE', '".$fechabitacora."')";
                $resultado4 = mysqli_query($conn, $sql4); 
                
                $sql5 ="update usuario SET nomusuario = '".$nomus."', userusuario = '".$userus."', passusuario = '".$passus."', puestousuario = '".$puesus."', idtienda = '".$idtus."' where idusuario = '".$idus."' ";
                $resultado5 = mysqli_query($conn, $sql5); 
                                          
                $sql6 ="SELECT * FROM usuario where idusuario = '".$idus."' ";
                $resultado6 = mysqli_query($conn, $sql6);
                if ($row6 = mysqli_fetch_row($resultado6)){ }
                print "<div class='columna'>";
                echo '<div class="color-arriba"><h3>DATOS DEL USUARIO MODIFICADOS CORRECTAMENTE</h3></div>';
                print "<table class='TablaA'>";                            
                print "<tr><td width = '35%'>ID:</td><td>$row6[0]</td></tr>";
                print "<tr><td>NOMBRE COMPLETO:</td><td>$row6[1]</td></tr>";
                print "<tr><td>USUARIO:</td><td>$row6[2]</td></tr>";
                print "<tr><td>CONTRASEÑA:</td><td>$row6[3]</td></tr>";
                print "<tr><td>TIPO DE USUARIO:</td><td>$row6[4]</td></tr>";
                print "<tr><td>TIENDA:</td><td>$row6[5]</td></tr>";                                            
                print "</table>";
                print "</div>";                             
            }  
       
            if (isset ($_POST['deleteu'])){ 
                
                $sql7 ="delete from usuario where idusuario = '".$idus."'";
                $resultado7 = mysqli_query($conn, $sql7); 
                echo '<div class="color-arriba"><h3>EL USUARIO -'.$nomus.'- HA SIDO ELIMINADO CORRECTAMENTE</h3></div>';   
                
                $sql8 ="insert into bitacora values ('NULL', '".$ussera."', 'FORM USUARIO ".$nomus."', 'DELETE', '".$fechabitacora."')";
                $resultado8 = mysqli_query($conn, $sql8);
            }  
        ?> 
        <script type="text/javascript" src="js/jquery-3.6.1.min.js"></script> 
        <script type="text/javascript" src="js/jquery-ui.js"></script>
        <script type="text/javascript" src="js/misfunciones.js"></script>
    </body>
</html>
