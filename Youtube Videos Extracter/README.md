# YouTube Videos Extractor

A standalone tool to extract video titles, URLs, and thumbnails from any YouTube channel.

## Setup
1. Open your terminal in this folder.
2. Install dependencies:
   ```bash
   npm install
   ```

## Usage
Run the extractor for the default channel (@svishyderabad):
```bash
node extractor.js
```

Run the extractor for a **different channel**:
```bash
node extractor.js https://www.youtube.com/@ChannelName/videos output.json
```

## Output
The data will be saved as a JSON array in `youtube-videos.json` (or your custom filename).
