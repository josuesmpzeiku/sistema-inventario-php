<?php
include_once 'usuarios.class.php';
$usuario = new Atencion();
echo json_encode($usuario ->buscarAt($_GET['term']));