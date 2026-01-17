<?php
error_reporting(0);
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");

function getFancodeData() {
    // FanCode Mobile-Web API endpoint jo sabse zyada data deta hai
    $url = "https://www.fancode.com/api/v1/content/home?section=all";
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Linux; Android 11; Pixel 5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.91 Mobile Safari/537.36");
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "X-Platform: web",
        "Origin: https://www.fancode.com",
        "Referer: https://www.fancode.com/"
    ]);
    
    $res = curl_exec($ch);
    curl_close($ch);
    
    $raw = json_decode($res, true);
    $matches = [];

    if (isset($raw['data']['sections'])) {
        foreach ($raw['data']['sections'] as $section) {
            if (isset($section['items'])) {
                foreach ($section['items'] as $item) {
                    // Match items ko filter karna (Cricket, Football, Sab)
                    if (isset($item['match_id']) || (isset($item['type']) && $item['type'] == 'MATCH')) {
                        $id = $item['id'] ?? $item['match_id'];
                        $matches[] = [
                            "event_category" => $item['category_name'] ?? "Sports",
                            "title" => $item['name'] ?? $item['title'],
                            "src" => $item['posterUrl'] ?? $item['image_url'],
                            "team_1" => $item['team1_name'] ?? "Team A",
                            "team_2" => $item['team2_name'] ?? "Team B",
                            "status" => ($item['is_live']) ? "LIVE" : "UPCOMING",
                            "event_name" => $item['event_name'] ?? "FanCode Event",
                            "match_name" => $item['name'] ?? "Match",
                            "match_id" => $id,
                            "startTime" => date("h:i:s A d-m-Y", ($item['startTime'] / 1000)),
                            "dai_url" => "https://dai.google.com/linear/hls/event/sid/master.m3u8",
                            "adfree_url" => "https://fancode-ind.vercel.app/api/stream.php?id=" . $id . "&ext=.m3u8"
                        ];
                    }
                }
            }
        }
    }
    // Duplicate hatane ke liye
    return array_values(array_column($matches, null, 'match_id'));
}

$output = [
    "name" => "Fancode Live Matches Data",
    "last update time" => date("h:i:s A d-m-Y"),
    "matches" => getFancodeData()
];

echo json_encode($output, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
?>
