<?php

include_once 'usuarios.class.php';
$usuario = new Aro();
echo json_encode($usuario ->buscarA($_GET['term']));