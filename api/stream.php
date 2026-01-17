<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/vnd.apple.mpegurl");

$id = $_GET['id'] ?? '';

if (!empty($id)) {
    // Note: Asali FanCode resolver logic future mein yahan lagega
    // Filhaal test m3u8 stream par redirect kar raha hai
    $stream_url = "https://dai.google.com/linear/hls/event/sid/master.m3u8";
    header("Location: $stream_url");
    exit;
}
?>
