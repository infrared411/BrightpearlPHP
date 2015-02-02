<?php
ini_set("memory_limit","128M");

class brightpearl{
	//should split cURL to another class. 4 common cURL methods redundant below.
	public function authenticate($accountId, $user, $password){
		$ch = curl_init();
		$authenticationDetails = array(
			'apiAccountCredentials' => array(
				'emailAddress' => $user,
				'password'     => $password,
			),
		);
		$encodedAuthenticationDetails = json_encode($authenticationDetails);
		$authenticationUrl = 'https://ws-usw.brightpearl.com/'.$accountId.'/authorise';
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
		return $authorisationToken;
	}
	public function acknowledgeOrder($accountId, $authorisationToken, $orderid){
		//defunct???
		$headers = array(
		  	"brightpearl-auth: $authorisationToken",
		  	'Content-Type: application/json'
		  );
		  
		  $ordersURL='https://ws-usw.brightpearl.com/2.0.0/'.$accountId.'/warehouse-service/shipping-method';
		  
		   	$ch = curl_init();
		  	curl_setopt($ch, CURLOPT_URL, $ordersURL);
		  	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		  	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		  	$response = curl_exec($ch);
		  	curl_close($ch);
			
			$shipMethods = json_decode($response, true);
			
			return $shipMethods;
	}
	
	public function updateOrderStatus($accountId, $authorisationToken, $orderId, $status='Processed (SO)'){
		
		$headers = array(
		  	"brightpearl-auth: $authorisationToken",
		  	'Content-Type: application/json'
		  );
		  
		  switch($status){
		  	case 'Processed (SO)':
				$statusMessage=array('orderStatusId'=>'22');
				break;
			case 'Ready to Ship':
				$statusMessage=array('orderStatusId'=>'17');//17 ready to ship
				break;
			case 'Add Shipping':
				$statusMessage=array('orderStatusId'=>'24');
				break;
			case 'Shipping Complete':
				$statusMessage=array('orderStatusId'=>'25');
				break;
		  }
		  
		  
		  $statusURL='https://ws-usw.brightpearl.com/2.0.0/'.$accountId.'/order-service/order/'.$orderId.'/status';
		  
		   	$ch = curl_init();
		  	curl_setopt($ch, CURLOPT_URL, $statusURL);
		  	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		  	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($statusMessage));
		  	$response = curl_exec($ch);//should try catch error here
		  	curl_close($ch);	
			
