const express = require("express");
const axios = require("axios");

const app = express();
const PORT = process.env.PORT || 3000;

const BOT_TOKEN = process.env.BOT_TOKEN;
const CHANNEL = process.env.CHANNEL;

let posts = [];

/* 🔓 CORS */
app.use((req, res, next) => {
  res.setHeader("Access-Control-Allow-Origin", "*");
  next();
});

/* 🔧 TEXT CLEANER */
function formatText(text = "") {
  return text
    .replace(/\r\n/g, "\n")
    .replace(/\n{2,}/g, "\n\n")
    .trim();
}

/* 📡 TELEGRAM FETCH */
async function fetchUpdates() {
  try {
    const url = `https://api.telegram.org/bot${BOT_TOKEN}/getUpdates`;
    const res = await axios.get(url);

    posts = [];

    for (let u of res.data.result.reverse()) {
      const msg = u.channel_post;
      if (!msg || msg.chat.username !== CHANNEL) continue;

      let image = null;
      if (msg.photo) {
        const fileId = msg.photo[msg.photo.length - 1].file_id;
        const file = await axios.get(
          `https://api.telegram.org/bot${BOT_TOKEN}/getFile?file_id=${fileId}`
        );
        image = `https://api.telegram.org/file/bot${BOT_TOKEN}/${file.data.result.file_path}`;
      }

      posts.push({
        date: new Date(msg.date * 1000).toISOString(),
        text: formatText(msg.text || msg.caption || ""),
        image,
        link: `https://t.me/${CHANNEL}/${msg.message_id}`,
      });
    }
  } catch (err) {
    console.log("Fetch error:", err.message);
  }
}

/* ⏱ AUTO REFRESH */
setInterval(fetchUpdates, 5000);
fetchUpdates();

/* 📤 API */
app.get("/data", (req, res) => {
  res.json(posts);
});

/* 🟢 START */
app.listen(PORT, () => {
  console.log("Server running on", PORT);
});
