const express = require("express");
const fs = require("fs");
const path = require("path");

// node 18+ me fetch already hota hai
const app = express();
app.use(express.json());

// 🔓 CORS (Vercel / GitHub Pages ke liye)
app.use((req, res, next) => {
  res.setHeader("Access-Control-Allow-Origin", "*");
  next();
});

const DATA_FILE = path.join(__dirname, "data.json");

// data.json create agar na ho
if (!fs.existsSync(DATA_FILE)) {
  fs.writeFileSync(DATA_FILE, "[]");
}

// 🔗 Telegram file URL nikalne ka helper
async function getFileUrl(fileId) {
  const r = await fetch(
    `https://api.telegram.org/bot${process.env.BOT_TOKEN}/getFile?file_id=${fileId}`
  );
  const j = await r.json();
  return `https://api.telegram.org/file/bot${process.env.BOT_TOKEN}/${j.result.file_path}`;
}

// 🔔 WEBHOOK
app.post("/webhook", async (req, res) => {
  try {
    const msg = req.body.message || req.body.channel_post;
    if (!msg) return res.send("ignored");

    const posts = JSON.parse(fs.readFileSync(DATA_FILE));

    let post = {
      date: new Date(msg.date * 1000).toISOString(),
      link: `https://t.me/${process.env.CHANNEL}/${msg.message_id}`,
      text: msg.text || msg.caption || "",
      type: "text"
    };

    // 🖼️ IMAGE
    if (msg.photo) {
      post.type = "photo";
      post.file = await getFileUrl(
        msg.photo[msg.photo.length - 1].file_id
      );
    }

    // 🎥 VIDEO
    if (msg.video) {
      post.type = "video";
      post.file = await getFileUrl(msg.video.file_id);
    }

    // latest post upar
    posts.unshift(post);

    // sirf last 50 posts rakho
    fs.writeFileSync(DATA_FILE, JSON.stringify(posts.slice(0, 50), null, 2));

    res.send("ok");
  } catch (e) {
    console.error(e);
    res.send("error");
  }
});

// 📦 DATA API (frontend yahin se fetch karega)
app.get("/data", (req, res) => {
  const posts = JSON.parse(fs.readFileSync(DATA_FILE));
  res.json(posts);
});

// health check
app.get("/", (req, res) => {
  res.send("Telegram bot server running ✅");
});

const PORT = process.env.PORT || 3000;
app.listen(PORT, () => {
  console.log("Server running on port", PORT);
});
