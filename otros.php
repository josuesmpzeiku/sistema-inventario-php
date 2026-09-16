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
        <div class="color-arriba"><h2 class ="subtitulo">COSTOS Y MATERIAL OTRAS OPTICAS</h2></div>
        <div class="fila">
            <div class="columna">               
                <form name="form1" method="post" action="">  
                    <select name="tta" class="decorar-input" required="">
                        <option value="" disabled selected>Elija un Tipo de Aro</option>
                        <option>COMPLETO</option>
                        <option>RANURADO</option> 
                        <option>PERFORADO</option>
                    </select>
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
                    <input class="decorar-input" name="material" type="text" placeholder="Tipo de Material de la Solicitud" required> 
                    <input class="decorar-input" name="lder" type="text" id="lder" placeholder="Escriba y Elija un Lente o Base para el LENTE DERECHO"><div id="nder"></div> 
                    <input class="decorar-input" name="lizq" type="text" id="lizq" placeholder="Escriba y Elija un Lente o Base para el LENTE IZQUIERDO"><div id="nizq"></div> 
                    <input class="decorar-input" name="grad" type="text" placeholder="Detalle de las graduaciones a realizar en los lentes" required>
                    <input class="decorar-input" name="add" type="text" placeholder="ADD">
                    <input class="decorar-input" name="otro" type="text" placeholder="Otros" required>
                    <input class="decorar-input" name="optica" type="text" placeholder="Nombre de la Optica" required>
                    <div id="botones"><button type="submit" name="inserta" class="btn-universal" id="inserta"><span class="icon icon-floppy-disk"></span></button></div> 
                </form>
            </div>
            <div class="columna">
                <?php                 
                $tta = $_POST['tta'];
                $ttlen = $_POST['ttlen'];
                $ttallado = $_POST['ttallado']; 
                $material = $_POST['material'];
                $lder = $_POST['idled'];        
                $lizq = $_POST['idlei'];
                $grad = $_POST['grad'];
                $add = $_POST['add'];
                $otro = $_POST['otro'];
                $optica = $_POST['optica'];
                date_default_timezone_set('America/Guatemala');                          
                $fechag = date('Y-m-d');
        
                if (isset ($_POST['inserta'])){                 
                    if ($lder == NULL OR $lizq == NULL){
                        print "<script> alert('TIENES QUE ELEGIR UN LENTE O MATERIAL DE LA LISTA');</script>";
                    }
                    else {                    
                        $sql3="SELECT COUNT(*) as conteo FROM armado";
                        $resultado3 = mysqli_query($conn, $sql3);
                        if($row3 = mysqli_fetch_array($resultado3)){ $conteo = $row3["conteo"]; }
                        $cuenta = $conteo + 1;               
                        $numero = str_pad($cuenta, 9, "0", STR_PAD_LEFT);                            
                        $idaten = 'E'.$numero;                                                    

                        switch ($tta){
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
                        $sql4 ="insert into armado values ('".$idaten."', '".$fechag."', '".$tta."', '".$pre1."', '".$ttlen."', '".$ttallado."', '".$material."', '".$lder."', '".$lizq."', '".$grad."', '".$add."', '".$otro."', '".$optica."')";
                        $resultado4 = mysqli_query($conn, $sql4);  

                        $sql5 ="insert into bitacora values ('NULL', '".$ussera."', 'FORM ARMADO ".$idaten."', 'INSERT', '".$fechabitacora."')";
                        $resultado5 = mysqli_query($conn, $sql5);

                        $sql6 ="UPDATE lente SET canlente=(canlente-1) WHERE idlente='".$lder."'";
                        $resultado6 = mysqli_query($conn, $sql6);
                        $sql7 ="UPDATE lente SET canlente=(canlente-1) WHERE idlente='".$lizq."' ";
                        $resultado7 = mysqli_query($conn, $sql7);
                    }
                    
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
                    print "<td>OTROS</td>";
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

