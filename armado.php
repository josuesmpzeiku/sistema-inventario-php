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
    $sql="Select * from atencion  WHERE idatencion = '".$idaten."'";
    $resultado = mysqli_query($conn, $sql);
    if($row = mysqli_fetch_array($resultado)){ $tienda = $row["idtienda"]; $faten = $row["fecatencion"];}

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
        <div class="color-arriba"><h2 class ="subtitulo">COSTOS Y MATERIAL PARA LENTES</h2></div>
        <div class="fila">
            <div class="columna">
                <h3>CONTRASEÑA:    <?php echo $idaten;?></h3>
                <form name="form1" method="post" action="">  
                    <select name="ttlen" class="decorar-input" required="">
                        <option value="" disabled selected>Elija un Tipo de Lente</option>
                        <option>Simple Vision</option>
                        <option>Bifocal</option> 
                        <option>Progresivo</option>
                    </select>
                    <select name="ttallado" class="decorar-input">
                        <option value="" disabled selected>Elija un Tallado</option>
                        <option>1.74</option>
                        <option>1.67</option>                        
                    </select>
                    <input class="decorar-input" name="lder" type="text" id="lder" placeholder="Escriba y Elija un Lente o Base para el LENTE DERECHO" required><div id="nder"></div> 
                    <input class="decorar-input" name="lizq" type="text" id="lizq" placeholder="Escriba y Elija un Lente o Base para el LENTE IZQUIERDO" required><div id="nizq"></div> 
                    <input class="decorar-input" name="grad" type="text" placeholder="Detalle de las graduaciones a realizar en los lentes" required>
                    <input class="decorar-input" name="add" type="text" placeholder="ADD">
                    <input class="decorar-input" name="otro" type="text" placeholder="COSTO" required> 
                    <div id="botones"><button type="submit" name="inserta" class="btn-universal" id="inserta"><span class="icon icon-floppy-disk"></span></button></div> 
                </form>
            </div>
            <div class="columna">
                <?php
            $sql1="SELECT ojograd, esferagrad, cilindrograd, ejegrad, dipgrad, addgrad FROM graduacion WHERE idatencion = '".$idaten."' AND usuariograd = 'ESPECIALISTA'";
            $resultado1 = mysqli_query($conn, $sql1);
            print "<label><b>CONTRASEÑA:  </b>".$idaten."</label>";
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
                    while ($row1 = mysqli_fetch_assoc($resultado1)){
                        print "<tr>";
                        foreach ($row1 as $item1){
                            print "<td>".($item1!==NULL ?htmlentities($item1):"&nbsp;")."</td>";
                        }
                        print "</tr>";
                    }
                $sql2="SELECT producto.descproducto FROM producto INNER JOIN ubicacion ON ubicacion.idproducto = producto.idproducto
                        INNER JOIN detalleventa ON detalleventa.idubicacion = ubicacion.idubicacion
                        WHERE detalleventa.idventa = '".$idaten."' AND producto.tipoproducto = 'LENTE';";
                $resultado2 = mysqli_query($conn, $sql2);
                if ($row2 = mysqli_fetch_row($resultado2)){ }        
                print "<tr><td> MATERIAL:</td>";
                print "<td colspan = '7'>$row2[0]</td></tr>";
                $sql3="SELECT producto.descproducto, producto.claseproducto FROM producto INNER JOIN ubicacion ON ubicacion.idproducto = producto.idproducto
                        INNER JOIN detalleventa ON detalleventa.idubicacion = ubicacion.idubicacion
                        WHERE detalleventa.idventa = '".$idaten."' AND producto.tipoproducto = 'ARO';";
                $resultado3 = mysqli_query($conn, $sql3);
                if ($row3 = mysqli_fetch_row($resultado3)){ }        
                print "<tr><td> LENTE:</td>";
                print "<td colspan = '7'>$row3[0], $row3[1]</td></tr>";
                print "<tr>";
                print "<td rowspan='2'>MEDIDA <br>DEL ARO</td>";
                print "<td>V</td>";
                print "<td>H</td>"; 
                print "<td>D</td>";
                print "<td>P</td>";
                print "<td>A</td>";
                print "</tr>";
                $sql4="SELECT vma, hma, dma, pma, abma FROM maro WHERE idmaro = '".$idaten."'";
                $resultado4 = mysqli_query($conn, $sql4);
                while ($row4 = mysqli_fetch_assoc($resultado4)){
                    print "<tr>";
                    foreach ($row4 as $item4){
                        print "<td>".($item4!==NULL ?htmlentities($item4):"&nbsp;")."</td>";
                    }
                    print "</tr>";
                }
            print "</table>"; 
            
            $sql8 ="SELECT prearm1, tipolarm, talladoarm, gradarm, addarm, prearm2 FROM armado WHERE idarm = '".$idaten."'";
            $resultado8 = mysqli_query($conn, $sql8);
            
            print "<br><b>COSTOS</b>";
                print "<table class='TablaH'>";                         
                print "<tr>";
                print "<td>COSTO</td>";
                print "<td>TIPO LENTE</td>";
                print "<td>TALLADO</td>"; 
                print "<td>GRADUACION</td>";
                print "<td>ADD</td>";
                print "<td>COSTO</td>";
                print "</tr>";
                    while ($row8 = mysqli_fetch_assoc($resultado8)){
                        print "<tr>";
                        foreach ($row8 as $item8){
                            print "<td>".($item8!==NULL ?htmlentities($item8):"&nbsp;")."</td>";
                        }
                        print "</tr>";
                    }
                print "</table>";                
                print "<table class='TablaG'>";                         
                print "<tr>";
                print "<td>LENTE</td>";
                print "<td>MATERIAL</td></tr>";
                $sql9="SELECT lente.lentedes FROM armado INNER JOIN lente ON lente.idlente = armado.mderarm  WHERE armado.idarm = '".$idaten."'";
                $resultado9 = mysqli_query($conn, $sql9);
                if ($row9 = mysqli_fetch_row($resultado9)){ } 
                print "<tr><td>DERECHO</td><td>$row9[0]</td></tr>";
                $sql10="SELECT lente.lentedes FROM armado INNER JOIN lente ON lente.idlente = armado.mizqarm  WHERE armado.idarm = '".$idaten."'";
                $resultado10 = mysqli_query($conn, $sql10);
                if ($row10 = mysqli_fetch_row($resultado10)){ } 
                print "<tr><td>IZQUIERDO</td><td>$row10[0]</td></tr>";
            ?>
            </div>
        </div>        
        <?php
            $ttlen = $_POST['ttlen'];
            $ttallado = $_POST['ttallado'];               
            $lder = $_POST['idled'];        
            $lizq = $_POST['idlei'];
            $grad = $_POST['grad'];
            $add = $_POST['add'];
            $otro = $_POST['otro'];
        
            if (isset ($_POST['inserta'])){                 
                if ($lder == NULL OR $lizq == NULL){
                    print "<script> alert('TIENES QUE ELEGIR UN LENTE O MATERIAL DE LA LISTA');</script>";
                }
                else {
                    $sql9="SELECT * FROM armado WHERE idarm = '".$idaten."'";
                    $resultado9 = mysqli_query($conn, $sql9);
                    if ($row9 = mysqli_fetch_row($resultado9)){ } 
                    if ($row9[0] == NULL) {
                        switch ($row3[1]){
                            case 'COMPLETO':
                                $pre1 = 25;
                            break; 
                            case 'RANURADO':
                                $pre1 = 40;
                            break;
                            case 'PERFORADO':
                                $pre1 = 65;
                            break;
                        }                    

                        $sql4 ="insert into armado values ('".$idaten."', '".$faten."', '".$row3[1]."', '".$pre1."', '".$ttlen."', '".$ttallado."', '".$row2[0]."', '".$lder."', '".$lizq."', '".$grad."', '".$add."', '".$otro."', 'MACARIO')";
                        $resultado4 = mysqli_query($conn, $sql4);  

                        $sql5 ="insert into bitacora values ('NULL', '".$ussera."', 'FORM ARMADO ".$idaten."', 'INSERT', '".$fechabitacora."')";
                        $resultado5 = mysqli_query($conn, $sql5);

                        $sql6 ="UPDATE lente SET canlente=(canlente-1) WHERE idlente='".$lder."'";
                        $resultado6 = mysqli_query($conn, $sql6);
                        $sql7 ="UPDATE lente SET canlente=(canlente-1) WHERE idlente='".$lizq."' ";
                        $resultado7 = mysqli_query($conn, $sql7);

                        echo '<div class="color-arriba"><h3>GUARDADO CORRECTAMENTE</h3></div>';
                        echo '<meta http-equiv=refresh content="1">';
                    }
                    else {
                       print "<script> alert('ESTA CONTRASEÑA YA FUE TRABAJADA');</script>"; 
                    }
                }
                
            }
        ?>        
	<script type="text/javascript" src="js/jquery-3.6.1.min.js"></script> 
        <script type="text/javascript" src="js/jquery-ui.js"></script>
        <script type="text/javascript" src="js/misfunciones.js"></script>
        <script type="text/javascript" src="js/versection.js"></script> 
        <script type="text/javascript" src="js/funcionlista.js"></script>
    </body>
</html>



