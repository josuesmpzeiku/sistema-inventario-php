<?php

include_once 'usuarios.class.php';
$usuario = new Servicio();
echo json_encode($usuario ->buscarSe($_GET['term']));