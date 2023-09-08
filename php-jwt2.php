<?php
//https://stackoverflow.com/questions/65633126/generating-oauth-token-for-firebase-cloud-messaging-php
// This function is needed, because php doesn't have support for base64UrlEncoded strings
// function base64UrlEncode($text)
// {
//     return str_replace(
//         ['+', '/', '='],
//         ['-', '_', ''],
//         base64_encode($text)
//     );
// }

function base64url_encode($data) {
    $b64 = base64_encode($data);

    if ($b64 === false) {
        return false;
    }

    $url = strtr($b64, "+/", "-_");

    return rtrim($url, "=");
}

// Read service account details
$authConfigString = file_get_contents("./push-notifications-3c140-042781ea3125.json");

// Parse service account details
$authConfig = json_decode($authConfigString);

// Read private key from service account details
$secret = openssl_pkey_get_private($authConfig->private_key);

// Create the token header
$header = json_encode(array(
    'typ' => 'JWT',
    'alg' => 'RS256'
));

// Get seconds since 1 January 1970
$time = time();

// Allow 1 minute time deviation
$start = $time - 60;
$end = $time + 3600;

$payload = json_encode(array(
    "iss" => $authConfig->client_email,
    "scope" => "https://www.googleapis.com/auth/firebase.messaging",
    "aud" => "https://oauth2.googleapis.com/token",
    "exp" => $end,
    "iat" => $start
));

// Encode Header
$base64UrlHeader = base64url_encode($header);

// Encode Payload
$base64UrlPayload = base64url_encode($payload);

// Create Signature Hash
$result = openssl_sign($base64UrlHeader . "." . $base64UrlPayload, $signature, $secret, OPENSSL_ALGO_SHA256);

// Encode Signature to Base64Url String
$base64UrlSignature = base64url_encode($signature);

// Create JWT
$jwt = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;

//-----Request token------
$options = array('http' => array(
    'method'  => 'POST',
    'content' => 'grant_type=urn:ietf:params:oauth:grant-type:jwt-bearer&assertion='.$jwt,
    'header'  =>
        "Content-Type: application/x-www-form-urlencoded"
));
$context  = stream_context_create($options);
$responseText = file_get_contents("https://oauth2.googleapis.com/token", false, $context);

$response = json_decode($responseText);

echo "access token ".$response->access_token;

$token = $response->access_token;

$body = "test body";
$title = "test title";
$image = "/assets/icons/icon-72x72.png";

$data = array(
  "message" => array(
    "name" => "projects/push-notifications-3c140/messages/1",
    "notification" => array(
        "body"  => $body,
        "title" => $title,
        "image" => $image
    ),
    "data" => array(
        "click_action"  =>  "FLUTTER_NOTIFICATION_CLICK",
        "id"            =>  "1",
        "status"        =>  "done",
    ),
    "token" => "ceIaIeXQ-LEeN5JizmRthG:APA91bF8IIFKR79HEVKijJPLM0BP829IM_4DagN4BAEMJswWYis_4INZ_Cy_3AsgJT8aSDStONmNeXlQHJVeQDglGC6P6SOsAsJONk-oyUzQSM2DiwTNf9L2O8SZ2DvXIygAkx03cM2Y"
  )
);

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/v1/projects/push-notifications-3c140/messages:send');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

$headers = array();
$headers[] = 'Content-Type: application/json';
$headers[] = 'Authorization: Bearer '. $token;
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$result = curl_exec($ch);

if ($result === FALSE) {
  //Failed
  echo 'Curl failed: ' . curl_error($ch)."\r\n"."\r\n";
}

curl_close ($ch);

echo $result."\r\n"."\r\n";