<?php

require_once("../libs/rb.php");


$dsn = 'mysql:dbname=myroommate;host=127.0.0.1';
$user = 'myroommate';
$password = 'roommatepwd';

try {
    R::setup($dsn,$user,$password);
} catch (Exception $e) {
    echo 'Connection failed: ' . $e->getMessage();
}

$mate = R::dispense("mate");
$mate->name = "Ciro";
$id = R::store($mate);

$nmate = R::load( "mate", $id );


echo $mate->name.' = '.$nmate->name;

R::trash( $mate );


echo 'OK';
 ?>