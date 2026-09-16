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
    $idaten = $_GET['atencion'];
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
        <div class="index-form-login">            
            <button id="bt-div-1" name = "bt-div-1" class="btn-universal"><span class="icon icon-file-text"></span></button>            
        </div>
        <section id="div1">
            <div class="fila">
                <div class="columna">
                    <?php 
                    $sql1="SELECT paciente.*, atencion.*,  TIMESTAMPDIFF(YEAR,fecnpaciente,CURDATE()) AS edad FROM paciente INNER JOIN atencion ON atencion.idpaciente = paciente.idpaciente WHERE idatencion = '".$idaten."'";
                    $resultado1 = mysqli_query($conn, $sql1);
                    if ($row1 = mysqli_fetch_row($resultado1)){ } 
                    $sql2="SELECT rescuest FROM cuestionario WHERE idatencion = '".$idaten."' and precuest = 'Motivo de la Consulta'";
                    $resultado2 = mysqli_query($conn, $sql2);
                    if ($row2 = mysqli_fetch_row($resultado2)){ }
                        print "<table class = 'TablaB'>";
                            print "<tr><td><b> Contraseña:</b>  $row1[9]</td></tr>";
                            print "<tr><td><b> Fecha de Atención:</b>  $row1[11]</td></tr>";
                            print "<tr><td><b> NIT/DPI:</b>  $row1[1]</td></tr>";
                            print "<tr><td><b> Nombre:</b>  $row1[2]</td></tr>";
                            print "<tr><td><b> Fecha de Nacimiento:</b>  $row1[3]   <b>Edad:</b>  $row1[14]  Años</td></tr>";
                            print "<tr><td><b> Domicilio:</b>  $row1[4]</td></tr>";
                            print "<tr><td><b> Telefono:</b>  $row1[5]</td></tr>";
                            print "<tr><td><b> Referencia:</b>  $row1[7]</td></tr>";
                            print "<tr><td><b> Telefono Referencia:</b>  $row1[8]</td></tr>";
                            print "<tr><td><b> A que se dedica:</b>  $row1[6]</td></tr>";
                            print "<tr><td><b> Motivo de la Consulta:</b>  $row2[0]</td></tr>";
                            $sql4="SELECT precuest, rescuest FROM cuestionario WHERE idatencion = '".$idaten."' ORDER by idcuest asc LIMIT 16,8";
                            $resultado4 = mysqli_query($conn, $sql4);
                            while ($row4 = mysqli_fetch_array($resultado4)){
                                print "<tr><td><b> {$row4['precuest']}</b>  {$row4['rescuest']}</tr>";
                            }
                        print "</table>";
                    ?>
                </div>
                <div class="columna">
                    <?php 
                        print "<table class = 'TablaB'>";
                            $sql3="SELECT precuest, rescuest FROM cuestionario WHERE idatencion = '".$idaten."' ORDER by idcuest asc LIMIT 0,15";
                            $resultado3 = mysqli_query($conn, $sql3);
                            while ($row3 = mysqli_fetch_array($resultado3)){
                                print "<tr><td width = '85%'><b> {$row3['precuest']}</b></td><td>  {$row3['rescuest']}</td></tr>";
                            }
                            $sql5="SELECT producto.descproducto FROM producto INNER JOIN detallereceta ON detallereceta.idproducto = producto.idproducto WHERE detallereceta.idreceta = '".$idaten."' ";
                            $resultado5 = mysqli_query($conn, $sql5);
                            print "<tr><td colspan='2'><b> Medicamento:</b>   ";
                            while ($row5 = mysqli_fetch_array($resultado5)){ 
                                print "{$row5['descproducto']}    ";
                                } 
                            print "</td></tr>";
                            $sql6="SELECT fecita FROM cita WHERE idatencion = '".$idaten."'";
                            $resultado6 = mysqli_query($conn, $sql6);
                            if ($row6 = mysqli_fetch_row($resultado6)){ }
                            setlocale(LC_TIME, "spanish.utf8");               
                            $fechacita= strftime("%A, %d de %B de %Y  a partir de las %H:%M horas ", strtotime($row6[0]));
                            print "<tr><td colspan = '2'><b>Proxima Cita:   </b>$fechacita</td></tr>";
                        print "</table>";
                    ?>
                </div>
            </div>
            <div class="fila">
                <div class="columna">
                    <?php
                        print "<b>AUTO REFRACTÓMETRO</b>";
                        $sql7="SELECT ojograd, esferagrad, cilindrograd, ejegrad, dipgrad, addgrad FROM graduacion WHERE idatencion = '".$idaten."' AND usuariograd = 'REFRACTÓMETRO'";
                        $resultado7 = mysqli_query($conn, $sql7);
                        print "<table class='TablaG'>";                         
                            print "<tr>";
                            print "<td>OJO</td>";
                            print "<td>ESF</td>";
                            print "<td>CIL</td>"; 
                            print "<td>EJE</td>";
                            print "<td>DIP</td>";
                            print "<td>ADD</td>";
                            print "</tr>";
                            while ($row7 = mysqli_fetch_assoc($resultado7)){
                                print "<tr>";
                                    foreach ($row7 as $item7){
                                        print "<td>".($item7!==NULL ?htmlentities($item7):"&nbsp;")."</td>";
                                    }
                                    print "</tr>";
                                }
                        print "</table>";
                        print "<b>LENSOMETRÍA</b>";
                        $sql8="SELECT ojograd, esferagrad, cilindrograd, ejegrad, dipgrad, addgrad FROM graduacion WHERE idatencion = '".$idaten."' AND usuariograd = 'LENSOMETRÍA'";
                        $resultado8 = mysqli_query($conn, $sql8);
                        print "<table class='TablaG'>";                         
                            print "<tr>";
                            print "<td>OJO</td>";
                            print "<td>ESF</td>";
                            print "<td>CIL</td>"; 
                            print "<td>EJE</td>";
                            print "<td>DIP</td>";
                            print "<td>ADD</td>";
                            print "</tr>";
                            while ($row8 = mysqli_fetch_assoc($resultado8)){
                                print "<tr>";
                                    foreach ($row8 as $item8){
                                        print "<td>".($item8!==NULL ?htmlentities($item8):"&nbsp;")."</td>";
                                    }
                                    print "</tr>";
                                }
                        print "</table>";     
                    ?>
                </div>
                <div class="columna">
                    <?php
                        print "<b>GRADUACIÓN TOTAL</b>";
                        $sql9="SELECT ojograd, esferagrad, cilindrograd, ejegrad, dipgrad, addgrad, avsc, avcc FROM graduacion WHERE idatencion = '".$idaten."' AND usuariograd = 'ESPECIALISTA'";
                        $resultado9 = mysqli_query($conn, $sql9);
                        print "<table class='TablaG'>";                         
                            print "<tr>";
                            print "<td>OJO</td>";
                            print "<td>ESF</td>";
                            print "<td>CIL</td>"; 
                            print "<td>EJE</td>";
                            print "<td>DIP</td>";
                            print "<td>ADD</td>";  
                            print "<td>AVSC</td>"; 
                            print "<td>AVCC</td>"; 
                            print "</tr>";
                                while ($row9 = mysqli_fetch_assoc($resultado9)){
                                    print "<tr>";
                                    foreach ($row9 as $item9){
                                        print "<td>".($item9!==NULL ?htmlentities($item9):"&nbsp;")."</td>";
                                    }
                                    print "</tr>";
                                }
                            $sql10="SELECT producto.descproducto FROM producto INNER JOIN ubicacion ON ubicacion.idproducto = producto.idproducto
                                    INNER JOIN detalleventa ON detalleventa.idubicacion = ubicacion.idubicacion
                                    WHERE detalleventa.idventa = '".$idaten."' AND producto.tipoproducto = 'LENTE';";
                            $resultado10 = mysqli_query($conn, $sql10);
                            if ($row10 = mysqli_fetch_row($resultado10)){ }        
                            print "<tr><td> MATERIAL:</td>";
                            print "<td colspan = '7'>$row10[0]</td></tr><br>";
                            $sql17="SELECT * FROM venta WHERE idventa = '".$idaten."'";
                            $resultado17= mysqli_query($conn, $sql17);
                            if ($row17 = mysqli_fetch_row($resultado17)){ }
                            print "<tr><td> OBSERVACION:</td>";
                            print "<td colspan = '7'>$row17[2]</td></tr><br>";                            
                        print "</table>";
                    ?>
                </div>            
            </div> 
        </section>
        <section id="div2">
            <div class="fila">                   
                <div class="columna">
                <h3>GRADUACIÓN TOTAL</h3>
                    <form name="form1" method="post" action="">
                    <table class='TablaG'>                    
                        <tr><td>OJO</td><td>ESF</td><td>CIL</td><td>EJE</td><td>DIP</td><td>ADD</td><td>AVSC</td><td>AVCC</td></tr>
                        <tr>
                            <td>DER</td>
                            <td><input class="decorar-input" name="desf" type="text"></td>
                            <td><input class="decorar-input" name="dcil" type="text"></td>   
                            <td><input class="decorar-input" name="deje" type="text"></td>   
                            <td><input class="decorar-input" name="ddip" type="text"></td>   
                            <td><input class="decorar-input" name="dadd" type="text"></td>
                            <td><input class="decorar-input" name="davsc" type="text"></td>
                            <td><input class="decorar-input" name="davcc" type="text"></td>
                        </tr>
                        <tr>
                            <td>IZQ</td>
                            <td><input class="decorar-input" name="iesf" type="text"></td>
                            <td><input class="decorar-input" name="icil" type="text"></td>   
                            <td><input class="decorar-input" name="ieje" type="text"></td>   
                            <td><input class="decorar-input" name="idip" type="text"></td>   
                            <td><input class="decorar-input" name="iadd" type="text"></td>
                            <td><input class="decorar-input" name="iavsc" type="text"></td>
                            <td><input class="decorar-input" name="iavcc" type="text"></td>
                        </tr>
                        <tr><td colspan="8"><input class="decorar-input" name="tlente" type="text" id="tlente" placeholder="Escriba y Elija un Material" required></td></tr>
                        <div id="nlente"></div> 
                        <tr><td colspan="8"><input class="decorar-input" name="tgla" type="text" placeholder="Glaucoma"></td></tr>
                        <tr><td colspan="8"><input class="decorar-input" name="tpat" type="text" placeholder="Patología"></td></tr> 
                        <tr>
                            <td colspan="4"><input class="decorar-input" name="tpioi" type="text" placeholder="PIO Ojo Derecho"></td>
                            <td colspan="4"><input class="decorar-input" name="tpiod" type="text" placeholder="PIO Ojo Izquierdo"></td>
                        </tr>
                    </table>
                    <div id="botones"><button type="submit" name="insertesp" class="btn-universal" id="insertesp"><span class="icon icon-checkmark"></span></button></div>
                </form>                
                <?php
                $desf = $_POST['desf'];
                $dcil = $_POST['dcil'];
                $deje = $_POST['deje'];
                $ddip = $_POST['ddip'];
                $dadd = $_POST['dadd']; 
                $davsc = $_POST['davsc'];
                $davcc = $_POST['davcc'];
                $iesf = $_POST['iesf'];
                $icil = $_POST['icil'];
                $ieje = $_POST['ieje'];
                $idip = $_POST['idip'];
                $iadd = $_POST['iadd'];
                $iavsc = $_POST['iavsc'];
                $iavcc = $_POST['iavcc'];
                $gla = $_POST['tgla'];
                $pat = $_POST['tpat'];  
                $pioi = $_POST['tpioi'];
                $piod = $_POST['tpiod'];
                $idpro = $_POST['tidpro'];
                $ppro = $_POST['tppro'];
                $mlente = $_POST['tlente'];
                
                if (isset ($_POST['insertesp'])){
                    if ($ussera == NULL or $ussera == 0){
                                print "<script> alert('TIENES QUE INICIAR SESION'); location.href = 'index.php'; </script>";
                    }
                    else {
                        $sql5="SELECT idbitacora FROM bitacora WHERE formbitacora = 'GRADUACIÓN DE ESPECIALISTA CONTRASEÑA ".$idaten."' AND sentbitacora = 'INSERT'";
                        $resultado5 = mysqli_query($conn, $sql5);
                        if($row5 = mysqli_fetch_array($resultado5)){ $idbitacora = $row5["idbitacora"]; }

                        $sql6="SELECT idubicacion FROM ubicacion WHERE idproducto = '".$idpro."' AND idtienda = 1";
                        $resultado6 = mysqli_query($conn, $sql6);
                        if($row6 = mysqli_fetch_array($resultado6)){ $idubicacion = $row6["idubicacion"]; }

                        if ($idbitacora == NULL) {                            
                            if ($idpro != NULL) {
                                date_default_timezone_set('America/Guatemala');                          
                                $fechagrad = date('Y-m-d');

                                $sql1 ="insert into graduacion values (NULL, '".$idaten."', '".$fechagrad."','ESPECIALISTA', 'DERECHO', '".$desf."', '".$dcil."', '".$deje."', '".$ddip."', '".$dadd."', '".$davsc."', '".$davcc."'),
                                        (NULL, '".$idaten."', '".$fechagrad."','ESPECIALISTA', 'IZQUIERDO', '".$iesf."', '".$icil."', '".$ieje."', '".$idip."', '".$iadd."', '".$iavsc."', '".$iavcc."')";
                                $resultado1 = mysqli_query($conn, $sql1);
                                $sql2 ="insert into cuestionario values
                                        (NULL, '".$idaten."', 'Material recetado por especialista', '".$mlente."', '".$fechagrad."'),
                                        (NULL, '".$idaten."', 'Glaucoma', '".$gla."', '".$fechagrad."'),
                                        (NULL, '".$idaten."', 'Patologia', '".$pat."', '".$fechagrad."'),
                                        (NULL, '".$idaten."', 'Presión Intraocular', 'Ojo Derecho ".$pioi.", Ojo Izquierdo ".$piod."', '".$fechagrad."')";
                                $resultado2 = mysqli_query($conn, $sql2);                                

                                $sql4 ="insert into detalleventa values ('NULL', '".$idaten."', '".$idubicacion."', '1', '".$ppro."', '0')";
                                $resultado4 = mysqli_query($conn, $sql4);
                                
                                $sql3 ="insert into bitacora values ('NULL', '".$ussera."', 'GRADUACIÓN DE ESPECIALISTA CONTRASEÑA ".$idaten."', 'INSERT', '".$fechabitacora."')";
                                $resultado3 = mysqli_query($conn, $sql3);

                                echo '<div class="color-arriba"><h3>GRADUACIÓN DE ESPECIALISTA GUARDADA CORRECTAMENTE</h3></div>';
                            }
                            else {
                                print "<script> alert('DEBE ELEGIR UN MATERIAL DE LA LISTA');</script>";
                            }
                        }
                        else{
                            print "<script> alert('ESTA CONTRASEÑA YA TIENE GRADUACIÓN DE ESPECIALISTA');</script>";
                        }
                    }
                }                
                ?>                
                </div> 
                <div class="columna">
                    <h3>AUTO REFRACTÓMETRO</h3>
                    <?php                
                    $sql7="SELECT ojograd, esferagrad, cilindrograd, ejegrad, dipgrad, addgrad FROM graduacion WHERE idatencion = '".$idaten."' AND usuariograd = 'REFRACTÓMETRO'";
                    $resultado7 = mysqli_query($conn, $sql7);
                    print "<table class='TablaG'>";                         
                            print "<tr>";
                            print "<td>OJO</td>";
                            print "<td>ESF</td>";
                            print "<td>CIL</td>"; 
                            print "<td>EJE</td>";
                            print "<td>DIP</td>";
                            print "<td>ADD</td>";                            
                            print "</tr>";
                            while ($row7 = mysqli_fetch_assoc($resultado7)){
                                print "<tr>";
                                    foreach ($row7 as $item7){
                                        print "<td>".($item7!==NULL ?htmlentities($item7):"&nbsp;")."</td>";
                                    }
                                    print "</tr>";
                                }
                        print "</table>";
                        print "<b>LENSOMETRÍA</b>";
                        $sql8="SELECT ojograd, esferagrad, cilindrograd, ejegrad, dipgrad, addgrad FROM graduacion WHERE idatencion = '".$idaten."' AND usuariograd = 'LENSOMETRÍA'";
                        $resultado8 = mysqli_query($conn, $sql8);
                        print "<table class='TablaG'>";                         
                            print "<tr>";
                            print "<td>OJO</td>";
                            print "<td>ESF</td>";
                            print "<td>CIL</td>"; 
                            print "<td>EJE</td>";
                            print "<td>DIP</td>";
                            print "<td>ADD</td>";
                            print "</tr>";
                            while ($row8 = mysqli_fetch_assoc($resultado8)){
                                print "<tr>";
                                    foreach ($row8 as $item8){
                                        print "<td>".($item8!==NULL ?htmlentities($item8):"&nbsp;")."</td>";
                                    }
                                    print "</tr>";
                                }
                        print "</table>";   

                        print "<br><h3>GRADUACIÓN TOTAL</h3>";
                        $sql15="SELECT ojograd, esferagrad, cilindrograd, ejegrad, dipgrad, addgrad, avsc, avcc FROM graduacion WHERE idatencion = '".$idaten."' AND usuariograd = 'ESPECIALISTA'";
                        $resultado15 = mysqli_query($conn, $sql15);
                        print "<table class='TablaG'>";                         
                            print "<tr>";
                            print "<td>OJO</td>";
                            print "<td>ESF</td>";
                            print "<td>CIL</td>"; 
                            print "<td>EJE</td>";
                            print "<td>DIP</td>";
                            print "<td>ADD</td>";
                            print "<td>AVSC</td>"; 
                            print "<td>AVCC</td>"; 
                            print "</tr>";
                                while ($row15 = mysqli_fetch_assoc($resultado15)){
                                    print "<tr>";
                                    foreach ($row15 as $item15){
                                        print "<td>".($item15!==NULL ?htmlentities($item15):"&nbsp;")."</td>";
                                    }
                                    print "</tr>";
                                }
                        $sql16="SELECT producto.descproducto FROM producto INNER JOIN ubicacion ON ubicacion.idproducto = producto.idproducto
                                INNER JOIN detalleventa ON detalleventa.idubicacion = ubicacion.idubicacion
                                WHERE detalleventa.idventa = '".$idaten."' AND producto.tipoproducto = 'LENTE';";
                        $resultado16 = mysqli_query($conn, $sql16);
                        if ($row16 = mysqli_fetch_row($resultado16)){ }        
                        print "<tr><td> MATERIAL:</td>";
                        print "<td colspan = '7'>$row16[0]</td></tr>";
                        $sql18="SELECT * FROM venta WHERE idventa = '".$idaten."'";
                        $resultado18= mysqli_query($conn, $sql18);
                        if ($row18 = mysqli_fetch_row($resultado18)){ }
                            print "<tr><td> OBSERVACION:</td>";
                            print "<td colspan = '7'>$row18[2]</td></tr><br>";
                        print "</table>";    
                ?>
                </div>             
            </div>
            <?php
                $sql11="SELECT * from receta WHERE idreceta = '".$idaten."'";
                $resultado11 = mysqli_query($conn, $sql11);
                if($row11 = mysqli_fetch_array($resultado11)){ $idrec = $row11["idreceta"];}            
                date_default_timezone_set('America/Guatemala');                          
                $fechare = date('Y-m-d');

                if ($idrec ==NULL){
                    $sql12 ="insert into receta values ('".$idaten."', '".$row1[2]."', '".$fechare."')";
                    $resultado12 = mysqli_query($conn, $sql12);
                } 
            ?>        
            <div class="fila"> 
                <div class="columna">
                    <form name="form2" method="post" action=""> 
                        <h3>MEDICAMENTO</h3>
                        <input class="decorar-input" name="tmedic" type="text" id="tmedic" placeholder="Escriba y Elija un Medicamento" required>
                        <input class="decorar-input" name="tcanm" type="tnumber" id="tcanm" placeholder="Cantidad de presentaciones" required>                    
                        <div><br></div><div id="nmedic" class="contenedor-transparente"></div>
                        <h3>DOSIS</h3>
                        <select name="tcan" class="decorar-input" required="">
                            <option value="" disabled selected>Cantidad Dosis</option>
                            <option>1 Gota</option>
                            <option>2 Gotas</option> 
                            <option>1 Pastilla</option>
                            <option>2 Pastillas</option> 
                            <option>1 Ampolla</option> 
                            <option>2 Ampollas</option>
                            <option>Aplicar</option>
                        </select>
                        <select name="thora" class="decorar-input" required="">
                            <option value="" disabled selected>A cada cuanto</option>
                            <option>Hora</option>
                            <option>2 Horas</option>
                            <option>3 Horas</option>
                            <option>4 Horas</option>
                            <option>6 Horas</option> 
                            <option>8 Horas</option>
                            <option>12 Horas</option> 
                            <option>Dia</option>
                            <option>2 Dias</option> 
                            <option>Noche</option> 
                            <option>Semana</option>
                        </select>
                        <select name="tdias" class="decorar-input" required="">
                            <option value="" disabled selected>Durante</option>
                            <option>Dure el Medicamento</option>
                            <option>2 dias</option> 
                            <option>3 dias</option>
                            <option>5 Dias</option>
                            <option>6 Dias</option>
                            <option>7 Dias</option>
                            <option>10 Dias</option>
                            <option>1 Semana</option>
                            <option>2 Semanas</option>
                            <option>1 Mes</option>
                            <option>2 Meses</option>
                            <option>3 Meses</option>
                            <option>De por vida</option>                       
                        </select>         
                        <div id="botones"><button type="submit" name="insertmed" class="btn-universal" id="insertmed"><span class="icon icon-checkmark"></span></button></div>            
                    </form>                    
                    <?php
                    $idmed = $_POST['tidpro2'];
                    $canm = $_POST['tcanm'];
                    $can = $_POST['tcan'];
                    $hora = $_POST['thora'];
                    $dia = $_POST['tdias'];
                    $pmed = $_POST['tppro2'];
                    $dosis = $can.' a cada '.$hora.' durante '.$dia; 
                    $fcita = $_POST['tfec'];
                    $decita = $_POST['tdesc'];

                    if (isset ($_POST['insertmed'])){ 
                        if ($ussera == NULL or $ussera == 0){
                                print "<script> alert('TIENES QUE INICIAR SESION'); location.href = 'index.php'; </script>";
                        }
                        else {
                            $sql4="SELECT idubicacion, cantubicacion, idproducto FROM ubicacion WHERE idproducto = '".$idmed."' AND idtienda = '".$tienda."'";
                            $resultado4 = mysqli_query($conn, $sql4);
                                if($row4 = mysqli_fetch_array($resultado4)){ $idubicacion = $row4["idubicacion"]; $cantpro = $row4["cantubicacion"]; }

                                if ($idmed != NULL) { 
                                    if ($cantpro != 0){
                                        $sql5 ="insert into detallereceta values ('NULL', '".$idaten."', '".$idmed."', '".$dosis."', '".$canm."')";
                                        $resultado5 = mysqli_query($conn, $sql5); 

                                        $sql6 ="insert into detalleventa values ('NULL', '".$idaten."', '".$idubicacion."', '".$canm."', '".$pmed."', '0')";
                                        $resultado6 = mysqli_query($conn, $sql6);                                       

                                        $sql13 ="UPDATE ubicacion SET cantubicacion = (cantubicacion - 1) WHERE idubicacion = '".$idubicacion."'";
                                        $resultado13 = mysqli_query($conn, $sql13);

                                        $sql14 ="update atencion SET estatencion = 'ESPECIALISTA' where idatencion = '".$idaten."'";
                                        $resultado14 = mysqli_query($conn, $sql14);
                                        
                                        $sql7 ="insert into bitacora values ('NULL', '".$ussera."', 'ESPECIALISTA AGREGÓ MEDICAMENTO ".$idmed." CONTRASEÑA ".$idaten."', 'INSERT', '".$fechabitacora."')";
                                        $resultado7 = mysqli_query($conn, $sql7);

                                        echo '<div class="color-arriba"><h3>MEDICAMENTO AGREGADO CORRECTAMENTE</h3></div>';
                                    }
                                    else {
                                        print "<script> alert('NO PUEDE ASIGNAR ESTE MEDICAMENTO NO HAY EXISTENCIAS EN LA SUCURSAL');</script>";  
                                    }
                                }
                                else{
                                     print "<script> alert('DEBE ELEGIR UN MEDICAMENTO');</script>";
                                }
                        }
                    }                     
                    ?>
                </div>
                <div class="columna"> 
                     <form name="form3" method="post" action=""> 
                        <h3>PROXIMA CITA</h3>
                        <input class="decorar-input" name="tfec" type="datetime-local" id="tfec" required >
                        <input class="decorar-input" name="tdesc" type="text" id="tdesc" placeholder="Descripción de la Cita" required>                    
                        <div id="botones"><button type="submit" name="insertci" class="btn-universal" id="insertci"><span class="icon icon-checkmark"></span></button></div>            
                    </form>
                    <?php 
                    if (isset ($_POST['insertci'])){ 
                        if ($ussera == NULL or $ussera == 0){
                                print "<script> alert('TIENES QUE INICIAR SESION'); location.href = 'index.php'; </script>";
                        }
                        else {
                            $sql9 ="insert into cita values ('NULL', '".$idaten."', '".$decita."', '".$fcita."', '', 'PENDIENTE')";
                            $resultado9 = mysqli_query($conn, $sql9);
                            
                            $sql8 ="insert into bitacora values ('NULL', '".$ussera."', 'ESPECIALISTA AGENDÓ CITA CONTRASEÑA ".$idaten."', 'INSERT', '".$fechabitacora."')";
                            $resultado8 = mysqli_query($conn, $sql8);

                            echo '<div class="color-arriba"><h3>CITA AGREGADA CORRECTAMENTE</h3></div>';
                        }
                    }                    
                    $sql12="SELECT * from receta WHERE idreceta = '".$idaten."'";
                    $resultado12 = mysqli_query($conn, $sql12);
                    if($row12 = mysqli_fetch_array($resultado12)){ } 

                    $sql13="SELECT * from cita WHERE idatencion = '".$idaten."'";
                    $resultado13 = mysqli_query($conn, $sql13);
                    if($row13 = mysqli_fetch_array($resultado13)){ }

                    print "<table class = 'TablaB'>";
                    print "<tr><td width = '70%'><b> Paciente:</b>  $row12[1]</td><td> <b>Fecha:</b>  $row12[2]</td></tr>";
                    print "<tr><td colspan = 2>";
                    echo "<form name='form' method='post' action=''>";
                        print "<table class = 'TablaC'>";
                            print "<tr><td>U</td><td>MEDICAMENTO</td><td>DOSIS</td><td>-</td></tr>";
                            $sql14 ="SELECT detallereceta.iddereceta, detallereceta.cantreceta, producto.descproducto, detallereceta.dosisreceta, detallereceta.idproducto FROM detallereceta INNER JOIN producto
                                    ON producto.idproducto = detallereceta.idproducto WHERE idreceta = '".$idaten."'";
                            $resultado14 = mysqli_query($conn, $sql14);
                            while ($row14 = mysqli_fetch_array($resultado14)){ 
                                $fila = $row14['iddereceta'];
                                print "<tr><td>{$row14['cantreceta']}</td><td>{$row14['descproducto']}</td><td>{$row14['dosisreceta']}</td>";
                                print "<td><button type='submit' name='delm".$fila."' class='btn-iud'>✕</button>";
                                print "<input name='tiddr".$fila."' type='hidden' value='".$fila."'></td></tr>";                                           
                                $tiddreceta= $_POST['tiddr'.$fila];                             

                                if (isset ($_POST['delm'.$fila])){  
                                    $sql4="SELECT idubicacion, cantubicacion, idproducto FROM ubicacion WHERE idproducto = '".$row14['idproducto']."' AND idtienda = '".$tienda."'";
                                    $resultado4 = mysqli_query($conn, $sql4);
                                    if($row4 = mysqli_fetch_array($resultado4)){ $idubicacion = $row4["idubicacion"]; $cantpro = $row4["cantubicacion"]; }    
                                    
                                    $sql16="SELECT iddventa FROM `detalleventa` WHERE idventa = '".$idaten."' AND idubicacion = '".$idubicacion."'";
                                    $resultado16 = mysqli_query($conn, $sql16);
                                    if($row16 = mysqli_fetch_array($resultado16)){ $iddventa = $row16["iddventa"]; }
                                    
                                    $sql7 ="delete from detallereceta where iddereceta = '".$tiddreceta."'";
                                    $resultado7 = mysqli_query($conn, $sql7);  

                                    $sql13 ="UPDATE ubicacion SET cantubicacion = (cantubicacion + 1) WHERE idubicacion = '".$idubicacion."'";
                                    $resultado13 = mysqli_query($conn, $sql13);
                                    
                                    $sql8 ="delete from detalleventa where iddventa = '".$iddventa."'";
                                    $resultado8 = mysqli_query($conn, $sql8);
                                    
                                    $sql15 ="insert into bitacora values ('NULL', '".$ussera."', 'ESPECIALISTA ELIMINÓ MEDICAMENTO  ".$row14['descproducto']." , CONTRASEÑA ".$idaten."', 'DELETE', '".$fechabitacora."')";
                                    $resultado15 = mysqli_query($conn, $sql15);

                                echo '<meta http-equiv=refresh content="0">';
                            }
                        }
                    print "</table>";
                echo "</form>";  
                    /*$sql14="SELECT detallereceta.cantreceta, producto.descproducto, detallereceta.dosisreceta 
                            FROM detallereceta INNER JOIN producto ON producto.idproducto = detallereceta.idproducto
                            WHERE detallereceta.idreceta =  '".$idaten."'";
                    $resultado14 = mysqli_query($conn, $sql14);
                    while ($row14 = mysqli_fetch_array($resultado14)){
                        print "{$row14['cantreceta']},  {$row14['descproducto']},  {$row14['dosisreceta']}<br>";
                    }     */          
                    print "</td></tr>";
                    print "<tr><td colspan = 2><b> Proxima Cita:</b>  $row13[3]</td>";
                    print "</table>"; 
                ?>
                <br>            
                <a class='btn-universal' href="printreceta.php?perfil=<?php echo $ussera; ?>&atencion=<?php echo $idaten ?>" target="_blank"><span class="icon icon-printer"></span></a>
                </div>
            </div>
            <div class="fila">
                <div class="columna">
                    <h3>KERATOMETRÍA (Aplica solo para MEGAOPTICA)</h3>
                    <form name="form3" method="post" action="">
                        <table>
                            <tr>
                                <td><input class="decorar-input" name="tkoi" type="text" placeholder="Ojo Derecho"></td>
                                <td><input class="decorar-input" name="tkod" type="text" placeholder="Ojo Izquierdo"></td>
                                <td><button type="submit" name="insertk" class="btn-universal" id="insertk"><span class="icon icon-checkmark"></span></button></td>
                            </tr>
                        </table>
                    </form>                
                <?php
                $koi = $_POST['tkoi'];
                $kod = $_POST['tkod'];
                
                if (isset ($_POST['insertk'])){
                    if ($ussera == NULL or $ussera == 0){
                                print "<script> alert('TIENES QUE INICIAR SESION'); location.href = 'index.php'; </script>";
                        }
                    else {                    
                        $sql5="SELECT idbitacora FROM bitacora WHERE formbitacora = 'ESPECIALISTA INGRESO KERATOMETRIA CONTRASEÑA ".$idaten."' AND sentbitacora = 'INSERT'";
                        $resultado5 = mysqli_query($conn, $sql5);
                        if($row5 = mysqli_fetch_array($resultado5)){ $idbitacora = $row5["idbitacora"]; }

                        $sql6="SELECT idubicacion FROM ubicacion WHERE idproducto = 'S007' AND idtienda = 1";
                        $resultado6 = mysqli_query($conn, $sql6);
                        if($row6 = mysqli_fetch_array($resultado6)){ $idubicacion = $row6["idubicacion"]; }

                        if ($idbitacora == NULL) {                            
                            if ($tienda == 1) {
                                date_default_timezone_set('America/Guatemala');                          
                                $fechagrad = date('Y-m-d');
                                $sql2 ="insert into cuestionario values                                   
                                        (NULL, '".$idaten."', 'Keratrometría', 'Ojo Derecho ".$koi.", Ojo Izquierdo ".$kod."', '".$fechagrad."')";
                                $resultado2 = mysqli_query($conn, $sql2);                              

                                $sql4 ="insert into detalleventa values ('NULL', '".$idaten."', '".$idubicacion."', '1', '800', '0')";
                                $resultado4 = mysqli_query($conn, $sql4);
                                
                                $sql3 ="insert into bitacora values ('NULL', '".$ussera."', 'ESPECIALISTA INGRESO KERATOMETRIA CONTRASEÑA ".$idaten."', 'INSERT', '".$fechabitacora."')";
                                $resultado3 = mysqli_query($conn, $sql3);

                                echo '<div class="color-arriba"><h3>KERATOMETRÍA GUARDADA CORRECTAMENTE</h3></div>';
                            }
                            else {
                                print "<script> alert('TU SUCURSAL NO PUEDE LLENAR ESTOS CAMPOS');</script>";
                            }
                        }
                        else{
                            print "<script> alert('ESTA CONTRASEÑA YA TIENE KERATOMETRÍA DE ESPECIALISTA');</script>";
                        }
                    }
                }                
                ?>
                    <br>
                    <h3>BIOMETRÍA (Aplica solo para MEGAOPTICA)</h3>
                    <form name="form3" method="post" action="">
                        <table>
                            <tr>
                                <td><input class="decorar-input" name="tboi" type="text" placeholder="Ojo Derecho"></td>
                                <td><input class="decorar-input" name="tbod" type="text" placeholder="Ojo Izquierdo"></td>
                                <td><button type="submit" name="insertb" class="btn-universal" id="insertb"><span class="icon icon-checkmark"></span></button></td>
                            </tr>
                        </table>
                    </form>                
                <?php
                $boi = $_POST['tboi'];
                $bod = $_POST['tbod'];
                
                if (isset ($_POST['insertb'])){
                    if ($ussera == NULL or $ussera == 0){
                                print "<script> alert('TIENES QUE INICIAR SESION'); location.href = 'index.php'; </script>";
                    }
                    else {    
                        $sql5="SELECT idbitacora FROM bitacora WHERE formbitacora = 'ESPECIALISTA INGRESO BIOMETRIA CONTRASEÑA ".$idaten."' AND sentbitacora = 'INSERT'";
                        $resultado5 = mysqli_query($conn, $sql5);
                        if($row5 = mysqli_fetch_array($resultado5)){ $idbitacora = $row5["idbitacora"]; }

                        $sql6="SELECT idubicacion FROM ubicacion WHERE idproducto = 'S008' AND idtienda = 1";
                        $resultado6 = mysqli_query($conn, $sql6);
                        if($row6 = mysqli_fetch_array($resultado6)){ $idubicacion = $row6["idubicacion"]; }

                        if ($idbitacora == NULL) {                            
                            if ($tienda == 1) {
                                date_default_timezone_set('America/Guatemala');                          
                                $fechagrad = date('Y-m-d');
                                $sql2 ="insert into cuestionario values                                   
                                        (NULL, '".$idaten."', 'Biometría', 'Ojo Derecho ".$boi.", Ojo Izquierdo ".$bod."', '".$fechagrad."')";
                                $resultado2 = mysqli_query($conn, $sql2);

                                $sql4 ="insert into detalleventa values ('NULL', '".$idaten."', '".$idubicacion."', '1', '800', '0')";
                                $resultado4 = mysqli_query($conn, $sql4);
                                
                                $sql3 ="insert into bitacora values ('NULL', '".$ussera."', 'ESPECIALISTA INGRESO BIOMETRIA CONTRASEÑA ".$idaten."', 'INSERT', '".$fechabitacora."')";
                                $resultado3 = mysqli_query($conn, $sql3);                                

                                echo '<div class="color-arriba"><h3>BIOMETRÍA GUARDADA CORRECTAMENTE</h3></div>';
                            }
                            else {
                                print "<script> alert('TU SUCURSAL NO PUEDE LLENAR ESTOS CAMPOS');</script>";
                            }
                        }
                        else{
                            print "<script> alert('ESTA CONTRASEÑA YA TIENE BIOMETRÍA DE ESPECIALISTA');</script>";
                        }
                    }
                }                
                ?>
                    
                </div>
                <div class="columna">
                    <h3>OBSERVACIONES EN CONTRASEÑA</h3>
                    <form name="form1" method="post" action="">                      
                        <textarea class="decorar-input" name="tobsv"  id="tobsv" placeholder="Utilice este espacio, cuando se haya equivocado en las graduaciones o se tenga que cambiar las graduaciones de los lentes del cliente" required rows="5"></textarea>
                        <div id="botones">
                           <button type="submit" name="updv" class="btn-universal" id="upda"><span class="icon icon-pencil"></span></button>  
                       </div>
                    </form>
                    <?php           
                    $obsv= $_POST['tobsv'];  

                    if (isset ($_POST['updv'])){ 
                        if ($ussera == NULL or $ussera == 0){
                                print "<script> alert('TIENES QUE INICIAR SESION'); location.href = 'index.php'; </script>";
                        }
                        else {
                            $sql5 ="update venta SET obsventa = '".$obsv."' where idventa = '".$idaten."' ";
                            $resultado5 = mysqli_query($conn, $sql5); 
                            
                            $sql4 ="insert into bitacora values ('NULL', '".$ussera."', 'ESPECIALISTA AGREGÓ OBSERVACIÓN CONTRASEÑA ".$idaten."', 'UPDATE', '".$fechabitacora."')";
                            $resultado4 = mysqli_query($conn, $sql4); 
                            
                            echo '<div class="color-arriba"><h3>LA OBSERVACIÓN FUE AGREGADA</h3></div>';   
                        }
                    }             

                    ?>
                </div>                    
            </div>
        </section>
	<script type="text/javascript" src="js/jquery-3.6.1.min.js"></script> 
        <script type="text/javascript" src="js/jquery-ui.js"></script>
        <script type="text/javascript" src="js/misfunciones.js"></script>
        <script type="text/javascript" src="js/versection.js"></script>     
    </body>
</html>

