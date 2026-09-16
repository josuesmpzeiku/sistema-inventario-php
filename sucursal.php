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
    $tidti = $_GET['idt'];
    $tfechg = $_GET['feg'];
    
    $sql="Select * from usuario  WHERE idusuario = '".$ussera."'";
    $resultado = mysqli_query($conn, $sql);
    if($row = mysqli_fetch_array($resultado)){ $tipou = $row["puestousuario"]; $nombre =$row["nomusuario"]; $tienda = $row["idtienda"];}
    
    $sql3 ="SELECT gestionm.*, tienda.* FROM gestionm INNER JOIN tienda ON tienda.idtienda = gestionm.idtienda where gestionm.idtienda  = '".$tidti."' AND gestionm.fechagestion = '".$tfechg."'";
    $resultado3 = mysqli_query($conn, $sql3);
    if ($row3 = mysqli_fetch_row($resultado3)){ }
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
        <div class="color-arriba"><h2 class ="subtitulo">SUCURSAL</h2></div> 
        <div class="fila"> 
            <div class="columna">
                <form name="form1" method="post" action="">
                    <input class="input-iud" name="ttienda" type="text" id="ttienda" placeholder="Escriba y Elija una Tienda" required><div id="ntienda"></div> 
                    <input class="input-iud" name="ttrans" type="text" id="ttrans" placeholder="Escriba un Transporte" required>
                    <div id="botones"><button type="submit" name="insertg" class="btn-universal"><span class="icon icon-list"></span></button></div>                    
                </form>
                <?php
                    $idti = $_POST['idti'];
                    $trans = $_POST['ttrans'];
                    date_default_timezone_set('America/Guatemala');                          
                    $fechag = date('Y-m-d');
                    if (isset ($_POST['insertg'])){                        
                        if ($idti != NULL) {
                            $sql3 ="SELECT gestionm.*, tienda.* FROM gestionm INNER JOIN tienda ON tienda.idtienda = gestionm.idtienda where gestionm.idtienda  = '".$idti."' AND gestionm.fechagestion = '".$fechag."'";
                            $resultado3 = mysqli_query($conn, $sql3);
                            if ($row3 = mysqli_fetch_row($resultado3)){ }                            
                            if ($row3[0] == NULL){
                                $sql1 ="insert into gestionm values (NULL, '".$idti."', '".$fechag."', '".$trans."')";
                                $resultado1 = mysqli_query($conn, $sql1);

                                $sql2 ="insert into bitacora values ('NULL', '".$ussera."', 'FORM ENVIADO SUCURSAL $idti', 'INSERT', '".$fechabitacora."')";
                                $resultado2 = mysqli_query($conn, $sql2);
                                
                                header("Location:sucursal.php?perfil=$ussera&idt=$idti&feg=$fechag");
                            }
                            else{
                               print "<script> alert('YA HAY UN ENVIO CON ESA FECHA');</script>"; 
                            }
                        }
                        else {
                             print "<script> alert('DEBE ELEGIR UNA TIENDA');</script>";
                        }
                       
                    }
                ?>
                <form name="form2" method="post" action="">         
                   <table class="TablaE">
                        <tr>                        
                            <td width="80%"><input class="input-iud" name="tmedic" type="text" id="tmedic" placeholder="Escriba y Elija un Medicamento"><div id="nmedic"></div></td>  
                            <td width="10%"><input class="input-iud" name="pm" type="text" placeholder="#"></td>
                            <td width="10%"><button type="submit" name="insertmed" class="btn-iud" id="insertmed">✔</button></td>
                        </tr>           
                        <tr>
                            <td><input class="input-iud" name="taro" type="text" id="taro" placeholder="Escriba y Elija un Aro"><div id="naro"></div></td>  
                            <td><input class="input-iud" name="pa" type="text" placeholder="#"></td>                  
                            <td><button type="submit" name="insertaro" class="btn-iud" id="insertaro">✔</button></td>
                        </tr>           
                        <tr>
                            <td><input class="input-iud" name="tac" type="text" id="tac" placeholder="Escriba y Elija un Accesorio"><div id="nac"></div></td>  
                            <td><input class="input-iud" name="pacs" type="text"  placeholder="#"></td>                 
                            <td><button type="submit" name="insertac" class="btn-iud" id="insertac">✔</button></td>
                        </tr>
                    </table>
                    <?php        
                    $idmed = $_POST['tidpro2'];           
                    $pm = $_POST['pm'];
                    $idaro = $_POST['tidpro3'];           
                    $pa = $_POST['pa'];           
                    $idac = $_POST['tidpro5'];           
                    $pacs = $_POST['pacs'];

                    if (isset ($_POST['insertmed'])){  
                        if ($row3[0] != NULL) { 
                            if ($idmed != NULL) { 
                                $sql11="SELECT * FROM ubicacion WHERE idproducto = '".$idmed."' AND idtienda = 10";
                                $resultado11 = mysqli_query($conn, $sql11);
                                if($row11 = mysqli_fetch_array($resultado11)){ $idubicacion = $row11["idubicacion"]; $canub = $row11["cantubicacion"];}
                                if ($pm != NULL and $canub != NULL and $pm <= $canub ){  

                                        $sql2 ="UPDATE ubicacion SET cantubicacion = (cantubicacion - $pm) WHERE idubicacion = '".$idubicacion."'";
                                        $resultado2 = mysqli_query($conn, $sql2);

                                        $sql3 ="insert into detallegestionm values ('NULL', '".$row3[0]."', '".$idubicacion."', '".$pm."', '".$pm."', 'ENVIADO')";
                                        $resultado3 = mysqli_query($conn, $sql3);

                                        $sql4 ="insert into bitacora values ('NULL', '".$ussera."', 'FORM SUCURSAL PARA ".$idmed.", INSERT MEDICAMENTO', 'INSERT', '".$fechabitacora."')";
                                        $resultado4 = mysqli_query($conn, $sql4);

                                        header("Location:sucursal.php?perfil=$ussera&idt=$tidti&feg=$tfechg");
                                }
                                else {
                                    print "<script> alert('CANTIDAD INSUFICIENTE EN BODEGA PARA ENVIAR A SUCURSAL ');</script>";
                                }                
                            }
                            else{
                                 print "<script> alert('DEBE ELEGIR UN MEDICAMENTO DE LA LISTA');</script>";
                            }                
                        }
                        else {
                            print "<script> alert('PRIMERO DEBE ASIGNAR UNA TIENDA');</script>";
                        }                
                    }

                    if (isset ($_POST['insertaro'])){
                        if ($row3[0] != NULL) { 
                            if ($idaro != NULL) { 
                                $sql11="SELECT * FROM ubicacion WHERE idproducto = '".$idaro."' AND idtienda = 10";
                                $resultado11 = mysqli_query($conn, $sql11);
                                    if($row11 = mysqli_fetch_array($resultado11)){ $idubicacion = $row11["idubicacion"]; $canub = $row11["cantubicacion"];}

                                if ($pa <= $canub){                            
                                        $sql2 ="UPDATE ubicacion SET cantubicacion = (cantubicacion - $pa) WHERE idubicacion = '".$idubicacion."'";
                                        $resultado2 = mysqli_query($conn, $sql2);

                                        $sql3 ="insert into detallegestionm values ('NULL', '".$row3[0]."', '".$idubicacion."', '".$pa."', '".$pa."', 'ENVIADO')";
                                        $resultado3 = mysqli_query($conn, $sql3);

                                        $sql4 ="insert into bitacora values ('NULL', '".$ussera."', 'FORM SUCURSAL PARA ".$idaro.", INSERT ARO', 'INSERT', '".$fechabitacora."')";
                                        $resultado4 = mysqli_query($conn, $sql4);    

                                        header("Location:sucursal.php?perfil=$ussera&idt=$tidti&feg=$tfechg");
                                }
                                else {
                                    print "<script> alert('CANTIDAD INSUFICIENTE EN BODEGA PARA ENVIAR A SUCURSAL ');</script>";
                                }                 
                            }
                            else{
                                 print "<script> alert('DEBE ELEGIR UN ARO');</script>";
                            }
                        }
                        else {
                            print "<script> alert('PRIMERO DEBE ASIGNAR UNA TIENDA');</script>";
                        }                
                    }
                    if (isset ($_POST['insertac'])){
                        if ($row3[0] != NULL) { 
                            if ($idac != NULL) { 
                                $sql11="SELECT * FROM ubicacion WHERE idproducto = '".$idac."' AND idtienda = 10";
                                $resultado11 = mysqli_query($conn, $sql11);
                                    if($row11 = mysqli_fetch_array($resultado11)){ $idubicacion = $row11["idubicacion"]; $canub = $row11["cantubicacion"];}

                                if ($pacs <= $canub){                            
                                        $sql2 ="UPDATE ubicacion SET cantubicacion = (cantubicacion - $pacs) WHERE idubicacion = '".$idubicacion."'";
                                        $resultado2 = mysqli_query($conn, $sql2);

                                        $sql3 ="insert into detallegestionm values ('NULL', '".$row3[0]."', '".$idubicacion."', '".$pacs."', '".$pacs."', 'ENVIADO')";
                                        $resultado3 = mysqli_query($conn, $sql3);

                                        $sql4 ="insert into bitacora values ('NULL', '".$ussera."', 'FORM SUCURSAL PARA ".$idac.", INSERT ACCESORIO', 'INSERT', '".$fechabitacora."')";
                                        $resultado4 = mysqli_query($conn, $sql4);

                                        header("Location:sucursal.php?perfil=$ussera&idt=$tidti&feg=$tfechg");
                                }
                                else {
                                    print "<script> alert('CANTIDAD INSUFICIENTE EN BODEGA PARA ENVIAR A SUCURSAL ');</script>";
                                }                 
                            }
                            else{
                                 print "<script> alert('DEBE ELEGIR UN MEDICAMENTO');</script>";
                            }
                        }
                        else {
                            print "<script> alert('PRIMERO DEBE ASIGNAR UNA TIENDA');</script>";
                        }               
                    }
                    ?>  
                </form>
                <form name="form3" method="post" action=""> 
                    <table width = '100%'>
                        <tr><td colspan="2"><h3>ELIJE UNA FECHA Y UNA TIENDA LUEGO EN EL BOTON PARA BUSCAR</h3></td></tr>
                        <tr>                           
                            <td><input type="date" name="fbus" class="input-iud" id="fbus" required></td>
                            <td><input class="input-iud" name="ttienda2" type="text" id="ttienda2" placeholder="Escriba y Elija una Tienda" required><div id="ntienda2"></div></td> 
                            <td><div id="botones"><button type="submit" name="selectb" class="btn-universal"><span class="icon icon-search"></span></button></div></td>
                        </tr>
                    </table>                    
                </form>                
                <?php
                    $fbus = $_POST['fbus'];
                    $idti2 = $_POST['idti2'];
                    if (isset ($_POST['selectb'])){
                        header("Location:sucursal.php?perfil=$ussera&idt=$idti2&feg=$fbus"); 
                    }
                    
                    $sql10="SELECT producto.descproducto, SUM(detallegestionm.idcantidad), SUM(detallegestionm.cans) FROM gestionm
                                INNER JOIN detallegestionm ON detallegestionm.idgestion = gestionm.idgestion
                                INNER JOIN ubicacion ON ubicacion.idubicacion = detallegestionm.idubicacion
                                INNER JOIN producto ON producto.idproducto = ubicacion.idproducto
                                WHERE gestionm.idtienda = '".$tidti."' AND producto.tipoproducto = 'ARO' AND gestionm.fechagestion = '".$tfechg."' 
                                GROUP BY producto.descproducto   ORDER BY `producto`.`descproducto` ASC;";
                        $resultado10 = mysqli_query($conn, $sql10); 
                        print "<table class='TablaJ'>";                         
                            print "<tr>";                    
                            print "<td>MARCA DE ARO</td>";                    
                            print "<td>ENVIADOS</td>";
                            print "<td>RECIBIDOS</td>"; 
                            print "</tr>";
                            while ($row10 = mysqli_fetch_assoc($resultado10)){
                                print "<tr>";
                                    foreach ($row10 as $item10){
                                        print "<td>".($item10!==NULL ?htmlentities($item10):"&nbsp;")."</td>";
                                    }
                                    print "</tr>";
                                }
                        print "</table><br>";                 
                        $sql12="SELECT producto.descproducto, detallegestionm.idcantidad, detallegestionm.cans FROM gestionm
                                INNER JOIN detallegestionm ON detallegestionm.idgestion = gestionm.idgestion
                                INNER JOIN ubicacion ON ubicacion.idubicacion = detallegestionm.idubicacion
                                INNER JOIN producto ON producto.idproducto = ubicacion.idproducto
                                WHERE gestionm.idtienda = '".$tidti."' AND producto.tipoproducto = 'MEDICAMENTO' AND gestionm.fechagestion = '".$tfechg."' 
                                GROUP BY producto.descproducto   ORDER BY `producto`.`descproducto` ASC;";
                        $resultado12 = mysqli_query($conn, $sql12);                
                        print "<table class='TablaJ'>";                         
                            print "<tr>";                    
                            print "<td>MEDICAMENTO</td>";                    
                            print "<td>ENVIADOS</td>";
                            print "<td>RECIBIDOS</td>"; 
                            print "</tr>";
                            while ($row12 = mysqli_fetch_assoc($resultado12)){
                                print "<tr>";
                                    foreach ($row12 as $item12){
                                        print "<td>".($item12!==NULL ?htmlentities($item12):"&nbsp;")."</td>";
                                    }
                                    print "</tr>";
                                }
                        print "</table><br>";                
                        $sql13="SELECT producto.descproducto, detallegestionm.idcantidad, detallegestionm.cans FROM gestionm
                                INNER JOIN detallegestionm ON detallegestionm.idgestion = gestionm.idgestion
                                INNER JOIN ubicacion ON ubicacion.idubicacion = detallegestionm.idubicacion
                                INNER JOIN producto ON producto.idproducto = ubicacion.idproducto
                                WHERE gestionm.idtienda = '".$tidti."' AND producto.tipoproducto = 'ACCESORIO' AND gestionm.fechagestion = '".$tfechg."' 
                                GROUP BY producto.descproducto   ORDER BY `producto`.`descproducto` ASC;";
                        $resultado13 = mysqli_query($conn, $sql13);                
                        print "<table class='TablaJ'>";                         
                            print "<tr>";                    
                            print "<td>ACCESORIO</td>";                    
                            print "<td>ENVIADOS</td>";
                            print "<td>RECIBIDOS</td>"; 
                            print "</tr>";
                            while ($row13 = mysqli_fetch_assoc($resultado13)){
                                print "<tr>";
                                    foreach ($row13 as $item13){
                                        print "<td>".($item13!==NULL ?htmlentities($item13):"&nbsp;")."</td>";
                                    }
                                    print "</tr>";
                                }
                        print "</table>";    
                ?> 
            </div>              
            <?php 
            print "<div class='columna'>";               
                print "<table class='TablaA'>";
                    print "<tr><td width='35%'>FECHA:</td><td>$row3[2]</td></tr>"; 
                    print "<tr><td width='35%'>TIENDA:</td><td>$row3[5]</td></tr>";
                    print "<tr><td width='35%'>TRANSPORTE:</td><td>$row3[3]</td></tr>";
                print "</table>";
                print "<form name='form' method='post' action=''>";
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

                                $sql6 ="update ubicacion SET cantubicacion = (cantubicacion + $tcanu) where idubicacion = $idub";
                                $resultado6 = mysqli_query($conn, $sql6);

                                $sql5 ="delete from detallegestionm where iddgestionm = '".$tid."'";
                                $resultado5 = mysqli_query($conn, $sql5);                                   

                                $sql7 ="insert into bitacora values ('NULL', '".$ussera."', 'FORM SUCURSAL', 'DELETE', '".$fechabitacora."')";
                                $resultado7 = mysqli_query($conn, $sql7);

                                echo '<meta http-equiv=refresh content="0">';
                            }
                        }
                    print "</table>";
                print "</form>";
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
