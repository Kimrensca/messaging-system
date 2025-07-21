<?php
require 'vendor/autoload.php';

use AfricasTalking\SDK\AfricasTalking;

$username = "sandbox"; // or your live username
$apiKey   = "atsk_04351b370b774f401cd3c49d15267ef760f52d261bb3eafac6cd7ddbcd25b310fed6e71a";

$AT = new AfricasTalking($username, $apiKey);
$sms = $AT->sms();

try {
    $result = $sms->send([
        'to'      => '+254711082123', // replace with a valid recipient number
        'message' => 'Hello from Africa\'s Talking!'
    ]);
    print_r($result);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
