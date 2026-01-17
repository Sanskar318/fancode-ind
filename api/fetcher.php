<?php
header('Content-Type: application/json; charset=utf-8');
header("Access-Control-Allow-Origin: *");

$file = '../matches.json';
if (!file_exists($file)) {
    echo json_encode(["status" => "error", "matches" => []]);
    exit;
}

$raw = json_decode(file_get_contents($file), true);
$matches = [];

// App API parsing logic
if (isset($raw['data']['layout'])) {
    foreach ($raw['data']['layout'] as $section) {
        if (isset($section['widgets'])) {
            foreach ($section['widgets'] as $widget) {
                if (isset($widget['items'])) {
                    foreach ($widget['items'] as $item) {
                        $id = $item['match_id'] ?? $item['id'];
                        if ($id) {
                            $matches[] = [
                                "title" => $item['name'] ?? $item['title'],
                                "src" => $item['posterUrl'] ?? $item['image_url'],
                                "status" => ($item['is_live'] == true) ? "LIVE" : "UPCOMING",
                                "match_id" => (int)$id,
                                "adfree_url" => "api/stream.php?id=" . $id . "&ext=.m3u8"
                            ];
                        }
                    }
                }
            }
        }
    }
}

echo json_encode([
    "status" => "success",
    "matches" => array_values(array_column($matches, null, 'match_id'))
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
