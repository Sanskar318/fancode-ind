<?php
// Script built by Jass with ❤️
require_once 'function.php';

$id = $_GET['id'] ?? '';
if (empty($id)) {
    header("Location: ./");
    exit;
}
// Script by Jass Saini with ❤️
// (\/)
// (*_*)
// (>❤️)
// script 100% free don't buy and sell, 
// join https://t.me/digitalkeyadda
$channel = getChannel($id);
if (!$channel) {
    header("Location: ./");
    exit;
}

$channelName = $channel['channel-name'] ?? 'Live Stream';
$channelLogo = $channel['tvg-logo'] ?? '';

$licenseKeyField = $channel['license_key'] ?? $channel['key'] ?? '';
$keyId = '';
$keyValue = '';

if (!empty($licenseKeyField)) {
    $delimiter = strpos($licenseKeyField, ':') !== false ? ':' : (strpos($licenseKeyField, ',') !== false ? ',' : '');
    if ($delimiter) {
        $parts = explode($delimiter, trim($licenseKeyField), 2);
        $keyId = trim($parts[0]);
        $keyValue = trim($parts[1]);
    }
}

if (empty($keyId)) {
    $keyId = $channel['kid'] ?? $channel['key-id'] ?? $channel['keyId'] ?? '';
}
if (empty($keyValue)) {
    $keyValue = $channel['keyVal'] ?? $channel['keyValue'] ?? '';
}
// Script by Jass Saini with ❤️
// (\/)
// (*_*)
// (>❤️)
// script 100% free don't buy and sell, 
// join https://t.me/digitalkeyadda
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';

$baseUri = $scheme . '://' . $host . rtrim(dirname($_SERVER['PHP_SELF']), '/\\') . '/';
$mpdUrl = $baseUri . "{$id}/manifest.mpd";

function convertToHex(string $data): string {
    $data = trim($data);
    if (empty($data)) return '';
    $isHex = (preg_match('/^[a-fA-F0-9]+$/', $data) === 1);
    if ($isHex && (strlen($data) == 32 || strlen($data) == 16 || strlen($data) == 64)) {
        return strtolower($data);
    }
    $decoded = base64_decode($data, true);
    if ($decoded !== false && base64_encode($decoded) === $data) {
        return bin2hex($decoded);
    }
    return bin2hex($data);
}
// Script by Jass Saini with ❤️
// (\/)
// (*_*)
// (>❤️)
// script 100% free don't buy and sell, 
// join https://t.me/digitalkeyadda
$hexKeyId = convertToHex($keyId);
$hexKeyVal = convertToHex($keyValue);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover" />
<meta name="robots" content="noindex" />
<title><?php echo htmlspecialchars($channelName); ?> - Jass IPTV Player</title>
<style type="text/css">
    body {
        margin: 0;
        padding: 0;
        background: #000;
        overflow: hidden;
        height: 100vh;
        width: 100vw;
        position: fixed;
    }
    #player-container {
        width: 100%;
        height: 100%;
        position: absolute;
        top: 0;
        left: 0;
    }
    #player {
        width: 100% !important;
        height: 100% !important;
        position: absolute !important;
        top: 0;
        left: 0;
    }
</style>
</head>
<body oncontextmenu="return false" onkeydown="return false;" onmousedown="return false;">

<div id="player-container">
    <div id="player"></div>
</div>

<script src="https://content.jwplatform.com/libraries/KB5zFt7A.js"></script>
<script> jwplayer.key='XSuP4qMl+9tK17QNb+4+th2Pm9AWgMO/cYH8CI0HGGr7bdjo';</script>

<script type="text/javascript">
var streamURL = "<?php echo $mpdUrl; ?>";
var streamIMG = "<?php echo $channelLogo; ?>";
var keyID = "<?php echo $hexKeyId; ?>";
var keyVal = "<?php echo $hexKeyVal; ?>";

var playerConfig = {
    "logo": { "file": "" },
    playlist: [{
        "title": "<?php echo addslashes($channelName); ?>",
        "description": "Jass IPTV Streaming",
        "image": streamIMG,
        "sources": [{
            "default": false,
            "type": "dash",
            "file": streamURL,
            "label": "HD"
        }]
    }],
    width: "100%",
    height: "100%",
    aspectratio: "16:9",
    autostart: false,
    stretching: "fill",
    cast: {},
    sharing: {}
};

if (keyID && keyVal) {
    playerConfig.playlist[0].sources[0].drm = {
        "clearkey": { "keyId": keyID, "key": keyVal }
    };
}

var playerInstance = jwplayer("player");
playerInstance.setup(playerConfig);
</script>

</body>
</html>