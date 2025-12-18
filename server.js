const express = require("express");
const fs = require("fs");
const path = require("path");

const app = express();

/* =========================
   ✅ CORS (FINAL FIX)
========================= */
app.use((req, res, next) => {
  res.header("Access-Control-Allow-Origin", "*");
  res.header("Access-Control-Allow-Methods", "GET,POST,OPTIONS");
  res.header("Access-Control-Allow-Headers", "Content-Type, Accept");
  if (req.method === "OPTIONS") return res.sendStatus(200);
  next();
});

app.use(express.json());

/* =========================
   📁 DATA FILE
========================= */
const DATA_FILE = path.join(__dirname, "posts.json");

if (!fs.existsSync(DATA_FILE)) {
  fs.writeFileSync(DATA_FILE, "[]");
}

/* =========================
   🔔 TELEGRAM WEBHOOK
========================= */
app.post("/webhook", (req, res) => {
  try {
    const msg = req.body.channel_post;
    if (!msg) return res.sendStatus(200);

    const posts = JSON.parse(fs.readFileSync(DATA_FILE, "utf8"));

    posts.push({
      text: msg.text || msg.caption || "",
      date: new Date().toISOString(),
      link: `https://t.me/${process.env.CHANNEL}/${msg.message_id}`
    });

    // sirf last 50 posts rakho
    if (posts.length > 50) posts.shift();

    fs.writeFileSync(DATA_FILE, JSON.stringify(posts, null, 2));
    res.sendStatus(200);
  } catch (err) {
    console.error("Webhook error:", err);
    res.sendStatus(500);
  }
});

/* =========================
   🌐 PUBLIC API
========================= */
app.get("/data", (req, res) => {
  try {
    const posts = JSON.parse(fs.readFileSync(DATA_FILE, "utf8"));
    res.json(posts);
  } catch (err) {
    res.status(500).json({ error: "Failed to read data" });
  }
});

/* =========================
   🚀 START SERVER
========================= */
const PORT = process.env.PORT || 3000;
app.listen(PORT, () => {
  console.log("Server running on port", PORT);
});
