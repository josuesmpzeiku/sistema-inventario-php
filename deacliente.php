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
        <title>DETALLE DE VENTA AL CLIENTE</title>
    </head>
    <body>        
        <?php
            $sql3="SELECT paciente.nompaciente, atencion.fecatencion FROM paciente INNER JOIN atencion
                    ON atencion.idpaciente = paciente.idpaciente WHERE idatencion = '".$idaten."'";
            $resultado3 = mysqli_query($conn, $sql3);            
            if($row3 = mysqli_fetch_array($resultado3)){ $nompa = $row3["nompaciente"]; $feat = $row3["fecatencion"];}
            
            $sql7="SELECT * FROM tienda WHERE idtienda ='".$tienda."'";
                        $resultado7 = mysqli_query($conn, $sql7);
                            if ($row7 = mysqli_fetch_row($resultado7)){ }
         
            print"<div class='contenedor'>";
            print"<div class='tabla3'>";            
                print "<table class = 'TablaM3'>";
                print "<tr><td rowspan='3'><img src='".$url."img/logo.png' width='100px' height='100px'></td><td><h1 class='titulo2'>OPTICA MACARIO</h1></td><td rowspan='3'><img src='".$url."img/logo.png' width='100px' height='100px'></td></tr>";
                print "<tr><td align='center'>$row7[2]</td></tr>";
                print "<tr><td align='center'><b>FECHA: </b>$feat</td></tr>";               
                print "<tr><td colspan='3'><b>NOMBRE: </b>$nompa</td>";
                print "<tr><td colspan='3'>"; 
                    $sql1="SELECT detalleventa.cantdventa, producto.tipoproducto, producto.descproducto, CONCAT(detalleventa.descdventa,' %'), CONCAT('Q ', detalleventa.subtdventa) 
                        FROM detalleventa INNER JOIN ubicacion ON ubicacion.idubicacion = detalleventa.idubicacion
                        INNER JOIN producto ON producto.idproducto = ubicacion.idproducto
                        WHERE detalleventa.idventa = '".$idaten."'";
                    $resultado1 = mysqli_query($conn, $sql1);
                    print "<table class='TablaB'>";                         
                        print "<tr>";
                        print "<td width='5%'>CANT</td>";
                        print "<td width='15%'>TIPO</td>";
                        print "<td>DESCRIPCIÓN</td>"; 
                        print "<td width='10%'>-%</td>";
                        print "<td width='15%'>SUBTOTAL</td>";                        
                        print "</tr>";
                            while ($row1 = mysqli_fetch_assoc($resultado1)){
                                print "<tr>";
                                foreach ($row1 as $item1){
                                    print "<td>".($item1!==NULL ?htmlentities($item1):"&nbsp;")."</td>";
                                }
                                print "</tr>";
                            }
                        print "<tr><td colspan='5'></td></tr>";
                        $sql2="SELECT SUM(pago.cantpago), venta.totalventa, (venta.totalventa - SUM(pago.cantpago)) as pendiente 
                        FROM venta INNER JOIN pago ON pago.idventa = venta.idventa
                        WHERE venta.idventa = '".$idaten."';";
                        $resultado2 = mysqli_query($conn, $sql2);
                            if ($row2 = mysqli_fetch_row($resultado2)){ }
                        $sql5="SELECT SUM(subtdventa) FROM `detalleventa` WHERE idventa = '".$idaten."';";
                        $resultado5 = mysqli_query($conn, $sql5);
                            if ($row5 = mysqli_fetch_row($resultado5)){ }    
                        print "<tr><td colspan='2'><b>ANTICIPO: </b>Q $row2[0]</td><td><b>PENDIENTE: </b>Q $row2[2]</td><td colspan='2'><b>TOTAL: Q $row5[0]</b></td></tr>";
                        $sql4="SELECT * FROM entrega WHERE identrega = '".$idaten."'";
                        $resultado4 = mysqli_query($conn, $sql4);
                            if ($row4 = mysqli_fetch_row($resultado4)){ }
                            setlocale(LC_TIME, "spanish.utf8");                           
                            $fechacom = strftime("%A, %d de %B de %Y  a partir de las %H:%M horas ", strtotime($row4[2]));
                            
                        print "<tr><td colspan='2'><b>FECHA ENTREGA:</b></td><td colspan='4'>$fechacom</td></tr>";                        
                    print "</table>";                
                print "</td></tr>";
                print "<tr><td colspan='3' align='center'><b>ES UN GUSTO SERVIRLE</b></td></tr>";             
                print "</table>"; 
                print "<button onclick='cerrarVentana()'>✕</button>";
            print"</div>";
            print"</div>";
        ?>    
        <script>
function cerrarVentana() {
  window.open('', '_self').close();
}
</script>   
    </body>
</html>
