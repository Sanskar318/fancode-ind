<?php
header('Content-Type: application/json; charset=utf-8');
header("Access-Control-Allow-Origin: *");
error_reporting(0);

function getLiveMatches() {
    // FanCode ki asali Live Events API
    $url = "https://www.fancode.com/api/v1/content/live-events";
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36");
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "X-Platform: web",
        "Origin: https://www.fancode.com",
        "Referer: https://www.fancode.com/"
    ]);
    
    $res = curl_exec($ch);
    curl_close($ch);
    
    $raw = json_decode($res, true);
    $matches = [];

    if (isset($raw['data']['liveEvents'])) {
        foreach ($raw['data']['liveEvents'] as $event) {
            $matches[] = [
                "event_category" => $event['categoryName'] ?? "Live Sports",
                "title" => $event['name'],
                "src" => $event['posterUrl'] ?? "https://www.fancode.com/skin/images/fancode-logo.png",
                "status" => "LIVE",
                "match_id" => (int)$event['id'],
                "startTime" => "LIVE NOW",
                "adfree_url" => "api/stream.php?id=" . $event['id'] . "&ext=.m3u8"
            ];
        }
    }
    return $matches;
}

echo json_encode([
    "status" => "success",
    "last update time" => date("h:i:s A d-m-Y"),
    "matches" => getLiveMatches()
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
?>
