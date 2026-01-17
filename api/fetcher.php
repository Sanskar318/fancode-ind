<?php
header('Content-Type: application/json; charset=utf-8');
header("Access-Control-Allow-Origin: *");
error_reporting(0);

function getFancodeData() {
    $matches = [];
    
    // Step 1: Sabse pehle asali Live Events pakdo (Direct API)
    $live_url = "https://www.fancode.com/api/v1/content/live-events";
    $ch = curl_init($live_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36");
    $res_live = curl_exec($ch);
    curl_close($ch);
    $data_live = json_decode($res_live, true);

    if (isset($data_live['data']['liveEvents'])) {
        foreach ($data_live['data']['liveEvents'] as $event) {
            $matches[] = [
                "event_category" => $event['categoryName'] ?? "Live",
                "title" => $event['name'],
                "src" => $event['posterUrl'],
                "status" => "LIVE", // Force Live status
                "match_id" => (int)$event['id'],
                "startTime" => "LIVE NOW",
                "adfree_url" => "api/stream.php?id=" . $event['id'] . "&ext=.m3u8"
            ];
        }
    }

    // Step 2: Ab baki ke Upcoming matches Layout API se uthao
    $layout_url = "https://www.fancode.com/api/v1/content/layout?pageType=home&section=all";
    $ch2 = curl_init($layout_url);
    curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch2, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36");
    $res_layout = curl_exec($ch2);
    curl_close($ch2);
    $data_layout = json_decode($res_layout, true);

    if (isset($data_layout['data']['layout'])) {
        foreach ($data_layout['data']['layout'] as $section) {
            if (isset($section['widgets'])) {
                foreach ($section['widgets'] as $widget) {
                    if (isset($widget['items'])) {
                        foreach ($widget['items'] as $item) {
                            if (isset($item['match_id'])) {
                                $matches[] = [
                                    "event_category" => $item['category_name'] ?? "Sports",
                                    "title" => $item['name'] ?? $item['title'],
                                    "src" => $item['posterUrl'] ?? $item['image_url'],
                                    "status" => ($item['is_live'] || $item['status'] == 'LIVE') ? "LIVE" : "UPCOMING",
                                    "match_id" => (int)$item['match_id'],
                                    "startTime" => date("h:i A d-m", ($item['startTime'] / 1000)),
                                    "adfree_url" => "api/stream.php?id=" . $item['match_id'] . "&ext=.m3u8"
                                ];
                            }
                        }
                    }
                }
            }
        }
    }

    // Duplicate remove karein match_id ke basis par
    return array_values(array_column($matches, null, 'match_id'));
}

echo json_encode([
    "name" => "Fancode Live Matches Data",
    "telegram" => "https://t.me/SanskarOTT",
    "last update time" => date("h:i:s A d-m-Y"),
    "matches" => getFancodeData()
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
?>
