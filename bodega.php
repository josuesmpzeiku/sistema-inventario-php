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
        <div class="color-arriba"><h2 class ="subtitulo">BODEGA</h2></div>
        <div class="fila"> 
            <div class="columna">
                <form name="form1" method="post" action="">
                    <h3>INSERTAR AROS NUEVOS</h3>
                    <input class="decorar-input" name="tidaro" type="text" placeholder="Escriba un Código para el ARO" required>
                    <input class="decorar-input" name="tdesaro" type="text" placeholder="Descripción del Aro (Marca, Modelo, Color, Otros)" required>
                    <select name="tdaro" class="decorar-input" required="">
                        <option value="" disabled selected>Elija un Detalle</option>
                        <option>COMPLETO</option>
                        <option>RANURADO</option> 
                        <option>PERFORADO</option>                         
                    </select>
                    <input class="decorar-input" name="tprearo" type="number" required placeholder="Precio al Publico" step="0.01" title="Puede ser entero o decimal">
                    <select name="tobsaro" class="decorar-input" required="">
                        <option value="" disabled selected>Elija un Tipo de Aro</option>
                        <option>PUNTO VERDE DE NIÑO</option>
                        <option>PUNTO VERDE DE NIÑA</option> 
                        <option>PUNTO VERDE DE CABALLERO</option> 
                        <option>PUNTO VERDE DE DAMA</option>
                        <option>PUNTO ROJO DE NIÑO</option>
                        <option>PUNTO ROJO DE NIÑA</option> 
                        <option>PUNTO ROJO DE CABALLERO</option> 
                        <option>PUNTO ROJO DE DAMA</option>
                        <option>PUNTO NEGRO DE NIÑO</option>
                        <option>PUNTO NEGRO DE NIÑA</option> 
                        <option>PUNTO NEGRO DE CABALLERO</option> 
                        <option>PUNTO NEGRO DE DAMA</option>
                        <option>MARCA DE NIÑO</option>
                        <option>MARCA DE NIÑA</option> 
                        <option>MARCA DE CABALLERO</option> 
                        <option>MARCA DE DAMA</option>
                    </select>
                    <div id="botones"><button type="submit" name="insertpa" class="btn-universal"><span class="icon icon-floppy-disk"></span></button></div>
                </form>
                <?php    
                $idar = $_POST['tidaro'];  $desar = $_POST['tdesaro'];  $prear = $_POST['tprearo'];  $obsar = $_POST['tobsaro']; $dar = $_POST['tdaro'];       
        
                if (isset ($_POST['insertpa'])){
                    $sql1 ="insert into producto values ('".$idar."', 'ARO', '".$dar."', '".$desar."', '', '".$prear."', '".$obsar."')";
                    $resultado1 = mysqli_query($conn, $sql1); 

                    $sql2 ="insert into bitacora values ('NULL', '".$ussera."', 'FORM PRODUCTO ARO ".$idar."', 'INSERT', '".$fechabitacora."')";
                    $resultado2 = mysqli_query($conn, $sql2);

                    $sql3 ="SELECT * FROM producto where idproducto = '".$idaro."'";
                    $resultado3 = mysqli_query($conn, $sql3);
                    if ($row3 = mysqli_fetch_row($resultado3)){ }

                    echo '<div class="color-arriba"><h3>ARO GUARDADO CORRECTAMENTE</h3></div>';                                      
                } 
                ?> 
            </div>
            <div class="columna">
                <form name="form2" method="post" action="">
                    <h3>MODIFICACIÓN DE INFORMACION DE AROS</h3>
                    <input class="decorar-input" name="tarom" id="tarom" type="text" placeholder="Escriba y Elija un Aro" required>
                    <div><br></div><div id="narom" class="contenedor-transparente"></div>                    
                </form> 
                 <?php 
                    if (isset ($_POST['updaro'])){
                        $sql4 ="insert into bitacora values ('NULL', '".$ussera."', 'FORM PRODUCTO ARO ".$idar."', 'UPDATE', '".$fechabitacora."')";
                        $resultado4 = mysqli_query($conn, $sql4); 

                        $sql5 ="update producto SET descproducto = '".$desar."', prevproducto = '".$prear."', obsproducto = '".$obsar."' where idproducto = '".$idar."' ";
                        $resultado5 = mysqli_query($conn, $sql5); 

                        $sql3 ="SELECT * FROM producto where idproducto = '".$idaro."'";
                        $resultado3 = mysqli_query($conn, $sql3);
                        if ($row3 = mysqli_fetch_row($resultado3)){ }
                       
                        echo '<div class="color-arriba"><h3>ARO GUARDADO CORRECTAMENTE</h3></div>';
                                          
                    }  

                    if (isset ($_POST['delaro'])){                 
                        $sql7 ="delete from producto where idproducto = '".$idar."'";
                        $resultado7 = mysqli_query($conn, $sql7); 
                        echo '<div class="color-arriba"><h3>EL PRODUCTO -'.$desar.'- HA SIDO ELIMINADO CORRECTAMENTE</h3></div>';   

                        $sql8 ="insert into bitacora values ('NULL', '".$ussera."', 'FORM PRODUCTO ARO ".$idar."', 'DELETE', '".$fechabitacora."')";
                        $resultado8 = mysqli_query($conn, $sql8);
                    }
                ?> 
            </div>            
        </div>
        <div class="fila"> 
            <div class="columna">
                <form name="form1" method="post" action=""> 
                    <h3>INGRESO DE PRODUCTOS A BODEGA</h3>
                    <div id="botones"><button type="submit" name="insertg" class="btn-universal"><span class="icon icon-spell-check"></span></button></div>
                </form>
                <form name="form2" method="post" action="">         
                    <table class="TablaE">
                        <tr>
                            <td width="80%"><input class="input-iud" name="tmedic" type="text" id="tmedic"  placeholder="Escriba y Elija un Medicamento"><div id="nmedic"></div></td>  
                            <td width="10%"><input class="input-iud" name="pm" type="text"  placeholder="#"></td>
                            <td width="10%"><button type="submit" name="insertmed" class="btn-iud" id="insertmed">✔</button></td>
                        </tr>           
                        <tr>
                            <td><input class="input-iud" name="taro" type="text" id="taro" placeholder="Escriba y Elija un Aro"><div id="naro"></div></td>  
                            <td><input class="input-iud" name="pa" type="text" placeholder="#"></td>               
                            <td><button type="submit" name="insertaro" class="btn-iud" id="insertaro">✔</button></td>
                        </tr>           
                        <tr>
                            <td><input class="input-iud" name="tac" type="text" id="tac"  placeholder="Escriba y Elija un Accesorio"><div id="nac"></div></td>
                            <td><input class="input-iud" name="pacs" type="text"  placeholder="#"></td>          
                            <td><button type="submit" name="insertac" class="btn-iud" id="insertac">✔</button></td>
                        </tr>
                    </table>
                </form>
                <form name="form3" method="post" action=""> 
                    <table width = '100%'>
                        <tr><td colspan="2"><h3>ELIJE UNA FECHA Y LUEGO EN EL BOTON PARA VER</h3></td></tr>
                        <tr>                           
                            <td width = '80%'><input type="date" name="fbus" class="input-iud" id="fbus" required></td>
                            <td><button type="submit" name="selectb" class="btn-universal" id="selectb"><span class="icon icon-search"></span></button></td>
                        </tr>
                    </table>
                    <?php
                    $fbus = $_POST['fbus'];
                    if (isset ($_POST['selectb'])){ 
                    $sql1="SELECT producto.descproducto, SUM(detallegestionm.idcantidad) FROM gestionm
                        INNER JOIN detallegestionm ON detallegestionm.idgestion = gestionm.idgestion
                        INNER JOIN ubicacion ON ubicacion.idubicacion = detallegestionm.idubicacion
                        INNER JOIN producto ON producto.idproducto = ubicacion.idproducto
                        WHERE gestionm.idtienda = 10 AND producto.tipoproducto = 'ARO' AND gestionm.fechagestion = '".$fbus."' 
                        GROUP BY producto.descproducto   ORDER BY `producto`.`descproducto` ASC;";
                    $resultado1 = mysqli_query($conn, $sql1);                
                print "<table class='TablaJ'>";                         
                    print "<tr>";                    
                    print "<td>MARCA DE ARO</td>";                    
                    print "<td>TOTAL</td>"; 
                    print "</tr>";
                    while ($row1 = mysqli_fetch_assoc($resultado1)){
                        print "<tr>";
                            foreach ($row1 as $item1){
                                print "<td>".($item1!==NULL ?htmlentities($item1):"&nbsp;")."</td>";
                            }
                            print "</tr>";
                        }
                print "</table><br>"; 
                
                $sql2="SELECT producto.descproducto, detallegestionm.idcantidad FROM gestionm
                        INNER JOIN detallegestionm ON detallegestionm.idgestion = gestionm.idgestion
                        INNER JOIN ubicacion ON ubicacion.idubicacion = detallegestionm.idubicacion
                        INNER JOIN producto ON producto.idproducto = ubicacion.idproducto
                        WHERE gestionm.idtienda = 10 AND producto.tipoproducto = 'MEDICAMENTO' AND gestionm.fechagestion = '".$fbus."' 
                        ORDER BY `producto`.`descproducto` ASC;";
                $resultado2 = mysqli_query($conn, $sql2);                
                print "<table class='TablaJ'>";                         
                    print "<tr>";                    
                    print "<td>MEDICAMENTO</td>";                    
                    print "<td>TOTAL</td>"; 
                    print "</tr>";
                    while ($row2 = mysqli_fetch_assoc($resultado2)){
                        print "<tr>";
                            foreach ($row2 as $item2){
                                print "<td>".($item2!==NULL ?htmlentities($item2):"&nbsp;")."</td>";
                            }
                            print "</tr>";
                        }
                print "</table><br>";
                
                $sql3="SELECT producto.descproducto, detallegestionm.idcantidad FROM gestionm
                        INNER JOIN detallegestionm ON detallegestionm.idgestion = gestionm.idgestion
                        INNER JOIN ubicacion ON ubicacion.idubicacion = detallegestionm.idubicacion
                        INNER JOIN producto ON producto.idproducto = ubicacion.idproducto
                        WHERE gestionm.idtienda = 10 AND producto.tipoproducto = 'ACCESORIO' AND gestionm.fechagestion = '".$fbus."'                         
                        ORDER BY `producto`.`descproducto` ASC";
                $resultado3 = mysqli_query($conn, $sql3);                
                print "<table class='TablaJ'>";                         
                    print "<tr>";                    
                    print "<td>ACCESORIO</td>";                    
                    print "<td>TOTAL</td>"; 
                    print "</tr>";
                    while ($row3 = mysqli_fetch_assoc($resultado3)){
                        print "<tr>";
                            foreach ($row3 as $item3){
                                print "<td>".($item3!==NULL ?htmlentities($item3):"&nbsp;")."</td>";
                            }
                            print "</tr>";
                        }
                print "</table>";
            }
            ?> 
                </form>
            </div>  
        <?php 
            date_default_timezone_set('America/Guatemala');                          
            $fechag = date('Y-m-d');
            
            $sql3 ="SELECT * FROM gestionm where idtienda = 10 and fechagestion = '".$fechag."'";
            $resultado3 = mysqli_query($conn, $sql3);
                if ($row3 = mysqli_fetch_row($resultado3)){ }
                    print "<div class='columna'>";               
                        print "<table class='TablaA'>";
                            print "<tr><td colspan = '2'>LISTADO DE PRODUCTOS INGRESADOS A BODEGA</td></tr>";
                            print "<tr><td width='35%'>FECHA:</td><td>$row3[2]</td></tr>";               
                        print "</table>";
                        
            if (isset ($_POST['insertg'])){ 
                if ($row3[0] == NULL){
                    $sql1 ="insert into gestionm values (NULL, 10, '".$fechag."', '')";
                    $resultado1 = mysqli_query($conn, $sql1);

                    $sql2 ="insert into bitacora values ('NULL', '".$ussera."', 'FORM BODEGA', 'INSERT', '".$fechabitacora."')";
                    $resultado2 = mysqli_query($conn, $sql2); 
                    
                    echo '<meta http-equiv=refresh content="0">';
                }
                else{
                   print "<script> alert('YA HAY UN LISTADO CON ESA FECHA');</script>"; 
                }
            }            
            
        
            $idmed = $_POST['tidpro2'];           
            $pm = $_POST['pm'];
            $idaro = $_POST['tidpro3'];           
            $pa = $_POST['pa'];           
            $idac = $_POST['tidpro5'];           
            $pacs = $_POST['pacs'];
        
            if (isset ($_POST['insertmed'])){
                if ($row3[0] != NULL){
                    if ($idmed != NULL) {                     
                        $sql11="SELECT * FROM ubicacion WHERE idproducto = '".$idmed."' AND idtienda = 10";
                        $resultado11 = mysqli_query($conn, $sql11);
                            if($row11 = mysqli_fetch_array($resultado11)){ $idubicacion = $row11["idubicacion"];}

                        if ($idubicacion != NULL){                        
                            $sql2 ="UPDATE ubicacion SET cantubicacion = (cantubicacion + $pm) WHERE idubicacion = '".$idubicacion."'";
                            $resultado2 = mysqli_query($conn, $sql2);                        
                        }
                        else {
                           $sql2 ="insert into ubicacion values ('NULL', '".$idmed."', 10, '".$pm."')";
                           $resultado2 = mysqli_query($conn, $sql2);
                        }
                        $sql11="SELECT * FROM ubicacion WHERE idproducto = '".$idmed."' AND idtienda = 10";
                        $resultado11 = mysqli_query($conn, $sql11);
                            if($row11 = mysqli_fetch_array($resultado11)){ $idubicacion = $row11["idubicacion"];} 

                        $sql3 ="insert into detallegestionm values ('NULL', '".$row3[0]."', '".$idubicacion."', '".$pm."', '', 'RECIBIDO')";
                        $resultado3 = mysqli_query($conn, $sql3);

                        $sql4 ="insert into bitacora values ('NULL', '".$ussera."', 'FORM DETALLEGESTION PARA ".$idmed.", INSERT MEDICAMENTO', 'INSERT', '".$fechabitacora."')";
                        $resultado4 = mysqli_query($conn, $sql4);                    
                    }
                    else{
                         print "<script> alert('DEBE ELEGIR UN MEDICAMENTO');</script>";
                    } 
                }
                else {
                     print "<script> alert('PRODUCTO NO INGRESADO, ANTES DEBE DAR CLICK EN NUEVO INGRESO');</script>";
                }
            }
            if (isset ($_POST['insertaro'])){
                if ($row3[0] != NULL){
                    if ($idaro != NULL) { 
                        $sql11="SELECT * FROM ubicacion WHERE idproducto = '".$idaro."' AND idtienda = 10";
                        $resultado11 = mysqli_query($conn, $sql11);
                            if($row11 = mysqli_fetch_array($resultado11)){ $idubicacion = $row11["idubicacion"];}

                        if ($idubicacion != NULL){                        
                            $sql2 ="UPDATE ubicacion SET cantubicacion = (cantubicacion + $pa) WHERE idubicacion = '".$idubicacion."'";
                            $resultado2 = mysqli_query($conn, $sql2);                        
                        }
                        else {
                           $sql2 ="insert into ubicacion values ('NULL', '".$idaro."', 10, '".$pa."')";
                           $resultado2 = mysqli_query($conn, $sql2);
                        }
                        $sql11="SELECT * FROM ubicacion WHERE idproducto = '".$idaro."' AND idtienda = 10";
                        $resultado11 = mysqli_query($conn, $sql11);
                            if($row11 = mysqli_fetch_array($resultado11)){ $idubicacion = $row11["idubicacion"];} 

                        $sql3 ="insert into detallegestionm values ('NULL', '".$row3[0]."', '".$idubicacion."', '".$pa."', '', 'RECIBIDO')";
                        $resultado3 = mysqli_query($conn, $sql3);

                        $sql4 ="insert into bitacora values ('NULL', '".$ussera."', 'FORM DETALLEGESTION PARA ".$idaro.", INSERT ARO', 'INSERT', '".$fechabitacora."')";
                        $resultado4 = mysqli_query($conn, $sql4);                    
                    }
                    else{
                         print "<script> alert('DEBE ELEGIR UN ARO');</script>";
                    }  
                }
                else {
                     print "<script> alert('PRODUCTO NO INGRESADO, ANTES DEBE DAR CLICK EN NUEVO INGRESO');</script>";
                }
            }
            
            if (isset ($_POST['insertac'])){
                if ($row3[0] != NULL){
                    if ($idac != NULL) { 
                        $sql11="SELECT * FROM ubicacion WHERE idproducto = '".$idac."' AND idtienda = 10";
                        $resultado11 = mysqli_query($conn, $sql11);
                            if($row11 = mysqli_fetch_array($resultado11)){ $idubicacion = $row11["idubicacion"];}

                        if ($idubicacion != NULL){                        
                            $sql2 ="UPDATE ubicacion SET cantubicacion = (cantubicacion + $pacs) WHERE idubicacion = '".$idubicacion."'";
                            $resultado2 = mysqli_query($conn, $sql2);                        
                        }
                        else {
                          $sql2 ="insert into ubicacion values ('NULL', '".$idac."', 10, '".$pacs."')";
                          $resultado2 = mysqli_query($conn, $sql2);                       
                        }
                        $sql11="SELECT * FROM ubicacion WHERE idproducto = '".$idac."' AND idtienda = 10";
                        $resultado11 = mysqli_query($conn, $sql11);
                            if($row11 = mysqli_fetch_array($resultado11)){ $idubicacion = $row11["idubicacion"];} 

                        $sql3 ="insert into detallegestionm values ('NULL', '".$row3[0]."', '".$idubicacion."', '".$pacs."', '', 'RECIBIDO')";
                        $resultado3 = mysqli_query($conn, $sql3);

                        $sql4 ="insert into bitacora values ('NULL', '".$ussera."', 'FORM DETALLEGESTION PARA ".$idac.", INSERT ACCESORIO', 'INSERT', '".$fechabitacora."')";
                        $resultado4 = mysqli_query($conn, $sql4);                    
                    }
                    else{
                         print "<script> alert('DEBE ELEGIR UN ACCESORIO');</script>";
                    }
                }
                else {
                     print "<script> alert('PRODUCTO NO INGRESADO, ANTES DEBE DAR CLICK EN NUEVO INGRESO');</script>";
                }
            } 

                    echo "<form name='form' method='post' action=''>";
                        print "<table class = 'TablaC'>";
                            print "<tr><td>CANT</td><td>PRODUCTO</td><td>ELIMINAR</td></tr>";
                            $sql4 ="SELECT detallegestionm.*, producto.*, ubicacion.*
                                    FROM detallegestionm INNER JOIN ubicacion ON ubicacion.idubicacion = detallegestionm.idubicacion
                                    INNER JOIN producto ON producto.idproducto = ubicacion.idproducto
                                    WHERE detallegestionm.idgestion = '".$row3[0]."'";
                            $resultado4 = mysqli_query($conn, $sql4);
                            while ($row4 = mysqli_fetch_array($resultado4)){ 
                                $fila = $row4['iddgestionm'];
                                print "<tr><td>{$row4['idcantidad']}</td><td>{$row4['descproducto']}</td>";
                                print "<td><button type='submit' name='deldg".$fila."' class='btn-iud'>✕</button>";
                                print "<input name='tiddg".$fila."' type='hidden' value='".$fila."'>";
                                print "<input name='tcanu".$fila."' type='hidden' value='{$row4['idcantidad']}'>";
                                print "<input name='idub".$fila."' type='hidden' value='{$row4['idubicacion']}'></td></tr>";
                                $tid = $_POST['tiddg'.$fila];
                                $idub = $_POST['idub'.$fila];
                                $tcanu = $_POST['tcanu'.$fila];
                                
                                if (isset ($_POST['deldg'.$fila])){                         
                                    
                                    $sql6 ="update ubicacion SET cantubicacion = (cantubicacion - $tcanu) where idubicacion = $idub";
                                    $resultado6 = mysqli_query($conn, $sql6);
                                    
                                    $sql5 ="delete from detallegestionm where iddgestionm = '".$tid."'";
                                    $resultado5 = mysqli_query($conn, $sql5);                                   

                                    $sql7 ="insert into bitacora values ('NULL', '".$ussera."', 'FORM BODEGA', 'DELETE', '".$fechabitacora."')";
                                    $resultado7 = mysqli_query($conn, $sql7);

                                    echo '<meta http-equiv=refresh content="0">';
                                }
                            }
                        print "</table>";
                    echo "</form>";
                print "</div>";           
        ?>
        </div>
	<script type="text/javascript" src="js/jquery-3.6.1.min.js"></script> 
        <script type="text/javascript" src="js/jquery-ui.js"></script>
        <script type="text/javascript" src="js/misfunciones.js"></script>
        <script type="text/javascript" src="js/versection.js"></script> 
        <script type="text/javascript" src="js/funcionlista.js"></script>
    </body>
</html>