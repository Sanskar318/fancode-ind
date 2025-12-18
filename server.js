const express = require("express");
const fs = require("fs");
const path = require("path");
const fetch = require("node-fetch");

const app = express();
const PORT = process.env.PORT || 3000;

const BOT_TOKEN = process.env.BOT_TOKEN;
const CHANNEL = process.env.CHANNEL;

const DATA_FILE = path.join(__dirname, "data.json");

/* ---------- CORS ---------- */
app.use((req, res, next) => {
  res.setHeader("Access-Control-Allow-Origin", "*");
  next();
});

app.use(express.json());

/* ---------- Utils ---------- */

// format telegram text nicely
function formatText(text = "") {
  // split by line breaks
  let lines = text
    .split(/\n+/)
    .map(l => l.trim())
    .filter(Boolean);

  return lines.join("\n\n"); // spacing between paragraphs
}

// extract all links from text
function extractLinks(text = "") {
  const regex = /(https?:\/\/[^\s]+)/g;
  return text.match(regex) || [];
}

/* ---------- Save message ---------- */
function savePost(post) {
  let data = [];
  if (fs.existsSync(DATA_FILE)) {
    data = JSON.parse(fs.readFileSync(DATA_FILE));
  }
  data.unshift(post); // latest first
  fs.writeFileSync(DATA_FILE, JSON.stringify(data, null, 2));
}

/* ---------- Telegram Webhook ---------- */
app.post(`/webhook/${BOT_TOKEN}`, async (req, res) => {
  try {
    const msg = req.body.message || req.body.channel_post;
    if (!msg || !msg.text && !msg.caption) return res.send("OK");

    const rawText = msg.text || msg.caption;
    const cleanText = formatText(rawText);
    const links = extractLinks(rawText);

    let image = null;

    if (msg.photo) {
      const fileId = msg.photo[msg.photo.length - 1].file_id;
      const fileRes = await fetch(
        `https://api.telegram.org/bot${BOT_TOKEN}/getFile?file_id=${fileId}`
      );
      const fileData = await fileRes.json();
      image = `https://api.telegram.org/file/bot${BOT_TOKEN}/${fileData.result.file_path}`;
    }

    const post = {
      date: new Date(msg.date * 1000).toISOString(),
      text: cleanText,
      links,
      image,
      telegram_link: `https://t.me/${CHANNEL}/${msg.message_id}`
    };

    savePost(post);
    res.send("OK");
  } catch (e) {
    console.error(e);
    res.send("ERROR");
  }
});

/* ---------- Data API ---------- */
app.get("/data", (req, res) => {
  if (!fs.existsSync(DATA_FILE)) return res.json([]);
  const data = JSON.parse(fs.readFileSync(DATA_FILE));
  res.json(data);
});

/* ---------- Health ---------- */
app.get("/", (req, res) => {
  res.send("Telegram Server Running ✅");
});

app.listen(PORT, () => {
  console.log("Server running on", PORT);
});
