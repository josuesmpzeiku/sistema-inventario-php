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
        <div class="color-arriba"><h2 class ="subtitulo">CITAS POR ATENDER</h2></div><br>  
        <?php
            $sql11 ="SELECT paciente.nompaciente, cita.fecita, cita.idatencion, cita.idcita FROM cita
                    INNER JOIN atencion ON atencion.idatencion = cita.idatencion
                    INNER JOIN paciente on paciente.idpaciente = atencion.idpaciente
                    WHERE atencion.idtienda = '".$tienda."' and cita.estadoc = 'PENDIENTE' ORDER BY cita.fecita";
            $resultado11 = mysqli_query($conn, $sql11);
            while ($row11 = mysqli_fetch_array($resultado11)){                     
                print "<button class='btn-listado'>{$row11['nompaciente']},  {$row11['fecita']}</button>"; 
                print "<div class='contenedor-lista'>";
                    $sql1="SELECT paciente.*, atencion.*, TIMESTAMPDIFF(YEAR,fecnpaciente,CURDATE()) AS edad FROM paciente INNER JOIN atencion ON atencion.idpaciente = paciente.idpaciente WHERE idatencion = {$row11['idatencion']}";
                    $resultado1 = mysqli_query($conn, $sql1);
                    if ($row1 = mysqli_fetch_row($resultado1)){ }
                    $sql2="SELECT rescuest FROM cuestionario WHERE idatencion = '{$row11['idatencion']}' and precuest = 'A que se dedica'";
                    $resultado2 = mysqli_query($conn, $sql2);
                    if ($row2 = mysqli_fetch_row($resultado2)){ }
                    print "<div class='fila'>";
                        print "<div class='columna'>";
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
                                print "<tr><td><b> Motivo Consulta:</b>  $row1[6]</td></tr>";
                                print "<tr><td><b> A que se dedica:</b>  $row2[0]</td></tr>";
                                $sql4="SELECT precuest, rescuest FROM cuestionario WHERE idatencion = {$row11['idatencion']} ORDER by idcuest asc LIMIT 15,5";
                                $resultado4 = mysqli_query($conn, $sql4);
                                while ($row4 = mysqli_fetch_array($resultado4)){
                                    print "<tr><td><b> {$row4['precuest']}</b>  {$row4['rescuest']}</tr>";
                                }
                            print "</table>";
                        print "</div>";
                        print "<div class='columna'>";
                            print "<table class = 'TablaB'>";
                                $sql3="SELECT precuest, rescuest FROM cuestionario WHERE idatencion = {$row11['idatencion']} ORDER by idcuest asc LIMIT 0,14";
                                $resultado3 = mysqli_query($conn, $sql3);
                                while ($row3 = mysqli_fetch_array($resultado3)){
                                    print "<tr><td width = '85%'><b> {$row3['precuest']}</b></td><td>  {$row3['rescuest']}</td></tr>";
                                }
                                $sql5="SELECT producto.descproducto FROM producto INNER JOIN detallereceta ON detallereceta.idproducto = producto.idproducto WHERE detallereceta.idreceta = {$row11['idatencion']} ";
                                $resultado5 = mysqli_query($conn, $sql5);
                                print "<tr><td colspan='2'><b> Medicamento:</b>   ";
                                while ($row5 = mysqli_fetch_array($resultado5)){ 
                                    print "{$row5['descproducto']}    ";
                                    } 
                                print "</td></tr>";
                                $sql6="SELECT fecita FROM cita WHERE idatencion = {$row11['idatencion']}";
                                $resultado6 = mysqli_query($conn, $sql6);
                                if ($row6 = mysqli_fetch_row($resultado6)){ }
                                setlocale(LC_TIME, "spanish.utf8");               
                                $fechacita= strftime("%A, %d de %B de %Y  a partir de las %H:%M horas ", strtotime($row6[0]));
                                print "<tr><td colspan = '2'><b>Proxima Cita:   </b>$fechacita</td></tr>";
                            print "</table>";                         
                        print "</div>";
                    print "</div>";
                    print "<div class='fila'>";
                        print "<div class='columna'>";
                            print "<b>AUTO REFRACTÓMETRO</b>";
                            $sql7="SELECT ojograd, esferagrad, cilindrograd, ejegrad, dipgrad, addgrad FROM graduacion WHERE idatencion = {$row11['idatencion']} AND usuariograd = 'REFRACTÓMETRO'";
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
                            $sql8="SELECT ojograd, esferagrad, cilindrograd, ejegrad, dipgrad, addgrad FROM graduacion WHERE idatencion = {$row11['idatencion']} AND usuariograd = 'LENSOMETRÍA'";
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
                        print "</div>";
                        print "<div class='columna'>";
                            print "<b>GRADUACIÓN TOTAL</b>";
                            $sql9="SELECT ojograd, esferagrad, cilindrograd, ejegrad, dipgrad, addgrad FROM graduacion WHERE idatencion = {$row11['idatencion']} AND usuariograd = 'ESPECIALISTA'";
                            $resultado9 = mysqli_query($conn, $sql9);
                            print "<table class='TablaG'>";                         
                            print "<tr>";
                            print "<td>OJO</td>";
                            print "<td>ESF</td>";
                            print "<td>CIL</td>"; 
                            print "<td>EJE</td>";
                            print "<td>DIP</td>";
                            print "<td>ADD</td>";                           
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
                                    WHERE detalleventa.idventa = {$row11['idatencion']} AND producto.tipoproducto = 'LENTE';";
                            $resultado10 = mysqli_query($conn, $sql10);
                            if ($row10 = mysqli_fetch_row($resultado10)){ }        
                            print "<tr><td> MATERIAL:</td>";
                            print "<td colspan = '7'>$row10[0]</td></tr><br>";
                            print "</table>";
                        print "</div>";
                    print "</div>";
                    print "<div class='fila'>";
                        print "<div class='columna'>";
                            echo "<form name='form' method='post' action=''>";                    
                                $fila = $row1[0]; 
                                echo "<div id='botones'><button type='submit' name='insa".$fila."' class='btn-universal'>Atender</button>"; 
                                echo "<button type='submit' name='updc".$fila."' class='btn-universal'>No se Presentó</button></div>";                       
                            echo "</form>";
                            $sql12="SELECT COUNT(*) as conteo FROM atencion";
                            $resultado12 = mysqli_query($conn, $sql12);
                            if($row12 = mysqli_fetch_array($resultado12)){ $conteo = $row12["conteo"]; }
                            $cuenta = $conteo + 1;               
                            $numero = str_pad($cuenta, 9, "0", STR_PAD_LEFT);                            
                            $idaten = $tienda.$numero;

                            date_default_timezone_set('America/Guatemala');                          
                            $fechaten = date('Y-m-d'); 

                            if (isset ($_POST['insa'.$fila])){ 
                                $sql9 ="insert into atencion values ('".$idaten."', '".$fila."', '".$fechaten."', 'RECEPCION', '".$tienda."')";
                                $resultado9 = mysqli_query($conn, $sql9); 

                                $sql1 ="insert into venta values ('".$idaten."', '".$fechaten."', '', '0')";
                                $resultado1 = mysqli_query($conn, $sql1);

                                $sql6 ="update cita SET fecaten = '".$fechabitacora."', estadoc = 'ATENDIDO ".$idaten."' where idcita = {$row11['idcita']}";
                                $resultado6 = mysqli_query($conn, $sql6);

                                $sql3 ="insert into bitacora values ('NULL', '".$ussera."', 'FORM CITA IDATENCION ".$idaten."', 'UPDATE', '".$fechabitacora."')";
                                $resultado3 = mysqli_query($conn, $sql3);

                                echo '<meta http-equiv=refresh content="0">';
                            } 
                             if (isset ($_POST['updc'.$fila])){

                                $sql6 ="update cita SET fecaten = '".$fechabitacora."', estadoc = 'NO SE PRESENTÓ' where idcita = {$row11['idcita']}";
                                $resultado6 = mysqli_query($conn, $sql6);

                                $sql3 ="insert into bitacora values ('NULL', '".$ussera."', 'FORM CITA IDATENCION ".$idaten."', 'UPDATE', '".$fechabitacora."')";
                                $resultado3 = mysqli_query($conn, $sql3);

                                echo '<meta http-equiv=refresh content="0">';
                            } 
                        print "</div>";
                    print "</div><br>"; 
                print "</div><br><br>";
            }
        ?>
	<script type="text/javascript" src="js/jquery-3.6.1.min.js"></script> 
        <script type="text/javascript" src="js/jquery-ui.js"></script>
        <script type="text/javascript" src="js/misfunciones.js"></script>
        <script type="text/javascript" src="js/versection.js"></script> 
        <script type="text/javascript" src="js/funcionlista.js"></script>
    </body>
</html>
