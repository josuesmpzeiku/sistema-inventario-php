<?php
include_once 'usuarios.class.php';
$usuario = new mlen();
echo json_encode($usuario ->buscarml($_GET['term']));