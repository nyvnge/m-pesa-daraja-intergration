<?php 
if(isset($_POST['submit'])){

date_default_timezone_set('Africa/Nairobi');

# Unique Access Points
$consumerKey = "4vnt0YCtlrA7gd2xewLiWMqaCfUB5hyK";
$consumerSecret = "5u25FKQMcwoY3VAv";

# Definition of Variables
# I Recommend you use the same variable names as used below. 
$BusinessShortCode = "174379";
$PassKey = "bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919";
# Party A is the phone number you want to initiate the mpesa stk. When Keying the phone number you are required to key it in the 254xxxxxxx format
# AccountReference you can put any number.
# TransactionDesc - Give the reason or describe the type of transaction you want to make
$PartyA = $_POST['phoneNumber'];
$AccountReference = "9238";
$TransactionDesc = "Donation Funds";
$Amount = $_POST['amount'];

$Timestamp = date("YmdHis");

$Password = base64_encode($BusinessShortCode.$PassKey.$Timestamp);

 # Access token header
 $headers = ['Content-Type:application/json; charset=utf8'];

 # URL M-PESA endpoints
$access_token_url = 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';
$initiate_url = 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';

# Callback url
# Once you have created an app with Heroku they will give you a unique URL which you will use to launch your app.
$CallBackURL = 'https://hidden-plains-66369.herokuapp.com//callback_url.php';  

$curl = curl_init($access_token_url);
curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($curl, CURLOPT_HEADER, FALSE);
curl_setopt($curl, CURLOPT_USERPWD, $consumerKey.':'.$consumerSecret);
$result = curl_exec($curl);
$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
$result = json_decode($result);
$access_token = $result->access_token;  
curl_close($curl);

# Stk Push Header
$stkheader = ['Content-Type:application/json','Authorization:Bearer '.$access_token];

# Transaction initiation
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $initiate_url);
curl_setopt($curl, CURLOPT_HTTPHEADER, $stkheader); //setting custom header

$curl_post_data = array(
 'BusinessShortCode' => $BusinessShortCode,
 'Password' => $Password,
 'Timestamp' => $Timestamp,
 'TransactionType' => 'CustomerPayBillOnline',
 'Amount' => $Amount,
 'PartyA' => $PartyA,
 'PartyB' => $BusinessShortCode,
 'PhoneNumber' => $PartyA,
 'CallBackURL' => $CallBackURL,
 'AccountReference' => $AccountReference,
 'TransactionDesc' => $TransactionDesc
);

$data_string = json_encode($curl_post_data);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
$curl_response = curl_exec($curl);
print_r($curl_response);

echo $curl_response;
};
?>
