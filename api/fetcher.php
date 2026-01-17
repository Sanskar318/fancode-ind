<?php
error_reporting(0);
header('Content-Type: application/json');

function getFancodeData() {
    // FanCode Mobile Discover API - Yeh hamesha data deti hai
    $url = "https://www.fancode.com/api/v1/content/discover/sports-home";
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, "FanCode/3.14.0 (Android 11; Pixel 4 XL)"); // Mobile UA use kar rahe hain
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "X-Platform: android",
        "X-Client: mobile",
        "Origin: https://www.fancode.com"
    ]);
    
    $res = curl_exec($ch);
    curl_close($ch);
    
    $raw = json_decode($res, true);
    $matches = [];

    // Data parsing for drmlive format
    if (isset($raw['data']['layout'])) {
        foreach ($raw['data']['layout'] as $section) {
            if (isset($section['widgets'])) {
                foreach ($section['widgets'] as $widget) {
                    if (isset($widget['items'])) {
                        foreach ($widget['items'] as $item) {
                            // Sirf Match items uthana
                            if (isset($item['match_id']) || $item['type'] == 'MATCH') {
                                $m_id = $item['id'] ?? $item['match_id'];
                                $matches[] = [
                                    "event_category" => $item['category_name'] ?? "Sports",
                                    "title" => $item['name'] ?? $item['title'],
                                    "src" => $item['posterUrl'] ?? $item['image_url'],
                                    "team_1" => $item['team1_name'] ?? "",
                                    "team_2" => $item['team2_name'] ?? "",
                                    "status" => ($item['is_live'] || $item['status'] == 'LIVE') ? "LIVE" : "UPCOMING",
                                    "event_name" => $item['event_name'] ?? "",
                                    "match_name" => $item['name'] ?? "",
                                    "match_id" => (int)$m_id,
                                    "startTime" => date("h:i:s A d-m-Y", ($item['startTime'] / 1000)),
                                    "dai_url" => "https://dai.google.com/linear/hls/event/sid/master.m3u8",
                                    "adfree_url" => "https://fancode-ind.vercel.app/api/stream.php?id=" . $m_id . "&ext=.m3u8"
                                ];
                            }
                        }
                    }
                }
            }
        }
    }
    
    // Duplicate matches hatane ke liye
    $unique_matches = array_values(array_column($matches, null, 'match_id'));
    return $unique_matches;
}

$output = [
    "name" => "Fancode Live Matches Data in Json",
    "telegram" => "https://t.me/SanskarOTT",
    "last update time" => date("h:i:s A d-m-Y"),
    "matches" => getFancodeData()
];

echo json_encode($output, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
?>
