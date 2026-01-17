<?php
error_reporting(0);
header('Content-Type: application/json');

function getFancodeData() {
    // FanCode Layout API jo zyada matches return karti hai
    $url = "https://www.fancode.com/api/v1/content/layout?pageType=home&section=all";
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/110.0.0.0 Safari/537.36");
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Origin: https://www.fancode.com",
        "Referer: https://www.fancode.com/",
        "Accept: application/json"
    ]);
    
    $res = curl_exec($ch);
    curl_close($ch);
    
    $raw = json_decode($res, true);
    $matches = [];

    // FanCode ke sections mein deep search karna
    if (isset($raw['data']['layout'])) {
        foreach ($raw['data']['layout'] as $section) {
            if (isset($section['widgets'])) {
                foreach ($section['widgets'] as $widget) {
                    if (isset($widget['items'])) {
                        foreach ($widget['items'] as $item) {
                            // Sirf matches filter karna
                            if (isset($item['match_id']) || $item['type'] == 'MATCH') {
                                $matches[] = [
                                    "event_category" => $item['category_name'] ?? "Sports",
                                    "title" => $item['name'] ?? $item['title'],
                                    "src" => $item['posterUrl'] ?? $item['image_url'],
                                    "team_1" => $item['team1_name'] ?? "",
                                    "team_2" => $item['team2_name'] ?? "",
                                    "status" => ($item['is_live'] || $item['status'] == 'LIVE') ? "LIVE" : "UPCOMING",
                                    "event_name" => $item['event_name'] ?? "",
                                    "match_name" => $item['name'] ?? "",
                                    "match_id" => $item['id'] ?? $item['match_id'],
                                    "startTime" => date("h:i:s A d-m-Y", ($item['startTime'] / 1000)),
                                    "dai_url" => "https://dai.google.com/linear/hls/event/sid/master.m3u8",
                                    "adfree_url" => "https://fancode-ind.vercel.app/api/stream.php?id=" . ($item['id'] ?? $item['match_id']) . "&ext=.m3u8"
                                ];
                            }
                        }
                    }
                }
            }
        }
    }
    // Agar layout se nahi mila toh fallback to live-events
    if(empty($matches)){
        $res_live = file_get_contents("https://www.fancode.com/api/v1/content/live-events");
        $data_live = json_decode($res_live, true);
        if(isset($data_live['data']['liveEvents'])){
            foreach($data_live['data']['liveEvents'] as $e){
                $matches[] = ["title" => $e['name'], "status" => "LIVE", "match_id" => $e['id'], "src" => $e['posterUrl'], "adfree_url" => "api/stream.php?id=".$e['id']];
            }
        }
    }
    return $matches;
}

$output = [
    "name" => "Fancode Live Matches Data in Json",
    "telegram" => "https://t.me/SanskarOTT",
    "last update time" => date("h:i:s A d-m-Y"),
    "matches" => getFancodeData()
];

echo json_encode($output, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
?>
