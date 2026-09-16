<?php
include_once 'usuarios.class.php';
$usuario = new Paciente();
echo json_encode($usuario ->buscarPa($_GET['term']));

