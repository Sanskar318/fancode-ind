<?php
// Script by Jass Saini with ❤️
// (\/)
// (*_*)
// (>❤️)
// script 100% free don't buy and sell, 
// join https://t.me/digitalkeyadda
require_once 'function.php';

$scheme  = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host    = $_SERVER['HTTP_HOST'] ?? 'localhost';
$baseUri = $scheme . '://' . $host . rtrim(dirname($_SERVER['PHP_SELF']), '/\\') . '/';
$playlistUrl = $baseUri . 'playlist.m3u';
// Script by Jass Saini with ❤️
// (\/)
// (*_*)
// (>❤️)
// script 100% free don't buy and sell, 
// join https://t.me/digitalkeyadda
$channels = getChannels();
$totalChannels = count($channels);
// Script by Jass Saini with ❤️
// (\/)
// (*_*)
// (>❤️)
// script 100% free don't buy and sell, 
// join https://t.me/digitalkeyadda
$groups = [];
foreach ($channels as $ch) {
    $g = $ch['group-title'] ?? 'General';
    if (!in_array($g, $groups)) {
        $groups[] = $g;
    }
}
sort($groups);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>Jass IPTV Hub - Pro Streaming Dashboard</title>
    <!-- Google Fonts & FontAwesome -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --bg-color: #f8fafc;
            --card-bg: #ffffff;
            --border-color: #cbd5e1;
            --text-primary: #0f172a;
            --text-secondary: #64748b;
            --accent-red: #ef4444;
            --accent-blue: #3b82f6;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Plus Jakarta Sans', sans-serif;
            -webkit-tap-highlight-color: transparent;
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-primary);
            min-height: 100vh;
            padding-bottom: 30px;
            overflow-x: hidden;
        }

        .container {
            max-width: 1240px;
            margin: 0 auto;
            padding: 8px;
            width: 100%;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 12px;
            background: var(--card-bg);
            border: 1.5px solid var(--accent-red);
            border-radius: 10px;
            margin-bottom: 10px;
            gap: 8px;
            box-shadow: 0 2px 10px rgba(239, 68, 68, 0.06);
            width: 100%;
        }

        .logo-area {
            display: flex;
            align-items: center;
            gap: 6px;
            min-width: 0;
        }

        .logo-icon {
            width: 30px;
            height: 30px;
            background: linear-gradient(135deg, var(--accent-red), var(--accent-blue));
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            color: #fff;
            flex-shrink: 0;
        }

        .logo-area h1 {
            font-size: 0.95rem;
            font-weight: 800;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .logo-area h1 span.blue { color: var(--accent-blue); }
        .logo-area h1 span.red { color: var(--accent-red); }

        .actions {
            display: flex;
            gap: 4px;
            flex-shrink: 0;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 5px 8px;
            border-radius: 5px;
            font-weight: 600;
            font-size: 0.7rem;
            text-decoration: none;
            cursor: pointer;
            white-space: nowrap;
        }

        .btn-telegram {
            background: rgba(59, 130, 246, 0.1);
            color: var(--accent-blue);
            border: 1px solid rgba(59, 130, 246, 0.3);
        }

        .btn-copy {
            background: rgba(239, 68, 68, 0.1);
            color: var(--accent-red);
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        .sticky-wrapper {
            position: sticky;
            top: 0;
            background: var(--bg-color);
            z-index: 99;
            padding-top: 2px;
            padding-bottom: 2px;
            margin-bottom: 8px;
            width: 100%;
        }

        .controls-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            padding: 8px 10px;
            border-radius: 8px;
            margin-bottom: 6px;
            gap: 8px;
        }

        .search-box {
            position: relative;
            flex-grow: 1;
            min-width: 0;
        }

        .search-box input {
            width: 100%;
            background: #f8fafc;
            border: 1px solid var(--border-color);
            padding: 7px 10px 7px 30px;
            border-radius: 6px;
            color: var(--text-primary);
            font-size: 16px;
            outline: none;
        }

        .search-box input:focus {
            border-color: var(--accent-blue);
        }

        .search-box i {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--accent-blue);
            font-size: 0.75rem;
        }

        .genre-scroll {
            display: flex;
            gap: 5px;
            overflow-x: auto;
            padding-bottom: 2px;
            scrollbar-width: none;
            -webkit-overflow-scrolling: touch;
        }

        .genre-scroll::-webkit-scrollbar {
            display: none;
        }

        .genre-pill {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            color: var(--text-secondary);
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 0.7rem;
            font-weight: 600;
            cursor: pointer;
            white-space: nowrap;
            flex-shrink: 0;
        }

        .genre-pill.active {
            background: var(--accent-blue);
            color: #fff;
            border-color: var(--accent-blue);
        }

        .section-title {
            font-size: 0.75rem;
            font-weight: 700;
            color: var(--text-secondary);
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .section-title span.blue { color: var(--accent-blue); }
        .section-title span.red { color: var(--accent-red); }

        .grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 6px;
            width: 100%;
        }

        @media (min-width: 768px) {
            .grid {
                grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
                gap: 12px;
            }
            .container {
                padding: 16px;
            }
        }

        .card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 6px;
            padding: 6px;
            display: flex;
            flex-direction: column;
            text-align: center;
            align-items: center;
            gap: 4px;
            text-decoration: none;
            min-width: 0; /* Crucial fix to prevent grid blowout */
            overflow: hidden;
        }

        @media (min-width: 768px) {
            .card {
                flex-direction: row;
                text-align: left;
                align-items: center;
                padding: 10px;
                gap: 10px;
            }
        }

        .card:active {
            background-color: #f1f5f9;
        }

        .channel-logo {
            width: 32px;
            height: 32px;
            border-radius: 5px;
            background: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            flex-shrink: 0;
            border: 1px solid var(--border-color);
        }

        @media (min-width: 768px) {
            .channel-logo {
                width: 40px;
                height: 40px;
                border-radius: 8px;
            }
        }

        .channel-logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            padding: 2px;
        }

        .channel-info {
            overflow: hidden;
            width: 100%;
            min-width: 0;
        }

        .channel-name {
            font-size: 0.72rem;
            font-weight: 700;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            color: var(--text-primary);
            margin-bottom: 1px;
            width: 100%;
        }

        @media (min-width: 768px) {
            .channel-name {
                font-size: 0.85rem;
            }
        }

        .channel-group {
            font-size: 0.55rem;
            color: var(--accent-blue);
            background: rgba(59, 130, 246, 0.08);
            border: 1px solid rgba(59, 130, 246, 0.2);
            padding: 1px 3px;
            border-radius: 3px;
            display: inline-block;
            font-weight: 600;
            max-width: 100%;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        @media (min-width: 768px) {
            .channel-group {
                font-size: 0.65rem;
                padding: 1px 5px;
            }
        }

        .toast {
            position: fixed;
            bottom: 15px;
            left: 50%;
            transform: translateX(-50%) translateY(100px);
            background: #0f172a;
            color: #ffffff;
            padding: 6px 14px;
            border-radius: 5px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            border: 1px solid var(--accent-blue);
            transition: transform 0.3s ease;
            z-index: 1000;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .toast.show {
            transform: translateX(-50%) translateY(0);
        }
    </style>
</head>
<body>

    <div class="container">
        <header>
            <div class="logo-area">
                <div class="logo-icon">
                    <i class="fa-solid fa-play"></i>
                </div>
                <h1><span class="blue">Jass</span> <span class="red">IPTV</span> Hub</h1>
            </div>
            <div class="actions">
                <a href="https://t.me/digitalkeyadda" target="_blank" class="btn btn-telegram">
                    <i class="fa-brands fa-telegram"></i> Telegram
                </a>
                <button onclick="copyToClipboard('<?php echo $playlistUrl; ?>', 'Playlist URL copied!')" class="btn btn-copy">
                    <i class="fa-solid fa-copy"></i> Copy
                </button>
            </div>
        </header>

        <div class="sticky-wrapper">
            <div class="controls-bar">
                <div class="search-box">
                    <i class="fa-solid fa-search"></i>
                    <input type="text" id="searchInput" placeholder="Search channels..." onkeyup="filterChannels()">
                </div>
                <div class="channel-count" style="color:var(--text-secondary); font-size:0.7rem; white-space:nowrap;">
                    Total: <span class="blue" id="countDisplay" style="color:var(--accent-blue); font-weight:700;"><?php echo $totalChannels; ?></span>
                </div>
            </div>

            <div class="genre-scroll">
                <button class="genre-pill active" onclick="filterGenre('all', this)">All</button>
                <?php foreach ($groups as $group): ?>
                    <button class="genre-pill" onclick="filterGenre('<?php echo htmlspecialchars($group, ENT_QUOTES); ?>', this)"><?php echo htmlspecialchars($group); ?></button>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="section-title"><span class="blue">Channels</span> <span class="red">Library</span></div>

        <div class="grid" id="channelGrid">
            <?php foreach ($channels as $ch): 
                $id    = htmlspecialchars($ch['tvg-id'] ?? '');
                $name  = htmlspecialchars($ch['channel-name'] ?? '');
                $group = htmlspecialchars($ch['group-title'] ?? 'General');
                $logo  = htmlspecialchars($ch['tvg-logo'] ?? '');
                $playerUrl = "player.php?id={$id}";
            ?>
            <a href="<?php echo $playerUrl; ?>" target="_blank" class="card" data-name="<?php echo strtolower($name); ?>" data-group="<?php echo htmlspecialchars($group); ?>">
                <div class="channel-logo">
                    <?php if (!empty($logo)): ?>
                        <img src="<?php echo $logo; ?>" alt="<?php echo $name; ?>" loading="lazy" onerror="this.src='https://via.placeholder.com/40?text=TV'">
                    <?php else: ?>
                        <i class="fa-solid fa-tv" style="color: var(--text-secondary); font-size: 0.7rem;"></i>
                    <?php endif; ?>
                </div>
                <div class="channel-info">
                    <div class="channel-name" title="<?php echo $name; ?>"><?php echo $name; ?></div>
                    <div class="channel-group"><?php echo $group; ?></div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>

    <div id="toast" class="toast">Link copied!</div>

    <script>
        let currentGenre = 'all';

        function copyToClipboard(text, message) {
            navigator.clipboard.writeText(text).then(() => {
                showToast(message);
            }).catch(err => {
                console.error('Failed to copy: ', err);
            });
        }

        function showToast(message) {
            const toast = document.getElementById('toast');
            toast.textContent = message;
            toast.classList.add('show');
            setTimeout(() => {
                toast.classList.remove('show');
            }, 2000);
        }

        function filterGenre(genre, element) {
            currentGenre = genre;
            document.querySelectorAll('.genre-pill').forEach(p => p.classList.remove('active'));
            element.classList.add('active');
            applyFilters();
        }

        function filterChannels() {
            applyFilters();
        }

        function applyFilters() {
            let input = document.getElementById('searchInput').value.toLowerCase();
            let cards = document.getElementsByClassName('card');
            let visibleCount = 0;

            for (let i = 0; i < cards.length; i++) {
                let name = cards[i].getAttribute('data-name');
                let group = cards[i].getAttribute('data-group');
                
                let matchesSearch = name.includes(input);
                let matchesGenre = (currentGenre === 'all' || group === currentGenre);

                if (matchesSearch && matchesGenre) {
                    cards[i].style.display = "flex";
                    visibleCount++;
                } else {
                    cards[i].style.display = "none";
                }
            }
            document.getElementById('countDisplay').textContent = visibleCount;
        }
    </script>
</body>
</html>