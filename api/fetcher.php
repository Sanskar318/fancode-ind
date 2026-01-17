<?php
error_reporting(0);
header('Content-Type: application/json');

function getFancodeData() {
    // FanCode ki asali API jahan se sara data milta hai
    $url = "https://www.fancode.com/api/v1/content/home?section=all";
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/115.0.0.0 Safari/537.36");
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Origin: https://www.fancode.com", "Referer: https://www.fancode.com/"]);
    $res = curl_exec($ch);
    curl_close($ch);
    
    $raw = json_decode($res, true);
    $matches = [];

    // FanCode ke data structure mein se matches nikalna
    if (isset($raw['data']['sections'])) {
        foreach ($raw['data']['sections'] as $section) {
            if (isset($section['items'])) {
                foreach ($section['items'] as $item) {
                    if ($item['type'] == 'MATCH' || isset($item['match_id'])) {
                        $matches[] = [
                            "event_category" => $item['category_name'] ?? "Sports",
                            "title" => $item['name'] ?? $item['title'],
                            "src" => $item['posterUrl'] ?? $item['image_url'],
                            "team_1" => $item['team1_name'] ?? "",
                            "team_2" => $item['team2_name'] ?? "",
                            "status" => ($item['is_live']) ? "LIVE" : "UPCOMING",
                            "event_name" => $item['event_name'] ?? "",
                            "match_name" => $item['name'] ?? "",
                            "match_id" => $item['id'] ?? $item['match_id'],
                            "startTime" => date("h:i:s A d-m-Y", ($item['startTime'] / 1000)),
                            "dai_url" => "https://dai.google.com/linear/hls/event/sid/master.m3u8", // Proxy Link
                            "adfree_url" => "https://fancode-ind.vercel.app/api/stream.php?id=" . $item['id'] . "&ext=.m3u8"
                        ];
                    }
                }
            }
        }
    }
    return $matches;
}

$output = [
    "name" => "Fancode Live Matches Data in Json",
    "telegram" => "https://t.me/your_channel",
    "last update time" => date("h:i:s A d-m-Y"),
    "matches" => getFancodeData()
];

echo json_encode($output, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
?>
