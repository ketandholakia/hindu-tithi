<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Vittix\Panchang\Panchang;
use Vittix\Panchang\ValueObject\GeoLocation;
use Vittix\Panchang\Enum\Varga;
use DateTimeImmutable;
use DateTimeZone;

class TelegramBotService
{
    private string $token;
    private string $apiUrl;

    public function __construct()
    {
        $this->token = config('telegram.bot_token');
        $this->apiUrl = "https://api.telegram.org/bot{$this->token}";
    }

    /**
     * Parse the incoming update and handle the command.
     */
    public function handleUpdate(array $update): void
    {
        $message = $update['message'] ?? $update['edited_message'] ?? null;
        
        if (!$message) {
            return;
        }

        $chatId = $message['chat']['id'];
        
        $text = $message['text'] ?? '';
        
        // Handle native location shares
        if (isset($message['location'])) {
            $lat = $message['location']['latitude'];
            $lon = $message['location']['longitude'];
            
            // Treat location sharing as a panchang request for today at that spot
            $this->handlePanchang($chatId, ['today', "{$lat},{$lon}"]);
            return;
        }

        if (empty($text) || !str_starts_with($text, '/')) {
            return;
        }

        // Parse command and arguments safely ignoring multiple spaces
        $parts = preg_split('/\s+/', trim($text));
        $command = strtolower(array_shift($parts));

        switch ($command) {
            case '/start':
            case '/help':
                $this->sendHelp($chatId);
                break;
            case '/panchang':
                $this->handlePanchang($chatId, $parts);
                break;
            case '/kundli':
                $this->handleKundli($chatId, $parts);
                break;
            case '/festival':
            case '/festivals':
                $this->handleFestival($chatId, $parts);
                break;
            default:
                $this->sendMessage($chatId, "Unknown command. Send /help to see available commands.");
                break;
        }
    }

    private function sendHelp(int|string $chatId): void
    {
        $help = "🙏 *Welcome to Vittix Panchang Bot!*\n\n"
              . "Available commands:\n"
              . "👉 `/panchang [date] [lat,lon]`\n"
              . "Gets the daily Panchang. Defaults to today at New Delhi.\n"
              . "Example: `/panchang 2026-08-15 19.07,72.87`\n\n"
              . "👉 `/kundli [YYYY-MM-DD HH:MM] [lat,lon]`\n"
              . "Gets a basic Kundli summary (Ascendant, Moon sign, etc.).\n"
              . "Example: `/kundli 1990-05-15 14:30 19.07,72.87`\n\n"
              . "👉 `/festival [date] [lat,lon]`\n"
              . "Gets upcoming festivals starting from the date (up to 30 days ahead).\n"
              . "Example: `/festival 2026-08-15 19.07,72.87`\n\n"
              . "You can also send a location pin 📍 to get its coordinates!";

        $this->sendMessage($chatId, $help, 'Markdown');
    }

    private function handlePanchang(int|string $chatId, array $args): void
    {
        try {
            // Parse arguments
            $dateStr = 'today';
            $lat = 28.6139; // Default New Delhi
            $lon = 77.2090;

            if (count($args) > 0) {
                if (str_contains($args[0], ',')) {
                    // First arg is location
                    [$lat, $lon] = explode(',', $args[0]);
                } else {
                    // First arg is date
                    $dateStr = $args[0];
                    if (isset($args[1])) {
                        [$lat, $lon] = explode(',', $args[1]);
                    }
                }
            }

            $date = $this->parseDate($dateStr);
            $location = new GeoLocation((float)$lat, (float)$lon, 0);
            $datetime = new DateTimeImmutable($date->format('Y-m-d H:i:s'), new DateTimeZone('Asia/Kolkata'));

            $panchangEngine = app(Panchang::class);
            $panchang = $panchangEngine->day($datetime, $location);

            $tithi = $panchang->tithiAtSunrise->nameEnglish() ?? 'Unknown';
            $nakshatra = $panchang->nakshatraAtSunrise->nameEnglish() ?? 'Unknown';
            $yoga = $panchang->yogaAtSunrise->nameEnglish() ?? 'Unknown';
            $karana = $panchang->karanaAtSunrise->nameEnglish() ?? 'Unknown';
            
            $locationName = $this->getLocationName((float)$lat, (float)$lon);

            $response = "🕉 *Panchang for {$date->format('d M Y')}*\n"
                      . "📍 Location: `$locationName`\n\n"
                      . "🗓 *Tithi:* $tithi\n"
                      . "🌟 *Nakshatra:* $nakshatra\n"
                      . "🧘 *Yoga:* $yoga\n"
                      . "⚡ *Karana:* $karana\n";

            $this->sendMessage($chatId, $response, 'Markdown');

        } catch (\Throwable $e) {
            Log::error("Telegram /panchang error: " . $e->getMessage());
            $this->sendMessage($chatId, "❌ Error calculating Panchang. Ensure format is `/panchang YYYY-MM-DD lat,lon`");
        }
    }

