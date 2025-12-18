const express = require("express");
const fs = require("fs");
const path = require("path");
const fetch = (...args) =>
  import("node-fetch").then(({ default: fetch }) => fetch(...args));

const app = express();
app.use(express.json());

/* =========================
   ✅ CORS (FINAL)
========================= */
app.use((req, res, next) => {
  res.header("Access-Control-Allow-Origin", "*");
  res.header("Access-Control-Allow-Methods", "GET,POST,OPTIONS");
  res.header("Access-Control-Allow-Headers", "Content-Type, Accept");
  if (req.method === "OPTIONS") return res.sendStatus(200);
  next();
});

/* =========================
   📁 DATA FILE
========================= */
const DATA_FILE = path.join(__dirname, "posts.json");

if (!fs.existsSync(DATA_FILE)) {
  fs.writeFileSync(DATA_FILE, "[]");
}

/* =========================
   🔧 HELPER: Telegram file URL
========================= */
async function getFileUrl(fileId) {
  const res = await fetch(
    `https://api.telegram.org/bot${process.env.BOT_TOKEN}/getFile?file_id=${fileId}`
  );
  const data = await res.json();
  return `https://api.telegram.org/file/bot${process.env.BOT_TOKEN}/${data.result.file_path}`;
}

/* =========================
   🔔 TELEGRAM WEBHOOK
========================= */
app.post("/webhook", async (req, res) => {
  try {
    const msg = req.body.message || req.body.channel_post;
    if (!msg) return res.sendStatus(200);

    const posts = JSON.parse(fs.readFileSync(DATA_FILE, "utf8"));

    let post = {
      text: msg.text || msg.caption || "",
      date: new Date(msg.date * 1000).toISOString(),
      link: `https://t.me/${process.env.CHANNEL}/${msg.message_id}`,
      type: "text",
      file: null
    };

    // 🖼️ PHOTO
    if (msg.photo) {
      const fileId = msg.photo[msg.photo.length - 1].file_id;
      post.type = "photo";
      post.file = await getFileUrl(fileId);
    }

    // 🎥 VIDEO
    if (msg.video) {
      post.type = "video";
      post.file = await getFileUrl(msg.video.file_id);
    }

    posts.unshift(post);

    // sirf last 50 posts rakho
    fs.writeFileSync(DATA_FILE, JSON.stringify(posts.slice(0, 50), null, 2));

    res.sendStatus(200);
  } catch (err) {
    console.error("Webhook error:", err);
    res.sendStatus(500);
  }
});

/* =========================
   🌐 API FOR WEBSITE
========================= */
app.get("/data", (req, res) => {
  try {
    const posts = JSON.parse(fs.readFileSync(DATA_FILE, "utf8"));
    res.json(posts);
  } catch (err) {
    res.status(500).json({ error: "Failed to load data" });
  }
});

/* =========================
   🩺 HEALTH CHECK
========================= */
app.get("/", (req, res) => {
  res.send("Telegram Bot Server Running");
});

/* =========================
   🚀 START SERVER
========================= */
const PORT = process.env.PORT || 3000;
app.listen(PORT, () => {
  console.log("Server running on port", PORT);
});
