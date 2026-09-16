<?php
    $http_referer = isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:null;
    $referer = $_SERVER['HTTP_REFERER'];
    if ($referer == "" ) {
       print "<script> alert('TIENES QUE INICIAR SESION'); location.href = 'index.php'; </script>";
    }
    error_reporting(0);
    session_start();
    include ("phpmysql.php");
    $usuario = $_POST['nombre'];
    $password = $_POST['contras'];
  

    if (isset ($_POST['sesion'])){
        $sql="Select * from usuario  WHERE userusuario = '".$usuario."' and passusuario = '".$password."'";
        $resultado = mysqli_query($conn, $sql);
        if ($row = mysqli_fetch_row($resultado)){ }

        if ($row[0] == NULL){
            print "<script> alert('IMPOSIBLE INICIAR SESION USUARIO O CONTRASEÑA INVALIDA'); location.href = 'index.php'; </script>";        
        }
        else{ 
            switch ($row[4]){
                case 'A':
                    $_SESSION['usuario'] = $row[0];
                    header("Location:tipoa.php");
                    break;
                case 'B':
                    $_SESSION['usuario'] = $row[0];
                    header("Location:tipob.php");
                    break;
                case 'C' :
                    $_SESSION['usuario'] = $row[0];
                    header("Location:tipoc.php");
                    break;
                case 'D' :
                    $_SESSION['usuario'] = $row[0];
                    header("Location:tipod.php");
                    break;
                 case 'E' :
                    $_SESSION['usuario'] = $row[0];
                    header("Location:tipoe.php");
                    break;
                case 'F' :
                    $_SESSION['usuario'] = $row[0];
                    header("Location:tipof.php");
                    break;
                case 'G' :
                    $_SESSION['usuario'] = $row[0];
                    header("Location:tipog.php");
                    break;
                case 'H' :
                    $_SESSION['usuario'] = $row[0];
                    header("Location:tipoh.php");
                    break;
                case 'I' :
                    $_SESSION['usuario'] = $row[0];
                    header("Location:tipoi.php");
                    break;
                }                                     
            }
    }

