<?php
include_once 'usuarios.class.php';
$usuario = new Usuariop();
echo json_encode($usuario ->buscarU($_GET['term']));

