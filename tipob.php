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
                            <li><a href="stock.php?perfil=<?php echo $ussera; ?>" target="principal"><span class="icon icon-barcode"></span>  Stock</a></li>
                            <li><a href="inicio.php?perfil=<?php echo $ussera; ?>" target="principal"><span class="icon icon-search"></span>  Seguimiento</a></li>
                            <li><a href="invensucursal.php?perfil=<?php echo $ussera; ?>" target="principal"><span class="icon icon-drawer"></span>  Inventario</a></li> 
                            <li><a href="insuc.php?perfil=<?php echo $ussera; ?>" target="principal"><span class="icon icon-drawer"></span>  Inventario Suc</a></li>                            
                            <li><a href="rdiario.php?perfil=<?php echo $ussera; ?>" target="principal"><span class="icon icon-book"></span>  Libro Diario</a></li>                                
                            <li><a href="bajas.php?perfil=<?php echo $ussera; ?>" target="principal"><span class="icon icon-bin2"></span>  Bajas</a></li>
                            <li><a href="usuario.php?perfil=<?php echo $ussera; ?>" target="principal"><span class="icon icon-user-plus"></span>  Usuarios</a></li>
                            <li><a href="rdeposito.php?perfil=<?php echo $ussera; ?>" target="principal"><span class="icon icon-coin-dollar"></span>  Depositos</a></li>
                            <li><a href="especialista.php?perfil=<?php echo $ussera; ?>" target="principal"><span class="icon icon-user-tie"></span>  Especialista</a></li>
                            <li><a href="index.php" target="_parent"><span class="icon icon-exit"></span>  Salir</a></li>
                        </ul>	
                    </nav>                            
                </div>                  
            </div>                
            <div class="derecha tm-content">
                 <iframe width = "100%" height = "100%" frameborder="0" NAME="principal" SRC="stock.php?perfil=<?php echo $ussera; ?>"></iframe>                        
            </div>		
        </div>        
	    <script type="text/javascript" src="js/jquery-3.6.1.min.js"></script>
            <script type="text/javascript" src="js/funcionmenu.js"></script>
    </body>    
</html>
