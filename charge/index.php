<!-- 
This is payment gateway Midtrans backend serverless using Heroku 
 -->
 <?php
// API Server key midtrans
$server_key = "SB-Mid-server-TePmS24ba4WWEfIKCTmsL1Hx";

$is_production = false;

$api_url = $is_production ? 
  'https://app.midtrans.com/snap/v1/transactions' : 
  'https://app.sandbox.midtrans.com/snap/v1/transactions';


if( !strpos($_SERVER['REQUEST_URI'], '/charge') ) {
  http_response_code(404); 
  echo "wrong path, make sure it's `/charge`"; exit();
}

if( $_SERVER['REQUEST_METHOD'] !== 'POST'){
  http_response_code(404);
  echo "Page not found or wrong HTTP request method is used"; exit();
}

// get the HTTP POST body of the request
$request_body = file_get_contents('php://input');
header('Content-Type: application/json');
// set response's content type as JSON
$charge_result = chargeAPI($api_url, $server_key, $request_body);
// set the response http status code
http_response_code($charge_result['http_code']);
// then print out the response body
echo $charge_result['body'];

/**
 * call charge API using Curl
 * @param string  $api_url
 * @param string  $server_key
 * @param string  $request_body
 */
function chargeAPI($api_url, $server_key, $request_body){
  $ch = curl_init();
  $curl_options = array(
    CURLOPT_URL => $api_url,
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_POST => 1,
    CURLOPT_HEADER => 0,
    // Add header to the request, including Authorization generated from server key
    CURLOPT_HTTPHEADER => array(
      'Content-Type: application/json',
      'Accept: application/json',
      'Authorization: Basic ' . base64_encode($server_key . ':')
    ),
    CURLOPT_POSTFIELDS => $request_body
  );
  curl_setopt_array($ch, $curl_options);
  $result = array(
    'body' => curl_exec($ch),
    'http_code' => curl_getinfo($ch, CURLINFO_HTTP_CODE),
  );
  return $result;
}