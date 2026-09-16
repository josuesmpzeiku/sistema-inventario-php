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
        <link type="text/css" rel="stylesheet" href="css/jquery-ui.css">       
        <link rel="stylesheet" href="css/miestilo.css">      
        <link rel="stylesheet" href="css/bootstrap.min.css">
	<link rel="stylesheet" href="css/tooplate-style.css">
        <link rel="stylesheet" href="css/demo.css">  
    </head>
    <body>
        <div class="color-arriba"><h2 class ="subtitulo">PRODUCTOS</h2></div>      
        <div class="fila">
            <table>
                <tr>
                    <td><button id="bt-filtro-1" class="btn-universal"> Aros </button></td>
                    <td><button id="bt-filtro-2" class="btn-universal"> Lentes </button></td>
                    <td><button id="bt-filtro-3" class="btn-universal"> Medicamentos </button></td>
                    <td><button id="bt-filtro-4" class="btn-universal"> Servicios </button></td> 
                    <td><button id="bt-filtro-5" class="btn-universal"> Accesorios </button></td>
                </tr>
            </table>
        </div>
        <section id="filtro1">
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
                </div>
                <div class="columna">
                    <form name="form2" method="post" action="">
                        <h3>MODIFICACIÓN DE INFORMACION DE AROS</h3>
                        <input class="decorar-input" name="tarom" id="tarom" type="text" placeholder="Escriba y Elija un Aro" required>
                        <div><br></div><div id="narom" class="contenedor-transparente"></div>                    
                    </form>        
                </div>                 
            </div>
        </section>
        <section id="filtro2">
            <div class="fila"> 
                <div class="columna">
                    <form name="form3" method="post" action="">
                        <h3>INSERTAR LENTES NUEVOS</h3>                        
                        <input class="decorar-input" name="tdeslen" type="text" placeholder="Descripción del LENTE (Material, Filtro, Color, Tipo Lente, otros)" required>
                        <select name="tgalen" class="decorar-input" required="">
                            <option value="" disabled selected>Elija una Gamma</option>
                            <option>Premium</option>
                            <option>Media</option> 
                            <option>Baja</option>                         
                        </select>
                        <input class="decorar-input" name="tprelen" type="number" required placeholder="Precio al Publico" step="0.01" title="Puede ser entero o decimal">
                        <input class="decorar-input" name="tobslen" type="text" placeholder="Observación para el Lente">
                        <div id="botones"><button type="submit" name="insertpl" class="btn-universal"><span class="icon icon-floppy-disk"></span></button></div>
                    </form>
                </div>
                <div class="columna">
                    <form name="form4" method="post" action="">
                        <h3>MODIFICACIÓN DE INFORMACION DE LENTES</h3>
                        <input class="decorar-input" name="tlenm" id="tlenm" type="text" placeholder="Escriba y Elija un Lente" required>
                        <div><br></div><div id="nlenm" class="contenedor-transparente"></div>                    
                    </form>        
                </div>                 
            </div>
        </section>        
        <section id="filtro3">
            <div class="fila"> 
                <div class="columna">
                    <form name="form5" method="post" action="">
                        <h3>INSERTAR MEDICAMENTO NUEVO</h3>
                        <input class="decorar-input" name="tdesmed" type="text" placeholder="Descripción del MEDICAMENTO (Marca, Presentacion, Otros)" required>
                        <input class="decorar-input" name="tpremed" type="number" required placeholder="Precio al Publico" step="0.01" title="Puede ser entero o decimal">
                        <label>Fecha de Vencimiento</label><input class="decorar-input" name="tvenmed" type="date" required>                        
                        <input class="decorar-input" name="tobsmed" type="text" placeholder="Observación para el Medicamento">
                        <div id="botones"><button type="submit" name="insertpm" class="btn-universal"><span class="icon icon-floppy-disk"></span></button></div>
                    </form>
                </div>
                <div class="columna">
                    <form name="form6" method="post" action="">
                        <h3>MODIFICACIÓN DE INFORMACION DE MEDICAMENTOS</h3>
                        <input class="decorar-input" name="tmedm" id="tmedm" type="text" placeholder="Escriba y Elija un Medicamento" required>
                        <div><br></div><div id="nmedm" class="contenedor-transparente"></div>                    
                    </form>        
                </div>                 
            </div>
        </section>
        <section id="filtro4">
            <div class="fila"> 
                <div class="columna">
                    <form name="form7" method="post" action="">
                        <h3>INSERTAR NUEVO SERVICIO</h3>
                        <input class="decorar-input" name="tdesser" type="text" placeholder="Descripción del SERVICIO" required>
                        <input class="decorar-input" name="tpreser" type="number" required placeholder="Precio al Publico" step="0.01" title="Puede ser entero o decimal">
                        <input class="decorar-input" name="tobsser" type="text" placeholder="Observación para el Servicio">
                        <div id="botones"><button type="submit" name="insertps" class="btn-universal"><span class="icon icon-floppy-disk"></span></button></div>
                    </form>
                </div>
                <div class="columna">
                    <form name="form2" method="post" action="">
                        <h3>MODIFICACIÓN DE INFORMACION DE SERVICIO</h3>
                        <input class="decorar-input" name="tserm" id="tserm" type="text" placeholder="Escriba y Elija un Servicio" required>
                        <div><br></div><div id="nserm" class="contenedor-transparente"></div>                    
                    </form>        
                </div>                 
            </div>
        </section>
        <section id="filtro5">
            <div class="fila"> 
                <div class="columna">
                    <form name="form7" method="post" action="">
                        <h3>INSERTAR NUEVO ACCESORIOS</h3>                        
                        <input class="decorar-input" name="tdesac" type="text" placeholder="Descripción del Accesorio" required>
                        <input class="decorar-input" name="tpreac" type="number" required placeholder="Precio al Publico" step="0.01" title="Puede ser entero o decimal">
                        <input class="decorar-input" name="tobsac" type="text" placeholder="Observación para el Accesorio">
                        <div id="botones"><button type="submit" name="insertpac" class="btn-universal"><span class="icon icon-floppy-disk"></span></button></div>
                    </form>
                </div>
                <div class="columna">
                    <form name="form2" method="post" action="">
                        <h3>MODIFICACIÓN DE INFORMACION DE ACCSESORIO</h3>
                        <input class="decorar-input" name="tacm" id="tacm" type="text" placeholder="Escriba y Elija un Accesorio" required>
                        <div><br></div><div id="nacm" class="contenedor-transparente"></div>                    
                    </form>        
                </div>                 
            </div>
        </section>
        <?php    
        $idaro = $_POST['tidaro'];  $desaro = $_POST['tdesaro'];  $prearo = $_POST['tprearo'];  $obsaro = $_POST['tobsaro']; $daro = $_POST['tdaro'];
        $idlen= $_POST['tidlen'];$deslen = $_POST['tdeslen'];  $galen = $_POST['tgalen'];  $prelen = $_POST['tprelen'];  $obslen = $_POST['tobslen'];
        $idmed = $_POST['tidmed'];  $desmed = $_POST['tdesmed'];  $premed = $_POST['tpremed'];  $venmed = $_POST['tvenmed'];  $obsmed = $_POST['tobsmed'];
        $idser = $_POST['tidser'];  $desser = $_POST['tdesser'];  $preser = $_POST['tpreser'];  $obsser = $_POST['tobsser'];
        $idac = $_POST['tidac'];  $desac = $_POST['tdesac'];  $preac = $_POST['tpreac'];  $obsac = $_POST['tobsac'];
        
            if (isset ($_POST['insertpa'])){
                $sql1 ="insert into producto values ('".$idaro."', 'ARO', '".$daro."', '".$desaro."', '', '".$prearo."', '".$obsaro."')";
                $resultado1 = mysqli_query($conn, $sql1); 

                $sql2 ="insert into bitacora values ('NULL', '".$ussera."', 'FORM PRODUCTO ARO ".$idaro."', 'INSERT', '".$fechabitacora."')";
                $resultado2 = mysqli_query($conn, $sql2);

                $sql3 ="SELECT * FROM producto where idproducto = '".$idaro."'";
                $resultado3 = mysqli_query($conn, $sql3);
                if ($row3 = mysqli_fetch_row($resultado3)){ }
                print "<div class='columna'>";
                echo '<div class="color-arriba"><h3>ARO GUARDADO CORRECTAMENTE</h3></div>';
                print "<table class='TablaA'>";                            
                print "<tr><td width = '35%'>ID:</td><td>$row3[0]</td></tr>";
                print "<tr><td>DESCRIPCIÓN:</td><td>$row3[3]</td></tr>";
                print "<tr><td>PRECIO:</td><td>$row3[5]</td></tr>";
                print "<tr><td>OBSERVACIÓN:</td><td>$row3[6]</td></tr>";
                print "</table>";
                print "</div>";                             
            }
        
            if (isset ($_POST['updaro'])){
                $sql4 ="insert into bitacora values ('NULL', '".$ussera."', 'FORM PRODUCTO ARO ".$idaro."', 'UPDATE', '".$fechabitacora."')";
                $resultado4 = mysqli_query($conn, $sql4); 
                
                $sql5 ="update producto SET descproducto = '".$desaro."', prevproducto = '".$prearo."', obsproducto = '".$obsaro."' where idproducto = '".$idaro."' ";
                $resultado5 = mysqli_query($conn, $sql5); 
                                          
                $sql3 ="SELECT * FROM producto where idproducto = '".$idaro."'";
                $resultado3 = mysqli_query($conn, $sql3);
                if ($row3 = mysqli_fetch_row($resultado3)){ }
                print "<div class='columna'>";
                echo '<div class="color-arriba"><h3>ARO GUARDADO CORRECTAMENTE</h3></div>';
                print "<table class='TablaA'>";                            
                print "<tr><td width = '35%'>ID:</td><td>$row3[0]</td></tr>";
                print "<tr><td>DESCRIPCIÓN:</td><td>$row3[3]</td></tr>";
                print "<tr><td>PRECIO:</td><td>$row3[5]</td></tr>";
                print "<tr><td>OBSERVACIÓN:</td><td>$row3[6]</td></tr>";
                print "</table>";
                print "</div>";                        
            }  
       
            if (isset ($_POST['delaro'])){                 
                $sql7 ="delete from producto where idproducto = '".$idaro."'";
                $resultado7 = mysqli_query($conn, $sql7); 
                echo '<div class="color-arriba"><h3>EL PRODUCTO -'.$desaro.'- HA SIDO ELIMINADO CORRECTAMENTE</h3></div>';   
                
                $sql8 ="insert into bitacora values ('NULL', '".$ussera."', 'FORM PRODUCTO ARO ".$idaro."', 'DELETE', '".$fechabitacora."')";
                $resultado8 = mysqli_query($conn, $sql8);
            }
            
            if (isset ($_POST['insertpl'])){
                $sql="SELECT COUNT(*) AS conteo FROM producto WHERE tipoproducto = 'LENTE'";
                $resultado = mysqli_query($conn, $sql);
                if($row = mysqli_fetch_array($resultado)){ $conteo = $row["conteo"]; }
                $cuenta = $conteo + 1;               
                $numero = str_pad($cuenta, 4, "0", STR_PAD_LEFT);                            
                $idlen = 'L'.$numero;                

                $sql1 ="insert into producto values ('".$idlen."', 'LENTE', '".$galen."', '".$deslen."', '', '".$prelen."', '".$obslen."')";
                $resultado1 = mysqli_query($conn, $sql1); 
                
                $sql4 ="insert into ubicacion values ('NULL','".$idlen."', 1, 0)";
                $resultado4 = mysqli_query($conn, $sql4);

                $sql2 ="insert into bitacora values ('NULL', '".$ussera."', 'FORM PRODUCTO LENTE ".$idlen."', 'INSERT', '".$fechabitacora."')";
                $resultado2 = mysqli_query($conn, $sql2);

                $sql3 ="SELECT * FROM producto where idproducto = '".$idlen."'";
                $resultado3 = mysqli_query($conn, $sql3);
                if ($row3 = mysqli_fetch_row($resultado3)){ }
                print "<div class='columna'>";
                echo '<div class="color-arriba"><h3>LENTE GUARDADO CORRECTAMENTE</h3></div>';
                print "<table class='TablaA'>";                            
                print "<tr><td width = '35%'>ID:</td><td>$row3[0]</td></tr>";                
                print "<tr><td>DESCRIPCIÓN:</td><td>$row3[3]</td></tr>";
                print "<tr><td>GAMMA:</td><td>$row3[2]</td></tr>";
                print "<tr><td>PRECIO:</td><td>$row3[5]</td></tr>";
                print "<tr><td>OBSERVACIÓN:</td><td>$row3[6]</td></tr>";
                print "</table>";
                print "</div>";                         
            }
        
            if (isset ($_POST['updlen'])){
                $sql4 ="insert into bitacora values ('NULL', '".$ussera."', 'FORM PRODUCTO LENTE ".$idlen."', 'UPDATE', '".$fechabitacora."')";
                $resultado4 = mysqli_query($conn, $sql4); 
                
                $sql5 ="update producto SET descproducto = '".$deslen."', claseproducto = '".$galen."', prevproducto = '".$prelen."', obsproducto = '".$obslen."' where idproducto = '".$idlen."' ";
                $resultado5 = mysqli_query($conn, $sql5); 
                                          
                $sql3 ="SELECT * FROM producto where idproducto = '".$idlen."'";
                $resultado3 = mysqli_query($conn, $sql3);
                if ($row3 = mysqli_fetch_row($resultado3)){ }
                print "<div class='columna'>";
                echo '<div class="color-arriba"><h3>LENTE GUARDADO CORRECTAMENTE</h3></div>';
                print "<table class='TablaA'>";                            
                print "<tr><td width = '35%'>ID:</td><td>$row3[0]</td></tr>";                
                print "<tr><td>DESCRIPCIÓN:</td><td>$row3[3]</td></tr>";
                print "<tr><td>GAMMA:</td><td>$row3[2]</td></tr>";
                print "<tr><td>PRECIO:</td><td>$row3[5]</td></tr>";
                print "<tr><td>OBSERVACIÓN:</td><td>$row3[6]</td></tr>";
                print "</table>";
                print "</div>";                           
            }  
       
            if (isset ($_POST['dellen'])){                 
                $sql7 ="delete from producto where idproducto = '".$idlen."'";
                $resultado7 = mysqli_query($conn, $sql7); 
                echo '<div class="color-arriba"><h3>EL PRODUCTO -'.$deslen.'- HA SIDO ELIMINADO CORRECTAMENTE</h3></div>';   
                
                $sql8 ="insert into bitacora values ('NULL', '".$ussera."', 'FORM PRODUCTO LENTE ".$idlen."', 'DELETE', '".$fechabitacora."')";
                $resultado8 = mysqli_query($conn, $sql8);
            }
            
            if (isset ($_POST['insertpm'])){
                $sql="SELECT COUNT(*) AS conteo FROM producto WHERE tipoproducto = 'MEDICAMENTO'";
                $resultado = mysqli_query($conn, $sql);
                if($row = mysqli_fetch_array($resultado)){ $conteo = $row["conteo"]; }
                $cuenta = $conteo + 1;               
                $numero = str_pad($cuenta, 3, "0", STR_PAD_LEFT);                            
                $idmed = 'M'.$numero;
                
                $sql1 ="insert into producto values ('".$idmed."', 'MEDICAMENTO', 'NO APLICA', '".$desmed."', '".$venmed."', '".$premed."', '".$obsmed."')";
                $resultado1 = mysqli_query($conn, $sql1); 

                $sql2 ="insert into bitacora values ('NULL', '".$ussera."', 'FORM PRODUCTO MEDICAMENTO ".$idmed."', 'INSERT', '".$fechabitacora."')";
                $resultado2 = mysqli_query($conn, $sql2);

                $sql3 ="SELECT * FROM producto where idproducto = '".$idmed."'";
                $resultado3 = mysqli_query($conn, $sql3);
                if ($row3 = mysqli_fetch_row($resultado3)){ }
                print "<div class='columna'>";
                echo '<div class="color-arriba"><h3>MEDICAMENTO GUARDADO CORRECTAMENTE</h3></div>';
                print "<table class='TablaA'>";                            
                print "<tr><td width = '35%'>ID:</td><td>$row3[0]</td></tr>";                
                print "<tr><td>DESCRIPCIÓN:</td><td>$row3[3]</td></tr>";
                print "<tr><td>FECHA DE VENCIMIENTO:</td><td>$row3[4]</td></tr>";
                print "<tr><td>PRECIO:</td><td>$row3[5]</td></tr>";
                print "<tr><td>OBSERVACIÓN:</td><td>$row3[6]</td></tr>";
                print "</table>";
                print "</div>";                             
            }
        
            if (isset ($_POST['updmed'])){
                $sql4 ="insert into bitacora values ('NULL', '".$ussera."', 'FORM PRODUCTO MEDICAMENTO ".$idmed."', 'UPDATE', '".$fechabitacora."')";
                $resultado4 = mysqli_query($conn, $sql4); 
                
                $sql5 ="update producto SET descproducto = '".$desmed."', fevenproducto = '".$venmed."', prevproducto = '".$premed."', obsproducto = '".$obsmed."' where idproducto = '".$idmed."' ";
                $resultado5 = mysqli_query($conn, $sql5); 
                                          
               $sql3 ="SELECT * FROM producto where idproducto = '".$idmed."'";
                $resultado3 = mysqli_query($conn, $sql3);
                if ($row3 = mysqli_fetch_row($resultado3)){ }
                print "<div class='columna'>";
                echo '<div class="color-arriba"><h3>MEDICAMENTO GUARDADO CORRECTAMENTE</h3></div>';
                print "<table class='TablaA'>";                            
                print "<tr><td width = '35%'>ID:</td><td>$row3[0]</td></tr>";                
                print "<tr><td>DESCRIPCIÓN:</td><td>$row3[3]</td></tr>";
                print "<tr><td>FECHA DE VENCIMIENTO:</td><td>$row3[4]</td></tr>";
                print "<tr><td>PRECIO:</td><td>$row3[5]</td></tr>";
                print "<tr><td>OBSERVACIÓN:</td><td>$row3[6]</td></tr>";
                print "</table>";
                print "</div>";                  
            }  
       
            if (isset ($_POST['delmed'])){                 
                $sql7 ="delete from producto where idproducto = '".$idmed."'";
                $resultado7 = mysqli_query($conn, $sql7); 
                echo '<div class="color-arriba"><h3>EL PRODUCTO -'.$desmed.'- HA SIDO ELIMINADO CORRECTAMENTE</h3></div>';   
                
                $sql8 ="insert into bitacora values ('NULL', '".$ussera."', 'FORM PRODUCTO MEDICAMENTO ".$idmed."', 'DELETE', '".$fechabitacora."')";
                $resultado8 = mysqli_query($conn, $sql8);
            }
            
            if (isset ($_POST['insertps'])){
                $sql="SELECT COUNT(*) AS conteo FROM producto WHERE tipoproducto = 'SERVICIO'";
                $resultado = mysqli_query($conn, $sql);
                if($row = mysqli_fetch_array($resultado)){ $conteo = $row["conteo"]; }
                $cuenta = $conteo + 1;               
                $numero = str_pad($cuenta, 3, "0", STR_PAD_LEFT);                            
                $idser = 'S'.$numero;
                
                $sql1 ="insert into producto values ('".$idser."', 'SERVICIO', 'NO APLICA', '".$desser."', '', '".$preser."', '".$obsser."')";
                $resultado1 = mysqli_query($conn, $sql1); 
                
                $sql4 ="insert into ubicacion values ('NULL','".$idser."', 1, 0)";
                $resultado4 = mysqli_query($conn, $sql4);

                $sql2 ="insert into bitacora values ('NULL', '".$ussera."', 'FORM PRODUCTO SERVICIO ".$idser."', 'INSERT', '".$fechabitacora."')";
                $resultado2 = mysqli_query($conn, $sql2);

                $sql3 ="SELECT * FROM producto where idproducto = '".$idser."'";
                $resultado3 = mysqli_query($conn, $sql3);
                if ($row3 = mysqli_fetch_row($resultado3)){ }
                print "<div class='columna'>";
                echo '<div class="color-arriba"><h3>SERVICIO GUARDADO CORRECTAMENTE</h3></div>';
                print "<table class='TablaA'>";                            
                print "<tr><td width = '35%'>ID:</td><td>$row3[0]</td></tr>";
                print "<tr><td>DESCRIPCIÓN:</td><td>$row3[3]</td></tr>";
                print "<tr><td>PRECIO:</td><td>$row3[5]</td></tr>";
                print "<tr><td>OBSERVACIÓN:</td><td>$row3[6]</td></tr>";
                print "</table>";
                print "</div>";                             
            }
        
            if (isset ($_POST['updser'])){
                $sql4 ="insert into bitacora values ('NULL', '".$ussera."', 'FORM PRODUCTO SERVICIO ".$idser."', 'UPDATE', '".$fechabitacora."')";
                $resultado4 = mysqli_query($conn, $sql4); 
                
                $sql5 ="update producto SET descproducto = '".$desser."', prevproducto = '".$preser."', obsproducto = '".$obsser."' where idproducto = '".$idser."' ";
                $resultado5 = mysqli_query($conn, $sql5); 
                                          
                $sql3 ="SELECT * FROM producto where idproducto = '".$idser."'";
                $resultado3 = mysqli_query($conn, $sql3);
                if ($row3 = mysqli_fetch_row($resultado3)){ }
                print "<div class='columna'>";
                echo '<div class="color-arriba"><h3>SERVICIO GUARDADO CORRECTAMENTE</h3></div>';
                print "<table class='TablaA'>";                            
                print "<tr><td width = '35%'>ID:</td><td>$row3[0]</td></tr>";
                print "<tr><td>DESCRIPCIÓN:</td><td>$row3[3]</td></tr>";
                print "<tr><td>PRECIO:</td><td>$row3[5]</td></tr>";
                print "<tr><td>OBSERVACIÓN:</td><td>$row3[6]</td></tr>";
                print "</table>";
                print "</div>";                   
            }  
       
            if (isset ($_POST['delser'])){                 
                $sql7 ="delete from producto where idproducto = '".$idser."'";
                $resultado7 = mysqli_query($conn, $sql7); 
                echo '<div class="color-arriba"><h3>EL PRODUCTO -'.$desser.'- HA SIDO ELIMINADO CORRECTAMENTE</h3></div>';   
                
                $sql8 ="insert into bitacora values ('NULL', '".$ussera."', 'FORM PRODUCTO ARO ".$idser."', 'DELETE', '".$fechabitacora."')";
                $resultado8 = mysqli_query($conn, $sql8);
            }
            
            if (isset ($_POST['insertpac'])){
                $sql="SELECT COUNT(*) AS conteo FROM producto WHERE tipoproducto = 'ACCESORIO'";
                $resultado = mysqli_query($conn, $sql);
                if($row = mysqli_fetch_array($resultado)){ $conteo = $row["conteo"]; }
                $cuenta = $conteo + 1;               
                $numero = str_pad($cuenta, 3, "0", STR_PAD_LEFT);                            
                $idac = 'AC'.$numero;
                
                $sql1 ="insert into producto values ('".$idac."', 'ACCESORIO', 'NO APLICA', '".$desac."', '', '".$preac."', '".$obsac."')";
                $resultado1 = mysqli_query($conn, $sql1); 

                $sql2 ="insert into bitacora values ('NULL', '".$ussera."', 'FORM PRODUCTO ACCESORIO ".$idac."', 'INSERT', '".$fechabitacora."')";
                $resultado2 = mysqli_query($conn, $sql2);

                $sql3 ="SELECT * FROM producto where idproducto = '".$idac."'";
                $resultado3 = mysqli_query($conn, $sql3);
                if ($row3 = mysqli_fetch_row($resultado3)){ }
                print "<div class='columna'>";
                echo '<div class="color-arriba"><h3>ACCESORIO GUARDADO CORRECTAMENTE</h3></div>';
                print "<table class='TablaA'>";                            
                print "<tr><td width = '35%'>ID:</td><td>$row3[0]</td></tr>";
                print "<tr><td>DESCRIPCIÓN:</td><td>$row3[3]</td></tr>";
                print "<tr><td>PRECIO:</td><td>$row3[5]</td></tr>";
                print "<tr><td>OBSERVACIÓN:</td><td>$row3[6]</td></tr>";
                print "</table>";
                print "</div>";                             
            }
        
            if (isset ($_POST['updac'])){
                $sql4 ="insert into bitacora values ('NULL', '".$ussera."', 'FORM PRODUCTO ACCESORIO ".$idac."', 'UPDATE', '".$fechabitacora."')";
                $resultado4 = mysqli_query($conn, $sql4); 
                
                $sql5 ="update producto SET descproducto = '".$desac."', prevproducto = '".$preac."', obsproducto = '".$obsac."' where idproducto = '".$idac."' ";
                $resultado5 = mysqli_query($conn, $sql5); 
                                          
                $sql3 ="SELECT * FROM producto where idproducto = '".$idac."'";
                $resultado3 = mysqli_query($conn, $sql3);
                if ($row3 = mysqli_fetch_row($resultado3)){ }
                print "<div class='columna'>";
                echo '<div class="color-arriba"><h3>ACCESORIO GUARDADO CORRECTAMENTE</h3></div>';
                print "<table class='TablaA'>";                            
                print "<tr><td width = '35%'>ID:</td><td>$row3[0]</td></tr>";
                print "<tr><td>DESCRIPCIÓN:</td><td>$row3[3]</td></tr>";
                print "<tr><td>PRECIO:</td><td>$row3[5]</td></tr>";
                print "<tr><td>OBSERVACIÓN:</td><td>$row3[6]</td></tr>";
                print "</table>";
                print "</div>";                      
            }  
       
            if (isset ($_POST['delac'])){                 
                $sql7 ="delete from producto where idproducto = '".$idac."'";
                $resultado7 = mysqli_query($conn, $sql7); 
                echo '<div class="color-arriba"><h3>EL PRODUCTO -'.$desac.'- HA SIDO ELIMINADO CORRECTAMENTE</h3></div>';   
                
                $sql8 ="insert into bitacora values ('NULL', '".$ussera."', 'FORM PRODUCTO ACCESORIO ".$idac."', 'DELETE', '".$fechabitacora."')";
                $resultado8 = mysqli_query($conn, $sql8);
            }
            
        ?>
        <script type="text/javascript" src="js/jquery-3.6.1.min.js"></script> 
        <script type="text/javascript" src="js/jquery-ui.js"></script>
        <script type="text/javascript" src="js/misfunciones.js"></script>
        <script type="text/javascript" src="js/funcionlista.js"></script>
        <script type="text/javascript" src="js/versection.js"></script>        
    </body>
</html>