const express = require("express");
const fs = require("fs");
const path = require("path");

const app = express();

/* 🔓 CORS (VERY IMPORTANT) */
app.use((req, res, next) => {
  res.setHeader("Access-Control-Allow-Origin", "*");
  res.setHeader("Access-Control-Allow-Methods", "GET,POST");
  res.setHeader("Access-Control-Allow-Headers", "Content-Type");
  next();
});

app.use(express.json());

/* 📁 DATA FILE */
const DATA_FILE = path.join(__dirname, "posts.json");

/* Ensure posts.json exists */
if (!fs.existsSync(DATA_FILE)) {
  fs.writeFileSync(DATA_FILE, JSON.stringify([]));
}

/* 🔔 TELEGRAM WEBHOOK */
app.post("/webhook", (req, res) => {
  try {
    const msg = req.body.channel_post;
    if (!msg) return res.sendStatus(200);

    const posts = JSON.parse(fs.readFileSync(DATA_FILE));

    posts.push({
      text: msg.text || "(no text)",
      date: new Date().toISOString(),
      link: `https://t.me/${process.env.CHANNEL}/${msg.message_id}`
    });

    /* Keep only last 50 posts */
    if (posts.length > 50) posts.shift();

    fs.writeFileSync(DATA_FILE, JSON.stringify(posts, null, 2));
    res.sendStatus(200);
  } catch (e) {
    console.error(e);
    res.sendStatus(500);
  }
});

/* 🌐 API FOR WEBSITE */
app.get("/data", (req, res) => {
  const posts = JSON.parse(fs.readFileSync(DATA_FILE));
  res.json(posts.slice().reverse());
});

/* 🚀 START SERVER */
const PORT = process.env.PORT || 3000;
app.listen(PORT, () => {
  console.log("Server running on port", PORT);
});
