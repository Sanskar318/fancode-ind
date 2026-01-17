<?php
header('Content-Type: application/json; charset=utf-8');
header("Access-Control-Allow-Origin: *");
error_reporting(0);

function getFancodeData() {
    // FanCode Discover API - Yeh hamesha data deti hai aur block kam hoti hai
    $url = "https://www.fancode.com/api/v1/content/discover/sports-home";
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // Real Android Mobile User Agent
    curl_setopt($ch, CURLOPT_USERAGENT, "FanCode/3.14.0 (Android 11; SM-G960F)");
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "X-Platform: android",
        "X-Client: mobile",
        "Origin: https://www.fancode.com",
        "Referer: https://www.fancode.com/",
        "Accept: application/json"
    ]);
    
    $res = curl_exec($ch);
    curl_close($ch);
    
    if (!$res) return [];

    $raw = json_decode($res, true);
    $matches = [];

    // Layout Widgets se data nikalna
    if (isset($raw['data']['layout'])) {
        foreach ($raw['data']['layout'] as $section) {
            if (isset($section['widgets'])) {
                foreach ($section['widgets'] as $widget) {
                    if (isset($widget['items'])) {
                        foreach ($widget['items'] as $item) {
                            if (isset($item['match_id']) || $item['type'] == 'MATCH') {
                                $id = $item['id'] ?? $item['match_id'];
                                $matches[] = [
                                    "event_category" => $item['category_name'] ?? "Sports",
                                    "title" => $item['name'] ?? $item['title'],
                                    "src" => $item['posterUrl'] ?? $item['image_url'],
                                    "status" => ($item['is_live'] || $item['status'] == 'LIVE') ? "LIVE" : "UPCOMING",
                                    "match_id" => (int)$id,
                                    "startTime" => date("h:i A d-m", ($item['startTime'] / 1000)),
                                    "adfree_url" => "api/stream.php?id=" . $id . "&ext=.m3u8"
                                ];
                            }
                        }
                    }
                }
            }
        }
    }
    
    // Duplicate matches ko remove karna
    return array_values(array_column($matches, null, 'match_id'));
}

echo json_encode([
    "name" => "Fancode Live Matches Data",
    "telegram" => "https://t.me/SanskarOTT",
    "last update time" => date("h:i:s A d-m-Y"),
    "matches" => getFancodeData()
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
?>
