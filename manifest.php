<?php
require_once 'function.php';
// Script by Jass Saini with ❤️
// (\/)
// (*_*)
// (>❤️)
// script 100% free don't buy and sell, 
// join https://t.me/digitalkeyadda
$id      = $_GET['id'] ?? '';
$segment = $_GET['segment'] ?? '';
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
$upstreamMpdUrl = $channel['mpd_url'] ?? '';
if (empty($upstreamMpdUrl)) {
    http_response_code(404);
    exit('Stream URL not found');
}

$upstreamMpdUrl = str_ireplace('jiotvpllive.cdn.jio.com', 'jiotvcod.cdn.jio.com', $upstreamMpdUrl);
// Script by Jass Saini with ❤️
// (\/)
// (*_*)
// (>❤️)
// script 100% free don't buy and sell, 
// join https://t.me/digitalkeyadda
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host   = $_SERVER['HTTP_HOST'] ?? 'localhost';
$baseDir = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
$selfBaseUrl = $scheme . '://' . $host . $baseDir . '/' . $id . '/segments/';
$selfMpdUrl  = $scheme . '://' . $host . $_SERVER['REQUEST_URI'];

header('Access-Control-Allow-Origin: *');
// Script by Jass Saini with ❤️
// (\/)
// (*_*)
// (>❤️)
// script 100% free don't buy and sell, 
// join https://t.me/digitalkeyadda
if (!empty($segment)) {
    handleSegmentRelay($upstreamMpdUrl, $segment);
} else {
    handleManifestProxy($upstreamMpdUrl, $selfMpdUrl, $selfBaseUrl);
}