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
    $sql1="SELECT paciente.nompaciente FROM paciente INNER JOIN atencion
           ON atencion.idpaciente = paciente.idpaciente WHERE idatencion = '".$idaten."'";
    $resultado1 = mysqli_query($conn, $sql1);
    if($row1 = mysqli_fetch_array($resultado1)){ $nompa = $row1["nompaciente"]; }
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
        <div class="color-arriba"><h2 class ="subtitulo">PACIENTE  <?php echo $nompa; ?></h2></div>        
        <div class="fila">
            <div class="columna">
                <?php
                    $sql2 ="SELECT atencion.idatencion, paciente.*
                    FROM atencion INNER JOIN paciente ON paciente.idpaciente = atencion.idpaciente
                    WHERE atencion.idatencion = '".$idaten."'";
                    $resultado2 = mysqli_query($conn, $sql2);
                    while ($row2 = mysqli_fetch_array($resultado2)){
                        echo "<form name='form' method='post' action=''>";
                            echo "<input name='tndpf' type='text' class='decorar-input' value='{$row2['nitpaciente']}' title='NIT O DPI del Paciente' placeholder = 'NIT o DPI'>";
                            echo "<input name='tnpf' type='text' class='decorar-input' value='{$row2['nompaciente']}' title='Nombre Completo del Paciente' required>";
                            echo "<input name='tfnpf' type='date' class='decorar-input' value='{$row2['fecnpaciente']}' title='Fecha de Nacimiento del Paciente' required>";
                            echo "<input name='tdpf' type='text' class='decorar-input' value='{$row2['dirpaciente']}' title='Dirección Domicilio del Paciente' placeholder='Dirección domiciliar' required>";
                            echo "<input name='ttpf' type='text' class='decorar-input' value='{$row2['telpaciente']}' title='Teléfono del Paciente 8 digitos sin espacio' required>";
                            echo "<input name='tmpf' type='text' class='decorar-input' value='{$row2['motpaciente']}' title='¿A que se dedica?' placeholder='¿A que se dedica?' required>";
                            echo "<input name='trpf' type='text' class='decorar-input' value='{$row2['refpaciente']}' title='Nombre de la Persona como referencia' placeholder='Referencia' required>";
                            echo "<input name='ttrpf' type='text' class='decorar-input' value='{$row2['telrpaciente']}' title='Teléfono de la persona que dejó como referencia,  8 digitos sin espacio' placeholder='Teléfono de la Referencia'>";
                            echo "<input name='tid' type='hidden' value='{$row2['idpaciente']}'>"; 
                            echo "<div id='botones'><button type='submit' name='upd' class='btn-universal'><span class='icon icon-pencil'></span></button></div>";
                        echo "</form>";                        
                    }
                        $tid = $_POST['tid'];
                        $tndpf = $_POST['tndpf'];
                        $tnpf = $_POST['tnpf'];
                        $tfnpf = $_POST['tfnpf'];
                        $tdpf = $_POST['tdpf'];
                        $ttpf = $_POST['ttpf'];
                        $tmpf = $_POST['tmpf'];
                        $trpf = $_POST['trpf'];
                        $ttrpf = $_POST['ttrpf'];               
                
                        if (isset ($_POST['upd'])){ 
                            if ($ussera == NULL or $ussera == 0){
                                print "<script> alert('TIENES QUE INICIAR SESION'); location.href = 'index.php'; </script>";
                            }
                            else {
                                $sql2 ="update paciente SET nitpaciente = '".$tndpf."', nompaciente = '".$tnpf."', fecnpaciente = '".$tfnpf."', dirpaciente = '".$tdpf."', telpaciente = '".$ttpf."', motpaciente = '".$tmpf."', refpaciente = '".$trpf."', telrpaciente = '".$ttrpf."'  where idpaciente = '".$tid."'";
                                $resultado2 = mysqli_query($conn, $sql2);
                            
                                $sql5 ="insert into detalleventa values ('NULL', '".$idaten."', '1', '1', '100', '0')";
                                $resultado5 = mysqli_query($conn, $sql5);
                            
                                $sql3 ="insert into bitacora values ('NULL', '".$ussera."', 'MODIFICACION INFO PACIENTE ".$tnpf." IDCONTRASEÑA  ".$idaten."', 'UPDATE', '".$fechabitacora."')";
                                $resultado3 = mysqli_query($conn, $sql3);
                            
                                echo '<div class="color-arriba"><h3>INFORMACIÓN DEL PACIENTE MODIFICADA CORRECTAMENTE</h3></div>';
                            }
                            
                        }
                ?>               
            </div>
            <div class="columna">
                <form name="form1" method="post" action="">               
                    <table>                        
                        <tr><td><label>¿Es diabético?</label></td><td><label><input name="p1" type="radio" value="SI"> SI</label></td><td><label><input name="p1" type="radio" value="NO" required> NO</label></td></tr>
                        <tr><td><label>¿Padece de Presión Alta?</label></td><td><label><input name="p2" type="radio" value="SI"> SI</label></td><td><label><input name="p2" type="radio" value="NO" required> NO</label></td></tr>
                        <tr><td><label>¿Utiliza Lentes?</label></td><td><label><input name="p3" type="radio" value="SI"> SI</label></td><td><label><input name="p3" type="radio" value="NO" required> NO</label></td></tr>
                        <tr><td><label>¿Algún familiar usa lentes?</label></td><td><label><input name="p4" type="radio" value="SI"> SI</label></td><td><label><input name="p4" type="radio" value="NO" required> NO</label></td></tr>
                        <tr><td><label>¿Ha sufrido algún golpe en la cabeza?</label></td><td><label><input name="p5" type="radio" value="SI"> SI</label></td><td><label><input name="p5" type="radio" value="NO" required> NO</label></td></tr>
                        <tr><td><label>¿Dolor de Ojos?</label></td><td><label><input name="p6" type="radio" value="SI"> SI</label></td><td><label><input name="p6" type="radio" value="NO" required> NO</label></td></tr>
                        <tr><td><label>¿Dolor de Cabeza?</label></td><td><label><input name="p15" type="radio" value="SI"> SI</label></td><td><label><input name="p15" type="radio" value="NO" required> NO</label></td></tr>                    
                        <tr><td><label>¿Ardor de Ojos?</label></td><td><label><input name="p7" type="radio" value="SI"> SI</label></td><td><label><input name="p7" type="radio" value="NO" required> NO</label></td></tr>
                        <tr><td><label>¿Picazón de Ojos?</label></td><td><label><input name="p8" type="radio" value="SI"> SI</label></td><td><label><input name="p8" type="radio" value="NO" required> NO</label></td></tr>
                        <tr><td><label>¿Visión Borrosa de Lejos?</label></td><td><label><input name="p9" type="radio" value="SI"> SI</label></td><td><label><input name="p9" type="radio" value="NO" required> NO</label></td></tr>
                        <tr><td><label>¿Visión Borrosa de Cerca?</label></td><td><label><input name="p10" type="radio" value="SI"> SI</label></td><td><label><input name="p10" type="radio" value="NO" required> NO</label></td></tr>
                        <tr><td><label>¿Molestias por el Sol?</label></td><td><label><input name="p11" type="radio" value="SI"> SI</label></td><td><label><input name="p11" type="radio" value="NO" required> NO</label></td></tr>
                        <tr><td><label>¿Molestias por Cel, TV, Computadoras?</label></td><td><label><input name="p12" type="radio" value="SI"> SI</label></td><td><label><input name="p12" type="radio" value="NO" required> NO</label></td></tr>
                        <tr><td><label>¿Ha sido Operado?</label></td><td><label><input name="p13" type="radio" value="SI"> SI</label></td><td><label><input name="p13" type="radio" value="NO" required> NO</label></td></tr>
                        <tr><td colspan = 3><input class="decorar-input" name="p16" type="text" placeholder="Tipo de Operacion" id="p16" title="Tipo de Operacion que le han realizado al Paciente" >
                        <tr><td colspan = 3><input class="decorar-input" name="p14" type="text" placeholder="¿Motivo de la Consulta?" required   id="p14" title="Motivo de la Consulta" >
                        </td></tr>
                    </table> 
                    <div id="botones"><button type="submit" name="insertcuest" class="btn-universal" id="insertcuest"><span class="icon icon-checkmark"></span></button></div>
                </form>
                 <?php
                $p1= $_POST['p1'];
                $p2= $_POST['p2'];
                $p3= $_POST['p3'];
                $p4= $_POST['p4'];
                $p5= $_POST['p5'];
                $p6= $_POST['p6'];
                $p7= $_POST['p7'];
                $p8= $_POST['p8'];
                $p9= $_POST['p9'];
                $p10= $_POST['p10'];
                $p11= $_POST['p11'];
                $p12= $_POST['p12'];
                $p13= $_POST['p13'];
                $p14= $_POST['p14'];
                $p15= $_POST['p15'];
                $p16= $_POST['p16'];
                
                if (isset ($_POST['insertcuest'])){ 
                    if ($ussera == NULL or $ussera == 0){
                                print "<script> alert('TIENES QUE INICIAR SESION'); location.href = 'index.php'; </script>";
                    }
                    else {
                        $sql5="SELECT idbitacora FROM bitacora WHERE formbitacora = 'CUESTIONARIO CONTRASEÑA ".$idaten."' AND sentbitacora = 'INSERT'";
                        $resultado5 = mysqli_query($conn, $sql5);
                            if($row5 = mysqli_fetch_array($resultado5)){ $idbitacora = $row5["idbitacora"]; }

                            if ($idbitacora == NULL) {                        
                                date_default_timezone_set('America/Guatemala');                          
                                $fechacuest = date('Y-m-d');

                                $sql2 ="insert into cuestionario values
                                        (NULL, '".$idaten."', 'Es diabético', '".$p1."', '".$fechacuest."'),
                                        (NULL, '".$idaten."', 'Padece de Presión Alta', '".$p2."', '".$fechacuest."'),
                                        (NULL, '".$idaten."', 'Utiliza Lentes', '".$p3."', '".$fechacuest."'),
                                        (NULL, '".$idaten."', 'Tiene Familiar con Lentes', '".$p4."', '".$fechacuest."'),
                                        (NULL, '".$idaten."', 'Ha sufrido golpes en la Cabeza', '".$p5."', '".$fechacuest."'),
                                        (NULL, '".$idaten."', 'Tiene dolor de ojos', '".$p6."', '".$fechacuest."'),
                                        (NULL, '".$idaten."', 'Tiene dolor de Cabeza', '".$p15."', '".$fechacuest."'),
                                        (NULL, '".$idaten."', 'Tiene ardor de ojos', '".$p7."', '".$fechacuest."'),
                                        (NULL, '".$idaten."', 'Tiene picazon de ojos', '".$p8."', '".$fechacuest."'),
                                        (NULL, '".$idaten."', 'Tiene visión borrosa de lejos', '".$p9."', '".$fechacuest."'),
                                        (NULL, '".$idaten."', 'Tiene visión borroda de cerca', '".$p10."', '".$fechacuest."'),
                                        (NULL, '".$idaten."', 'Tiene molestias por el sol', '".$p11."', '".$fechacuest."'),
                                        (NULL, '".$idaten."', 'Tiene molestias por Cel, TV y Computadoras', '".$p12."', '".$fechacuest."'),
                                        (NULL, '".$idaten."', 'Ha sido operado', '".$p13."', '".$fechacuest."'),   
                                        (NULL, '".$idaten."', 'Tipo de Operación', '".$p16."', '".$fechacuest."'),
                                        (NULL, '".$idaten."', 'Motivo de la Consulta', '".$p14."', '".$fechacuest."')";
                                $resultado2 = mysqli_query($conn, $sql2);  
                                $sql8 ="update atencion SET estatencion = 'ASESOR' where idatencion = '".$idaten."'";
                                $resultado8 = mysqli_query($conn, $sql8);
                                
                                $sql3 ="insert into bitacora values ('NULL', '".$ussera."', 'CUESTIONARIO CONTRASEÑA ".$idaten."', 'INSERT', '".$fechabitacora."')";
                                $resultado3 = mysqli_query($conn, $sql3);

                                echo '<div class="color-arriba"><h3>CUESTIONARIO AGREGADO CORRECTAMENTE</h3></div>';                           
                            }
                            else{
                                 print "<script> alert('ESTA CONTRASEÑA YA TIENE CUESTIONARIO');</script>";
                            }
                    }
                }                 
            ?> 
            </div> 
        </div>
        <div class="fila">
            <div class="columna">
                <form name="form1" method="post" action="">                
                    <h3>AUTO REFRÁCTOMETRO</h3>
                    <table class='TablaG'>                    
                        <tr><td>OJO</td><td>ESF</td><td>CIL</td><td>EJE</td><td>DIP</td><td>ADD</td></tr>
                        <tr>
                            <td>DERECHO</td>
                            <td><input class="decorar-input" name="desf" type="text"></td>
                            <td><input class="decorar-input" name="dcil" type="text"></td>   
                            <td><input class="decorar-input" name="deje" type="text"></td>   
                            <td><input class="decorar-input" name="ddip" type="text"></td>
                            <td><input class="decorar-input" name="dad" type="text"></td>
                        </tr>
                        <tr>
                            <td>IZQUIERDO</td>
                            <td><input class="decorar-input" name="iesf" type="text"></td>
                            <td><input class="decorar-input" name="icil" type="text"></td>   
                            <td><input class="decorar-input" name="ieje" type="text"></td>   
                            <td><input class="decorar-input" name="idip" type="text"></td>
                            <td><input class="decorar-input" name="iad" type="text"></td>
                        </tr>
                    </table> 
                    <div id="botones"><button type="submit" name="inserteva" class="btn-universal" id="inserteva"><span class="icon icon-checkmark"></span></button></div>
                </form>
                <?php
                $desf = $_POST['desf'];
                $dcil = $_POST['dcil'];
                $deje = $_POST['deje'];
                $ddip = $_POST['ddip'];
                $dad = $_POST['dad'];
                $iesf = $_POST['iesf'];
                $icil = $_POST['icil'];
                $ieje = $_POST['ieje'];
                $idip = $_POST['idip'];
                $iad = $_POST['iad'];
                if (isset ($_POST['inserteva'])){ 
                    if ($ussera == NULL or $ussera == 0){
                                print "<script> alert('TIENES QUE INICIAR SESION'); location.href = 'index.php'; </script>";
                    }
                    else {
                        $sql5="SELECT idbitacora FROM bitacora WHERE formbitacora = 'GRADUACIÓN DE REFRACTÓMETRO CONTRASEÑA ".$idaten."' AND sentbitacora = 'INSERT'";
                        $resultado5 = mysqli_query($conn, $sql5);
                            if($row5 = mysqli_fetch_array($resultado5)){ $idbitacora = $row5["idbitacora"]; }

                            if ($idbitacora == NULL) {                        
                                date_default_timezone_set('America/Guatemala');                          
                                $fechagrad = date('Y-m-d');

                                $sql2 ="insert into graduacion values (NULL, '".$idaten."', '".$fechagrad."','REFRACTÓMETRO', 'DERECHO', '".$desf."', '".$dcil."', '".$deje."', '".$ddip."', '".$dad."', '', ''),
                                        (NULL, '".$idaten."', '".$fechagrad."','REFRACTÓMETRO', 'IZQUIERDO', '".$iesf."', '".$icil."', '".$ieje."', '".$idip."', '".$iad."', '', '')";
                                $resultado2 = mysqli_query($conn, $sql2);                    

                                $sql3 ="insert into bitacora values ('NULL', '".$ussera."', 'GRADUACIÓN DE REFRACTÓMETRO CONTRASEÑA ".$idaten."', 'INSERT', '".$fechabitacora."')";
                                $resultado3 = mysqli_query($conn, $sql3);  

                                echo '<div class="color-arriba"><h3>GRADUACIÓN DE REFRACTÓMETRO INSERTADA CORRECTAMENTE</h3></div>';
                            }
                            else{
                                 print "<script> alert('ESTA CONTRASEÑA YA TIENE GRADUACIÓN DE REFRACTÓMETRO');</script>";
                            }
                        }                   
                }           
                ?> 
                <form name="form2" method="post" action="">                
                    <h3>LENSOMETRÍA</h3>
                    <table class='TablaG'>                    
                        <tr><td>OJO</td><td>ESF</td><td>CIL</td><td>EJE</td><td>DIP</td><td>ADD</td></tr>
                        <tr>
                            <td>DERECHO</td>
                            <td><input class="decorar-input" name="ldesf" type="text"></td>
                            <td><input class="decorar-input" name="ldcil" type="text"></td>   
                            <td><input class="decorar-input" name="ldeje" type="text"></td>   
                            <td><input class="decorar-input" name="lddip" type="text"></td> 
                            <td><input class="decorar-input" name="ldad" type="text"></td> 
                        </tr>
                        <tr>
                            <td>IZQUIERDO</td>
                            <td><input class="decorar-input" name="liesf" type="text"></td>
                            <td><input class="decorar-input" name="licil" type="text"></td>   
                            <td><input class="decorar-input" name="lieje" type="text"></td>   
                            <td><input class="decorar-input" name="lidip" type="text"></td> 
                            <td><input class="decorar-input" name="liad" type="text"></td> 
                        </tr>
                    </table> 
                    <div id="botones"><button type="submit" name="insertlen" class="btn-universal" id="insertle"><span class="icon icon-checkmark"></span></button></div>
                </form>
                <?php
                $ldesf = $_POST['ldesf'];
                $ldcil = $_POST['ldcil'];
                $ldeje = $_POST['ldeje'];
                $lddip = $_POST['lddip'];
                $ldad = $_POST['ldad'];
                $liesf = $_POST['liesf'];
                $licil = $_POST['licil'];
                $lieje = $_POST['lieje'];
                $lidip = $_POST['lidip'];
                $liad = $_POST['liad'];
                if (isset ($_POST['insertlen'])){   
                    if ($ussera == NULL or $ussera == 0){
                                print "<script> alert('TIENES QUE INICIAR SESION'); location.href = 'index.php'; </script>";
                    }
                    else {
                        $sql5="SELECT idbitacora FROM bitacora WHERE formbitacora = 'GRADUACIÓN DE LENSOMETRÍA CONTRASEÑA ".$idaten."' AND sentbitacora = 'INSERT'";
                        $resultado5 = mysqli_query($conn, $sql5);
                        if($row5 = mysqli_fetch_array($resultado5)){ $idbitacora = $row5["idbitacora"]; }

                        if ($idbitacora == NULL) {                        
                            date_default_timezone_set('America/Guatemala');                          
                            $fechagrad = date('Y-m-d');

                            $sql2 ="insert into graduacion values (NULL, '".$idaten."', '".$fechagrad."','LENSOMETRÍA', 'DERECHO', '".$ldesf."', '".$ldcil."', '".$ldeje."', '".$lddip."', '".$ldad."', '', ''),
                                    (NULL, '".$idaten."', '".$fechagrad."','LENSOMETRÍA', 'IZQUIERDO', '".$liesf."', '".$licil."', '".$lieje."', '".$lidip."', '".$liad."','', '')";
                            $resultado2 = mysqli_query($conn, $sql2);                    

                            $sql3 ="insert into bitacora values ('NULL', '".$ussera."', 'GRADUACIÓN DE LENSOMETRÍA CONTRASEÑA ".$idaten."', 'INSERT', '".$fechabitacora."')";
                            $resultado3 = mysqli_query($conn, $sql3);

                            echo '<div class="color-arriba"><h3>GRADUACIÓN DE LENSOMETRÍA INSERTADA CORRECTAMENTE</h3></div>';
                        }
                        else{
                             print "<script> alert('ESTA CONTRASEÑA YA TIENE GRADUACIÓN DE LENSOMETRÍA');</script>";
                        }
                    }
                }               
                ?> 
            </div>
            <div class="columna">
                <form name="form1" method="post" action=""> 
                    <h3>EXAMENES DE PRESIÓN Y AZUCAR</h3>
                    <input class="decorar-input" name="npre" type="text" placeholder="Valor nivel de Presión" required title="En numeros" >
                    <input class="decorar-input" name="nazu" type="text" placeholder="Valor nivel de Azucar" required title="En numeros" >
                    <div id="botones"><button type="submit" name="insertpaz" class="btn-universal" id="insertpaz"><span class="icon icon-checkmark"></span></button></div>
            
                </form> 
                <?php
                $npre = $_POST['npre'];
                $nazu = $_POST['nazu'];               
                
                if (isset ($_POST['insertpaz'])){ 
                    if ($ussera == NULL or $ussera == 0){
                                print "<script> alert('TIENES QUE INICIAR SESION'); location.href = 'index.php'; </script>";
                    }
                    else {                    
                        $sql4="SELECT idbitacora FROM bitacora WHERE formbitacora = 'EXAMEN PRESION AZUCAR CONTRASEÑA ".$idaten."' AND sentbitacora = 'INSERT'";
                        $resultado4 = mysqli_query($conn, $sql4);
                            if($row4 = mysqli_fetch_array($resultado4)){ $idbitacora = $row4["idbitacora"]; }

                            if ($idbitacora == NULL) {                        
                                date_default_timezone_set('America/Guatemala');                          
                                $fechaepre = date('Y-m-d');

                                $sql6 ="insert into cuestionario values
                                        (NULL, '".$idaten."', 'Nivel de Presión', '".$npre."', '".$fechaepre."'),
                                        (NULL, '".$idaten."', 'Nivel de Azucar', '".$nazu."', '".$fechaepre."')";
                                $resultado6 = mysqli_query($conn, $sql6);                    

                                $sql7 ="insert into bitacora values ('NULL', '".$ussera."', 'EXAMEN PRESION AZUCAR CONTRASEÑA ".$idaten."', 'INSERT', '".$fechabitacora."')";
                                $resultado7 = mysqli_query($conn, $sql7);

                                $sql8 ="insert into detalleventa values ('NULL', '".$idaten."', '2', '1', '50', '0')";
                                $resultado8 = mysqli_query($conn, $sql8);                            

                                echo '<div class="color-arriba"><h3>EVALUACION PRELIMINAR INSERTADA CORRECTAMENTE</h3></div>';
                            }
                            else{
                                 print "<script> alert('ESTA CONTRASEÑA YA TIENE EVALUACION PRELIMINAR');</script>";
                            }
                    }
                }
                ?>
            </div> 
        </div> 
	<script type="text/javascript" src="js/jquery-3.6.1.min.js"></script> 
        <script type="text/javascript" src="js/jquery-ui.js"></script>
        <script type="text/javascript" src="js/misfunciones.js"></script>
        <script type="text/javascript" src="js/versection.js"></script>     
    </body>
</html>