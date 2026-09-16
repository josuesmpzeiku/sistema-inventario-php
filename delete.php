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
        <div class="color-arriba"><h2 class ="subtitulo">ELIMINACIONES</h2></div> 
        <div class="fila">
            <div class="columna">                
                <form name="form1" method="post" action="">
                    <table width="100%">
                        <tr>
                            <td width="40%">Eliminar Armado</td>
                            <td width="40%"><input class="decorar-input" name="tarm" type="text" placeholder="ID armado" required></td>
                            <td><button type="submit" name="delarm" class="btn-universal"><span class="icon icon-bin"></span></button></td>
                       </tr>
                    </table>
                </form>
                <?php
                $idarma = $_POST['tarm'];
                    if (isset ($_POST['delarm'])){ 
                        $sql2="SELECT * FROM armado where idarm = '".$idarma."' ";
                        $resultado2 = mysqli_query($conn, $sql2);
                        if ($row2 = mysqli_fetch_row($resultado2)){ }
                        if($row2[0]== NULL){
                            print "<script> alert('ID de armado incorrecto o no existe');</script>";
                        }
                        else{
                            $sql1 ="delete from armado where idarm = '".$idarma."'";                              
                            $resultado1 = mysqli_query($conn, $sql1);
                            $sql3 ="insert into bitacora values ('NULL', '".$ussera."', 'Eliminacion de Armado id ".$idarma."', 'delete', '".$fechabitacora."')";
                            $resultado3 = mysqli_query($conn, $sql3);
                            echo '<div class="color-arriba"><h3>ID DE ARMADO ELIMINADO EXITOSAMENTE</h3></div>';
                        }
                    }
                ?> 
                <form name="form2" method="post" action="">
                    <table width="100%">
                        <tr>
                            <td width="40%">Eliminar Cita</td>
                            <td width="40%"><input class="decorar-input" name="tcita" type="text" placeholder="ID Atención" required></td>
                            <td><button type="submit" name="delcita" class="btn-universal"><span class="icon icon-bin"></span></button></td>
                       </tr>
                    </table>
                </form>
                <?php
                    $idcita = $_POST['tcita'];
                    if (isset ($_POST['delcita'])){  
                        $sql2="SELECT * FROM cita where idatencion = '".$idcita."' ";
                        $resultado2 = mysqli_query($conn, $sql2);
                        if ($row2 = mysqli_fetch_row($resultado2)){ }
                        if($row2[0]== NULL){
                            print "<script> alert('ID de atención incorrecto o no existe');</script>";
                        }
                        else{
                            $sql1 ="delete from cita where idatencion = '".$idcita."'";                              
                            $resultado1 = mysqli_query($conn, $sql1);
                            $sql3 ="insert into bitacora values ('NULL', '".$ussera."', 'Eliminacion de Cita de la Atencion".$idcita."', 'delete', '".$fechabitacora."')";
                            $resultado3 = mysqli_query($conn, $sql3);
                            echo '<div class="color-arriba"><h3>CITA ELIMINADA EXITOSAMENTE</h3></div>';
                        }
                    }
                ?>  
                <form name="form3" method="post" action="">
                    <table width="100%">
                        <tr>
                            <td width="40%">Eliminar Receta</td>
                            <td width="40%"><input class="decorar-input" name="trec" type="text" placeholder="ID Receta" required></td>
                            <td><button type="submit" name="delrec" class="btn-universal"><span class="icon icon-bin"></span></button></td>
                       </tr>
                    </table>
                </form>
                <?php
                    $idrec = $_POST['trec'];
                    if (isset ($_POST['delrec'])){  
                        $sql2="SELECT * FROM receta where idreceta = '".$idrec."' ";
                        $resultado2 = mysqli_query($conn, $sql2);
                        if ($row2 = mysqli_fetch_row($resultado2)){ }
                        if($row2[0]== NULL){
                            print "<script> alert('ID de receta incorrecto o no existe');</script>";
                        }
                        else{
                            $sql1 ="delete from receta where idreceta = '".$idrec."'";                              
                            $resultado1 = mysqli_query($conn, $sql1);
                            $sql4 ="delete from detallereceta where idreceta = '".$idrec."'";                              
                            $resultado4 = mysqli_query($conn, $sql4);
                            $sql3 ="insert into bitacora values ('NULL', '".$ussera."', 'Eliminacion de RECETA  con id ".$idrec."', 'delete', '".$fechabitacora."')";
                            $resultado3 = mysqli_query($conn, $sql3);
                            echo '<div class="color-arriba"><h3>RECETA ELIMINADA EXITOSAMENTE</h3></div>';
                        }
                    }
                ?>                  
            </div>           
        </div>       
	<script type="text/javascript" src="js/jquery-3.6.1.min.js"></script> 
        <script type="text/javascript" src="js/jquery-ui.js"></script>
        <script type="text/javascript" src="js/misfunciones.js"></script>
        <script type="text/javascript" src="js/versection.js"></script> 
        <script type="text/javascript" src="js/funcionlista.js"></script>
    </body>
</html>
