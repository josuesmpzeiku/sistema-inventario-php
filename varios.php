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
        <div class="color-arriba"><h2 class ="subtitulo">OPERACIONES VARIAS</h2></div> 
        <div class="fila">
            <div class="columna">
                <h3>OBSERVACIONES EN VENTA</h3>
                <form name="form1" method="post" action=""> 
                    <input class="decorar-input" name="taten" type="text" id="taten" placeholder="Escriba una Contraseña, Paciente o Fecha" required><div id="naten"></div>                    
                    <div id="botones">
                       <button type="submit" name="updv" class="btn-universal" id="upda"><span class="icon icon-pencil"></span></button>  
                   </div>
                </form>
            </div>
            <div class="columna">
              <h3>Cambiar Estación o Traslado de Paciente</h3>
                <form name="form2" method="post" action=""> 
                    <input class="decorar-input" name="taten2" type="text" id="taten2" placeholder="Escriba una Contraseña, Paciente o Fecha"><div id="naten2"></div>
                    <table width = '100%'>
                        <tr>
                            <td width = '80%'>
                                <select name="testadoa" class="decorar-input">
                                    <option value="NO">Elija una Estacion</option>                        
                                    <option value="RECEPCION">ASESOR</option>
                                    <option value="ASESOR">ESPECIALISTA</option> 
                                    <option value="LABORATORIO">PAGOS</option> 
                                </select>
                            </td>
                            <td>
                                <button type="submit" name="upda" class="btn-universal" id="upda"><span class="icon icon-tab"></span></button> 
                            </td>
                        </tr>
                        <tr>
                            <td width = '80%'>
                                <?php 
                                print "<select name='ttienda' class='decorar-input'>";
                                print "<option value='0'>Elija una Tienda</option>"; 
                                $sql1="Select * from tienda";                               
                                $resultado1 = mysqli_query($conn, $sql1);
                                while ($row1 = mysqli_fetch_array($resultado1)){
                                    print "<option value = {$row1['idtienda']}>{$row1['nomtienda']}</option>";                       
                                }
                                print "</select>";
                                ?>  
                            </td>
                            <td>
                                <button type="submit" name="updt" class="btn-universal" id="updt"><span class="icon icon-tab"></span></button> 
                            </td>
                        </tr>
                    </table>
                </form>  
            </div>
        </div>
        <div class="fila">
            <div class="columna">
                <h3>FINALIZAR CONTRASEÑA</h3>
                <form name="form3" method="post" action="">                    
                    <input class="decorar-input" name="tcon" type="text" id="tcon" placeholder="Escriba una Contraseña para Finalizar" required>
                    <div id="botones"><button type="submit" name="delc" class="btn-universal" id="delc"><span class="icon icon-exit"></span></button></div>
                </form> 
            </div>
            <div class='columna'>
                <h3>DEPÓSITOS</h3>
                <form name='form' method='post' action=''>
                    <input name='tban' type='text' class='decorar-input' title='Nombre del Banco' placeholder = 'Nombre del Banco' required>
                    <input name='tcantp' type='number'  step="0.01" class='decorar-input' title='Cantidad en Numeros' placeholder = 'Monto del Depósito' required>
                    <input name='tdoc' type='text' class='decorar-input' title='Numero de Boleta o transacción' placeholder = 'Numero de Documento' >
                    <input name='tfecp' type='date' class='decorar-input' required >
                    <div id='botones'>
                        <button type='submit' name='insp' class='btn-universal'><span class='icon icon-coin-dollar'></span></button>                        
                    </div>                   
                </form> 
            <?php     
            $doc= $_POST['tdoc'];
            $tcantp = $_POST['tcantp'];
            $tban = $_POST['tban'];
            $tfecp = $_POST['tfecp'];          

            if (isset ($_POST['insp'])){ 
                
                $sql2 ="insert into deposito values ('NULL', '".$tienda."', '".$tban."', '".$doc."',  '".$tcantp."', '".$tfecp."')";
                $resultado2 = mysqli_query($conn, $sql2);                    

                $sql3 ="insert into bitacora values ('NULL', '".$ussera."', 'FORM DEPOSITO ".$tfecp."', 'INSERT', '".$fechabitacora."')";
                $resultado3 = mysqli_query($conn, $sql3);

                echo '<div class="color-arriba"><h3>DEPOSITO GUARDADO CORRECTAMENTE</h3></div>';

            }           
            ?> 
            </div> 
        </div>
        <?php
            $contras= $_POST['idat'];
            $contrau= $_POST['idat2'];
            $esta= $_POST['testadoa'];
            $ttie= $_POST['ttienda'];
            $contrad= $_POST['tcon'];
            $obsv= $_POST['tobsv'];
            

            if (isset ($_POST['updv'])){ 
               $sql3 ="SELECT idtienda FROM atencion WHERE idatencion = '".$contras."'";
                $resultado3 = mysqli_query($conn, $sql3);
                if($row3 = mysqli_fetch_array($resultado3)){ $idaten2 = $row3["idtienda"];}                
                if ($idaten2 != $tienda){
                    print "<script> alert('NO PUEDES MODIFICAR PORQUE NO CORRESPONDE A TU SUCURSAL');</script>";
                }
                else {
                    $sql4 ="insert into bitacora values ('NULL', '".$ussera."', 'FORM VARIOS', 'UPDATE', '".$fechabitacora."')";
                    $resultado4 = mysqli_query($conn, $sql4); 
                
                    $sql5 ="update venta SET obsventa = '".$obsv."' where idventa = '".$contras."' ";
                    $resultado5 = mysqli_query($conn, $sql5); 
                    echo '<div class="color-arriba"><h3>LA MODIFICACIÓN FUE AGREGADA</h3></div>';                 
                }
            }             

            
            if (isset ($_POST['upda'])){ 
                $sql3 ="SELECT idtienda FROM atencion WHERE idatencion = '".$contrau."'";
                $resultado3 = mysqli_query($conn, $sql3);
                if($row3 = mysqli_fetch_array($resultado3)){ $idaten2 = $row3["idtienda"];} 
                if ($esta == 'NO' and $contrasu == NULL){
                    print "<script> alert('TIENE QUE ELEGIR UNA CONTRASEÑA Y UNA ESTACIÓN');</script>";
                }
                else {    
                    if ($idaten2 != $tienda){
                        print "<script> alert('NO PUEDES CAMBIAR PORQUE NO CORRESPONDE A TU SUCURSAL');</script>";
                    }
                    else {
                        $sql4 ="insert into bitacora values ('NULL', '".$ussera."', 'FORM VARIOS', 'UPDATE', '".$fechabitacora."')";
                        $resultado4 = mysqli_query($conn, $sql4); 

                        $sql5 ="update atencion SET estatencion = '".$esta."' where idatencion = '".$contrau."' ";
                        $resultado5 = mysqli_query($conn, $sql5); 
                        echo '<div class="color-arriba"><h3>LA ESTACION FUE MODIFICADA</h3></div>';                 
                    }
                }
            } 
            
            if (isset ($_POST['updt'])){                 
                if ($ttie == '0' or $contrau == NULL){
                    print "<script> alert('TIENE QUE ELEGIR UNA CONTRASEÑA Y UNA TIENDA');</script>";
                }
                else {
                    $sql4 ="insert into bitacora values ('NULL', '".$ussera."', 'FORM VARIOS', 'UPDATE', '".$fechabitacora."')";
                    $resultado4 = mysqli_query($conn, $sql4); 
                
                    $sql5 ="update atencion SET idtienda = '".$ttie."' where idatencion = '".$contrau."' ";
                    $resultado5 = mysqli_query($conn, $sql5); 
                    echo '<div class="color-arriba"><h3>EL PACIENTE FUE TRASLADADO</h3></div>';                 
                }
            } 
            
             if (isset ($_POST['delc'])){ 
                $sql ="SELECT * FROM atencion WHERE idatencion = '".$contrad."'";
                $resultado = mysqli_query($conn, $sql);
                if ($row = mysqli_fetch_row($resultado)){ }                
                if ($row[0] == NULL){
                    print "<script> alert('OPERACION INVALIDA, VERIFIQUE CONTRASEÑA');</script>";
                }
                else {
                    
                    $sql2 ="update atencion set estatencion='FINALIZADO' where idatencion = '".$contrad."'";
                    $resultado2 = mysqli_query($conn, $sql2);
                    
                    
                    echo '<div class="color-arriba"><h3>LA CONTRASEÑA '.$contrad.' FUE FINALIZADA</h3></div>';                 
                }
            } 
        ?>
        <div class="fila">
            <div class="columna">
                <h3>ELIMINAR PAGO</h3>
                <?php
                echo "<form name='form' method='post' action=''>";
                date_default_timezone_set('America/Guatemala');                          
                $fechag = date('Y-m-d');
                    print "<table class = 'TablaC'>";
                        print "<tr><td>DESCRIPCION</td><td>MONTO</td><td>RECIBE</td><td>-</td></tr>";
                       $sql5 ="SELECT idgasto, descg, montog, qrega FROM gasto WHERE fecg='".$fechag."' and idtienda = '".$tienda."'";
                        $resultado5 = mysqli_query($conn, $sql5);
                        while ($row5 = mysqli_fetch_array($resultado5)){ 
                            $fila = $row5['idgasto'];
                           print "<tr><td>{$row5['descg']}</td><td>{$row5['montog']}</td><td>{$row5['qrega']}</td>";
                            print "<td><button type='submit' name='delg".$fila."' class='btn-iud'>✕</button>";
                            print "<input name='tidg".$fila."' type='hidden' value='".$fila."'></td></tr>";                                           
                            $tidgasto= $_POST['tidg'.$fila];                             

                            if (isset ($_POST['delg'.$fila])){  
                                $sql7 ="delete from gasto where idgasto = '".$tidgasto."'";
                                $resultado7 = mysqli_query($conn, $sql7);  

                                echo '<meta http-equiv=refresh content="0">';
                            }
                        }
                    print "</table>";
                echo "</form>";  
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