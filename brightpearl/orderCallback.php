<?php

include ('class.brightpearl.php');

$brightpearl = new brightpearl();
$authtoke=$brightpearl->authenticate('butterflytwistsusa','larry@thewarehouseusa.com', 'N0137dxz');
$brightpearl->getOrders('butterflytwistsusa', $authtoke);

//make provision for zero byte files.
//test simple xml load and if error , clean folder of bad file and get order by id again. 

?>