			return true;
	}
	
	public function newOrderServiceRow($accountId, $authorisationToken, $orderId, $carrierMethod=NULL){
		
		$headers = array(
		  	"brightpearl-auth: $authorisationToken",
		  	'Content-Type: application/json'
		  );
		  $statusMessage=array('orderStatusId'=>'22');//create more of these
		  $carrierMethod='{
    						"productName": '.$productName.',                              
    						"quantity": {
        						"magnitude": "'.$quantity.'"
    						},
    						"rowValue":{
        						"taxCode":"'.$taxCode.'",
        						"rowNet":{
            						"value": "'.$rowNet.'"
        						},
        						"rowTax":{
            						"value": "'.$rowTax.'"
        						}
    						},
    							"nominalCode": "1000"
							}';
		  $carrierMethod='{
    						"productName": "larry",                              
    						"quantity": {
        						"magnitude": "1"
    						},
    						"rowValue":{
        						"taxCode":"N",
        						"rowNet":{
            						"value": "0.00"
        						},
        						"rowTax":{
            						"value": "0.00"
        						}
    						}
							}';
		  $statusURL='https://ws-usw.brightpearl.com/2.0.0/'.$accountId.'/order-service/order/'.$orderId.'/row/';
		  
		   	$ch = curl_init();
		  	curl_setopt($ch, CURLOPT_URL, $statusURL);
		  	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		  	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $carrierMethod);
		  	$response = curl_exec($ch);//should try catch error here
		  	curl_close($ch);	
			
			return $response;
	}
	
	public function updateOrderServiceRow($accountId, $authorisationToken, $orderId, $carrierMethod=NULL){
		
		$headers = array(
		  	"brightpearl-auth: $authorisationToken",
		  	'Content-Type: application/json'
		  );
		  $statusMessage=array('orderStatusId'=>'22');//create more of these
		  $carrierMethod='{
    						"productName": "'.$productName.'",    
   							"quantity": {
        						"magnitude": "'.$quantity.'"
    						},
    						"rowValue":{
        						"taxCode":"T",
        						"rowNet":{
            						"value": "'.$shippingCost.'"
        						},
        						"rowTax":{
            						"value": "'.$tax.'"
        						}
    						},
    						"nominalCode": "1000"
							}';
		  
		  $statusURL='https://ws-usw.brightpearl.com/2.0.0/'.$accountId.'/order-service/order/'.$orderId.'/status';
		  
		   	$ch = curl_init();
		  	curl_setopt($ch, CURLOPT_URL, $statusURL);
		  	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		  	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($statusMessage));
		  	$response = curl_exec($ch);//should try catch error here
		  	curl_close($ch);	
			
			return true;
	}
	
	public function getOrderRow($accountId, $authorisationToken, $orderId, $rowId){
		
		$headers = array(
		  	"brightpearl-auth: $authorisationToken",
		  	'Content-Type: application/json'
		  );
		  
		  $statusURL='https://ws-usw.brightpearl.com/2.0.0/'.$accountId.'/order-service/order/'.$orderId.'/row/'.$rowId;
		  
		   	$ch = curl_init();
		  	curl_setopt($ch, CURLOPT_URL, $statusURL);
		  	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		  	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		  	$response = curl_exec($ch);//should try catch error here
		  	curl_close($ch);	
			
			return $response;
	}
	
	public function getTaxCode($accountId, $authorisationToken){
		
		$headers = array(
		  	"brightpearl-auth: $authorisationToken",
		  	'Content-Type: application/json'
		  );
		  
		  $statusURL='https://ws-usw.brightpearl.com/2.0.0/'.$accountId.'/accounting-service/tax-code';
		  
		   	$ch = curl_init();
		  	curl_setopt($ch, CURLOPT_URL, $statusURL);
		  	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		  	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		  	$response = curl_exec($ch);//should try catch error here
		  	curl_close($ch);	
			
			return $response;
	}
	
	public function getShipmentIdList($accountId, $authorisationToken){
		$headers = array(
		  	"brightpearl-auth: $authorisationToken",
		  	'Content-Type: application/json'
		  );
		  
		  $ordersURL='https://ws-usw.brightpearl.com/2.0.0/'.$accountId.'/warehouse-service/shipping-method';
		  
		   	$ch = curl_init();
		  	curl_setopt($ch, CURLOPT_URL, $ordersURL);
		  	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		  	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		  	$response = curl_exec($ch);
		  	curl_close($ch);
			
			$shipMethods = json_decode($response, true);
			
			return $shipMethods;
	}
	
	public function getShipMethodFromId($accountId, $authorisationToken, $id){
		$headers = array(
		  	"brightpearl-auth: $authorisationToken",
		  	'Content-Type: application/json'
		  );
		  
		  $ordersURL='https://ws-usw.brightpearl.com/2.0.0/'.$accountId.'/warehouse-service/shipping-method/'.$id;
		  
		   	$ch = curl_init();
		  	curl_setopt($ch, CURLOPT_URL, $ordersURL);
		  	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		  	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		  	$response = curl_exec($ch);
		  	curl_close($ch);
			
			$shipMethods = json_decode($response, true);
			
			return $shipMethods;
	}
	
	public function deleteShipmentId($accountId, $authorisationToken, $shipMethod){
		$headers = array(
		  	"brightpearl-auth: $authorisationToken",
		  	'Content-Type: application/json'
		  );
		  
		  $ordersURL='https://ws-usw.brightpearl.com/2.0.0/'.$accountId.'/warehouse-service/shipping-method/'.$shipMethod;
		  
		   	$ch = curl_init();
		  	curl_setopt($ch, CURLOPT_URL, $ordersURL);
		  	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		  	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
		  	$response = curl_exec($ch);
		  	curl_close($ch);
			
			$shipMethods = json_decode($response, true);
			
			return $shipMethods;
	}
	
	public function getOrderById($accountId, $authorisationToken, $orderId, $noXML=TRUE){
		  $headers = array(
		  	"brightpearl-auth: $authorisationToken",
		  	'Content-Type: application/json'
		  );
		  
		  $ordersURL='https://ws-usw.brightpearl.com/2.0.0/'.$accountId.'/order-service/order/'.$orderId;
		  
		   	$ch = curl_init();
		  	curl_setopt($ch, CURLOPT_URL, $ordersURL);
		  	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		  	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		  	$response = curl_exec($ch);
		  	curl_close($ch);
		  $xml='<?xml version="1.0"?>
				<Orders>';
		  	$orders = json_decode($response, true);				
			for($a=0; $a<count($orders); $a++){ 
				$shipmethod=$this->getShipMethodFromId($accountId, $authorisationToken, $orders['response'][$a]['delivery']['shippingMethodId']);
			$explodedMethod=explode('-',$shipmethod['response'][0]['name']);
				$tpAcct=explode(' ',$explodedMethod[1]);
				$carrierMethod=explode(' ', $explodedMethod[0]);
			 
			  $xml.='
					<order>
						<ordernumber>'.$orders['response'][$a]['id'].'</ordernumber>
						<createdate>'.$orders['response'][$a]['createdOn'].'</createdate>
						<customername>'.$orders['response'][$a]['parties']['delivery']['addressFullName'].'</customername>
						<subtotal>'.$orders['response'][$a]['totalValue']['net'].'</subtotal>
						<currency>USD</currency>
						
					<shipto>
						<companyname>'.$orders['response'][$a]['parties']['delivery']['companyName'].'</companyname>
						<fullname>'.$orders['response'][$a]['parties']['delivery']['addressFullName'].'</fullname>
						<address1>'.$orders['response'][$a]['parties']['delivery']['addressLine1'].'</address1>
						<address2>'.$orders['response'][$a]['parties']['delivery']['addressLine2'].'</address2>
						<city>'.$orders['response'][$a]['parties']['delivery']['addressLine3'].'</city>
						<state>'.$orders['response'][$a]['parties']['delivery']['addressLine4'].'</state>				
						<postalcode>'.$orders['response'][$a]['parties']['delivery']['postalCode'].'</postalcode>
						<email>'.$orders['response'][$a]['parties']['delivery']['email'].'</email>
						<phone>'.$orders['response'][$a]['parties']['delivery']['telephone'].'</phone>
					</shipto>
					<billto>
						<companyname>'.$orders['response'][$a]['parties']['billing']['companyName'].'</companyname>
						<fullname>'.$orders['response'][$a]['parties']['billing']['addressFullName'].'</fullname>
						<address1>'.$orders['response'][$a]['parties']['billing']['addressLine1'].'</address1>
						<address2>'.$orders['response'][$a]['parties']['billing']['addressLine2'].'</address2>
						<city>'.$orders['response'][$a]['parties']['billing']['addressLine3'].'</city>
						<state>'.$orders['response'][$a]['parties']['billing']['addressLine4'].'</state>				
						<postalcode>'.$orders['response'][$a]['parties']['billing']['postalCode'].'</postalcode>
						<email>'.$orders['response'][$a]['parties']['billing']['email'].'</email>
						<phone>'.$orders['response'][$a]['parties']['billing']['telephone'].'</phone>
					</billto>
					<shipping>
						<shipping_method>'.$carrierMethod[1].'</shipping_method>
						<carrier>'.$carrierMethod[0].'</carrier>
							<tpacct>'.$tpAcct[3].'</tpacct>				
					</shipping>
					<notes>
						<note></note>
					</notes>
					<order_status>processing</order_status>
					<itemsordered>';
			  
			  	$key=array_keys($orders['response'][$a]['orderRows']);
			  
				for($b=0; $b<count($key); $b++){
				if($orders['response'][$a]['orderRows'][$key[$b]]['productSku']=='' || $orders['response'][$a]['orderRows'][$key[$b]]['productSku']==NULL){
					$orders['response'][$a]['reference'].='-'.$orders['response'][$a]['orderRows'][$key[$b]]['productName'];
				}
				$xml.='<item>
						  <sku>'.$orders['response'][$a]['orderRows'][$key[$b]]['productSku'].'</sku>
						  <name>'.$orders['response'][$a]['orderRows'][$key[$b]]['productName'].'</name>
						  <quantity>'.$orders['response'][$a]['orderRows'][$key[$b]]['quantity']['magnitude'].'</quantity>
					   </item>';					   	
				}
			 
		  
			  $xml.='</itemsordered>
			  <shippinginstructions>
							<shipinstructions>'.$orders['response'][$a]['reference'].'</shipinstructions>
				</shippinginstructions>
				</order>';
			}
			$xml.='</Orders>';
		$xml=str_replace('&', '&amp;', $xml);
		$xml=str_replace("'", '&quot;', $xml);
		switch($noXML){
			case TRUE:	  
				return $orders;
			case FALSE:
				return $xml;
				}
	}
	
	public function getOrderStatusList($accountId, $authorisationToken){
			  $headers = array(
				"brightpearl-auth: $authorisationToken",
				'Content-Type: application/json'
			  );
			  
			  $orderstatusURL='https://ws-usw.brightpearl.com/2.0.0/'.$accountId.'/order-service/order-status';
			  
			  $ch = curl_init();
			  curl_setopt($ch, CURLOPT_URL, $orderstatusURL);
			  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			  $response = curl_exec($ch);
			  curl_close($ch);
			  
			  $orderStatus = json_decode($response, true);
		  
		  return $orderStatus;
	}
	
	public function getOrderShipMethodId($accountId, $authorisationToken, $order){
			  $headers = array(
				"brightpearl-auth: $authorisationToken",
				'Content-Type: application/json'
			  );
			  
			  $ordersURL='https://ws-usw.brightpearl.com/2.0.0/'.$accountId.'/order-service/order/'.$order;
		
		   	$ch = curl_init();
		  	curl_setopt($ch, CURLOPT_URL, $ordersURL);
		  	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		  	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		  	$response = curl_exec($ch);
		  	curl_close($ch);
			  
			  $orders = json_decode($response, true);
		  $Id=$orders['response'][0]['delivery']['shippingMethodId'];
		  
		  return $Id;
	}
	
	public function getOpenOrderList($accountId, $authorisationToken){
			  $headers = array(
				"brightpearl-auth: $authorisationToken",
				'Content-Type: application/json'
			  );
			  
			  $orderstatusURL='https://ws-usw.brightpearl.com/2.0.0/'.$accountId.'/order-service/order-search?orderStatusId=17';
			  
			  $ch = curl_init();
			  curl_setopt($ch, CURLOPT_URL, $orderstatusURL);
			  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			  $response = curl_exec($ch);
			  curl_close($ch);
			  
			  $orders = json_decode($response, true);
		  
		  return $orders['response']['results'];
	}
	
	public function getOrders($accountId, $authorisationToken){
		$headers = array(
		  "brightpearl-auth: $authorisationToken",
		  'Content-Type: application/json'
		);
		  
		$fulfillment=$this->getOpenOrderList($accountId,$authorisationToken);
		include ('../connection.php'); 
		for($b=0; $b<count($fulfillment); $b++){
			$xml=$this->getOrderById($accountId,$authorisationToken,$fulfillment[$b][0],false);
			sleep(2);
			print_r($this->updateOrderStatus($accountId,$authorisationToken,$fulfillment[$b][0],'Processed (SO)'));
			sleep(2);
			//print_r($this->createGoodsOutNote($accountId,$authorisationToken,$fulfillment[$b][0]));
			sleep(2);
			print_r($this->saveXML($xml, $FTPServer, $FTPUsername, $FTPPassword, $FTPpath, $fulfillment[$b][0]));		
		}
		return true;
	}
	
	public function getContactList($accountId, $authorisationToken){
		
		$headers = array(
		"brightpearl-auth: $authorisationToken",
		'Content-Type: application/json'
		);
		
	  	$contactsURL='https://ws-usw.brightpearl.com/2.0.0/'.$accountId.'/contact-service/contact/408';
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $contactsURL);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);	
		$response = curl_exec($ch);
		curl_close($ch);
		$contacts=json_decode($response, true);
		print_r($contacts);
	}
	
	public function getGoodsOutNoteId($accountId, $authorisationToken, $orderid){
		
		$headers = array(
		"brightpearl-auth: $authorisationToken",
		'Content-Type: application/json'
		);
		
	  	$goodsnoteURL='https://ws-usw.brightpearl.com/2.0.0/'.$accountId.'/warehouse-service/order/'.$orderid.'/goods-note/goods-out';
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $goodsnoteURL);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);	
		$response = curl_exec($ch);
		curl_close($ch);
		$note=json_decode($response, true);
		$key=array_keys($note['response']);
		return $key[0];
	}
	
	public function getGoodsOutNoteShipId($accountId, $authorisationToken, $orderid){
		
		$headers = array(
		"brightpearl-auth: $authorisationToken",
		'Content-Type: application/json'
		);
		
	  	$goodsnoteURL='https://ws-usw.brightpearl.com/2.0.0/'.$accountId.'/warehouse-service/order/'.$orderid.'/goods-note/goods-out';
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $goodsnoteURL);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);	
		$response = curl_exec($ch);
		curl_close($ch);
		$note=json_decode($response,true);
		
		return $note['response'][$this->getGoodsOutNoteId($accountId, $authorisationToken, $orderid)]['shipping']['shippingMethodId'];
	}
	
	public function getGoodsOutNote($accountId, $authorisationToken, $orderid){
		
		$headers = array(
		"brightpearl-auth: $authorisationToken",
		'Content-Type: application/json'
		);
		
	  	$goodsnoteURL='https://ws-usw.brightpearl.com/2.0.0/'.$accountId.'/warehouse-service/order/'.$orderid.'/goods-note/goods-out';
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $goodsnoteURL);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);	
		$response = curl_exec($ch);
		curl_close($ch);
		$note=json_decode($response, true);
		
		return $response;
	}
	
	public function getReservations($accountId, $authorisationToken){
		
		$headers = array(
		"brightpearl-auth: $authorisationToken",
		'Content-Type: application/json'
		);
		
	  	$goodsnoteURL='https://ws-usw.brightpearl.com/2.0.0/'.$accountId.'/warehouse-service/reservations';
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $goodsnoteURL);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);	
		$response = curl_exec($ch);
		curl_close($ch);
		$note=json_decode($response, true);
		
		return $response;
	}
	
	public function deleteReservation($accountId, $authorisationToken, $orderId){
		
		$headers = array(
		"brightpearl-auth: $authorisationToken",
		'Content-Type: application/json'
		);
		
	  	$goodsnoteURL='https://ws-usw.brightpearl.com/2.0.0/'.$accountId.'/warehouse-service/order/'.$orderId.'/reservation';
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $goodsnoteURL);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
		$response = curl_exec($ch);
		curl_close($ch);
		$note=json_decode($response, true);
		
		return $response;
	}
	
	public function getUnshipped($accountId, $authorisationToken){
		
		$headers = array(
		"brightpearl-auth: $authorisationToken",
		'Content-Type: application/json'
		);
		
		$ordersURL='https://ws-usw.brightpearl.com/2.0.0/'.$accountId.'/warehouse-service/goods-note/goods-out-search?shipped=false';
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $ordersURL);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		
		$response = curl_exec($ch);
		curl_close($ch);
		return $response;
		}
	
	public function updateGoodsOutNote($accountId, $authorisationToken, $orderid, $status=1){
		$noteId=$this->getGoodsOutNoteId($accountId,$authorisationToken,$orderid);
		$headers = array(
		"brightpearl-auth: $authorisationToken",
		'Content-Type: application/json'
		);
		$statuses=array('Printed'=>'PRI','Unprinted'=>'UPR','Picked'=>'PIC','Unpicked'=>'UPI','Packed'=>'PAC','Unpacked'=>'UPA','Shipped'=>'SHW');
		
		$objDateTime = new DateTime('NOW');
		switch($status){
			case 1: $postBody='{"events": [
			  {
				  "eventCode": "'.$statuses['Printed'].'",
				  "occured": "'.$objDateTime->format('c').'",
				  "eventOwnerId": 408
			  }
			  ]}';
			case 2: $postBody='{"events": [
			  {
				  "eventCode": "'.$statuses['Picked'].'",
				  "occured": "'.$objDateTime->format('c').'",
				  "eventOwnerId": 408
			  }
			  ]}';
			case 3: $postBody='{"events": [
			  {
				  "eventCode": "'.$statuses['Packed'].'",
				  "occured": "'.$objDateTime->format('c').'",
				  "eventOwnerId": 408
			  }
			  ]}';
			case 4: $postBody='{"events": [
			  {
				  "eventCode": "'.$statuses['Shipped'].'",
				  "occured": "'.$objDateTime->format('c').'",
				  "eventOwnerId": 408
			  }
			  ]}';
		}
	
	  	$ordersURL='https://ws-usw.brightpearl.com/2.0.0/'.$accountId.'/warehouse-service/goods-note/goods-out/'.$noteId.'/event';
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $ordersURL);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postBody);
		$response = curl_exec($ch);
		curl_close($ch);
		return $response;
	}
	
	public function updateGoodsOutNoteShipping($accountId, $authorisationToken, $orderid, $tracking){
		$noteId=$this->getGoodsOutNoteId($accountId,$authorisationToken,$orderid);
		$shipMethod=$this->getGoodsOutNoteShipId($accountId,$authorisationToken,$orderid);
		$headers = array(
		"brightpearl-auth: $authorisationToken",
		'Content-Type: application/json'
		);
		if($shipMethodId=='0'){
			$shipMethod='6';
		}				
		$postBody=array('priority'=>false, 'shipping'=>array('shippingMethodId'=>$shipMethod, 'reference'=>$tracking, 'boxes'=>$boxcount, 'weight'=>$weight));
			
	  	$ordersURL='https://ws-usw.brightpearl.com/2.0.0/'.$accountId.'/warehouse-service/goods-note/goods-out/'.$noteId;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $ordersURL);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postBody));		
		$response = curl_exec($ch);
		curl_close($ch);
		
		return $response;
	}
	
	public function createGoodsOutNote($accountId, $authorisationToken, $orderid){
		$objOrder=$this->getOrderById($accountId, $authorisationToken, $orderid, TRUE);
		
		$headers = array(
		"brightpearl-auth: $authorisationToken",
		'Content-Type: application/json'
		);
		$objDateTime = new DateTime('NOW');
		
		
		for ($a=0; $a<count($objOrder['response']); $a++) {
		  $key=array_keys($objOrder['response'][$a]['orderRows']);
		  $shipMethod=$objOrder['response'][$a]['delivery']['shippingMethodId'];
		  //print $shipMethod;
		  //get id not name
		  for($b=0; $b<count($key); $b++){
			  
			  		$product.="{\"productId\":".$objOrder['response'][$a]['orderRows'][$key[$b]]['productId'].",\"salesOrderRowId\":".$objOrder['response'][$a]['orderRows'][$key[$b]]['orderRowSequence'].",\"quantity\":".$objOrder['response'][$a]['orderRows'][$key[$b]]['quantity']['magnitude'].'},';
		  }	
		  $product=rtrim($product, ",");	  
		}
		$postBody ='{"warehouses":[{"releaseDate":"'.$objDateTime->format('c').'","warehouseId":2,"transfer":false,
		"products":['.$product.']}],
		"priority":false,
		"shippingMethodId":'.$shipMethod.'}';//$shipMethod
		print $postBody;
		$ordersURL='https://ws-usw.brightpearl.com/2.0.0/'.$accountId.'/warehouse-service/order/'.$orderid.'/goods-note/goods-out';
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $ordersURL);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postBody);
		$response = curl_exec($ch);
		curl_close($ch);
		return $response;
	}
	
	public function saveXML($xml, $FTPServer, $FTPusername, $FTPpassword, $FTPpath, $orderid){
		
		$ftppath='orders_'.date('Ymd').'_'.$orderid.'.xml';
		$localMount='/mnt/share/butterflytwist/orders/orders_'.date('Ymd').'_'.$orderid.'.xml';
		
		file_put_contents($localMount, $xml);
		
		$FTPconn = ftp_connect($FTPServer) or die("Could not connect");
		
		ftp_login($FTPconn,$FTPusername,$FTPpassword);
		ftp_pasv($FTPconn,true);
		ftp_chdir($FTPconn, '/'.$FTPpath.'/');
		ftp_put($FTPconn,$ftppath ,$localMount, FTP_ASCII);
		unlink($localMount);
		return true;
	}
	
	public function createWebhook($accountId, $authorisationToken, $url, 
	$template='{\n \"accountCode\": \"${account-code}\",\n        \"resourceType\": \"${resource-type}\",\n \"id\": \"${resource-id}\",\n \"lifecycle-event\": \"${lifecycle-event}\"\n\n}'){
		
		$headers = array(
		"brightpearl-auth: $authorisationToken",
		'Content-Type: application/json'
		);
		
		$webhookURL='https://ws-usw.brightpearl.com/2.0.0/'.$accountId.'/integration-service/webhook';
		
		$webhookDetails = array(	
		'subscribeTo' => 'order.modified.order-status',
		'httpMethod' => 'POST',
		'uriTemplate' => $url,
		'bodyTemplate' => '{\n \"accountCode\": \"${account-code}\",\n        \"resourceType\": \"${resource-type}\",\n \"id\": \"${resource-id}\",\n \"lifecycle-event\": \"${lifecycle-event}\"\n\n}',
		'contentType' => 'application/json',
		'idSetAccepted' => 'true'
		);
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $webhookURL);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($webhookDetails));
		$response = curl_exec($ch);
		curl_close($ch);
		print_r($response);	
	}
	
	public function getWebhook($accountId, $authorisationToken, $webhookId=NULL){
		
		$headers = array(
		"brightpearl-auth: $authorisationToken",
		'Content-Type: application/json'
		);
		
		$webhookURL='https://ws-usw.brightpearl.com/2.0.0/'.$accountId.'/integration-service/webhook';
				
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $webhookURL);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		$response = curl_exec($ch);
		curl_close($ch);
		return $response;	
	}	
	
	public function deleteWebhook($accountId, $authorisationToken, $webhookId=NULL){
		
		$headers = array(
		"brightpearl-auth: $authorisationToken",
		'Content-Type: application/json'
		);
		
		$webhookURL='https://ws-usw.brightpearl.com/2.0.0/'.$accountId.'/integration-service/webhook/'.$webhookId;
				
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $webhookURL);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
		$response = curl_exec($ch);
		curl_close($ch);
		return $response;	
	}
	
	public function simulateWebhook($accountId, $authorisationToken, $webhookId=NULL){
		
		$headers = array(
		"brightpearl-auth: $authorisationToken",
		'Content-Type: application/json'
		);
		
		$webhookURL='https://ws-usw.brightpearl.com/2.0.0/'.$accountId.'/integration-service/webhook/'.$webhookId.'/simulate';
				
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $webhookURL);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		$response = curl_exec($ch);
		curl_close($ch);
		return $response;	
	}		
	
}
?>