<? 
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

$webhookURL='https://ws-usw.brightpearl.com/2.0.0/butterflytwistsusa/integration-service/webhook';

$webhookDetails = array(	
	'subscribeTo' => 'order.modified.order-status',
    'httpMethod' => 'POST',
    'uriTemplate' => 'http://216.85.120.50/butterflytwsits/brightpearl/orderCallback.php',
    'bodyTemplate' => '{\n \"accountCode\": \"${account-code}\",\n        \"resourceType\": \"${resource-type}\",\n \"id\": \"${resource-id}\",\n \"lifecycle-event\": \"${lifecycle-event}\"\n\n}',
    'contentType' => 'application/json',
    'idSetAccepted' => 'true'
);

//print json_encode($webhookDetails);
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $webhookURL);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
//curl_setopt($ch, CURLOPT_POST, true);
//curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($webhookDetails));
$response = curl_exec($ch);
curl_close($ch);
print_r($response);
?>