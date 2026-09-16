<?php
include_once 'usuarios.class.php';
$usuario = new nlen();
echo json_encode($usuario ->buscarnl($_GET['term']));

