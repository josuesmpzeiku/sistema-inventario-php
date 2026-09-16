<?php
$http_referer = isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:null;
    $referer = $_SERVER['HTTP_REFERER'];
    if ($referer == "" ) {
        print "<script> alert('TIENES QUE INICIAR SESION'); location.href = 'index.php'; </script>";
    }   
    error_reporting(0);
    session_start(); 
    $ussera = $_SESSION["usuario"];
    include ("phpmysql.php"); 
    $sql="Select * from usuario  WHERE idusuario = '".$ussera."'";
    $resultado = mysqli_query($conn, $sql);
    if($row = mysqli_fetch_array($resultado)){ $tipou = $row["puestousuario"]; $nombre =$row["userusuario"]; $tienda = $row["idtienda"];}
?>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">     
        <link rel="stylesheet" href="css/miestilo.css">      
        <link rel="stylesheet" href="css/bootstrap.min.css">
        <link rel="stylesheet" href="css/tooplate-style.css">
        <link rel="stylesheet" href="css/demo.css"> 
        <title><?php echo $tienda.' '.$nombre; ?></title>            
    </head>
    <body> 
        <img src="img/ofondo.png"  class="responsive"> 
        <div id="loader-wrapper">
            <div id="loader"></div>
            <div class="loader-section section-left"></div>
            <div class="loader-section section-right"></div>
        </div>
        <div class="contenedor-principal">		                        
            <div id="tmSideBar" class="sidebar">
                <button id="tmMainNavToggle" class="menu-icon"><span class="icon icon-menu"></span></button> 
                <div class="inner">                            
                    <nav id="tmMainNav" class="tm-main-nav">
                        <ul>                           
                            <li><a href="laboratorio.php?perfil=<?php echo $ussera; ?>" target="principal"><span class="icon icon-home"></span>  Inicio</a></li>                            
                            <li><a href="invenlab.php?perfil=<?php echo $ussera; ?>" target="principal"><span class="icon icon-barcode"></span>  Productos</a></li>
                            <li><a href="rsucursal.php?perfil=<?php echo $ussera; ?>" target="principal"><span class="icon icon-stats-bars"></span>  Reporte Sucursal</a></li>
                            <li><a href="rfecha.php?perfil=<?php echo $ussera; ?>" target="principal"><span class="icon icon-stats-dots"></span>  Reporte Todos</a></li>
                            <li><a href="rmaterial.php?perfil=<?php echo $ussera; ?>" target="principal"><span class="icon icon-file-text"></span>  Reporte Material</a></li>
                            <li><a href="rinvenlab.php?perfil=<?php echo $ussera; ?>" target="principal"><span class="icon icon-drawer"></span>  Inventario</a></li>
                            <li><a href="reposicion.php?perfil=<?php echo $ussera; ?>" target="principal"><span class="icon icon-wrench"></span>  Reposiciones</a></li>
                            <li><a href="otros.php?perfil=<?php echo $ussera; ?>" target="principal"><span class="icon icon-profile"></span>  Otros</a></li>
                            <li><a href="bolab.php?perfil=<?php echo $ussera; ?>" target="principal"><span class="icon icon-box-add"></span>  Bodega</a></li>
                            <li><a href="sucursalab.php?perfil=<?php echo $ussera; ?>" target="principal"><span class="icon icon-truck"></span>  Sucursal</a></li>
                            <li><a href="traslado.php?perfil=<?php echo $ussera; ?>" target="principal"><span class="icon icon-move-up"></span>  Traslado</a></li>
                            <li><a href="index.php" target="_parent"><span class="icon icon-exit"></span>  Salir</a></li>
                        </ul>	
                    </nav>                            
                </div>                  
            </div>                
            <div class="derecha tm-content">
                 <iframe width = "100%" height = "100%" frameborder="0" NAME="principal" SRC="laboratorio.php?perfil=<?php echo $ussera; ?>"></iframe>                        
            </div>		
        </div>        
	    <script type="text/javascript" src="js/jquery-3.6.1.min.js"></script>
            <script type="text/javascript" src="js/funcionmenu.js"></script>
    </body>    
</html>