<?php
error_reporting(0);
header('Content-Type: application/json');

function getFancodeData() {
    // FanCode GraphQL API - Yeh sabse reliable source hai
    $url = "https://www.fancode.com/api/v1/content/home?section=all";
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // Rotating User-Agent to avoid bot detection
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Linux; Android 10; SM-G960F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/108.0.0.0 Mobile Safari/537.36");
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Origin: https://www.fancode.com",
        "Referer: https://www.fancode.com/",
        "Accept: application/json",
        "X-Platform: web"
    ]);
    
    $res = curl_exec($ch);
    curl_close($ch);
    
    if(!$res) return [];

    $raw = json_decode($res, true);
    $matches = [];

    // FanCode raw data structure parsing
    if (isset($raw['data']['sections'])) {
        foreach ($raw['data']['sections'] as $section) {
            if (isset($section['items'])) {
                foreach ($section['items'] as $item) {
                    // Filter for matches only
                    if (isset($item['match_id']) || (isset($item['type']) && $item['type'] == 'MATCH')) {
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
    
    // Duplicate remove and sorting
    return array_values(array_column($matches, null, 'match_id'));
}

$output = [
    "name" => "Fancode Live Matches Data in Json",
    "telegram" => "https://t.me/SanskarOTT",
    "last update time" => date("h:i:s A d-m-Y"),
    "matches" => getFancodeData()
];

echo json_encode($output, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
?>
