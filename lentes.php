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
    $nomtienda = $_GET['tienda'];
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
        <div class="color-arriba"><h2 class ="subtitulo">TIEMPO ESTIMADO DE ENTREGA</h2></div> 
        <div class="fila">
            <div class="columna">
            <?php
            $sql6="SELECT ojograd, esferagrad, cilindrograd, ejegrad, dipgrad, addgrad FROM graduacion WHERE idatencion = '".$idaten."' AND usuariograd = 'ESPECIALISTA'";
            $resultado6 = mysqli_query($conn, $sql6);
            $sql9="SELECT venta.*, paciente.* from venta 
                    INNER JOIN atencion ON atencion.idatencion = venta.idventa
                    INNER JOIN paciente ON paciente.idpaciente = atencion.idpaciente
                    where venta.idventa = '".$idaten."'";
            $resultado9 = mysqli_query($conn, $sql9);
            if ($row9 = mysqli_fetch_row($resultado9)){ }
            print "<label><b>CONTRASEÑA:  </b>".$idaten."   <b>TIENDA:  </b>".$nomtienda."</label><br>";
            print "<label><b>PACIENTE:  </b>$row9[6]</label><br>";
            print "<b>GRADUACIÓN TOTAL</b>";
                print "<table class='TablaG'>";                         
                print "<tr>";
                print "<td>OJO</td>";
                print "<td>ESF</td>";
                print "<td>CIL</td>"; 
                print "<td>EJE</td>";
                print "<td>DIP</td>";
                print "<td>ADD</td>";
                print "</tr>";
                    while ($row6 = mysqli_fetch_assoc($resultado6)){
                        print "<tr>";
                        foreach ($row6 as $item6){
                            print "<td>".($item6!==NULL ?htmlentities($item6):"&nbsp;")."</td>";
                        }
                        print "</tr>";
                    }
                $sql7="SELECT producto.descproducto FROM producto INNER JOIN ubicacion ON ubicacion.idproducto = producto.idproducto
                        INNER JOIN detalleventa ON detalleventa.idubicacion = ubicacion.idubicacion
                        WHERE detalleventa.idventa = '".$idaten."' AND producto.tipoproducto = 'LENTE';";
                $resultado7 = mysqli_query($conn, $sql7);
                if ($row7 = mysqli_fetch_row($resultado7)){ }        
                print "<tr><td> LENTE:</td>";
                print "<td colspan = '7'>$row7[0]</td></tr>";
                $sql8="SELECT producto.descproducto, producto.claseproducto FROM producto INNER JOIN ubicacion ON ubicacion.idproducto = producto.idproducto
                        INNER JOIN detalleventa ON detalleventa.idubicacion = ubicacion.idubicacion
                        WHERE detalleventa.idventa = '".$idaten."' AND producto.tipoproducto = 'ARO';";
                $resultado8 = mysqli_query($conn, $sql8);
                if ($row8 = mysqli_fetch_row($resultado8)){ }        
                print "<tr><td> ARO:</td>";
                print "<td colspan = '7'>$row8[0], $row8[1]</td></tr>";
                print "<tr><td> OBSERVACION:</td>";
                print "<td colspan = '7'>$row9[2]</td></tr>";
                print "<tr>";
                print "<td rowspan='2'>MEDIDA <br>DEL ARO</td>";
                print "<td>V</td>";
                print "<td>H</td>"; 
                print "<td>D</td>";
                print "<td>P</td>";
                print "<td>A</td>";
                print "</tr>";
                $sql3="SELECT vma, hma, dma, pma, abma FROM maro WHERE idmaro = '".$idaten."'";
                $resultado3 = mysqli_query($conn, $sql3);
                while ($row3 = mysqli_fetch_assoc($resultado3)){
                    print "<tr>";
                    foreach ($row3 as $item3){
                        print "<td>".($item3!==NULL ?htmlentities($item3):"&nbsp;")."</td>";
                    }
                    print "</tr>";
                }
            print "</table>"; 
            echo "<form name='form1' method='post' action=''>";                                
                echo "<div class='botones2'><label>POSIBLE ENTREGA:</label> ";
                echo "<input name='tfpe' type='datetime-local' class='input-iud' title='Elija una Fecha y Hora'  required>";
                echo "<button type='submit' name='insfpd' class='btn-iud'>✔</button></div>"; 
            echo "</form>";
            echo "<form name='form2' method='post' action=''>";                                
                echo "<div class='botones2'><label>RECIBIDO:</label>  ";
                echo "<input name='tfr' type='datetime-local' class='input-iud' title='Elija una Fecha y Hora'  required>";
                echo "<button type='submit' name='updfr' class='btn-iud'>✔</button></div>"; 
            echo "</form>";
            echo "<form name='form3' method='post' action=''>";                                
                echo "<div class='botones2'><label>TERMINADO:</label>  ";
                echo "<input name='tft' type='datetime-local' class='input-iud' title='Elija una Fecha y Hora'  required>";
                echo "<button type='submit' name='updft' class='btn-iud'>✔</button></div>"; 
            echo "</form>";                    
            $sql2 ="SELECT * from entrega where identrega = '".$idaten."' ";
            $resultado2 = mysqli_query($conn, $sql2);
            if ($row2 = mysqli_fetch_row($resultado2)){ } 
            print "<table class='TablaG'>";
                print "<tr><td colspan = '2'>FECHAS DEL PRODUCTO</td></tr>";
                print "<tr><td>POSIBLE ENTREGA:  </td><td>$row2[2]</td></tr>";
                print "<tr><td>RECIBIDO:  </td><td>$row2[3]</td></tr>";
                print "<tr><td>FINALIZADO:  </td><td>$row2[4]</td></tr>";
            print "</table>";

            $tfpe = $_POST['tfpe'];
            $tfr = $_POST['tfr'];
            $tft = $_POST['tft'];

            if (isset ($_POST['insfpd'.$fila])){ 
                $sql3 ="update entrega SET fecrent = '".$tfpe."', estadoe = 'T' where identrega = '".$idaten."'";
                $resultado3 = mysqli_query($conn, $sql3);

                $sql4 ="insert into bitacora values ('NULL', '".$ussera."', 'FORM FECHA POSIBLE DE ENTREGA PARA $idaten', 'INSERT', '".$fechabitacora."')";
                $resultado4 = mysqli_query($conn, $sql4);
                echo '<meta http-equiv=refresh content="0">';
}

            if (isset ($_POST['updfr'.$fila])){ 
                $sql3 ="update entrega SET fecesent = '".$tfr."' where identrega = '".$idaten."'";
                $resultado3 = mysqli_query($conn, $sql3);

                $sql4 ="insert into bitacora values ('NULL', '".$ussera."', 'FORM ENTREGA FECHA RECIBIDO PARA $idaten', 'UPDATE', '".$fechabitacora."')";
                $resultado4 = mysqli_query($conn, $sql4);
                echo '<meta http-equiv=refresh content="0">';
            }

            if (isset ($_POST['updft'.$fila])){ 
                $sql3 ="update entrega SET fecent = '".$tft."', estadoe = 'F' where identrega = '".$idaten."'";
                $resultado3 = mysqli_query($conn, $sql3);

                $sql4 ="insert into bitacora values ('NULL', '".$ussera."', 'FORM ENTREGA FECHA FINALIZADO PARA $idaten', 'UPDATE', '".$fechabitacora."')";
                $resultado4 = mysqli_query($conn, $sql4);
                echo '<meta http-equiv=refresh content="0">';
            } 
            print "<br><a class='btn-universal' href='armado.php?perfil=".$ussera."&atencion=".$idaten."&tienda=".$tienda."'><span class='icon icon-wrench'></span></a><br>";                                 
       
            ?>
                
            </div>
            <div class="columna">
                <form name='form4' method='post' action=''>
                    <select name="tbuscarm" class="decorar-input" required="">
                             <option value="" disabled selected>Elija un Material</option>
                            <option>ANTI BLUE AR VERDE</option>
                            <option>ANTI BLUE AR VERDE POSITIVO</option>
                            <option>PC PHOTOCROMATICO GREY AR VERDE</option>
                            <option>CR-39 PHOTOCROMATICO GREY AR VERDE</option>
                            <option>PC PHOTOCROMATICO GREY AR VERDE POSITIVO</option>
                            <option>CR-39 PHOTOCROMATICO GREY AR VERDE POSITIVO</option>
                            <option>PC BLUE RAY AR AZUL</option>
                            <option>PC BLUE RAY AR AZUL POSITIVO</option>
                            <option>CR-39 PHOTOBLUE AR VERDE</option>
                            <option>PC CON AR VERDE</option>
                            <option>PC CON AR VERDE POSITIVO</option>
                            <option>VIDRIO PGX</option>
                            <option>VIDRIO BLANCO</option>
                            <option>CR-39 CON AR VERDE</option>
                            <option>1.56 CR-39</option>
                            <option>1.59 PC BFT</option>
                            <option>1.56 CR-39 PROG</option>
                            <option>1.59  PC ANTI BLUE AR VERDE</option>    
                    </select>
                    <div id="botones"><button type="submit" name="buscarl" class="btn-universal" id="buscarl"><span class="icon icon-search"></span></button></div>
                </form>
         <?php
        $buscam = $_POST['tbuscarm'];
        if (isset ($_POST['buscarl'])){ 
            $sql1="SELECT lentedes, canlente FROM lente WHERE lentedes LIKE '%".$buscam."%' AND canlente != 0";
            $resultado1 = mysqli_query($conn, $sql1);
                print "<table class='TablaH'>";                         
                print "<tr>";
                print "<td>MATERIAL</td>";
                print "<td>CANTIDAD</td>";
                print "</tr>";
                    while ($row1 = mysqli_fetch_assoc($resultado1)){
                        print "<tr>";
                        foreach ($row1 as $item1){
                            print "<td>".($item1!==NULL ?htmlentities($item1):"&nbsp;")."</td>";
                        }
                        print "</tr>";
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