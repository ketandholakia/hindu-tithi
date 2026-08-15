# Tithi Darshan Bot - Complete User Guide

Welcome to the **Tithi Darshan Bot**! This bot brings the full power of the `vittix/panchang` Vedic Astrology engine directly to your Telegram chats. 

You can ask the bot for daily panchang details, generate Kundlis, find upcoming festivals, calculate auspicious muhurtas, and much more!

---

## 📍 Smart Location Feature

**You don't need to type coordinates manually!**
The easiest way to use the bot is to simply use Telegram's built-in **Share Location** feature (the map pin icon 📎). 
When you share a location pin, the bot will automatically calculate **today's Panchang** for that exact spot and reply instantly! 

The bot uses Reverse Geocoding, which means it will read your coordinates and nicely display your actual City and State name in its responses.

---

## 📝 General Rules for Commands

- **Dates:** Can be formatted as `YYYY-MM-DD` or `DD-MM-YYYY` (e.g., `2026-08-15` or `15-08-2026`). You can also use the word `today`.
- **Times:** Should be in 24-hour format (e.g., `14:30`). You can also use the word `now`.
- **Location:** If a command requires a location, provide it as `latitude,longitude` (e.g., `28.61,77.20`). If you leave it blank, the bot defaults to New Delhi.

---

## 📚 Complete Command List

### 📅 Core Features
- **`/panchang [date] [lat,lon]`**
  Get the Daily Panchang for a specific date and location. Includes Tithi, Nakshatra, Yoga, and Karana.
  *Example:* `/panchang 15-08-2026 19.07,72.87` (Panchang for Mumbai)

- **`/kundli [date time] [lat,lon]`**
  Generates a basic D1 (Rasi) Kundli summary. Shows your Ascendant (Lagna), Moon Sign, and the exact positions of all planets.
  *Example:* `/kundli 15-08-2026 14:30 28.61,77.20`

- **`/festival [date] [lat,lon]`**
  Scans the next 30 days starting from the given date and lists all major Hindu festivals.
  *Example:* `/festival today`

### ⏳ Timing & Auspiciousness
- **`/muhurta [date] [lat,lon]`**
  Calculates the exact start and end times for the most important daily segments:
  ✅ **Abhijit Muhurta** (Highly auspicious)
  ❌ **Rahu Kaal**, ⚠️ **Yamaganda**, ⚠️ **Gulika Kaal** (Inauspicious)
  *Example:* `/muhurta today`

- **`/choghadiya [date] [lat,lon]`**
  Generates the complete Day and Night Choghadiya timetable (Amrit, Shubh, Labh, Chal, Udveg, Rog, Kaal) so you can plan your daily activities.
  *Example:* `/choghadiya 15-08-2026`

- **`/electional [date time] [lat,lon]`**
  Acts as your personal Muhurta consultant! Evaluates the specific moment provided and gives it an **Auspiciousness Score out of 100**, letting you know instantly if it's a good time to start new activities.
  *Example:* `/electional now`

### 🔭 Advanced Astrology
- **`/ascendant [date time] [lat,lon]`** (or `/lagna`)
  Calculates the exact Zodiac sign rising on the Eastern horizon at that moment, including its precise longitude in degrees.
  *Example:* `/lagna now`

- **`/yogas [date time] [lat,lon]`**
  Instantly scans the sky for classical astrological Yogas forming at a given moment (e.g., Raj Yogas, Pancha Mahapurusha Yogas).
  *Example:* `/yogas now`

- **`/rashi [date time]`**
  A quick command to check the Moon's current sign (Janma Rashi).
  *Example:* `/rashi now`

- **`/dasha [date time]`**
  Calculates the Vimshottari Mahadasha sequence based on the exact Moon position, listing the ruling planets and the end dates for their major periods.
  *Example:* `/dasha now`

- **`/shadbala [date time] [lat,lon]`**
  Computes the intricate Six-Fold strength of the 7 visible planets. Outputs them sorted from strongest (🔥/⭐) to weakest (⚠️) along with their exact Rupa score, instantly revealing the chart's dominant planet.
  *Example:* `/shadbala now`

- **`/varga [type] [date time] [lat,lon]`**
  Calculates any of the Parashari divisional charts (from D2 Hora to D60 Shashtiamsha). If you don't provide a type, it defaults to the highly important Navamsha (D9) chart.
  *Example:* `/varga D9 now`
  *Example:* `/varga D10 15-08-2026 14:30 28.61,77.20`

### 🗓️ Traditional Calendar
- **`/calendar [date]`**
  Returns the classical Hindu Calendar details for any given day. Shows Vikram Samvat, Shaka Samvat, Amanta and Purnimanta month names (including Adhika Masa/leap month detection), lunar phase (Paksha), and Season (Ritu).
  *Example:* `/calendar today`

---

## 🛠️ Admin Configuration

If you are the bot administrator, you can configure the bot's Webhook URL and Token directly from your web application's dashboard. 
Navigate to `yourdomain.com/admin/telegram` in your browser to access the sleek control panel where you can easily connect the bot to Telegram's servers.
