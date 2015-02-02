<?php
$ch = curl_init();
$authenticationDetails = array(
    'apiAccountCredentials' => array(
        'emailAddress' => 'larry@thewarehouseusa.com',
        'password'     => 'N0137dxz',
    ),
);
$encodedAuthenticationDetails = json_encode($authenticationDetails);
$authenticationUrl = 'https://ws-usw.brightpearl.com/butterflytwistsusa/authorise';
$header = array('Content-Type: application/json');

curl_setopt($ch, CURLOPT_URL, $authenticationUrl);
curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($authenticationDetails));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
if (false === $response) {
    echo 'Request unsuccessful' . PHP_EOL;
    curl_close($ch);
    exit(1);
}
$responseCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
$responseBody = json_decode($response);
curl_close($ch);

if (200 !== $responseCode) {
    echo 'Authentication failed' . PHP_EOL;
    foreach ($responseBody->errors as $error) {
        echo $error->code . ': ' . $error->message . PHP_EOL;
    }
    exit(1);
}
$authorisationToken = $responseBody->response;
echo 'Authentication success with token ' . $authorisationToken . PHP_EOL;
echo '<br/>';

$headers = array(
"brightpearl-auth: $authorisationToken",
'Content-Type: application/json'
);

$orderstatusURL='https://ws-usw.brightpearl.com/2.0.0/butterflytwistsusa/order-service/order-search?orderStatusId=17';
$ordersURL='https://ws-usw.brightpearl.com/2.0.0/butterflytwistsusa/order-service/order/333';
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $ordersURL);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
$response = curl_exec($ch);
curl_close($ch);

$orders = json_decode($response, true);

for ($a=0; $a<count($orders['response']); $a++) {	
echo "Order ID:            ".$orders['response'][$a]['id']."<br/>"; 
echo "Order Reference:     ".$orders['response'][$a]['reference']."<br/>";
echo "Order Status:        ".$orders['response'][$a]['orderStatus']['name']."<br/>";
echo "Placed On:           ".$orders['response'][$a]['createdOn']."<br/>";
echo "Shipping Method Id:  ".$orders['response'][$a]['delivery']['shippingMethodId']."<br/>";
echo "Shipping Name:       ".$orders['response'][$a]['parties']['delivery']['addressFullName']."<br/>";
echo "Shipping Company:    ".$orders['response'][$a]['parties']['delivery']['comapnyName']."<br/>";
echo "Shipping Line:       ".$orders['response'][$a]['parties']['delivery']['addressLine1']."<br/>";
echo "Shipping Line:       ".$orders['response'][$a]['parties']['delivery']['addressLine2']."<br/>";
echo "Shipping City:       ".$orders['response'][$a]['parties']['delivery']['addressLine3']."<br/>";
echo "Shipping State:      ".$orders['response'][$a]['parties']['delivery']['addressLine4']."<br/>";
echo "Shipping Zip:        ".$orders['response'][$a]['parties']['delivery']['postalCode']."<br/>";
echo "Shipping Country:    ".$orders['response'][$a]['parties']['delivery']['country']."<br/>";
echo "Shipping Phone:      ".$orders['response'][$a]['parties']['delivery']['telephone']."<br/>";
echo "Shipping Info:       ".$orders['response'][$a]['parties']['delivery']['email']."<br/>";
echo "Items:<br/>";
$key=array_keys($orders['response'][$a]['orderRows']);
for($b=0; $b<count($key); $b++){
	echo "SKU: ". $orders['response'][$a]['orderRows'][$key[$b]]['productSku']."<br/>Description: ".$orders['response'][$a]['orderRows'][$key[$b]]['productName'].'<br/>';
	echo "Quantity: ". $orders['response'][$a]['orderRows'][$key[$b]]['quantity']['magnitude']."<br/>";	
}
}


?>