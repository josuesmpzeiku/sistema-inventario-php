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
        <div class="color-arriba"><h2 class ="subtitulo">ENTRADA PRODUCTOS</h2></div> 
        <div class="fila"> 
            <div class="columna">                
                <form name="form2" method="post" action="">         
                    <table>
                        <tr>
                            <td width ="70%"><input class="input-iud" name="tprolab" type="text" id="tprolab"  placeholder="Escriba y Elija un Producto"><div id="nprolab"></div></td>  
                            <td><input class="input-iud" name="cla" type="text"  placeholder="Cantidad"></td>
                            <td><button type="submit" name="insertpl" class="btn-iud" id="insertpl">✔</button></td>
                        </tr>
                        <tr><td colspan="3"> </td></tr><tr>
                            <td></td>
                            <td><input class="input-iud" name="clamin" type="text"  placeholder="Cantidad"></td>
                            <td><button type="submit" name="updateci" class="btn-iud" id="updateci">⬇</button></td>
                        </tr>
                    </table>
                </form>
            </div> 
            <div class="columna"> 
                <h2>NUEVO PRODUCTO</h2>
                <form name="form1" method="post" action="">
                    <!--<select name="ttlente" class="decorar-input" required="">
                        <option value="" disabled selected>Elija un Tipo</option>
                        <option>LENTE</option>
                        <option>ACCESORIO</option>                        
                    </select>-->
                    <input class="decorar-input" name="tnombrel" type="text" placeholder="Descripción de LENTE (Material y Graduación)" required   id="tnombrel" title="Material y Graduación" >
                    <div id="botones"><button type="submit" name="insertln" class="btn-universal"><span class="icon icon-floppy-disk"></span></button></div>
                </form> 
                <?php
                $nombrel = $_POST['tnombrel'];  
               /* $tlen = $_POST['ttlente'];*/
                if (isset ($_POST['insertln'])){                    
                    $sql1="INSERT into lente values ('NULL', '".$nombrel."', 'LENTE', 0)";
                    $resultado1 = mysqli_query($conn, $sql1); 
                    
                    $sql2 ="insert into bitacora values ('NULL', '".$ussera."', 'FORM INVENLAB', 'INSERT', '".$fechabitacora."')";
                    $resultado2 = mysqli_query($conn, $sql2); 
                    
                    echo '<div class="color-arriba"><h3>LENTE GUARDADO CORRECTAMENTE</h3></div>';
                }
                ?>
            </div> 
        </div>
        <div class="fila">
            <div class="columna"> 
            <?php  
            $can = $_POST['preln'];
            $idcl = $_POST['idpl'];           
            $cla = $_POST['cla']; 
            $clam = $_POST['clamin']; 
            date_default_timezone_set('America/Guatemala');                          
            $fechag = date('Y-m-d');

                if (isset ($_POST['insertpl'])){

                    $sql3 ="insert into inlab values (NULL, '".$idcl."', '".$cla."', '".$fechag."')";
                    $resultado3 = mysqli_query($conn, $sql3);

                    $sql4 ="UPDATE lente SET canlente = (canlente + $cla) WHERE idlente = '".$idcl."'";
                    $resultado4 = mysqli_query($conn, $sql4);
                }
                
                if (isset ($_POST['updateci'])){
                    $sql4 ="UPDATE lente SET canlente = (canlente - $clam) WHERE idlente = '".$idcl."'";
                    $resultado4 = mysqli_query($conn, $sql4);
                    echo '<div class="color-arriba"><h3>La cantidad del producto se ha moficado</h3></div>';
                }

                echo "<form name='form' method='post' action=''>";
                print "<h3>PRODUCTOS RECIBIDOS EN LA FECHA:  $fechag</h3>";
                    print "<table class = 'TablaC'>";
                        print "<tr><td>CANT</td><td>PRODUCTO</td><td>ELIMINAR</td></tr>";
                        $sql5 ="SELECT inlab.*, lente.* FROM lente 
                                INNER JOIN inlab ON inlab.idlente = lente.idlente
                                WHERE inlab.fecinlab = '".$fechag."'";
                        $resultado5 = mysqli_query($conn, $sql5);
                        while ($row5 = mysqli_fetch_array($resultado5)){ 
                            $fila = $row5['idinlab'];
                            print "<tr><td>{$row5['cantinlab']}</td><td>{$row5['lentedes']}</td>";
                            print "<input name='tidinl".$fila."' type='hidden' value='".$fila."'>";
                            print "<input name='tcan".$fila."' type='hidden' value='{$row5['cantinlab']}'>";
                            print "<input name='tidl".$fila."' type='hidden' value='{$row5['idlente']}'>";
                            print "<td><button type='submit' name='delil".$fila."' class='btn-iud'>✕</button>";                       
                            $tidinlab = $_POST['tidinl'.$fila]; 
                            $caninlab = $_POST['tcan'.$fila]; 
                            $idlen = $_POST['tidl'.$fila]; 

                            if (isset ($_POST['delil'.$fila])){                         

                                $sql6 ="UPDATE lente SET canlente = (canlente - $caninlab) WHERE idlente = '".$idlen."'";
                                $resultado6 = mysqli_query($conn, $sql6);

                                $sql7 ="delete from inlab where idinlab = '".$tidinlab."'";
                                $resultado7 = mysqli_query($conn, $sql7);  

                                echo '<meta http-equiv=refresh content="0">';
                            }
                        }
                    print "</table>";
                echo "</form>";         
            ?>
            </div>
            <div class="columna">
                <h2>MODIFICACIÓN DE PRODUCTO</h2>
                <form name="form1" method="post" action="">
                    <input class="decorar-input" name="tproml1" type="text" id="tproml1" placeholder="Escriba y Elija el Producto a Corregir"><div id="nproml1"></div>  
                    <input class="decorar-input" name="tproml2" type="text" id="tproml2" placeholder="Escriba y Elija Corregido"><div id="nproml2"></div>  
                    <div id="botones"><button type="submit" name="updp" class="btn-universal"><span class="icon icon-loop2"></span></button></div>
                </form> 
                <?php  
                $idpm1 = $_POST['idpl1'];
                $idpm2 = $_POST['idpl2'];           
               

                if (isset ($_POST['updp'])){
                    if ($idpm1 == NULL or $idpm2 == NULL){
                        print "<script> alert('TIENES QUE ELGEGIR UN PRODUCTO DE LA LISTA');</script>";
                    }
                    else {
                        $sql1 ="UPDATE lente SET canlente = (canlente - 1) WHERE idlente = '".$idpm1."'";
                        $resultado1 = mysqli_query($conn, $sql1);
                        $sql2 ="UPDATE lente SET canlente = (canlente + 1) WHERE idlente = '".$idpm2."'";
                        $resultado2 = mysqli_query($conn, $sql2);
                        echo '<div class="color-arriba"><h3>PRODUCTO MODIFICADO CORRECTAMENTE</h3></div>';
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