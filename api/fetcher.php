<?php
header('Content-Type: application/json; charset=utf-8');
header("Access-Control-Allow-Origin: *");

// DRMLive ka auto-updated source (Yeh 100% stable hai)
$url = "https://raw.githubusercontent.com/byte-capsule/FanCode-Hls-Fetcher/main/Fancode_hls_m3u8.Json";

$res = file_get_contents($url);
if (!$res) {
    echo json_encode(["status" => "error", "matches" => []]);
    exit;
}

$data = json_decode($res, true);
$matches = [];

// Unke JSON format ke hisaab se data parse karna
if (isset($data['matches'])) {
    foreach ($data['matches'] as $item) {
        $matches[] = [
            "event_category" => $item['event_catagory'] ?? "Sports",
            "title" => $item['match_name'] ?? $item['event_name'],
            "src" => $item['banner'] ?? "",
            "status" => "LIVE",
            "match_id" => (int)$item['match_id'],
            "startTime" => "LIVE NOW",
            // Inka stream link direct hota hai, hum use parse kar rahe hain
            "adfree_url" => "api/stream.php?id=" . $item['match_id'] . "&ext=.m3u8"
        ];
    }
}

echo json_encode([
    "status" => "success",
    "matches" => $matches
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
