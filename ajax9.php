<?php
include_once 'usuarios.class.php';
$usuario = new Producto();
echo json_encode($usuario ->buscarP($_GET['term']));