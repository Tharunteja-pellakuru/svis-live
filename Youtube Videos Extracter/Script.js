const fs = require("fs");

const videos = require("./youtube-videos.json");

let sql = `
INSERT INTO videos 
(video_url, video_title, video_description, thumbnail)
VALUES
`;

const values = videos.map((video) => {
  const videoUrl = video.videoUrl.replace(/'/g, "\\'");
  const videoTitle = video.videoTitle.replace(/'/g, "\\'");
  const videoDescription = (
    video.videoDescription || ""
  ).replace(/'/g, "\\'");
  const thumbnail = (
    video.thumbnail || ""
  ).replace(/'/g, "\\'");

  return `(
    '${videoUrl}',
    '${videoTitle}',
    '${videoDescription}',
    '${thumbnail}'
  )`;
});

sql += values.join(",\n");
sql += ";";

fs.writeFileSync("videos.sql", sql);

console.log("videos.sql generated");