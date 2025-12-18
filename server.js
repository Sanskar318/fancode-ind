const express = require("express");
const fs = require("fs");
const path = require("path");

const app = express();
app.use(express.json());

// ✅ CORS (MOST IMPORTANT)
app.use((req, res, next) => {
  res.setHeader("Access-Control-Allow-Origin", "*");
  next();
});

const DATA_FILE = path.join(__dirname, "data.json");

// Init file
if (!fs.existsSync(DATA_FILE)) {
  fs.writeFileSync(DATA_FILE, "[]");
}

// 🔹 Webhook endpoint
app.post("/webhook", (req, res) => {
  const msg = req.body.message || req.body.channel_post;
  if (!msg || !msg.text) return res.send("ignored");

  const posts = JSON.parse(fs.readFileSync(DATA_FILE));

  posts.unshift({
    text: msg.text,
    date: new Date(msg.date * 1000).toISOString(),
    link: `https://t.me/${process.env.CHANNEL}/${msg.message_id}`
  });

  fs.writeFileSync(DATA_FILE, JSON.stringify(posts.slice(0, 50)));
  res.send("ok");
});

// 🔹 API for website
app.get("/data", (req, res) => {
  const posts = JSON.parse(fs.readFileSync(DATA_FILE));
  res.json(posts);
});

// 🔹 Health check
app.get("/", (req, res) => {
  res.send("Telegram bot server running");
});

const PORT = process.env.PORT || 3000;
app.listen(PORT, () => console.log("Server running"));
