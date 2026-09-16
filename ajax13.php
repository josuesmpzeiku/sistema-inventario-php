<?php

include_once 'usuarios.class.php';
$usuario = new Reparacion();
echo json_encode($usuario ->buscarR($_GET['term']));
