<?php
    $http_referer = isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:null;
    $referer = $_SERVER['HTTP_REFERER'];
    if ($referer == "" ) {
        print "<script> alert('TIENES QUE INICIAR SESION'); location.href = 'index.php'; </script>";
    }
    error_reporting(0);
    session_start();
    include ("phpmysql.php"); 
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
        <div class="color-arriba"><h2 class ="subtitulo">INVENTARIO POR SUCURSAL</h2></div>
        <div class="fila">
            <div class="columna">
                <h3>ELIGA UNA OPCION</h3>
                <form name="form1" method="post" action=""> 
                     <?php                     
                        $sql ="SELECT * from tienda";
                        $resultado = mysqli_query($conn, $sql);                   
                        print "<select required class='decorar-input' name='tidt'>
                            <option value='' disabled selected>Elija una Tienda</option>";
                            while ($row = mysqli_fetch_array($resultado)){                            
                                print"<option value='{$row['idtienda']}'>{$row['nomtienda']}</option>";
                            }
                        print "</select>";
                        ?> 
                    <div id="botones">
                        <button type="submit" name="selects" class="btn-universal" id="selectb"><span class="icon icon-search"></span></button>
                    </div>
                </form>
            </div>           
        </div>
        <?php 
            $idti = $_POST['tidt'];
            if (isset ($_POST['selects'])){ 
                $sql4="SELECT producto.obsproducto, SUM(ubicacion.cantubicacion) 
                        FROM ubicacion INNER JOIN producto ON producto.idproducto = ubicacion.idproducto
                        WHERE ubicacion.idtienda = '".$idti."' AND producto.tipoproducto = 'ARO' and
                        producto.descproducto != 'ARO PROPIO COMPLETO' AND producto.descproducto != 'ARO PROPIO PERFORADO' AND producto.descproducto != 'ARO PROPIO RANURADO' 
                        GROUP BY producto.obsproducto";
                $resultado4 = mysqli_query($conn, $sql4); 
                print "<div class= 'fila'>";
                print "<div class= 'columna'>";
                print "<table class='TablaJ'>";                         
                    print "<tr>";                    
                    print "<td>CLASE DE ARO</td>";                    
                    print "<td>TOTAL</td>"; 
                    print "</tr>";
                    while ($row4 = mysqli_fetch_assoc($resultado4)){
                        print "<tr>";
                            foreach ($row4 as $item4){
                                print "<td>".($item4!==NULL ?htmlentities($item4):"&nbsp;")."</td>";
                            }
                            print "</tr>";                    
                        }
                $sql5="SELECT SUM(ubicacion.cantubicacion) 
                FROM ubicacion INNER JOIN producto ON producto.idproducto = ubicacion.idproducto
                WHERE ubicacion.idtienda = '".$idti."' AND producto.tipoproducto = 'ARO' and
                producto.descproducto != 'ARO PROPIO COMPLETO' AND producto.descproducto != 'ARO PROPIO PERFORADO' AND producto.descproducto != 'ARO PROPIO RANURADO'";
                $resultado5 = mysqli_query($conn, $sql5);  
                if ($row5 = mysqli_fetch_row($resultado5)){ }
                    print "<tr><td><b>TOTAL AROS</b></td><td><b>$row5[0]</b></td></tr>"; 
                print "</table><br>";
                
                $sql2="SELECT producto.descproducto, ubicacion.cantubicacion
                      FROM producto INNER JOIN ubicacion on ubicacion.idproducto=producto.idproducto
                      WHERE ubicacion.idtienda = '".$idti."' AND producto.tipoproducto = 'MEDICAMENTO' 
                      ORDER BY `producto`.`descproducto` ASC";
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
                
                $sql3="SELECT producto.descproducto, ubicacion.cantubicacion
                      FROM producto INNER JOIN ubicacion on ubicacion.idproducto=producto.idproducto
                      WHERE ubicacion.idtienda = '".$idti."' AND producto.tipoproducto = 'ACCESORIO' 
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
                print "</table><br>";                
                
                $sql1="SELECT producto.idproducto, producto.descproducto, producto.obsproducto, ubicacion.cantubicacion
                      FROM producto INNER JOIN ubicacion on ubicacion.idproducto=producto.idproducto
                      WHERE ubicacion.idtienda = '".$idti."' AND producto.tipoproducto = 'ARO' AND
                       producto.descproducto != 'ARO PROPIO COMPLETO' AND producto.descproducto != 'ARO PROPIO PERFORADO' AND producto.descproducto != 'ARO PROPIO RANURADO'
                      ORDER BY `producto`.`obsproducto` ASC, producto.idproducto asc;";
                $resultado1 = mysqli_query($conn, $sql1);
               
                print "<table class='TablaJ'>";                         
                    print "<tr>";
                    print "<td>CÓDIGO</td>"; 
                    print "<td>DESCRIPCION</td>";
                    print "<td>TIPO</td>";                     
                    print "<td>TOTAL</td>"; 
                    print "</tr>";
                    while ($row1 = mysqli_fetch_assoc($resultado1)){
                        print "<tr>";
                            foreach ($row1 as $item1){
                                print "<td>".($item1!==NULL ?htmlentities($item1):"&nbsp;")."</td>";
                            }
                            print "</tr>";
                        }
                print "</table>"; 
                print "</div>";
                print "</div>";
            }
        ?>
	<script type="text/javascript" src="js/jquery-3.6.1.min.js"></script> 
        <script type="text/javascript" src="js/jquery-ui.js"></script>
        <script type="text/javascript" src="js/misfunciones.js"></script>
        <script type="text/javascript" src="js/versection.js"></script> 
        <script type="text/javascript" src="js/funcionlista.js"></script>
    </body>
</html>