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
    $idaten = $_GET['iventa'];
    $sql="Select * from usuario  WHERE idusuario = '".$ussera."'";
    $resultado = mysqli_query($conn, $sql);
    if($row = mysqli_fetch_array($resultado)){ $tipou = $row["puestousuario"]; $nombre =$row["nomusuario"]; $tienda = $row["idtienda"];}
    $sql21="SELECT paciente.nompaciente FROM paciente INNER JOIN atencion
           ON atencion.idpaciente = paciente.idpaciente WHERE idatencion = '".$idaten."'";
    $resultado21 = mysqli_query($conn, $sql21);
    if($row21 = mysqli_fetch_array($resultado21)){ $nompa = $row21["nompaciente"]; }
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
        <div class="color-arriba"><h2 class ="subtitulo">VENTA Y PAGOS PARA  <?php echo $nompa;?></h2></div> 
        <div class="fila">
            <div class="columna">
            <form name="form2" method="post" action="">                
                <table class="TablaE">                
                    <tr>
                        <td width="80%"><input class="input-iud" name="tmedic" type="text" id="tmedic" placeholder="Escriba y Elija un Medicamento"><div id="nmedic"></div></td>  
                        <td width="10%"><input class="input-iud" name="pm" type="text" placeholder="-%"></td>
                        <td width="10%"><button type="submit" name="insertmed" class="btn-iud" id="insertmed">✔</button></td>
                    </tr>                
                    <tr>
                        <td><input class="input-iud" name="trep" type="text" id="trep" placeholder="Escriba y Elija un Tipo de Reparación"><div id="nrep" ></div></td>  
                        <td><input class="input-iud" name="pr" type="text" placeholder="-%"></td>
                        <td><button type="submit" name="insertrep" class="btn-iud" id="insertrep">✔</button></td>
                    </tr>
                    <tr>
                        <td><input class="input-iud" name="taro" type="text" id="taro" placeholder="Escriba y Elija un Aro"><div id="naro"></div></td>   
                        <td><input class="input-iud" name="pa" type="text" placeholder="-%" ></td> 
                        <td><button type="submit" name="insertaro" class="btn-iud" id="insertaro">✔</button></td> 
                    </tr> 
                    <tr>
                        <td><input class="input-iud" name="tservi" type="text" id="tservi" placeholder="Escriba y Elija un Servicio"><div id="nservi"></div></td>  
                        <td><input class="input-iud" name="ps" type="text" placeholder="-%"></td>
                        <td><button type="submit" name="insertser" class="btn-iud" id="insertser">✔</button></td>
                    </tr>
                    <tr>
                        <td><input class="input-iud" name="tac" type="text" id="tac" placeholder="Escriba y Elija un Accesorio"><div id="nac"></div></td>  
                        <td><input class="input-iud" name="pacs" type="text" placeholder="-%" ></td>
                        <td><button type="submit" name="insertac" class="btn-iud" id="insertac">✔</button></td>
                    </tr>
                    <tr>
                        <td><input class="input-iud" name="tlente" type="text" id="tlente" placeholder="Escriba y Elija un Material"><div id="nlente"></div></td>  
                        <td><input class="input-iud" name="pr2" type="text" placeholder="-%" ></td>
                        <td><button type="submit" name="insertm" class="btn-iud" id="insertam">✔</button></td>
                    </tr>
            </table> 
        </form>
        <br>      
        <?php 
            $idlente = $_POST['tidpro6'];
            $plente = $_POST['tppro6'];
            $pl = $_POST['pr'];
            $idlente2 = $_POST['tidpro'];
            $plente2 = $_POST['tppro'];
            $pl2 = $_POST['pr2'];
            $idmed = $_POST['tidpro2'];
            $pmed = $_POST['tppro2'];
            $pm = $_POST['pm'];
            $idaro = $_POST['tidpro3'];
            $paro = $_POST['tppro3'];
            $pa = $_POST['pa'];
            $idserv = $_POST['tidpro4'];
            $pserv = $_POST['tppro4'];
            $ps = $_POST['ps'];
            $idac = $_POST['tidpro5'];
            $pac = $_POST['tppro5'];
            $pacs = $_POST['pacs'];
            
            if (isset ($_POST['insertmed'])){  
                if ($ussera == NULL or $ussera == 0){
                                print "<script> alert('TIENES QUE INICIAR SESION'); location.href = 'index.php'; </script>";
                        }
                else { 
                    $sql1="SELECT * FROM ubicacion WHERE idproducto = '".$idmed."' AND idtienda = '".$tienda."'";
                    $resultado1 = mysqli_query($conn, $sql1);
                        if($row1 = mysqli_fetch_array($resultado1)){ $idubicacion = $row1["idubicacion"]; $cantpro = $row1["cantubicacion"]; }
                        if($pm == NULL){
                            $npm = 0;
                            $nprem = $pmed;
                        }
                        else{
                            $npm = $pm;
                            $nprem = $pmed - ($pmed * $pm / 100);
                        }

                    if ($idmed != NULL) { 
                       if ($cantpro != 0){                       
                            $sql2 ="UPDATE ubicacion SET cantubicacion = (cantubicacion - 1) WHERE idubicacion = '".$idubicacion."'";
                            $resultado2 = mysqli_query($conn, $sql2);
 
                            $sql3 ="insert into detalleventa values (NULL, '".$idaten."', '".$idubicacion."', '1', '".$nprem."', '".$npm."')";
                            $resultado3 = mysqli_query($conn, $sql3);
 
                            $sql4 ="insert into bitacora values (NULL, '".$ussera."', 'ASESOR AGREGÓ MEDICAMENTO ".$idmed.", CONTRASEÑA ".$idaten."', 'INSERT', CURRENT_TIMESTAMP)";
                            $resultado4 = mysqli_query($conn, $sql4); 

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
            
            if (isset ($_POST['insertrep'])){ 
                if ($ussera == NULL or $ussera == 0){
                                print "<script> alert('TIENES QUE INICIAR SESION'); location.href = 'index.php'; </script>";
                        }
                else {
                    $sql5="SELECT * FROM ubicacion WHERE idproducto = '".$idlente."' AND idtienda = 1";
                    $resultado5 = mysqli_query($conn, $sql5);
                        if($row5 = mysqli_fetch_array($resultado5)){ $idubicacion = $row5["idubicacion"]; $cantpro = $row5["cantubicacion"]; }
                        if($pl == NULL){
                            $npl = 0;
                            $nprel = $plente;
                        }
                        else{
                            $npl = $pl;
                            $nprel = $plente - ($plente * $pl / 100);
                        }

                    if ($idlente != NULL) {                

                            $sql7 ="insert into detalleventa values ('NULL', '".$idaten."', '".$idubicacion."', '1', '".$nprel."', '".$npl."')";
                            $resultado7 = mysqli_query($conn, $sql7);

                            $sql8 ="insert into bitacora values ('NULL', '".$ussera."', 'ASESOR AGREGÓ REPARACIÓN ".$idlente.", CONTRASEÑA ".$idaten."', 'INSERT', '".$fechabitacora."')";
                            $resultado8 = mysqli_query($conn, $sql8);

                            echo '<div class="color-arriba"><h3>REPARACION AGREGADO CORRECTAMENTE</h3></div>';
                        }                
                    else{
                         print "<script> alert('DEBE ELEGIR UNA REPARACION');</script>";
                    }
                }
            }
            
            if (isset ($_POST['insertm'])){ 
                if ($ussera == NULL or $ussera == 0){
                                print "<script> alert('TIENES QUE INICIAR SESION'); location.href = 'index.php'; </script>";
                        }
                else {
                    $sql5="SELECT * FROM ubicacion WHERE idproducto = '".$idlente2."' AND idtienda = 1";
                    $resultado5 = mysqli_query($conn, $sql5);
                        if($row5 = mysqli_fetch_array($resultado5)){ $idubicacion = $row5["idubicacion"]; $cantpro = $row5["cantubicacion"]; }
                        if($pl2 == NULL){
                            $npl2 = 0;
                            $nprel = $plente2;
                        }
                        else{
                            $npl2 = $pl2;
                            $nprel = $plente2 - ($plente2 * $pl2 / 100);
                        }

                    if ($idlente2 != NULL) {                

                            $sql7 ="insert into detalleventa values ('NULL', '".$idaten."', '".$idubicacion."', '1', '".$nprel."', '".$npl2."')";
                            $resultado7 = mysqli_query($conn, $sql7);

                            $sql8 ="insert into bitacora values ('NULL', '".$ussera."', 'ASESOR AGREGÓ MATERIAL LENTE ".$idlente2.", CONTRASEÑA ".$idaten."', 'INSERT', '".$fechabitacora."')";
                            $resultado8 = mysqli_query($conn, $sql8);

                            echo '<div class="color-arriba"><h3>MATERIAL AGREGADO CORRECTAMENTE</h3></div>';
                        }                
                    else{
                         print "<script> alert('DEBE ELEGIR UN MATERIAL');</script>";
                    }
                }
            }
            
            if (isset ($_POST['insertaro'])){   
                if ($ussera == NULL or $ussera == 0){
                                print "<script> alert('TIENES QUE INICIAR SESION'); location.href = 'index.php'; </script>";
                        }
                else {
                    $sql9="SELECT * FROM ubicacion WHERE idproducto = '".$idaro."' AND idtienda = '".$tienda."'";
                    $resultado9 = mysqli_query($conn, $sql9);
                        if($row9 = mysqli_fetch_array($resultado9)){ $idubicacion = $row9["idubicacion"]; $cantpro = $row9["cantubicacion"]; }
                        if($pa == NULL){
                            $npa = 0;
                            $nprea = $paro;
                        }
                        else{
                            $npa = $pa;
                            $nprea = $paro - ($paro * $pa / 100);
                        }                    

                    if ($idaro != NULL) { 
                        if ($cantpro != 0){                        
                            $sql10 ="UPDATE ubicacion SET cantubicacion = (cantubicacion - 1) WHERE idubicacion = '".$idubicacion."'";
                            $resultado10 = mysqli_query($conn, $sql10);

                            $sql11 ="insert into detalleventa values ('NULL', '".$idaten."', '".$idubicacion."', '1', '".$nprea."', '".$npa."')";
                            $resultado11 = mysqli_query($conn, $sql11);

                            $sql12 ="insert into bitacora values ('NULL', '".$ussera."', 'ASESOR AGREGÓ ARO ".$idaro.", CONTRASEÑA ".$idaten."', 'INSERT', '".$fechabitacora."')";
                            $resultado12 = mysqli_query($conn, $sql12);

                            echo '<div class="color-arriba"><h3>ARO AGREGADO CORRECTAMENTE</h3></div>';
                        }
                        else {
                            print "<script> alert('NO PUEDE ASIGNAR ESTE ARO NO HAY EXISTENCIAS EN LA SUCURSAL');</script>";  
                        }
                    }
                    else{
                         print "<script> alert('DEBE ELEGIR UN ARO');</script>";
                    }
                }
            }
            
            if (isset ($_POST['insertser'])){ 
                if ($ussera == NULL or $ussera == 0){
                                print "<script> alert('TIENES QUE INICIAR SESION'); location.href = 'index.php'; </script>";
                        }
                else {
                    $sql13="SELECT * FROM ubicacion WHERE idproducto = '".$idserv."' AND idtienda = 1";
                    $resultado13 = mysqli_query($conn, $sql13);
                        if($row13 = mysqli_fetch_array($resultado13)){ $idubicacion = $row13["idubicacion"]; $cantpro = $row13["cantubicacion"]; }
                        if($ps == NULL){
                            $nps = 0;
                            $npres = $pserv;
                        }
                        else{
                            $nps = $ps;
                            $npres = $pserv - ($pserv * $ps / 100);
                        }
                    if ($idserv != NULL) {  
                            $sql14 ="insert into detalleventa values ('NULL', '".$idaten."', '".$idubicacion."', '1', '".$npres."', '".$nps."')";
                            $resultado14 = mysqli_query($conn, $sql14);

                            $sql15 ="insert into bitacora values ('NULL', '".$ussera."', 'ASESOR AGREGÓ SERVICIO ".$idserv.". CONTRASEÑA ".$idaten."', 'INSERT', '".$fechabitacora."')";
                            $resultado15 = mysqli_query($conn, $sql15);

                            echo '<div class="color-arriba"><h3>SERVICIO AGREGADO CORRECTAMENTE</h3></div>';
                        }                  
                    else{
                         print "<script> alert('DEBE ELEGIR UN SERVICIO');</script>";
                    }
                }
            }
            
            if (isset ($_POST['insertac'])){  
                if ($ussera == NULL or $ussera == 0){
                                print "<script> alert('TIENES QUE INICIAR SESION'); location.href = 'index.php'; </script>";
                        }
                else {
                    $sql1="SELECT * FROM ubicacion WHERE idproducto = '".$idac."' AND idtienda = '".$tienda."'";
                    $resultado1 = mysqli_query($conn, $sql1);
                        if($row1 = mysqli_fetch_array($resultado1)){ $idubicacion = $row1["idubicacion"]; $cantpro = $row1["cantubicacion"]; }
                        if($pacs == NULL){
                            $npac = 0;
                            $npreac = $pac;
                        }
                        else{
                            $npac = $pacs;
                            $npreac = $pac - ($pac * $pacs / 100);
                        }

                    if ($idac != NULL) { 
                        if ($cantpro != 0){                        
                            $sql2 ="UPDATE ubicacion SET cantubicacion = (cantubicacion - 1) WHERE idubicacion = '".$idubicacion."'";
                            $resultado2 = mysqli_query($conn, $sql2);

                            $sql3 ="insert into detalleventa values ('NULL', '".$idaten."', '".$idubicacion."', '1', '".$npreac."', '".$npac."')";
                            $resultado3 = mysqli_query($conn, $sql3);

                            $sql4 ="insert into bitacora values ('NULL', '".$ussera."', 'ASESOR AGREGÓ ACCESORIO ".$idac.", CONTRASEÑA  ".$idaten."', 'INSERT', '".$fechabitacora."')";
                            $resultado4 = mysqli_query($conn, $sql4);

                            echo '<div class="color-arriba"><h3>ACCESORIO AGREGADO CORRECTAMENTE</h3></div>';
                        }
                        else {
                            print "<script> alert('NO PUEDE ASIGNAR ESTE ACCESORIO NO HAY EXISTENCIAS EN LA SUCURSAL');</script>";  
                        }
                    }
                    else{
                         print "<script> alert('DEBE ELEGIR UN ACCESORIO');</script>";
                    }
                }
            }
            
            $sql1 ="SELECT SUM(subtdventa) as ST FROM detalleventa WHERE idventa = '".$idaten."'";
            $resultado1 = mysqli_query($conn, $sql1);   
            if ($row1 = mysqli_fetch_row($resultado1)){ }
            
            if ($row1[0] != NULL){        
            $sql2 ="update venta SET totalventa = $row1[0] where idventa = '".$idaten."'";
            $resultado2 = mysqli_query($conn, $sql2); 
            }
            
            $sql3 ="SELECT * FROM venta WHERE idventa = '".$idaten."'";
            $resultado3 = mysqli_query($conn, $sql3);   
            if ($row3 = mysqli_fetch_row($resultado3)){ }      
          
            echo "<form name='form' method='post' action=''>";               
                print "<table class = 'TablaD'>";
                    print "<tr><td>  </td><td>PRODUCTO</td><td width = '10%'>-%</td><td width = '20%'>SUBTOTAL</td><td width = '20%'>CAMBIOS</td></tr>";                            
                    $sql4 ="SELECT detalleventa.*, producto.*, ubicacion.*, venta.*
                        FROM venta INNER JOIN detalleventa ON detalleventa.idventa = venta.idventa
                        INNER JOIN ubicacion ON ubicacion.idubicacion = detalleventa.idubicacion
                        INNER JOIN producto ON producto.idproducto = ubicacion.idproducto
                        WHERE detalleventa.idventa = '".$idaten."'";
                    $resultado4 = mysqli_query($conn, $sql4);
                    while ($row4 = mysqli_fetch_array($resultado4)){ 
                        $fila = $row4['iddventa'];
                        print "<tr><td>{$row4['cantdventa']}</td><td>{$row4['descproducto']}</td><td>{$row4['descdventa']} %</td>";
                        print "<td>Q <input name='td".$fila."' type='text' class='input-iud' size='3' value='{$row4['subtdventa']}' ></td>";
                        print "<input name='tid".$fila."' type='hidden' value='".$fila."'>";
                        print "<input name='idub".$fila."' type='hidden' value='{$row4['idubicacion']}'>";                                
                        print "<input name='tppv".$fila."' type='hidden' value='{$row4['prevproducto']}'>";
                        print "<td><div id='botonesiud'>"; 
                            print "<button type='submit' name='updv".$fila."' class='btn-iud'>✎</button>"; 
                            print "<button type='submit' name='deldv".$fila."' class='btn-iud'>✕</button>"; 
                        print "</div></td></tr>";   
                        $tid = $_POST['tid'.$fila];
                        $td = $_POST['td'.$fila];                                
                        $idub = $_POST['idub'.$fila];
                        $tppv = $_POST['tppv'.$fila];                                        

                        if (isset ($_POST['updv'.$fila])){ 
                            if ($td != $tppv){
                                $npd = 100 - ($td * 100 / $tppv); 

                                $sql6 ="update detalleventa SET subtdventa = '".$td."', descdventa = '".$npd."'  where iddventa = '".$tid."'";
                                $resultado6 = mysqli_query($conn, $sql6);   
                            } 
                            else {                                        
                                $sql6 ="update detalleventa SET subtdventa = '".$td."', descdventa = '0'  where iddventa = '".$tid."'";
                                $resultado6 = mysqli_query($conn, $sql6); 
                            }
                            $sql7 ="insert into bitacora values ('NULL', '".$ussera."', 'ASESOR MODIFICÓ PRECIO % A ".$row4['descproducto'].", CONTRASEÑA  ".$idaten."', 'UPDATE', '".$fechabitacora."')";
                            $resultado7 = mysqli_query($conn, $sql7);
                            echo '<meta http-equiv=refresh content="0">';
                        }

                        if (isset ($_POST['deldv'.$fila])){                                                                           

                            $sql8 ="delete from detalleventa where iddventa = '".$tid."'";
                            $resultado8 = mysqli_query($conn, $sql8);

                            $sql9 ="UPDATE ubicacion as u INNER JOIN producto as p ON p.idproducto = u.idproducto 
                                    SET u.cantubicacion = (u.cantubicacion + 1) 
                                    WHERE u.idubicacion = '".$idub."' AND p.tipoproducto IN ('MEDICAMENTO', 'ARO', 'ACCESORIO')";
                            $resultado9 = mysqli_query($conn, $sql9);

                            $sql10 ="insert into bitacora values ('NULL', '".$ussera."', 'ASESOR ELIMINÓ ".$row4['descproducto'].", CONTRASEÑA  ".$idaten."', 'DELETE', '".$fechabitacora."')";
                            $resultado10 = mysqli_query($conn, $sql10);

                            echo '<meta http-equiv=refresh content="0">';
                        } 
                    } 
                print "<tr><td colspan = '3'>TOTAL</td><td colspan = '2'>Q $row3[3]</td></tr>";
                print "</table>";
                echo "</form>";
            print "<br>";              
        ?>
                <form name="form3" method="post" action="">                
                    <h3>MEDIDAS DEL ARO Y LUGAR DE ENTREGA</h3>
                    <table class='TablaG'>                    
                        <tr><td>V</td><td>H</td><td>D</td><td>P</td><td>A</td></tr>
                        <tr>
                            <td><input class="decorar-input" name="tmv" type="text"></td>
                            <td><input class="decorar-input" name="tmh" type="text"></td>
                            <td><input class="decorar-input" name="tmd" type="text"></td>   
                            <td><input class="decorar-input" name="tmp" type="text"></td>   
                            <td><input class="decorar-input" name="tma" type="text"></td> 
                        </tr>
                        <tr><td colspan ='5'>
                        <?php
                            $sql27 ="SELECT * from tienda";
                            $resultado27 = mysqli_query($conn, $sql27);                   
                            print "<select required class='decorar-input' name='idtie'>
                            <option value='' disabled selected>ELIJE UNA TIENDA PARA LA ENTREGA</option>";
                            while ($row27 = mysqli_fetch_array($resultado27)){                            
                                print"<option>{$row27['nomtienda']}</option>";
                            }
                            print "<option>ENVÍO PAQUETERÍA</option>";
                            print "</select>";
                        ?>
                        </td></tr>     
                        <tr><td colspan ='5'><input class="decorar-input" name="paque" type="text" placeholder="Escriba una Empresa de Paqueteria" title="Nombre de empresa de paqueteria utilizado para el envío"></td></tr>                                            
                    </table> 
                    <div id="botones"><button type="submit" name="insertma" class="btn-universal" id="insertma"><span class="icon icon-checkmark"></span></button></div>
                </form>
            <?php
                $mv = $_POST['tmv'];
                $mh = $_POST['tmh'];
                $md = $_POST['tmd'];
                $mp = $_POST['tmp'];
                $ma = $_POST['tma']; 
                $tieen = $_POST['idtie'];
                $paq = $_POST['paque'];
                if (isset ($_POST['insertma'])){
                    if ($ussera == NULL or $ussera == 0){
                        print "<script> alert('TIENES QUE INICIAR SESION'); location.href = 'index.php'; </script>";
                    }
                    else {
                        $sql5="SELECT idbitacora FROM bitacora WHERE formbitacora = 'ASESOR AGREGÓ MEDIDA DE ARO CONTRASEÑA ".$idaten."' AND sentbitacora = 'INSERT'";
                        $resultado5 = mysqli_query($conn, $sql5);
                        if($row5 = mysqli_fetch_array($resultado5)){ $idbitacora = $row5["idbitacora"]; }

                        if ($idbitacora == NULL) {
                            $sql2 ="insert into maro values ('".$idaten."', '".$mv."', '".$mh."', '".$md."', '".$mp."', '".$ma."')";
                            $resultado2 = mysqli_query($conn, $sql2);

                            $sql4 ="insert into entrega values ('".$idaten."', $tienda, '', '', '', '".$tieen."', '".$paq."', 'E')";
                            $resultado4 = mysqli_query($conn, $sql4);

                            $sql20 ="update atencion set estatencion = 'LABORATORIO' where idatencion = '".$idaten."'";
                            $resultado20 = mysqli_query($conn, $sql20);

                            $sql3 ="insert into bitacora values ('NULL', '".$ussera."', 'ASESOR AGREGÓ MEDIDA DE ARO CONTRASEÑA ".$idaten."', 'INSERT', '".$fechabitacora."')";
                            $resultado3 = mysqli_query($conn, $sql3);

                            print "<script> alert('CONTRAEÑA ENVIADA AL LABORATORIO');</script>";
                        }
                        else{
                             print "<script> alert('ESTA CONTRASEÑA YA TIENE MEDIDA DE ARO');</script>";
                        }
                    }
                } 
            ?>
            </div>
            <div class="columna">               
                <?php  
                $sql22="SELECT producto.tipoproducto, CONCAT('Q ', SUM(detalleventa.subtdventa)) FROM detalleventa
                        INNER JOIN ubicacion ON ubicacion.idubicacion = detalleventa.idubicacion
                        INNER JOIN producto ON producto.idproducto = ubicacion.idproducto
                        WHERE detalleventa.idventa = '".$idaten."'
                        GROUP BY producto.tipoproducto;";
                $resultado22 = mysqli_query($conn, $sql22);                
                print "<table class='TablaD'>";                         
                    print "<tr>";
                    print "<td>TIPO PRODUCTO</td>";
                    print "<td>SUBTOTAL</td>";                   
                    print "</tr>";
                    while ($row22 = mysqli_fetch_assoc($resultado22)){
                        print "<tr>";
                            foreach ($row22 as $item22){
                                print "<td>".($item22!==NULL ?htmlentities($item22):"&nbsp;")."</td>";
                            }
                            print "</tr>";
                        }
                    print "<tr><td>TOTAL</td><td>Q $row3[3]</td></tr>";
                print "</table>"; 
                print "<BR>";
                 $sql23="SELECT SUM(detalleventa.subtdventa) FROM detalleventa
                        INNER JOIN ubicacion ON ubicacion.idubicacion = detalleventa.idubicacion
                        INNER JOIN producto ON producto.idproducto = ubicacion.idproducto
                        WHERE detalleventa.idventa = '".$idaten."'
                        AND producto.tipoproducto IN ('ARO', 'LENTE');";
                $resultado23 = mysqli_query($conn, $sql23);
                if ($row23 = mysqli_fetch_row($resultado23)){ }  
                
                 $sql24="SELECT SUM(detalleventa.subtdventa) FROM detalleventa
                        INNER JOIN ubicacion ON ubicacion.idubicacion = detalleventa.idubicacion
                        INNER JOIN producto ON producto.idproducto = ubicacion.idproducto
                        WHERE detalleventa.idventa = '".$idaten."'
                        AND producto.tipoproducto IN ('MEDICAMENTO', 'SERVICIO');";
                $resultado24 = mysqli_query($conn, $sql24);
                if ($row24 = mysqli_fetch_row($resultado24)){ }  
                 print "<table class='TablaD'>";                         
                    print "<tr>";
                    print "<td>DESCRIPCION</td>";
                    print "<td>SUBTOTAL</td>";                   
                    print "</tr>";
                    print "<tr><td>OBLIGATORIO A PAGAR</td><td>Q $row24[0]</td></tr>";
                    print "<tr><td>ANTICIPO SOBRE</td><td>Q $row23[0]</td></tr>";
                    print "<tr><td>TOTAL</td><td>Q $row3[3]</td></tr>";
                print "</table>";
                
                $sql25 ="SELECT SUM(pago.cantpago), venta.totalventa, (venta.totalventa - SUM(pago.cantpago)) as pendiente 
                        FROM venta INNER JOIN pago ON pago.idventa = venta.idventa
                        WHERE venta.idventa = '".$idaten."'";
                $resultado25 = mysqli_query($conn, $sql25);
                if ($row25 = mysqli_fetch_row($resultado25)){ } 
                        
                $sql26 ="SELECT * FROM entrega WHERE identrega = '".$idaten."'";
                $resultado26 = mysqli_query($conn, $sql26);
                if ($row26 = mysqli_fetch_row($resultado26)){ }
                setlocale(LC_TIME, "spanish.utf8");                           
                $fechacom = strftime("%A, %d de %B de %Y  a partir de las %H:%M horas ", strtotime($row26[2]));
                print "<br>";        
                print "<h4>TOTAL: Q $row25[1]   ANTICIPO: Q $row25[0]   PENDIENTE: Q $row25[2]</h4>";
                print "<label> ENTREGA:   $fechacom</label>";
                ?> 
                <h3>Efectuar Pago</h3>
                <form name='form' method='post' action=''>
                    <select name='ttpa' class='decorar-input' required>
                        <option value='' disabled selected>Elija un Tipo de Pago</option>
                        <option>Efectivo</option>
                        <option>Visacuotas</option>
                        <option>Tarjeta Credito</option>
                        <option>Tarjeta Debito</option> 
                        <option>Deposito</option>  
                        <option>Transferencia</option>                        
                    </select>
                    <input name='tcantp' type='number'  step="0.01" class='decorar-input' title='Cantidad en Numeros' placeholder = 'Cantidad del Pago a Realizar' required>
                    <input name='tdoc' type='text' class='decorar-input' title='Numero de Boleta, Cheque, Transaccion' placeholder = 'Numero de Documento' >
                    <input name='tfecp' type='date' class='decorar-input' required >
                    <div id='botones'>
                        <button type='submit' name='insp' class='btn-universal'><span class='icon icon-coin-euro'></span></button> 
                        <button type='submit' name='updat' class='btn-universal' formnovalidate><span class='icon icon-cloud-check'></span></button>
                    </div>                   
                </form> 
                <?php   
            $doc= $_POST['tdoc'];
            $tcantp = $_POST['tcantp'];
            $ttpa = $_POST['ttpa'];
            $tfecp = $_POST['tfecp'];
            $esta= $_POST['testadoa'];

            if (isset ($_POST['insp'])){ 
                if ($ussera == NULL or $ussera == 0){
                         print "<script> alert('TIENES QUE INICIAR SESION'); location.href = 'index.php'; </script>";
                    }
                else {
                    $sql ="SELECT venta.totalventa FROM venta WHERE venta.idventa = '".$idaten."'";
                    $resultado = mysqli_query($conn, $sql);
                    if ($row = mysqli_fetch_row($resultado)){ }
                    if ($row5[0] == NULL ){
                        $anticipo = 0; 
                        $pendiente = $row[0];
                    }
                    else {
                        $anticipo = $row5[0]; 
                        $pendiente = $row5[2];
                    }
                     if ($anticipo != $row[0]){

                        if($tcantp <= $pendiente){
                           $sql2 ="insert into pago values ('NULL', '".$idaten."', '".$ttpa."', '".$tcantp."',  '".$tfecp."', '".$doc."', '".$tienda."')";
                            $resultado2 = mysqli_query($conn, $sql2);                    

                            $sql3 ="insert into bitacora values ('NULL', '".$ussera."', 'GERENTE INGRESO PAGO DE Q".$tcantp.".00  A CONTRASEÑA ".$idaten."', 'INSERT', '".$fechabitacora."')";
                            $resultado3 = mysqli_query($conn, $sql3);

                            echo '<div class="color-arriba"><h3>PAGO AL ID VENTA -'.$idaten.'- REALIZADO CORRECTAMENTE</h3></div>'; 
                            echo '<meta http-equiv=refresh content="1">';
                        }
                        else{
                            print "<script> alert('EL PAGO EXCEDE EL TOTAL PENDIENTE DE LA VENTA'); </script>";
                        }
                    }
                    else{
                            print "<script> alert('EL TOTAL DE LA VENTA YA FUE CANCELADO'); </script>";
                    }
                }

            }
            if (isset ($_POST['updat'])){ 
                if ($ussera == NULL or $ussera == 0){
                         print "<script> alert('TIENES QUE INICIAR SESION'); location.href = 'index.php'; </script>";
                }
                else {
                $sql9 ="SELECT venta.totalventa, SUM(pago.cantpago) FROM venta INNER JOIN pago ON pago.idventa = venta.idventa WHERE venta.idventa = '".$idaten."'";
                $resultado9 = mysqli_query($conn, $sql9);
                if($row9 = mysqli_fetch_array($resultado9)){ $tpa = $row9["SUM(pago.cantpago)"]; $tve =$row9["totalventa"];} 

                    if($tpa != $tve or $tpa == NULL){                        
                      print "<script> alert('NO PUEDE FINALIZAR EL PROCESO HASTA QUE EL COSTO TOTAL SEA CANCELADO');</script>";  
                    }
                    else {
                        $sql7 ="update atencion set estatencion = 'FINALIZADO' where idatencion = '".$idaten."'";
                        $resultado7 = mysqli_query($conn, $sql7);                    

                        $sql8 ="insert into bitacora values ('NULL', '".$ussera."', 'GERENTE FINALIZÓ CONTRASEÑA ".$idaten." EN PAGOS', 'UPDATE', '".$fechabitacora."')";
                        $resultado8 = mysqli_query($conn, $sql8);
                        print "<script> alert('EL PROCESO CON LA CONTRASEÑA $idaten FUE FINALIZADO');</script>";
                    }
                }
            }
            
            print "<div id='botones'>";
                print "<a class='btn-universal' href='printpass.php?perfil=$ussera&atencion={$idaten}' target='_blank'><span class='icon icon-key2'></span></a>";               
                print "<a class='btn-universal' href='printreceta.php?perfil=$ussera&atencion={$idaten}' target='_blank'><span class='icon icon-plus'></span></a>";
                print "<a class='btn-universal' href='printfin.php?perfil=$ussera&atencion={$idaten}' target='_blank'><span class='icon icon-printer'></span></a>";
                print "</div>";  
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