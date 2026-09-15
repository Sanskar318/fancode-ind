<?php
require_once 'function.php';

// Script by Jass Saini with ❤️
// (\/)
// (*_*)
// (>❤️)
// script 100% free don't buy and sell, 
// join https://t.me/digitalkeyadda
$id      = $_GET['id'] ?? '';
$channel = getChannel($id);

if (!$channel) {
    http_response_code(404);
    exit('Channel not found');
}
// Script by Jass Saini with ❤️
// (\/)
// (*_*)
// (>❤️)
// script 100% free don't buy and sell, 
// join https://t.me/digitalkeyadda
$licenseKey = $channel['license_key'] ?? '';
header('Content-Type: application/json; charset=utf-8');

list($keyId, $key) = explode(':', $licenseKey) + [null, null];

// Helper function for Base64URL encoding (RFC 4648)
function base64url_encode($data) {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}
// Script by Jass Saini with ❤️
// (\/)
// (*_*)
// (>❤️)
// script 100% free don't buy and sell, 
// join https://t.me/digitalkeyadda
if ($keyId && $key) {
    echo json_encode([
        "keys" => [
            [
                "kty" => "oct",
                "kid" => base64url_encode(hex2bin($keyId)),
                "k"   => base64url_encode(hex2bin($key))
            ]
        ]
    ]);
} else {
    echo json_encode(["license_key" => $licenseKey]);
}