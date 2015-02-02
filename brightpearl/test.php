<? 

include ('class.brightpearl.php');

$brightpearl = new brightpearl();
$authtoke=$brightpearl->authenticate('butterflytwistsusa','larry@thewarehouseusa.com', 'N0137dxz');

//print_r($brightpearl->getOrderById('butterflytwistsusa',$authtoke, '1062', FALSE));

//print_r($brightpearl->updateGoodsOutNote('butterflytwistsusa', $authtoke, $orderid, '4'));

//print_r($brightpearl->getUnshipped('butterflytwistsusa',$authtoke));
$response=$brightpearl->getUnshipped('butterflytwistsusa',$authtoke);

$decoded=json_decode($response, true);
//print $response;

foreach($decoded['response']['results'] as $result){
	
	print $result[5].'<br/>';
	$brightpearl->updateGoodsOutNote('butterflytwistsusa', $authtoke, $result[5], '4');
	$brightpearl->updateGoodsOutNote('butterflytwistsusa', $authtoke, $result[5], '4');
	$brightpearl->updateGoodsOutNote('butterflytwistsusa', $authtoke, $result[5], '4');
}

?>