    private function handleKundli(int|string $chatId, array $args): void
    {
        try {
            if (count($args) < 2) {
                $this->sendMessage($chatId, "⚠️ Usage: `/kundli YYYY-MM-DD HH:MM [lat,lon]`\nExample: `/kundli 1990-05-15 14:30 28.6,77.2`", 'Markdown');
                return;
            }

            $dateStr = $args[0] . ' ' . $args[1];
            $lat = 28.6139;
            $lon = 77.2090;

            if (isset($args[2])) {
                [$lat, $lon] = explode(',', $args[2]);
            }

            $date = $this->parseDate($dateStr);
            $location = new GeoLocation((float)$lat, (float)$lon, 0);
            $datetime = new DateTimeImmutable($date->format('Y-m-d H:i:s'), new DateTimeZone('Asia/Kolkata'));

            $panchangEngine = app(Panchang::class);
            
            if (!method_exists($panchangEngine, 'kundali')) {
                $this->sendMessage($chatId, "❌ Kundli feature is not available in the current Panchang engine version.");
                return;
            }

            $kundali = $panchangEngine->kundali($datetime, $location);

            $ascendant = $kundali->ascendant->rashi->name ?? 'Unknown';
            
            // Find Moon sign
            $moonSign = 'Unknown';
            $placements = [];
            foreach ($kundali->placements as $pl) {
                if ($pl->planet->name === 'Moon') {
                    $moonSign = $pl->rashi->name ?? 'Unknown';
                }
                $placements[] = "• " . str_pad($pl->planet->name, 8) . ": " . ($pl->rashi->name ?? '-');
            }

            $locationName = $this->getLocationName((float)$lat, (float)$lon);

            $response = "🪷 *Kundli Summary*\n"
                      . "📅 Date: {$date->format('d M Y H:i')}\n"
                      . "📍 Location: `$locationName`\n\n"
                      . "🌅 *Ascendant (Lagna):* $ascendant\n"
                      . "🌙 *Moon Sign (Rashi):* $moonSign\n\n"
                      . "*Planetary Positions:*\n"
                      . "`" . implode("\n", $placements) . "`";

            $this->sendMessage($chatId, $response, 'Markdown');

        } catch (\Throwable $e) {
            Log::error("Telegram /kundli error: " . $e->getMessage());
            $this->sendMessage($chatId, "❌ Error calculating Kundli. Ensure format is `/kundli YYYY-MM-DD HH:MM lat,lon`");
        }
    }

