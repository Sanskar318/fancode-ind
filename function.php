<?php
declare(strict_types=1);

function callUnifiedApi(bool $isPost = false) {
    $apiUrl = 'https://data.cricindia95.workers.dev/';
    
    $ch = curl_init($apiUrl);
    $options = [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_TIMEOUT        => 8
    ];
// Script by Jass Saini with ❤️
// (\/)
// (*_*)
// (>❤️)
// script 100% free don't buy and sell, 
// join https://t.me/digitalkeyadda
    if ($isPost) {
        $options[CURLOPT_POST] = true;
        $options[CURLOPT_POSTFIELDS] = ''; 
    }
// Script by Jass Saini with ❤️
// (\/)
// (*_*)
// (>❤️)
// script 100% free don't buy and sell, 
// join https://t.me/digitalkeyadda
    curl_setopt_array($ch, $options);
    $response = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($response === false || $status !== 200) {
        return null;
    }
    return $response;
}
function getChannels(): array {
    $cacheFile = __DIR__ . '/data.json';
    if (file_exists($cacheFile) && (time() - filemtime($cacheFile) < 43200)) {
        $data = file_get_contents($cacheFile);
        if ($data) {
            $decoded = json_decode($data, true);
            if (is_array($decoded) && !empty($decoded)) {
                return $decoded;
            }
        }
    }
// Script by Jass Saini with ❤️
// (\/)
// (*_*)
// (>❤️)
// script 100% free don't buy and sell, 
// join https://t.me/digitalkeyadda
    $data = callUnifiedApi(false);

    if ($data) {
        $decoded = json_decode($data, true);
        if (is_array($decoded) && !empty($decoded)) {
            @file_put_contents($cacheFile, $data, LOCK_EX);
            return $decoded;
        }
    } 
    if (file_exists($cacheFile)) {
        return json_decode((string)file_get_contents($cacheFile), true) ?? [];
    }
// Script by Jass Saini with ❤️
// (\/)
// (*_*)
// (>❤️)
// script 100% free don't buy and sell, 
// join https://t.me/digitalkeyadda
    return [];
}
function getChannel(string $id): ?array {
    foreach (getChannels() as $channel) {
        if (($channel['tvg-id'] ?? '') === $id) {
            return $channel;
        }
    }
    return null;
}
function getToken(): string {
    $cacheDir = __DIR__ . '/cache';
    if (!is_dir($cacheDir)) {
        @mkdir($cacheDir, 0777, true);
    }
// Script by Jass Saini with ❤️
// (\/)
// (*_*)
// (>❤️)
// script 100% free don't buy and sell, 
// join https://t.me/digitalkeyadda   
    $cacheFile = $cacheDir . '/token.jass';
    if (file_exists($cacheFile) && (time() - filemtime($cacheFile) < 43200)) {
        $token = trim((string)file_get_contents($cacheFile));
        if (!empty($token)) {
            return $token;
        }
    }
    $token = callUnifiedApi(true);
    $token = trim($token ?? '');

    if (!empty($token)) {
        @file_put_contents($cacheFile, $token, LOCK_EX);
        return $token;
    }
/**
 * // Script built by Jass Saini with ❤️
 **/ 
    return '';
}
function handleSegmentRelay(string $upstreamMpdUrl, string $segment): void {
    $token = getToken();
    $targetBase = dirname($upstreamMpdUrl);
    $upstreamFilename = str_ireplace('.mp4', '.dash', basename($segment));
    $targetUrl = $targetBase . '/dash/' . ltrim($upstreamFilename, '/') . '?' . $token;
    $headers = [
        'User-Agent: Jass/5.0',
        'Cookie: ' . $token
    ];
    if (!empty($_SERVER['HTTP_RANGE'])) {
        $headers[] = 'Range: ' . $_SERVER['HTTP_RANGE'];
    }
/**
 * // Script built by Jass Saini with ❤️
 **/ 
    $ch = curl_init($targetUrl);
    curl_setopt_array($ch, [
        CURLOPT_HTTPHEADER     => $headers,
        CURLOPT_RETURNTRANSFER => false,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_TIMEOUT        => 15,
        CURLOPT_HEADERFUNCTION => function ($curl, string $headerLine) {
            if (stripos($headerLine, 'Content-Type:') === 0 || stripos($headerLine, 'Content-Range:') === 0 || stripos($headerLine, 'Accept-Ranges:') === 0) {
                header(trim($headerLine));
            }
            return strlen($headerLine);
        }
    ]);
    curl_exec($ch);
    curl_close($ch);
    exit;
}
/**
 * // Script built by Jass Saini with ❤️
 **/ 
function handleManifestProxy(string $upstreamMpdUrl, string $selfMpdUrl, string $selfBaseUrl): void {
    $token = getToken();
    $currentTime = time();
    $beginTime = gmdate('Y-m-d\TH:i:s\Z', $currentTime - 1199);
    $endTime   = gmdate('Y-m-d\TH:i:s\Z', $currentTime + 120);

    $manifestUrl = $upstreamMpdUrl . '?' . $token . '&begin=' . $beginTime . '&end=' . $endTime;

    $ch = curl_init($manifestUrl);
    curl_setopt_array($ch, [
        CURLOPT_HTTPHEADER     => [
            'User-Agent: Jass/5.0',
            'Cookie: ' . $token,
            'Cache-Control: no-cache, no-store'
        ],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_TIMEOUT        => 8
    ]);

    $mpd = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($mpd === false || $status !== 200) {
        http_response_code(502);
        exit("Failed upstream MPD");
    }
/**
 * // Script built by Jass Saini with ❤️
 **/ 
    $mpd = preg_replace('/<BaseURL\b[^>]*>.*?<\/BaseURL>/is', '', $mpd);
    $mpd = preg_replace('/<Location\b[^>]*>.*?<\/Location>/is', '', $mpd);
    $mpd = preg_replace('/<UTCTiming\b[^>]*\/?>/is', '', $mpd);

    $topInjections = "\n  <Location>" . htmlspecialchars($selfMpdUrl) . "</Location>" .
                     "\n  <UTCTiming schemeIdUri=\"urn:mpeg:dash:utc:http-iso:2014\" value=\"https://time.akamai.com/?iso\"/>";
    $mpd = preg_replace('/(<MPD\b[^>]*>)/i', '$1' . $topInjections, $mpd, 1);

    $mpd = preg_replace('/type="static"/i', 'type="dynamic"', $mpd);
    $mpd = preg_replace('/minimumUpdatePeriod="[^"]*"/i', 'minimumUpdatePeriod="PT2S"', $mpd);

    $baseUrlTag = "\n    <BaseURL>" . htmlspecialchars($selfBaseUrl) . "</BaseURL>";
    $mpd = preg_replace('/(<Period\b[^>]*>)/i', '$1' . $baseUrlTag, $mpd, 1);
    $mpd = str_replace('.dash"', '.mp4"', $mpd);
/**
 * // Script built by Jass Saini with ❤️
 **/ 
    header('Content-Type: application/dash+xml; charset=utf-8');
    echo $mpd;
    exit;
}