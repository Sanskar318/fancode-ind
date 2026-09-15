<?php
require_once 'function.php';
header('Content-Type: audio/x-mpegurl; charset=utf-8');
// Script by Jass Saini with ❤️
// (\/)
// (*_*)
// (>❤️)
// script 100% free don't buy and sell, 
// join https://t.me/digitalkeyadda
$scheme  = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host    = $_SERVER['HTTP_HOST'] ?? 'localhost';
$baseUri = $scheme . '://' . $host . dirname($_SERVER['PHP_SELF']) . '/';

echo "#EXTM3U\n";
foreach (getChannels() as $ch) {
    $id    = $ch['tvg-id'] ?? '';
    $name  = $ch['channel-name'] ?? '';
    $group = $ch['group-title'] ?? 'General';
    $logo  = $ch['tvg-logo'] ?? '';
    // Script by Jass Saini with ❤️
// (\/)
// (*_*)
// (>❤️)
// script 100% free don't buy and sell, 
// join https://t.me/digitalkeyadda
    $manifestUrl = $baseUri . "{$id}/manifest.mpd";
    $keyUrl      = $baseUri . "{$id}/lic";
    // Script by Jass Saini with ❤️
// (\/)
// (*_*)
// (>❤️)
// script 100% free don't buy and sell, 
// join https://t.me/digitalkeyadda
    echo "#EXTINF:-1 tvg-id=\"{$id}\" tvg-name=\"{$name}\" tvg-logo=\"{$logo}\" group-title=\"{$group}\",{$name}\n";
    echo "#KODIPROP:inputstream.adaptive.license_type=clearkey\n";
    echo "#KODIPROP:inputstream.adaptive.license_key={$keyUrl}\n";
    echo "{$manifestUrl}\n";
}