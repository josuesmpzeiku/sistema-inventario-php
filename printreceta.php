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
        <link rel="stylesheet" href="css/print.css">   
        <title>Receta</title>
    </head>
    <body> 
        <?php
            $sql7="SELECT * from tienda WHERE idtienda = '".$tienda."'";
            $resultado7 = mysqli_query($conn, $sql7);
            if($row7 = mysqli_fetch_array($resultado7)){ } 
            
            $sql6="SELECT paciente.nompaciente, paciente.idpaciente FROM paciente INNER JOIN atencion
                    ON atencion.idpaciente = paciente.idpaciente WHERE idatencion = '".$idaten."'";
            $resultado6 = mysqli_query($conn, $sql6);            
            if($row6 = mysqli_fetch_array($resultado6)){ $nompa = $row6["nompaciente"]; $idpac = $row6["idpaciente"];}
            
            $sql1="SELECT * from receta WHERE idreceta = '".$idaten."'";
            $resultado1 = mysqli_query($conn, $sql1);
            if($row1 = mysqli_fetch_array($resultado1)){ } 

            $sql2="SELECT * from cita WHERE idatencion = '".$idaten."'";
            $resultado2 = mysqli_query($conn, $sql2);
            if($row2 = mysqli_fetch_array($resultado2)){ }
            
            $sql4="SELECT * from cuestionario WHERE idatencion = '".$idaten."' and precuest = 'Patologia'";
            $resultado4 = mysqli_query($conn, $sql4);
            if($row4 = mysqli_fetch_array($resultado4)){ }
            
            print"<div class='contenedor'>";
            print"<div class='tabla2'>";            
                print "<table class = 'TablaM2'>";
                print "<tr><td rowspan='3'><img src='".$url."img/logo.png' width='75px' height='75px'></td><td><button class='btn-universal' type='button' onclick='cerrarVentana()'>OPTICA MACARIO</button></td><td rowspan='3'><img src='".$url."img/logo.png' width='75px' height='75px'></td></tr>";
                print "<tr><td align='center'>$row7[1]</td></tr>";
                print "<tr><td align='center'>$row7[2]  Cel $row7[3]</td></tr>";
                print "<tr><td colspan='3' align='right'><br><b>FECHA: </b>$row1[2]</td></tr>";
                print "<tr><td colspan='3'><b>NOMBRE: </b>$nompa</td></tr>";
                print "<tr><td colspan='3'>";
                $sql5="SELECT ojograd, esferagrad, cilindrograd, ejegrad, dipgrad, addgrad FROM graduacion WHERE idatencion = $idaten AND usuariograd = 'ESPECIALISTA'";
                $resultado5 = mysqli_query($conn, $sql5);
                    print "<table class='TablaB'>";                         
                        print "<tr>";
                        print "<td >OJO</td>";
                        print "<td width='15%'>ESF</td>";
                        print "<td width='15%'>CIL</td>"; 
                        print "<td width='15%'>EJE</td>";
                        print "<td width='15%'>DIP</td>";
                        print "<td width='15%'>ADD</td>";
                        print "</tr>";
                            while ($row5 = mysqli_fetch_assoc($resultado5)){
                                print "<tr>";
                                foreach ($row5 as $item5){
                                    print "<td>".($item5!==NULL ?htmlentities($item5):"&nbsp;")."</td>";
                                }
                                print "</tr>";
                            }
                    print "</table>";
                print "</td></tr>";
                print "<tr><td colspan='3'>";
                    print "<table class = 'TablaA'>";
                    print "<tr><td><b>  PATOLOGIA:</b> $row4[3] </td></tr>";
                    print "<tr><td>";
                        $sql3="SELECT detallereceta.cantreceta, producto.descproducto, detallereceta.dosisreceta 
                        FROM detallereceta INNER JOIN producto ON producto.idproducto = detallereceta.idproducto
                        WHERE detallereceta.idreceta =  '".$idaten."'";
                        $resultado3 = mysqli_query($conn, $sql3);
                        while ($row3 = mysqli_fetch_array($resultado3)){
                            print "   <b>{$row3['cantreceta']},  {$row3['descproducto']}</b><br>   {$row3['dosisreceta']}<br><br>";
                        }
                    print "</td></tr>";
                    print "<tr><td>  <b>PROXIMA CITA: </b>   $row2[3]</td></tr>";
                    print "</table>"; 
                print "</td></tr>";
                print "<tr><td colspan='3' align='center'><b>PRESTIGIO - CALIDAD - DISTINCION</b></td></tr>";
                print "<tr><td colspan='3' align='center'><b>¡TIENES MUCHO QUE VER!</b></td></tr>";
                print "<tr><td colspan='3' align='center'>Sugerencias o reclamos cel: 53174181 valido por ocho dias depués de la entrega</td></tr>";
                print "<tr><td> </td></tr>";
                $sql13="SELECT nomtienda, teltienda FROM tienda where idtienda != 10";
                $resultado13 = mysqli_query($conn, $sql13);
                    print "<tr><td colspan='3' align='center'><font size=1.5>";
                        while ($row13 = mysqli_fetch_array($resultado13)){ 
                            print "<b>{$row13['nomtienda']}</b>  Cel: {$row13['teltienda']}   ";
                        } 
                    print "</font></td></tr>";
                print "</table>";   
            print"</div>";
            print"</div>";
        ?>  
        <script>
            function cerrarVentana() {
            // Intenta cerrar en navegadores estándar
                window.open('', '_self', ''); 
                window.close();
    
            // Si lo anterior falla (algunos navegadores móviles), intenta este:
                setTimeout(function() {
                window.history.back(); // Como respaldo, regresa a la página anterior
                }, 500);
            }
        </script>
    </body>
</html>