    private function handleFestival(int|string $chatId, array $args): void
    {
        try {
            // Parse arguments
            $dateStr = 'today';
            $lat = 28.6139; // Default New Delhi
            $lon = 77.2090;

            if (count($args) > 0) {
                if (str_contains($args[0], ',')) {
                    // First arg is location
                    [$lat, $lon] = explode(',', $args[0]);
                } else {
                    // First arg is date
                    $dateStr = $args[0];
                    if (isset($args[1])) {
                        [$lat, $lon] = explode(',', $args[1]);
                    }
                }
            }

            $date = $this->parseDate($dateStr);
            $location = new GeoLocation((float)$lat, (float)$lon, 0);
            $datetime = new DateTimeImmutable($date->format('Y-m-d H:i:s'), new DateTimeZone('Asia/Kolkata'));

            $panchangEngine = app(Panchang::class);
            
            if (!method_exists($panchangEngine, 'festivals')) {
                $this->sendMessage($chatId, "❌ Festival feature is not available in the current Panchang engine version.");
                return;
            }

            $festivalEngine = $panchangEngine->festivals();
            $start = new \DateTimeImmutable($date->format('Y-m-d'), new \DateTimeZone('Asia/Kolkata'));

            $festivalsList = [];
            
            // Check next 30 days for festivals
            for ($i = 0; $i < 30; $i++) {
                $d = $start->modify("+$i day");
                $festivals = $festivalEngine->getFestivalsForDate($d, $location);
                
                if (!empty($festivals)) {
                    $names = is_array($festivals) ? implode(', ', $festivals) : $festivals;
                    $festivalsList[] = "• {$d->format('d M')}: $names";
                }
            }

            $locationName = $this->getLocationName((float)$lat, (float)$lon);

            if (empty($festivalsList)) {
                $response = "🪔 *Upcoming Festivals*\n"
                          . "📍 Location: `$locationName`\n\n"
                          . "No major festivals found in the next 30 days starting from {$start->format('d M Y')}.";
            } else {
                $response = "🪔 *Upcoming Festivals (Next 30 Days)*\n"
                          . "📍 Location: `$locationName`\n"
                          . "🗓️ Starting from: {$start->format('d M Y')}\n\n"
                          . implode("\n", $festivalsList);
            }

            $this->sendMessage($chatId, $response, 'Markdown');

        } catch (\Throwable $e) {
            Log::error("Telegram /festival error: " . $e->getMessage());
            $this->sendMessage($chatId, "❌ Error calculating festivals. Ensure format is `/festival YYYY-MM-DD lat,lon`");
        }
    }

    private function handleMuhurta(int|string $chatId, array $args): void
    {
        try {
            $dateStr = 'today';
            $lat = 28.6139;
            $lon = 77.2090;

            if (count($args) > 0) {
                if (str_contains($args[0], ',')) {
                    [$lat, $lon] = explode(',', $args[0]);
                } else {
                    $dateStr = $args[0];
                    if (isset($args[1])) {
                        [$lat, $lon] = explode(',', $args[1]);
                    }
                }
            }

            $date = $this->parseDate($dateStr);
            $location = new GeoLocation((float)$lat, (float)$lon, 0);
            $datetime = new DateTimeImmutable($date->format('Y-m-d H:i:s'), new DateTimeZone('Asia/Kolkata'));
            $locationName = $this->getLocationName((float)$lat, (float)$lon);

            $panchangEngine = app(Panchang::class);
            $day = $panchangEngine->day($datetime, $location);
            
            if (!$day->solarEvents->sunrise || !$day->solarEvents->sunset) {
                $this->sendMessage($chatId, "❌ Could not determine sunrise/sunset for this location.");
                return;
            }

            $muhurtaCalc = $panchangEngine->muhurta();
            $classical = new \Vittix\Panchang\Muhurta\ClassicalMuhurta($muhurtaCalc);
            $weekdayIndex = (int) $date->format('w');
            
            $abhijit = $classical->getAbhijit($day->solarEvents->sunrise, $day->solarEvents->sunset);
            $rahu = $classical->getRahuKaal($day->solarEvents->sunrise, $day->solarEvents->sunset, $weekdayIndex);
            $yama = $classical->getYamaganda($day->solarEvents->sunrise, $day->solarEvents->sunset, $weekdayIndex);
            $gulika = $classical->getGulika($day->solarEvents->sunrise, $day->solarEvents->sunset, $weekdayIndex);

            $fmt = fn($w) => "{$w->start->format('H:i')} - {$w->end->format('H:i')}";

            $response = "⏳ *Muhurta Timings for {$date->format('d M Y')}*
"
                      . "📍 Location: `$locationName`

"
                      . "✅ *Abhijit:* " . $fmt($abhijit) . "
"
                      . "❌ *Rahu Kaal:* " . $fmt($rahu) . "
"
                      . "⚠️ *Yamaganda:* " . $fmt($yama) . "
"
                      . "⚠️ *Gulika Kaal:* " . $fmt($gulika);

            $this->sendMessage($chatId, $response, 'Markdown');
        } catch (\Throwable $e) {
            Log::error("Telegram /muhurta error: " . $e->getMessage());
            $this->sendMessage($chatId, "❌ Error calculating Muhurta. Format: `/muhurta YYYY-MM-DD lat,lon`");
        }
    }

