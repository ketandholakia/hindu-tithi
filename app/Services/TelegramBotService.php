<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Vittix\Panchang\Engine\PanchangEngine;
use Vittix\Panchang\ValueObject\Location;
use Vittix\Panchang\ValueObject\DateTime;

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
        if (!isset($update['message'])) {
            return;
        }

        $message = $update['message'];
        $chatId = $message['chat']['id'];
        
        $text = $message['text'] ?? '';
        
        if (empty($text)) {
            // Handle location if sent as attachment instead of text
            if (isset($message['location'])) {
                $lat = $message['location']['latitude'];
                $lon = $message['location']['longitude'];
                $this->sendMessage($chatId, "📍 Received location: `$lat, $lon`\nYou can use these coordinates with `/panchang` or `/kundli`.\nExample: `/panchang today $lat,$lon`", 'Markdown');
            }
            return;
        }

        // Parse command and arguments
        $parts = explode(' ', trim($text));
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

            $date = \Carbon\Carbon::parse($dateStr)->timezone('Asia/Kolkata');
            $location = new Location((float)$lat, (float)$lon);
            $datetime = new DateTime($date->format('Y-m-d H:i:s'), 'Asia/Kolkata');

            $engine = new PanchangEngine($datetime, $location);
            $panchang = $engine->calculate();

            $tithi = $panchang->tithi->name ?? 'Unknown';
            $nakshatra = $panchang->nakshatra->name ?? 'Unknown';
            $yoga = $panchang->yoga->name ?? 'Unknown';
            $karana = $panchang->karana->name ?? 'Unknown';

            $response = "🕉 *Panchang for {$date->format('d M Y')}*\n"
                      . "📍 Location: `$lat, $lon`\n\n"
                      . "🗓 *Tithi:* $tithi\n"
                      . "🌟 *Nakshatra:* $nakshatra\n"
                      . "🧘 *Yoga:* $yoga\n"
                      . "⚡ *Karana:* $karana\n";

            $this->sendMessage($chatId, $response, 'Markdown');

        } catch (\Exception $e) {
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

            $date = \Carbon\Carbon::parse($dateStr)->timezone('Asia/Kolkata');
            $location = new Location((float)$lat, (float)$lon);
            $datetime = new DateTime($date->format('Y-m-d H:i:s'), 'Asia/Kolkata');

            $engine = new PanchangEngine($datetime, $location);
            
            if (!method_exists($engine, 'calculateKundali')) {
                $this->sendMessage($chatId, "❌ Kundli feature is not available in the current Panchang engine version.");
                return;
            }

            $kundali = $engine->calculateKundali();

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

            $response = "🪷 *Kundli Summary*\n"
                      . "📅 Date: {$date->format('d M Y H:i')}\n"
                      . "📍 Location: `$lat, $lon`\n\n"
                      . "🌅 *Ascendant (Lagna):* $ascendant\n"
                      . "🌙 *Moon Sign (Rashi):* $moonSign\n\n"
                      . "*Planetary Positions:*\n"
                      . "`" . implode("\n", $placements) . "`";

            $this->sendMessage($chatId, $response, 'Markdown');

        } catch (\Exception $e) {
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

            $date = \Carbon\Carbon::parse($dateStr)->timezone('Asia/Kolkata');
            $location = new Location((float)$lat, (float)$lon);
            $datetime = new DateTime($date->format('Y-m-d H:i:s'), 'Asia/Kolkata');

            $engine = new PanchangEngine($datetime, $location);
            
            if (!method_exists($engine, 'festivals')) {
                $this->sendMessage($chatId, "❌ Festival feature is not available in the current Panchang engine version.");
                return;
            }

            $festivalEngine = $engine->festivals();
            $start = new \DateTimeImmutable($date->format('Y-m-d'), new \DateTimeZone('Asia/Kolkata'));
            $loc = new \Vittix\Panchang\ValueObject\GeoLocation((float)$lat, (float)$lon, 0);

            $festivalsList = [];
            
            // Check next 30 days for festivals
            for ($i = 0; $i < 30; $i++) {
                $d = $start->modify("+$i day");
                $festivals = $festivalEngine->getFestivalsForDate($d, $loc);
                
                if (!empty($festivals)) {
                    $names = is_array($festivals) ? implode(', ', $festivals) : $festivals;
                    $festivalsList[] = "• {$d->format('d M')}: $names";
                }
            }

            if (empty($festivalsList)) {
                $response = "🪔 *Upcoming Festivals*\n"
                          . "📍 Location: `$lat, $lon`\n\n"
                          . "No major festivals found in the next 30 days starting from {$start->format('d M Y')}.";
            } else {
                $response = "🪔 *Upcoming Festivals (Next 30 Days)*\n"
                          . "📍 Location: `$lat, $lon`\n"
                          . "🗓️ Starting from: {$start->format('d M Y')}\n\n"
                          . implode("\n", $festivalsList);
            }

            $this->sendMessage($chatId, $response, 'Markdown');

        } catch (\Exception $e) {
            Log::error("Telegram /festival error: " . $e->getMessage());
            $this->sendMessage($chatId, "❌ Error calculating festivals. Ensure format is `/festival YYYY-MM-DD lat,lon`");
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
}
