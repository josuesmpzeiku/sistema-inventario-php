<?php

include_once 'usuarios.class.php';
$usuario = new Medic();
echo json_encode($usuario ->buscarMe($_GET['term']));