    private function handleAscendant(int|string $chatId, array $args): void
    {
        try {
            $timeStr = 'now';
            $lat = 28.6139;
            $lon = 77.2090;

            if (count($args) > 0) {
                if (str_contains($args[0], ',')) {
                    [$lat, $lon] = explode(',', $args[0]);
                } else {
                    $timeStr = implode(' ', $args);
                    // Extremely basic check for lat/lon at end
                    $last = end($args);
                    if (str_contains($last, ',')) {
                        [$lat, $lon] = explode(',', $last);
                        $timeStr = trim(str_replace($last, '', $timeStr));
                    }
                }
            }

            if ($timeStr === 'now' || $timeStr === '') $timeStr = 'now';
            $date = \Carbon\Carbon::parse($timeStr)->timezone('Asia/Kolkata');
            $location = new GeoLocation((float)$lat, (float)$lon, 0);
            $datetime = new DateTimeImmutable($date->format('Y-m-d H:i:s'), new DateTimeZone('Asia/Kolkata'));
            $locationName = $this->getLocationName((float)$lat, (float)$lon);

            $panchangEngine = app(Panchang::class);
            $ascendant = $panchangEngine->ascendant($datetime, $location);

            $response = "🌅 *Ascendant (Lagna)*
"
                      . "⏰ Time: {$date->format('d M Y H:i')}
"
                      . "📍 Location: `$locationName`

"
                      . "♈ *Sign:* {$ascendant->rashi->nameEnglish()}
"
                      . "📐 *Longitude:* " . round($ascendant->longitude, 2) . "°";

            $this->sendMessage($chatId, $response, 'Markdown');
        } catch (\Throwable $e) {
            Log::error("Telegram /ascendant error: " . $e->getMessage());
            $this->sendMessage($chatId, "❌ Error calculating Ascendant. Format: `/ascendant YYYY-MM-DD HH:MM lat,lon`");
        }
    }

