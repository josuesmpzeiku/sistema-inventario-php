<?php

include_once 'usuarios.class.php';
$usuario = new Lente();
echo json_encode($usuario ->buscarLen($_GET['term']));