const express = require("express");
const fetch = require("node-fetch");

const app = express();

// ====== ENV ======
const BOT_TOKEN = process.env.BOT_TOKEN;
const CHANNEL = process.env.CHANNEL; // without @

// ====== CORS FIX ======
app.use((req, res, next) => {
  res.setHeader("Access-Control-Allow-Origin", "*");
  res.setHeader("Access-Control-Allow-Headers", "*");
  next();
});

let POSTS = [];

// ====== TEXT FORMATTER ======
function formatText(text = "") {
  return text
    .replace(/\n+/g, "\n\n") // extra line breaks clean
    .replace(/Download link\s*:-/gi, "\nDownload link :-")
    .replace(/Share & Support Us/gi, "\n🔺 Share & Support Us 🔻\n")
    .replace(/@/g, "➖ @")
    .trim();
}

// ====== TELEGRAM FETCH ======
async function fetchTelegram() {
  try {
    const url = `https://api.telegram.org/bot${BOT_TOKEN}/getUpdates`;
    const res = await fetch(url);
    const data = await res.json();

    if (!data.ok) return;

    const messages = data.result
      .map(u => u.channel_post)
      .filter(Boolean)
      .filter(m => m.chat.username === CHANNEL)
      .slice(-10)
      .reverse();

    POSTS = messages.map(m => ({
      text: formatText(m.caption || m.text || ""),
      image: m.photo
        ? `https://api.telegram.org/file/bot${BOT_TOKEN}/${
            m.photo[m.photo.length - 1].file_id
          }`
        : null,
      date: new Date(m.date * 1000).toISOString(),
      link: `https://t.me/${CHANNEL}/${m.message_id}`
    }));

  } catch (err) {
    console.error("Telegram fetch error:", err.message);
  }
}

// ====== AUTO REFRESH ======
setInterval(fetchTelegram, 10000);
fetchTelegram();

// ====== API ======
app.get("/data", (req, res) => {
  res.json(POSTS);
});

// ====== SERVER ======
const PORT = process.env.PORT || 3000;
app.listen(PORT, () => {
  console.log("Server running on port", PORT);
});
