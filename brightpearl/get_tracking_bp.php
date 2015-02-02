<?

include ('class.brightpearl.php');

$brightpearl = new brightpearl();
$authtoke=$brightpearl->authenticate('butterflytwistsusa','larry@thewarehouseusa.com', 'N0137dxz');
//need stub for sending tracking and cost to Goods Out Notes
$brightpearl->updateOrderStatus('butterflytwistsusa',$authtoke, $_GET['orderid'], 'Shipping Complete');


?>