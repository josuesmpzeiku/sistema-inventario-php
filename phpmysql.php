<?php
$conn =  new mysqli('localhost','Miguel','Zeiku+2021','optica');
mysqli_set_charset($conn, 'utf8');  
$url = 'https://www.sysomac.com/';

date_default_timezone_set('America/Guatemala');                          
$fechabitacora = Date('Y-m-d h:i:sa');
       
$nfecha = Date('Y-m-d h:i:sa');