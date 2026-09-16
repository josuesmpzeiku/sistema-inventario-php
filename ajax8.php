<?php
include_once 'usuarios.class.php';
$usuario = new Accesorio();
echo json_encode($usuario ->buscarAc($_GET['term']));