<?php
// Requires: composer require firebase/php-jwt
require_once("vendor/autoload.php");
use Firebase\JWT\JWT;

// Get your service account's email address and private key from the JSON key file
$service_account_email = "firebase-adminsdk-b8387@push-notifications-3c140.iam.gserviceaccount.com";
$private_key = "-----BEGIN PRIVATE KEY-----\nMIIEvwIBADANBgkqhkiG9w0BAQEFAASCBKkwggSlAgEAAoIBAQC9NcF7Q2hXiYGV\nLQFS2XueqTNhGEHwsQ/jhOICj6fsZsk5WWI+LXOFa2G9jrxWZhec/nUYAPs7/2Lw\ngpmqI9LS2EFz4SMCwEU34CPqrEEsoTTTVFHjB7Fnlx6fpq2wKaTom0WP3YjmTbro\nXW9xvTJ5coTd1EHN3wDshxZ/bdJ2c2Zi+2wqrX5z73cbjBTk5xMZZqD3Yc1SrlCW\n3bwO64Hdpg8qr1Jn3WqxFCEMb5rqgt9VstEo/1Y6FlEy+Fl8qH9Bpqfkj04Cs0md\nc33qVzpdjhzq08UTFktWVA+ViD+kuY8lwPMCbpYGq7GPRMGNRGoqUVwULFTfI/Cw\nDb77WWXvAgMBAAECggEAXj87D4+tl0Dus6RVzvN28LqeVaR0IBTKcc28mIL/JbGz\nVOYxmgAg1Vn+NyI0rSKwa2qSX/EJM8MMAPpE2at7dbvzqml/+3xTXjg/G8NOuZDZ\nE9Uh7uDjnNJ5FhQ72w57TsQdG8LBUNpyGhGElBamYDlLdoWme4oz1x2Y9sN4/Le0\n/usyQVCL1xmMOOtc6U/LLZIXlVWIAiumZ44Zu06rq1mG14hkqZdequYQXAysX7Ew\npMVkZxtxOvWSjo1q1pGY4EOU08rVJT43Os52W7rNiYF86qnBWTlXokMPoLPNCtIt\nHIWz8j3bn0OQUFqwUhLOKEMJ0YpJHJk/gGrB2erVbQKBgQD34MugHJf0+cT1Kjjt\nq6hWJBJrPTe3ykjqdPYP7CBr7OXL7DXqjrGSOATJlTzpDLYMVWobIu4Cikc486Os\nMTqir4eMdzvaYomun28wpHOftMaH2PLFJWLal0luW5Z3FTf0q5MMxT5rNjJV+y8s\nqOW56qfUJhP/t1v2RXAao2CDPQKBgQDDaNn35VykxjYafJJVhZD3UOgHCkQj/PCH\nOGtipRH6YyA5cueFtAHsgdpc1SZHCf9XEcmG9qnGGWVdM5xrvDPMqQxQHGkPmYB0\n2CplsxKgfssFixicj96GmetsVmNeqhbQk/sqWgRf1WK5lfNgilWQ/F01IQxJOoFT\nC8ykJ3CwmwKBgQDdQEBg9MTJ/BtKfdp8gijqYp4yLF1MZnl5FNcBVVGHI9flkjx5\n1c435lqXl+bbWeYw0hi4ihAKImT6N7ZTH0noJmcGAPNitWuRe2vi7hbqaZB/dy0S\nvZEj7b+0inmeZ0kf0fmaf4B8b860IlV0Nnl+3i3ZVfep239xLX+nt0aRPQKBgQC4\no8lZkFLSqyuSKWUIBDXvSnaDuHKcYrNPwcLOKdVr9uALCIS1dFphBG21S/5oH40y\n027N5SKUOYjq4QqLTgDQAGfPBplLESssvNiK0gLmvgfNzBnMTbDhFG08KACrASKf\noUQxR29csj8fxw15ihzB64OS4RA/3VU3iC2sakvzwwKBgQDXaW1j63HSiwUy91KK\nywszL+BglDfjiixq75P7vBR0KJwDr6unL3O/gKnnw+NVu1Y17a8Ehn2rJ2j/Jt78\nLaoVnbXeTzzVsNHDFhH4XcJR8iw51i6irB4z0cRP5ColP6ebdPYFr46izg3lMrbr\nhoLk5DAyQOG/pwzmvGuYQXjVpg==\n-----END PRIVATE KEY-----\n";

function create_custom_token($uid, $is_premium_account) {
  global $service_account_email, $private_key;

  $now_seconds = time();
  $payload = array(
    "iss" => $service_account_email,
    "sub" => $service_account_email,
    "aud" => "https://identitytoolkit.googleapis.com/google.identity.identitytoolkit.v1.IdentityToolkit",
    "iat" => $now_seconds,
    "exp" => $now_seconds+(60*60),  // Maximum expiration time is one hour
    "uid" => $uid,
    "scope" => "https://www.googleapis.com/auth/firebase.messaging",
    "claims" => array(
      "premium_account" => $is_premium_account,
    )
  );
  return JWT::encode($payload, $private_key, "RS256");
}

$token = create_custom_token("hBwdnCXDssSfhc1uuqCZOkxz5rf1", false);

echo $token."\r\n"."\r\n";

$body = "test body";
$title = "test title";
$image = "/assets/icons/icon-72x72.png";

$data = [
  "name" => "projects/push-notifications-3c140/messages/1",
  "notification" => [
      "body"  => $body,
      "title" => $title,
      "image" => $image
  ],
  "data" => [
      "click_action"  =>  "FLUTTER_NOTIFICATION_CLICK",
      "id"            =>  "1",
      "status"        =>  "done",
      "info"          =>  [
          "title"  => $title,
          "image"  => $image
      ]
  ],
  "token" => "ceIaIeXQ-LEeN5JizmRthG:APA91bF8IIFKR79HEVKijJPLM0BP829IM_4DagN4BAEMJswWYis_4INZ_Cy_3AsgJT8aSDStONmNeXlQHJVeQDglGC6P6SOsAsJONk-oyUzQSM2DiwTNf9L2O8SZ2DvXIygAkx03cM2Y"
];

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