    private function handleYogas(int|string $chatId, array $args): void
    {
        try {
            $timeStr = 'now';
            $lat = 28.6139;
            $lon = 77.2090;

            if (count($args) > 0) {
                if (str_contains($args[0], ',')) {
                    [$lat, $lon] = explode(',', $args[0]);
                } else {
                    $timeStr = implode(' ', $args);
                    $last = end($args);
                    if (str_contains($last, ',')) {
                        [$lat, $lon] = explode(',', $last);
                        $timeStr = trim(str_replace($last, '', $timeStr));
                    }
                }
            }

            if ($timeStr === 'now' || $timeStr === '') $timeStr = 'now';
            $date = \Carbon\Carbon::parse($timeStr)->timezone('Asia/Kolkata');
            $location = new GeoLocation((float)$lat, (float)$lon, 0);
            $datetime = new DateTimeImmutable($date->format('Y-m-d H:i:s'), new DateTimeZone('Asia/Kolkata'));
            $locationName = $this->getLocationName((float)$lat, (float)$lon);

            $panchangEngine = app(Panchang::class);
            $yogas = $panchangEngine->yogas($datetime, $location);

            $names = array_map(fn($y) => "• " . $y->name, $yogas);
            $yogaList = empty($names) ? "No major classical Yogas found." : implode("
", $names);

            $response = "✨ *Astrological Yogas*
"
                      . "⏰ Time: {$date->format('d M Y H:i')}
"
                      . "📍 Location: `$locationName`

"
                      . $yogaList;

            $this->sendMessage($chatId, $response, 'Markdown');
        } catch (\Throwable $e) {
            Log::error("Telegram /yogas error: " . $e->getMessage());
            $this->sendMessage($chatId, "❌ Error detecting Yogas. Format: `/yogas YYYY-MM-DD HH:MM lat,lon`");
        }
    }

    private function handleChoghadiya(int|string $chatId, array $args): void
    {
        try {
            $dateStr = 'today';
            $lat = 28.6139;
            $lon = 77.2090;

            if (count($args) > 0) {
                if (str_contains($args[0], ',')) {
                    [$lat, $lon] = explode(',', $args[0]);
                } else {
                    $dateStr = $args[0];
                    if (isset($args[1])) {
                        [$lat, $lon] = explode(',', $args[1]);
                    }
                }
            }

            $date = $this->parseDate($dateStr);
            $location = new GeoLocation((float)$lat, (float)$lon, 0);
            $datetime = new DateTimeImmutable($date->format('Y-m-d H:i:s'), new DateTimeZone('Asia/Kolkata'));
            $locationName = $this->getLocationName((float)$lat, (float)$lon);

            $panchangEngine = app(Panchang::class);
            $day = $panchangEngine->day($datetime, $location);
            
            if (!$day->solarEvents->sunrise || !$day->solarEvents->sunset) {
                $this->sendMessage($chatId, "❌ Could not determine sunrise/sunset for this location.");
                return;
            }
            
            // Note: getNightChoghadiya requires next sunrise. 
            // We get tomorrow's panchang for next sunrise.
            $tomorrow = $date->copy()->addDay();
            $tomorrowDatetime = new DateTimeImmutable($tomorrow->format('Y-m-d H:i:s'), new DateTimeZone('Asia/Kolkata'));
            $tomorrowDay = $panchangEngine->day($tomorrowDatetime, $location);

            $muhurtaCalc = $panchangEngine->muhurta();
            $weekdayIndex = (int) $date->format('w');
            
            $dayChog = $muhurtaCalc->getDayChoghadiya($day->solarEvents->sunrise, $day->solarEvents->sunset, $weekdayIndex);
            $nightChog = $muhurtaCalc->getNightChoghadiya($day->solarEvents->sunset, $tomorrowDay->solarEvents->sunrise, $weekdayIndex);

            $fmt = fn($w) => str_pad($w->name, 8) . " {$w->start->format('H:i')} - {$w->end->format('H:i')}";
            
            $dayList = array_map($fmt, $dayChog);
            $nightList = array_map($fmt, $nightChog);

            $response = "🕒 *Choghadiya Timings for {$date->format('d M Y')}*
"
                      . "📍 Location: `$locationName`

"
                      . "☀️ *Day Choghadiya:*
`"
                      . implode("
", $dayList) . "`

"
                      . "🌙 *Night Choghadiya:*
`"
                      . implode("
", $nightList) . "`";

            $this->sendMessage($chatId, $response, 'Markdown');
        } catch (\Throwable $e) {
            Log::error("Telegram /choghadiya error: " . $e->getMessage());
            $this->sendMessage($chatId, "❌ Error calculating Choghadiya. Format: `/choghadiya YYYY-MM-DD lat,lon`");
        }
    }

    private function handleRashi(int|string $chatId, array $args): void
    {
        try {
            $timeStr = 'now';
            
            if (count($args) > 0) {
                $timeStr = implode(' ', $args);
            }

            if ($timeStr === 'now' || $timeStr === '') $timeStr = 'now';
            $date = \Carbon\Carbon::parse($timeStr)->timezone('Asia/Kolkata');
            $datetime = new DateTimeImmutable($date->format('Y-m-d H:i:s'), new DateTimeZone('Asia/Kolkata'));

            $panchangEngine = app(Panchang::class);
            $rashi = $panchangEngine->janmarashi($datetime);

            $response = "🌙 *Moon Sign (Janma Rashi)*
"
                      . "⏰ Time: {$date->format('d M Y H:i')}

"
                      . "♋ *Rashi:* {$rashi->nameEnglish()}
";

            $this->sendMessage($chatId, $response, 'Markdown');
        } catch (\Throwable $e) {
            Log::error("Telegram /rashi error: " . $e->getMessage());
            $this->sendMessage($chatId, "❌ Error calculating Rashi. Format: `/rashi YYYY-MM-DD HH:MM`");
        }
    }

    private function handleDasha(int|string $chatId, array $args): void
    {
        try {
            $timeStr = 'now';
            $lat = 28.6139;
            $lon = 77.2090;

            if (count($args) > 0) {
                if (str_contains($args[0], ',')) {
                    [$lat, $lon] = explode(',', $args[0]);
                } else {
                    $timeStr = implode(' ', $args);
                    $last = end($args);
                    if (str_contains($last, ',')) {
                        [$lat, $lon] = explode(',', $last);
                        $timeStr = trim(str_replace($last, '', $timeStr));
                    }
                }
            }

            if ($timeStr === 'now' || $timeStr === '') $timeStr = 'now';
            $date = \Carbon\Carbon::parse($timeStr)->timezone('Asia/Kolkata');
            $datetime = new DateTimeImmutable($date->format('Y-m-d H:i:s'), new DateTimeZone('Asia/Kolkata'));
            
            // Note: Dasha uses Moon's position so location isn't strictly needed for the dasha calculation itself
            // but we parse it anyway for consistency.
            $location = new GeoLocation((float)$lat, (float)$lon, 0);

            $panchangEngine = app(Panchang::class);
            $dashas = $panchangEngine->vimshottariDasha($datetime);

            $dashaList = [];
            foreach ($dashas as $dasha) {
                $dashaList[] = "• *" . str_pad($dasha->planet->name, 7) . "*: until {$dasha->end->format('M Y')}";
            }

            $response = "⏳ *Vimshottari Mahadasha*
"
                      . "⏰ Time: {$date->format('d M Y H:i')}

"
                      . implode("
", $dashaList);

            $this->sendMessage($chatId, $response, 'Markdown');
        } catch (\Throwable $e) {
            Log::error("Telegram /dasha error: " . $e->getMessage());
            $this->sendMessage($chatId, "❌ Error calculating Dasha. Format: `/dasha YYYY-MM-DD HH:MM`");
        }
    }

    private function handleShadbala(int|string $chatId, array $args): void
    {
        try {
            $timeStr = 'now';
            $lat = 28.6139;
            $lon = 77.2090;

            if (count($args) > 0) {
                if (str_contains($args[0], ',')) {
                    [$lat, $lon] = explode(',', $args[0]);
                } else {
                    $timeStr = implode(' ', $args);
                    $last = end($args);
                    if (str_contains($last, ',')) {
                        [$lat, $lon] = explode(',', $last);
                        $timeStr = trim(str_replace($last, '', $timeStr));
                    }
                }
            }

            if ($timeStr === 'now' || $timeStr === '') $timeStr = 'now';
            $date = \Carbon\Carbon::parse($timeStr)->timezone('Asia/Kolkata');
            $location = new GeoLocation((float)$lat, (float)$lon, 0);
            $datetime = new DateTimeImmutable($date->format('Y-m-d H:i:s'), new DateTimeZone('Asia/Kolkata'));
            $locationName = $this->getLocationName((float)$lat, (float)$lon);

            $panchangEngine = app(Panchang::class);
            $shadbala = $panchangEngine->shadbala($datetime, $location);
            $totals = $shadbala->getTotalBala();
            
            arsort($totals); // Sort by highest strength

            $list = [];
            foreach ($totals as $planet => $rupas) {
                // Determine strength level
                $emoji = $rupas > 7 ? "🔥" : ($rupas > 5 ? "⭐" : "⚠️");
                $list[] = "$emoji *" . str_pad($planet, 7) . "*: " . round($rupas, 2) . " Rupas";
            }

            $response = "💪 *Planetary Strength (Shadbala)*
"
                      . "⏰ Time: {$date->format('d M Y H:i')}
"
                      . "📍 Location: `$locationName`

"
                      . implode("
", $list);

            $this->sendMessage($chatId, $response, 'Markdown');
        } catch (\Throwable $e) {
            Log::error("Telegram /shadbala error: " . $e->getMessage());
            $this->sendMessage($chatId, "❌ Error calculating Shadbala. Format: `/shadbala YYYY-MM-DD HH:MM lat,lon`");
        }
    }

    private function handleVarga(int|string $chatId, array $args): void
    {
        try {
            $vargaType = 'D9';
            $timeStr = 'now';
            $lat = 28.6139;
            $lon = 77.2090;

            if (count($args) > 0) {
                // If first arg is something like D9 or D10
                if (preg_match('/^D\d+$/i', $args[0])) {
                    $vargaType = strtoupper(array_shift($args));
                }
                
                if (count($args) > 0) {
                    if (str_contains($args[0], ',')) {
                        [$lat, $lon] = explode(',', $args[0]);
                    } else {
                        $timeStr = implode(' ', $args);
                        $last = end($args);
                        if (str_contains($last, ',')) {
                            [$lat, $lon] = explode(',', $last);
                            $timeStr = trim(str_replace($last, '', $timeStr));
                        }
                    }
                }
            }

            if ($timeStr === 'now' || $timeStr === '') $timeStr = 'now';
            $date = \Carbon\Carbon::parse($timeStr)->timezone('Asia/Kolkata');
            $location = new GeoLocation((float)$lat, (float)$lon, 0);
            $datetime = new DateTimeImmutable($date->format('Y-m-d H:i:s'), new DateTimeZone('Asia/Kolkata'));
            $locationName = $this->getLocationName((float)$lat, (float)$lon);

            // Validate Varga
            $vargaEnum = Varga::tryFrom($vargaType);
            if (!$vargaEnum) {
                $this->sendMessage($chatId, "❌ Invalid Varga type. Try D9, D10, etc.");
                return;
            }

            $panchangEngine = app(Panchang::class);
            $kundali = $panchangEngine->varga($datetime, $location, $vargaEnum);

            $ascendant = $kundali->ascendant->rashi->name ?? 'Unknown';
            
            $placements = [];
            foreach ($kundali->placements as $pl) {
                $placements[] = "• " . str_pad($pl->planet->name, 8) . ": " . ($pl->rashi->name ?? '-');
            }

            $response = "🪷 *{$vargaEnum->nameEnglish()}*
"
                      . "📅 Date: {$date->format('d M Y H:i')}
"
                      . "📍 Location: `$locationName`

"
                      . "🌅 *Ascendant:* $ascendant

"
                      . "*Planetary Positions:*
"
                      . "`" . implode("
", $placements) . "`";

            $this->sendMessage($chatId, $response, 'Markdown');
        } catch (\Throwable $e) {
            Log::error("Telegram /varga error: " . $e->getMessage());
            $this->sendMessage($chatId, "❌ Error calculating Varga. Format: `/varga D9 YYYY-MM-DD HH:MM lat,lon`");
        }
    }

    private function handleCalendar(int|string $chatId, array $args): void
    {
        try {
            $dateStr = 'today';
            
            if (count($args) > 0) {
                if (!str_contains($args[0], ',')) {
                    $dateStr = $args[0];
                }
            }

            $date = $this->parseDate($dateStr);
            $datetime = new DateTimeImmutable($date->format('Y-m-d H:i:s'), new DateTimeZone('Asia/Kolkata'));

            $panchangEngine = app(Panchang::class);
            $cal = $panchangEngine->calendar($datetime);

            $amanta = $cal->amantaMonth->nameEnglish();
            if ($cal->isAmantaAdhika) $amanta = "Adhika $amanta";
            
            $purnimanta = $cal->purnimantaMonth->nameEnglish();
            if ($cal->isPurnimantaAdhika) $purnimanta = "Adhika $purnimanta";

            $response = "🗓️ *Hindu Calendar*
"
                      . "📅 Date: {$date->format('d M Y')}

"
                      . "🔹 *Vikram Samvat:* {$cal->vikramSamvat}
"
                      . "🔹 *Shaka Samvat:* {$cal->shakaSamvat}

"
                      . "🌙 *Amanta Month:* $amanta
"
                      . "🌕 *Purnimanta Month:* $purnimanta
"
                      . "🌗 *Paksha (Phase):* {$cal->paksha->nameEnglish()}
"
                      . "🍃 *Ritu (Season):* {$cal->ritu->nameEnglish()}";

            $this->sendMessage($chatId, $response, 'Markdown');
        } catch (\Throwable $e) {
            Log::error("Telegram /calendar error: " . $e->getMessage());
            $this->sendMessage($chatId, "❌ Error getting calendar. Format: `/calendar YYYY-MM-DD`");
        }
    }

    private function handleElectional(int|string $chatId, array $args): void
    {
        try {
            $timeStr = 'now';
            $lat = 28.6139;
            $lon = 77.2090;

            if (count($args) > 0) {
                if (str_contains($args[0], ',')) {
                    [$lat, $lon] = explode(',', $args[0]);
                } else {
                    $timeStr = implode(' ', $args);
                    $last = end($args);
                    if (str_contains($last, ',')) {
                        [$lat, $lon] = explode(',', $last);
                        $timeStr = trim(str_replace($last, '', $timeStr));
                    }
                }
            }

            if ($timeStr === 'now' || $timeStr === '') $timeStr = 'now';
            $date = \Carbon\Carbon::parse($timeStr)->timezone('Asia/Kolkata');
            $location = new GeoLocation((float)$lat, (float)$lon, 0);
            $datetime = new DateTimeImmutable($date->format('Y-m-d H:i:s'), new DateTimeZone('Asia/Kolkata'));
            $locationName = $this->getLocationName((float)$lat, (float)$lon);

            $panchangEngine = app(Panchang::class);
            $momentObj = $panchangEngine->moment($datetime, $location);
            $eval = $panchangEngine->electional()->evaluate($momentObj);

            $score = $eval->score();
            $rating = $eval->rating(); // Auspicious, Inauspicious, Neutral, Highly Auspicious

            $emoji = match(true) {
                str_contains(strtolower($rating), 'highly') => "✨",
                str_contains(strtolower($rating), 'auspicious') => "✅",
                str_contains(strtolower($rating), 'inauspicious') => "❌",
                default => "➖",
            };

            $response = "🎯 *Activity Rating (Electional)*
"
                      . "⏰ Time: {$date->format('d M Y H:i')}
"
                      . "📍 Location: `$locationName`

"
                      . "{$emoji} *Rating:* $rating
"
                      . "📊 *Score:* {$score}/100

"
                      . "_Score is based on Tithi, Nakshatra, Yoga, Karana, and Weekday compatibility._";

            $this->sendMessage($chatId, $response, 'Markdown');
        } catch (\Throwable $e) {
            Log::error("Telegram /electional error: " . $e->getMessage());
            $this->sendMessage($chatId, "❌ Error evaluating time. Format: `/electional YYYY-MM-DD HH:MM lat,lon`");
        }
    }

    private function sendMessage(int|string $chatId, string $text, string $parseMode = ''): void
    {
        if (empty($this->token)) {
            Log::warning("Telegram bot token not configured. Message not sent: $text");
            return;
        }

        $payload = [
            'chat_id' => $chatId,
            'text' => $text,
        ];

        if ($parseMode) {
            $payload['parse_mode'] = $parseMode;
        }

        $response = Http::post("{$this->apiUrl}/sendMessage", $payload);

        if (!$response->successful()) {
            Log::error("Telegram API Error: " . $response->body());
        }
    }

    /**
     * Reverse geocode coordinates to a human-readable location name (e.g. "Mumbai, Maharashtra").
     */
    private function getLocationName(float $lat, float $lon): string
    {
        $cacheKey = "geocode_v1_{$lat}_{$lon}";

        return Cache::remember($cacheKey, now()->addDays(30), function () use ($lat, $lon) {
            try {
                $response = Http::timeout(5)->withHeaders([
                    'User-Agent' => 'Hindutithi-Telegram-Bot/1.0 (contact@hindutithi.in)',
                    'Accept-Language' => 'en',
                ])->get('https://nominatim.openstreetmap.org/reverse', [
                    'format' => 'jsonv2',
                    'lat' => $lat,
                    'lon' => $lon,
                    'zoom' => 10, // City level
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    
                    if (!empty($data['name'])) {
                        $parts = [];
                        $parts[] = $data['name'];
                        if (isset($data['address']['state']) && $data['address']['state'] !== $data['name']) {
                            $parts[] = $data['address']['state'];
                        } elseif (isset($data['address']['country']) && $data['address']['country'] !== $data['name']) {
                            $parts[] = $data['address']['country'];
                        }
                        
                        return implode(', ', $parts) . " (" . round($lat, 2) . ", " . round($lon, 2) . ")";
                    }
                }
            } catch (\Exception $e) {
                Log::warning("Geocoding failed for $lat, $lon: " . $e->getMessage());
            }

            // Fallback
            return round($lat, 4) . ', ' . round($lon, 4);
        });
    }

    /**
     * Parse date string flexibly supporting YYYY-MM-DD and DD-MM-YYYY formats.
     */
    private function parseDate(string $dateStr): \Carbon\Carbon
    {
        if (strtolower($dateStr) === 'today') {
            return \Carbon\Carbon::today('Asia/Kolkata');
        }

        // Standardize slashes to dashes so PHP correctly treats DD-MM-YYYY (European) 
        // rather than trying to parse MM/DD/YYYY (American) and failing.
        $standardized = str_replace('/', '-', $dateStr);

        return \Carbon\Carbon::parse($standardized)->timezone('Asia/Kolkata');
    }
}
