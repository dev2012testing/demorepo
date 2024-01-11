<?php


$apiUrl = 'https://1151066.restlets.api.netsuite.com/app/site/hosting/restlet.nl';

$oauthCredentials = [
    'consumer_key' => '"daf7b22039319dec5b850c1b3fefa13d4129f3fe40b71243e1e7472df578f838"',
    'consumer_secret' => '"9e054f5b92079a9e84935e23e9db08638ba489344faef0fd1b105ad4da07c41c"',
    'token' => '"53a89a93dcfe5635a62c459683944c5a51133d03b4f737887925057fc0dd6a1a"',
    'token_secret' => '"6bd7e944b96894a88f7f2756b5626152fd195a4c7616b55b0a7312a5ecc0c1ac"',
];


// Generate OAuth nonce and timestamp
$oauthNonce = md5(mt_rand());
$oauthTimestamp = time();


// Set up OAuth parameters
$oauthParameters = [
	'deploy' => '"1"',
     'realm' => '"1151066"',
    'oauth_consumer_key' => $oauthCredentials['consumer_key'],
    'oauth_nonce' => '"'.$oauthNonce.'"',
     'oauth_signature_method' => '"HMAC-SHA256"',
    'oauth_timestamp' => '"'.$oauthTimestamp.'"',
    'oauth_token' => $oauthCredentials['token'],
    'oauth_version' => '1.0',
	'script' => '391'
];


 $data = '<?xml version="1.0" encoding="UTF-8"?>
<root>
<Orders>
 <Order>
 <Winery>WOOCT</Winery>
 <OrderNumber>55555</OrderNumber>
 <RecipientName>John Doe</RecipientName>
 <CompanyName>Test Company</CompanyName>
 <AddressLine1>1 Main Street</AddressLine1>
 <AddressLine2></AddressLine2>
 <City>Dixon</City>
 <State>CA</State>
 <Zip>95620</Zip>
 <Country>United States</Country>
 <ShipMethod>FXO</ShipMethod>
 <ShipDate>7/18/2016</ShipDate>
 <GiftMessage></GiftMessage>
 <SpecialInstructions>
 **Pls ship Fedex Overnight for arrival on Fri 7/22/16**
 </SpecialInstructions>
 <Ice>True</Ice>
 <Phone>555-555-5555</Phone>
 <Email>gsmith@mercurytechdev.com</Email>
 <Item>
 <ItemSku>2013Cab750</ItemSku>
 <ItemDescription>2013 Test Cab 750ml</ItemDescription>
 <Quantity>1</Quantity>
 </Item>
 </Order>
</Orders>
</root>
';

$allParameters = [
	'deploy' => '1',
     'realm' => '1151066',
    'oauth_consumer_key' => $oauthCredentials['consumer_key'],
    'oauth_nonce' => $oauthNonce,
     'oauth_signature_method' => 'HMAC-SHA256',
    'oauth_timestamp' => $oauthTimestamp,
    'oauth_token' => $oauthCredentials['token'],
    'oauth_version' => '1.0',
	'script' => '391'
];;

 
// Construct the base string
 $baseString = 'POST&' . rawurlencode($apiUrl) . '&' . rawurlencode(http_build_query($allParameters, '', '&', PHP_QUERY_RFC3986));

// Construct the signing key
$signingKey = rawurlencode($oauthCredentials['token_secret']) . '&' . rawurlencode($oauthCredentials['consumer_secret']);

// Generate the OAuth signature
$oauthSignature = base64_encode(hash_hmac('sha256', $baseString, $signingKey, true));

// Add the signature to the OAuth parameters
$oauthParameters['oauth_signature'] =  $oauthSignature;


// Set up cURL
$ch = curl_init($apiUrl);

echo str_replace('%22','"',http_build_query($oauthParameters, '', ', ', PHP_QUERY_RFC3986));

// Set cURL options for a POST request
curl_setopt($ch, CURLOPT_VERBOSE, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: text/plain',
    "Authorization: OAuth \" str_replace('%22','\"', implode(',', $oauthParameters) \"",
]);

// Execute cURL and get the response
$response = curl_exec($ch);

// Check for cURL errors
if (curl_errno($ch)) {
    echo 'Curl error: ' . curl_error($ch);
}

// Close cURL
curl_close($ch);

// Display the API response
echo $response;

?>
