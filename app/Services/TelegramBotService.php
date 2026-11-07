<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use App\Models\TelegramSubscription;
use App\Models\TelegramReminder;
use App\Models\TelegramBirthProfile;
use Vittix\Panchang\Panchang;
use Vittix\Panchang\ValueObject\GeoLocation;
use Vittix\Panchang\Enum\Varga;
use Vittix\Panchang\Panchanga\PanchangService;
use DateTimeImmutable;
use DateTimeZone;

class TelegramBotService
{
    private const DEFAULT_TIMEZONE = 'Asia/Kolkata';
    private const REMINDER_TYPES = ['ekadashi', 'purnima', 'amavasya', 'festival'];

    private string $token;
    private string $apiUrl;
    private int|string|null $activeUserId = null;
    private bool $isPrivateChat = true;

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
        $chatId = $message['chat']['id'] ?? null;

        try {
            $this->dispatchUpdate($update);
        } catch (\Throwable $e) {
            Log::error('Unhandled Telegram update error: ' . $e->getMessage(), ['chat_id' => $chatId]);
            if ($chatId !== null) {
                $this->sendMessage($chatId, 'I could not complete that request. Check the command with /help and try again in a moment.');
            }
        }
    }

    private function dispatchUpdate(array $update): void
    {
        $message = $update['message'] ?? $update['edited_message'] ?? null;
        
        if (!$message) {
            return;
        }

        $chatId = $message['chat']['id'];
        $this->activeUserId = $message['from']['id'] ?? $chatId;
        $this->isPrivateChat = ($message['chat']['type'] ?? 'private') === 'private';
        
        $text = $message['text'] ?? '';
        
        // Handle native location shares
        if (isset($message['location'])) {
            $lat = $message['location']['latitude'];
            $lon = $message['location']['longitude'];

            $this->saveChatLocation($chatId, (float) $lat, (float) $lon);
            $this->syncSubscriptionPreferences($chatId, ['latitude' => (float) $lat, 'longitude' => (float) $lon]);
            $this->handlePanchang($chatId, ['today', "{$lat},{$lon}"]);
            return;
        }

        $buttonCommands = [
            'panchang today' => 'panchang',
            'muhurta today' => 'muhurta',
            'upcoming festivals' => 'festivals',
            'hindu calendar' => 'calendar',
            'my location' => 'location',
            'more commands' => 'help',
        ];
        $buttonCommand = strtolower(trim($text));
        if (isset($buttonCommands[$buttonCommand])) {
            $this->handleMenuAction($chatId, $buttonCommands[$buttonCommand]);
            return;
        }

        if (empty($text) || !str_starts_with($text, '/')) {
            return;
        }

        // Parse command and arguments safely ignoring multiple spaces
        $parts = preg_split('/\s+/', trim($text));
        $command = strtolower(explode('@', array_shift($parts), 2)[0]);

        switch ($command) {
            case '/start':
                $this->handleMenu($chatId);
                break;
            case '/help':
                $this->sendHelp($chatId);
                break;
            case '/menu':
                $this->handleMenu($chatId);
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
            case '/muhurta':
                $this->handleMuhurta($chatId, $parts);
                break;
            case '/ascendant':
            case '/lagna':
                $this->handleAscendant($chatId, $parts);
                break;
            case '/yogas':
                $this->handleYogas($chatId, $parts);
                break;
            case '/choghadiya':
                $this->handleChoghadiya($chatId, $parts);
                break;
            case '/rashi':
                $this->handleRashi($chatId, $parts);
                break;
            case '/dasha':
                $this->handleDasha($chatId, $parts);
                break;
            case '/shadbala':
                $this->handleShadbala($chatId, $parts);
                break;
            case '/varga':
                $this->handleVarga($chatId, $parts);
                break;
            case '/calendar':
                $this->handleCalendar($chatId, $parts);
                break;
            case '/electional':
                $this->handleElectional($chatId, $parts);
                break;
            case '/setlocation':
            case '/set_location':
                $this->handleSetLocation($chatId, $parts);
                break;
            case '/location':
                $this->handleShowLocation($chatId);
                break;
            case '/clear-location':
            case '/clearlocation':
                $this->handleClearLocation($chatId);
                break;
            case '/settimezone':
            case '/set_timezone':
                $this->handleSetTimezone($chatId, $parts);
                break;
            case '/timezone':
                $this->handleShowTimezone($chatId);
                break;
            case '/clear-timezone':
            case '/cleartimezone':
                $this->handleClearTimezone($chatId);
                break;
            case '/setlanguage':
            case '/set_language':
                $this->handleSetLanguage($chatId, $parts);
                break;
            case '/language':
                $this->handleShowLanguage($chatId);
                break;
            case '/clear-language':
            case '/clearlanguage':
                $this->handleClearLanguage($chatId);
                break;
            case '/subscribe':
                $this->handleSubscribe($chatId, $parts);
                break;
            case '/unsubscribe':
                $this->handleUnsubscribe($chatId);
                break;
            case '/settime':
                $this->handleSetSubscriptionTime($chatId, $parts);
                break;
            case '/subscription':
                $this->handleSubscriptionStatus($chatId);
                break;
            case '/remind':
                $this->handleAddReminder($chatId, $parts);
                break;
            case '/unremind':
                $this->handleRemoveReminder($chatId, $parts);
                break;
            case '/reminders':
                $this->handleListReminders($chatId);
                break;
            case '/setbirth':
                $this->handleSetBirthProfile($chatId, $parts);
                break;
            case '/birthprofile':
                $this->handleShowBirthProfile($chatId);
                break;
            case '/clearbirth':
                $this->handleClearBirthProfile($chatId);
                break;
            default:
                $this->sendMessage($chatId, "Unknown command. Send /help to see available commands.");
                break;
        }
    }

    private function sendHelp(int|string $chatId): void
    {
        $help = "🙏 *Welcome to Tithi Darshan Bot!*\n\n"
              . "Available commands:\n"
              . "👉 `/panchang [date] [lat,lon]`\n"
              . "Gets the daily Panchang.\n\n"
              . "👉 `/kundli [YYYY-MM-DD HH:MM] [lat,lon]`\n"
              . "Gets a basic Kundli summary.\n\n"
              . "👉 `/festival [date] [lat,lon]`\n"
              . "Gets upcoming festivals (next 30 days).\n\n"
              . "👉 `/muhurta [date] [lat,lon]`\n"
              . "Gets Muhurta timings.\n\n"
              . "👉 `/choghadiya [date] [lat,lon]`\n"
              . "Gets Choghadiya timings.\n\n"
              . "👉 `/ascendant [YYYY-MM-DD HH:MM] [lat,lon]`\n"
              . "Gets Ascendant (Lagna).\n\n"
              . "👉 `/yogas [YYYY-MM-DD HH:MM] [lat,lon]`\n"
              . "Gets Astrological Yogas.\n\n"
              . "👉 `/rashi [YYYY-MM-DD HH:MM]`\n"
              . "Gets Moon Sign (Janma Rashi).\n\n"
              . "👉 `/dasha [YYYY-MM-DD HH:MM]`\n"
              . "Gets Vimshottari Dasha.\n\n"
              . "👉 `/shadbala [YYYY-MM-DD HH:MM] [lat,lon]`\n"
              . "Gets Planetary Strength.\n\n"
              . "👉 `/varga [type] [YYYY-MM-DD HH:MM] [lat,lon]`\n"
              . "Gets Varga chart details (e.g. D9).\n\n"
              . "👉 `/calendar [date]`\n"
              . "Gets Hindu Calendar details.\n\n"
              . "👉 `/electional [YYYY-MM-DD HH:MM] [lat,lon]`\n"
              . "Evaluates moment's auspiciousness.\n\n"
              . "👉 `/setlocation lat,lon` — Save a default location for this chat.\n"
              . "👉 `/location` — Show the saved location.\n"
              . "👉 `/clear-location` — Remove the saved location.\n\n"
              . "👉 `/settimezone Area/City` — Set this chat's timezone (for example, `America/New_York`).\n"
              . "👉 `/timezone` — Show the current timezone.\n"
              . "👉 `/clear-timezone` — Reset to `Asia/Kolkata`.\n\n"
              . "👉 `/setlanguage code` — Set Panchang language (`en`, `hi`, `gu`, `mr`, `bn`, `ta`, `te`, `kn`, `ml`, `sa`).\n"
              . "👉 `/language` — Show the current language.\n"
              . "👉 `/clear-language` — Reset to English.\n\n"
              . "👉 `/subscribe [HH:MM]` — Get a daily Panchang (default 07:00).\n"
              . "👉 `/settime HH:MM` — Change the delivery time.\n"
              . "👉 `/subscription` — Check daily delivery settings.\n"
              . "👉 `/unsubscribe` — Stop daily Panchang messages.\n\n"
              . "👉 `/remind ekadashi|purnima|amavasya|festival [HH:MM]` — Add a calendar reminder.\n"
              . "👉 `/reminders` — List reminders. `/unremind type` — Remove one.\n\n"
              . "👉 `/setbirth YYYY-MM-DD HH:MM [lat,lon]` — Save birth details.\n"
              . "👉 `/birthprofile` — Review saved details. `/clearbirth` — Delete them.\n"
              . "Use `saved` with `/kundli`, `/rashi`, `/dasha`, `/ascendant`, `/yogas`, `/shadbala`, and `/varga`.\n\n"
              . "You can also send a location pin 📍 to save it and get today's Panchang!";

        $this->sendMessage($chatId, $help, 'Markdown');
    }

    private function handleMenu(int|string $chatId): void
    {
        $keyboard = [
            'keyboard' => [
                [['text' => 'Panchang Today'], ['text' => 'Muhurta Today']],
                [['text' => 'Upcoming Festivals'], ['text' => 'Hindu Calendar']],
                [['text' => 'My Location'], ['text' => 'More Commands']],
                [['text' => '📍 Share Location', 'request_location' => true]],
            ],
            'resize_keyboard' => true,
            'is_persistent' => true,
            'input_field_placeholder' => 'Choose a Panchang service',
        ];

        $this->sendMessage($chatId, 'Choose a service below, or type /help to see every command.', '', $keyboard);
    }

    private function handleMenuAction(int|string $chatId, string $action): void
    {
        switch ($action) {
            case 'panchang':
                $this->handlePanchang($chatId, ['today']);
                break;
            case 'muhurta':
                $this->handleMuhurta($chatId, ['today']);
                break;
            case 'festivals':
                $this->handleFestival($chatId, ['today']);
                break;
            case 'calendar':
                $this->handleCalendar($chatId, ['today']);
                break;
            case 'location':
                $this->handleShowLocation($chatId);
                break;
            case 'help':
                $this->sendHelp($chatId);
                break;
        }
    }

    public function sendDailyPanchang(int|string $chatId, float $lat, float $lon, string $timezone, string $language): bool
    {
        if (!in_array($timezone, DateTimeZone::listIdentifiers(DateTimeZone::ALL_WITH_BC), true)
            || !array_key_exists($language, PanchangTranslator::SUPPORTED)) {
            Log::warning('Skipping Telegram subscription with invalid preferences.', ['chat_id' => $chatId]);
            return false;
        }

        try {
            $this->saveChatLocation($chatId, $lat, $lon);
            Cache::forever($this->timezoneCacheKey($chatId), $timezone);
            Cache::forever($this->languageCacheKey($chatId), $language);
            return $this->handlePanchang($chatId, ['today', $lat . ',' . $lon]);
        } catch (\Throwable $e) {
            Log::error('Telegram daily Panchang delivery failed: ' . $e->getMessage(), ['chat_id' => $chatId]);
            return false;
        }
    }

    public function sendReminderMessage(int|string $chatId, string $type, string $detail, DateTimeImmutable $date): bool
    {
        return $this->sendMessage(
            $chatId,
            "🔔 " . ucfirst($type) . " reminder\n{$detail}\nDate: " . $date->format('D, d M Y T'),
        );
    }

    private function handlePanchang(int|string $chatId, array $args): bool
    {
        try {
            $timezone = $this->getChatTimezone($chatId);
            // Parse arguments
            $dateStr = 'today';
            [$lat, $lon] = $this->getChatLocation($chatId);

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

            $date = $this->parseDate($dateStr, $timezone);
            $location = $this->makeLocation($lat, $lon);
            $datetime = new DateTimeImmutable($date->format('Y-m-d H:i:s'), new DateTimeZone($timezone));

            $panchangEngine = app(Panchang::class);
            $panchang = $panchangEngine->day($datetime, $location);
            $daily = app(PanchangService::class)->getForDate($datetime, $location);

            $language = $this->getChatLanguage($chatId);
            $tithi = PanchangTranslator::translateWith($panchang->tithiAtSunrise, $language) ?? 'Unknown';
            $nakshatra = PanchangTranslator::translateWith($panchang->nakshatraAtSunrise, $language) ?? 'Unknown';
            $yoga = PanchangTranslator::translateWith($panchang->yogaAtSunrise, $language) ?? 'Unknown';
            $karana = PanchangTranslator::translateWith($panchang->karanaAtSunrise, $language) ?? 'Unknown';
            $fmt = fn ($value) => $value?->format('D H:i T') ?? 'Unavailable';
            $tithiName = PanchangTranslator::translateWith($daily->tithi, $language) ?? 'Tithi';
            $tithiEnd = $fmt($daily->tithiEndsAt);
            if ($daily->tithiSecond !== null) {
                $secondTithi = PanchangTranslator::translateWith($daily->tithiSecond, $language) ?? 'Next tithi';
                $tithiEnd .= "\n{$secondTithi} begins later that Panchang day";
                if ($daily->tithiSecondEndsAt !== null) {
                    $tithiEnd .= '; ends ' . $fmt($daily->tithiSecondEndsAt);
                }
            }
            
            $locationName = $this->getLocationName((float)$lat, (float)$lon);

            $response = "🕉 *Panchang for {$date->format('d M Y')}*\n"
                      . "📍 Location: `$locationName`\n\n"
                      . "🗓 *Tithi:* $tithi\n"
                      . "🌟 *Nakshatra:* $nakshatra\n"
                      . "🧘 *Yoga:* $yoga\n"
                      . "⚡ *Karana:* $karana\n\n"
                      . "☀️ *Sunrise / sunset:* " . $fmt($daily->solarEvents->sunrise) . ' / ' . $fmt($daily->solarEvents->sunset) . "\n"
                      . "🌙 *Moonrise / moonset:* " . $fmt($daily->lunarEvents->moonrise) . ' / ' . $fmt($daily->lunarEvents->moonset) . "\n"
                      . "⏳ *{$tithiName} ends:* {$tithiEnd}\n"
                      . "⭐ *Nakshatra ends:* " . $fmt($daily->nakshatraEndsAt);

            return $this->sendMessage($chatId, $response, 'Markdown');

        } catch (\Throwable $e) {
            Log::error("Telegram /panchang error: " . $e->getMessage());
            $this->sendMessage($chatId, "❌ Error calculating Panchang. Ensure format is `/panchang YYYY-MM-DD lat,lon`");
            return false;
        }
    }

    private function handleKundli(int|string $chatId, array $args): void
    {
        try {
            $timezone = $this->getChatTimezone($chatId);
            $profile = $this->requestedBirthProfile($chatId, $args);
            if ($profile === false) {
                $this->sendMessage($chatId, 'Saved birth profile unavailable here. Save and use it in a private chat with /setbirth.');
                return;
            }
            if ($profile === null && count($args) < 2) {
                $this->sendMessage($chatId, "⚠️ Usage: `/kundli YYYY-MM-DD HH:MM [lat,lon]`\nExample: `/kundli 1990-05-15 14:30 28.6,77.2`", 'Markdown');
                return;
            }

            if ($profile !== null) {
                $timezone = $profile->timezone;
                $dateStr = $profile->birth_date->format('Y-m-d') . ' ' . $profile->birth_time;
                [$lat, $lon] = [$profile->latitude, $profile->longitude];
            } else {
                $dateStr = $args[0] . ' ' . $args[1];
                [$lat, $lon] = $this->getChatLocation($chatId);
                if (isset($args[2])) {
                    [$lat, $lon] = explode(',', $args[2]);
                }
            }

            $date = $this->parseDate($dateStr, $timezone);
            $location = $this->makeLocation($lat, $lon);
            $datetime = new DateTimeImmutable($date->format('Y-m-d H:i:s'), new DateTimeZone($timezone));

            $panchangEngine = app(Panchang::class);
            
            if (!method_exists($panchangEngine, 'kundali')) {
                $this->sendMessage($chatId, "❌ Kundli feature is not available in the current Panchang engine version.");
                return;
            }

            $kundali = $panchangEngine->kundali($datetime, $location);

            $language = $this->getChatLanguage($chatId);
            $ascendant = PanchangTranslator::translateWith($kundali->ascendant->rashi, $language) ?? 'Unknown';
            
            // Find Moon sign
            $moonSign = 'Unknown';
            $placements = [];
            foreach ($kundali->placements as $pl) {
                if ($pl->planet->name === 'Moon') {
                    $moonSign = PanchangTranslator::translateWith($pl->rashi, $language) ?? 'Unknown';
                }
                $placements[] = "• " . str_pad($pl->planet->name, 8) . ": " . (PanchangTranslator::translateWith($pl->rashi, $language) ?? '-');
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
            $timezone = $this->getChatTimezone($chatId);
            // Parse arguments
            $dateStr = 'today';
            [$lat, $lon] = $this->getChatLocation($chatId);

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

            $date = $this->parseDate($dateStr, $timezone);
            $location = $this->makeLocation($lat, $lon);
            $datetime = new DateTimeImmutable($date->format('Y-m-d H:i:s'), new DateTimeZone($timezone));

            $panchangEngine = app(Panchang::class);
            
            if (!method_exists($panchangEngine, 'festivals')) {
                $this->sendMessage($chatId, "❌ Festival feature is not available in the current Panchang engine version.");
                return;
            }

            $festivalEngine = $panchangEngine->festivals();
            $start = new \DateTimeImmutable($date->format('Y-m-d'), new \DateTimeZone($timezone));

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
            $timezone = $this->getChatTimezone($chatId);
            $dateStr = 'today';
            [$lat, $lon] = $this->getChatLocation($chatId);

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

            $date = $this->parseDate($dateStr, $timezone);
            $location = $this->makeLocation($lat, $lon);
            $datetime = new DateTimeImmutable($date->format('Y-m-d H:i:s'), new DateTimeZone($timezone));
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
            $timezone = $this->getChatTimezone($chatId);
            $timeStr = 'now';
            [$lat, $lon] = $this->getChatLocation($chatId);
            $profile = $this->requestedBirthProfile($chatId, $args);
            if ($profile === false) {
                $this->sendMessage($chatId, 'Saved birth profile unavailable here. Save and use it in a private chat with /setbirth.');
                return;
            }

            if ($profile !== null) {
                $timezone = $profile->timezone;
                $timeStr = $profile->birth_date->format('Y-m-d') . ' ' . $profile->birth_time;
                [$lat, $lon] = [$profile->latitude, $profile->longitude];
            } elseif (count($args) > 0) {
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
            $date = \Carbon\Carbon::parse($timeStr, $timezone)->timezone($timezone);
            $location = $this->makeLocation($lat, $lon);
            $datetime = new DateTimeImmutable($date->format('Y-m-d H:i:s'), new DateTimeZone($timezone));
            $locationName = $this->getLocationName((float)$lat, (float)$lon);

            $panchangEngine = app(Panchang::class);
            $ascendant = $panchangEngine->ascendant($datetime, $location);
            $ascendantSign = PanchangTranslator::translateWith($ascendant->rashi, $this->getChatLanguage($chatId));

            $response = "🌅 *Ascendant (Lagna)*
"
                      . "⏰ Time: {$date->format('d M Y H:i')}
"
                      . "📍 Location: `$locationName`

"
                      . "♈ *Sign:* {$ascendantSign}
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
            $timezone = $this->getChatTimezone($chatId);
            $timeStr = 'now';
            [$lat, $lon] = $this->getChatLocation($chatId);
            $profile = $this->requestedBirthProfile($chatId, $args);
            if ($profile === false) {
                $this->sendMessage($chatId, 'Saved birth profile unavailable here. Save and use it in a private chat with /setbirth.');
                return;
            }

            if ($profile !== null) {
                $timezone = $profile->timezone;
                $timeStr = $profile->birth_date->format('Y-m-d') . ' ' . $profile->birth_time;
                [$lat, $lon] = [$profile->latitude, $profile->longitude];
            } elseif (count($args) > 0) {
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
            $date = \Carbon\Carbon::parse($timeStr, $timezone)->timezone($timezone);
            $location = $this->makeLocation($lat, $lon);
            $datetime = new DateTimeImmutable($date->format('Y-m-d H:i:s'), new DateTimeZone($timezone));
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
            $timezone = $this->getChatTimezone($chatId);
            $dateStr = 'today';
            [$lat, $lon] = $this->getChatLocation($chatId);

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

            $date = $this->parseDate($dateStr, $timezone);
            $location = $this->makeLocation($lat, $lon);
            $datetime = new DateTimeImmutable($date->format('Y-m-d H:i:s'), new DateTimeZone($timezone));
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
            $tomorrowDatetime = new DateTimeImmutable($tomorrow->format('Y-m-d H:i:s'), new DateTimeZone($timezone));
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
            $timezone = $this->getChatTimezone($chatId);
            $timeStr = 'now';
            $profile = $this->requestedBirthProfile($chatId, $args);
            if ($profile === false) {
                $this->sendMessage($chatId, 'Saved birth profile unavailable here. Save and use it in a private chat with /setbirth.');
                return;
            }

            if ($profile !== null) {
                $timezone = $profile->timezone;
                $timeStr = $profile->birth_date->format('Y-m-d') . ' ' . $profile->birth_time;
            } elseif (count($args) > 0) {
                $timeStr = implode(' ', $args);
            }

            if ($timeStr === 'now' || $timeStr === '') $timeStr = 'now';
            $date = \Carbon\Carbon::parse($timeStr, $timezone)->timezone($timezone);
            $datetime = new DateTimeImmutable($date->format('Y-m-d H:i:s'), new DateTimeZone($timezone));

            $panchangEngine = app(Panchang::class);
            $rashi = $panchangEngine->janmarashi($datetime);
            $rashiName = PanchangTranslator::translateWith($rashi, $this->getChatLanguage($chatId));

            $response = "🌙 *Moon Sign (Janma Rashi)*
"
                      . "⏰ Time: {$date->format('d M Y H:i')}

"
                      . "♋ *Rashi:* {$rashiName}
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
            $timezone = $this->getChatTimezone($chatId);
            $timeStr = 'now';
            [$lat, $lon] = $this->getChatLocation($chatId);
            $profile = $this->requestedBirthProfile($chatId, $args);
            if ($profile === false) {
                $this->sendMessage($chatId, 'Saved birth profile unavailable here. Save and use it in a private chat with /setbirth.');
                return;
            }

            if ($profile !== null) {
                $timezone = $profile->timezone;
                $timeStr = $profile->birth_date->format('Y-m-d') . ' ' . $profile->birth_time;
                [$lat, $lon] = [$profile->latitude, $profile->longitude];
            } elseif (count($args) > 0) {
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
            $date = \Carbon\Carbon::parse($timeStr, $timezone)->timezone($timezone);
            $datetime = new DateTimeImmutable($date->format('Y-m-d H:i:s'), new DateTimeZone($timezone));
            
            // Note: Dasha uses Moon's position so location isn't strictly needed for the dasha calculation itself
            // but we parse it anyway for consistency.
            $location = $this->makeLocation($lat, $lon);

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
            $timezone = $this->getChatTimezone($chatId);
            $timeStr = 'now';
            [$lat, $lon] = $this->getChatLocation($chatId);
            $profile = $this->requestedBirthProfile($chatId, $args);
            if ($profile === false) {
                $this->sendMessage($chatId, 'Saved birth profile unavailable here. Save and use it in a private chat with /setbirth.');
                return;
            }

            if ($profile !== null) {
                $timezone = $profile->timezone;
                $timeStr = $profile->birth_date->format('Y-m-d') . ' ' . $profile->birth_time;
                [$lat, $lon] = [$profile->latitude, $profile->longitude];
            } elseif (count($args) > 0) {
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
            $date = \Carbon\Carbon::parse($timeStr, $timezone)->timezone($timezone);
            $location = $this->makeLocation($lat, $lon);
            $datetime = new DateTimeImmutable($date->format('Y-m-d H:i:s'), new DateTimeZone($timezone));
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
            $timezone = $this->getChatTimezone($chatId);
            $vargaType = 'D9';
            $timeStr = 'now';
            [$lat, $lon] = $this->getChatLocation($chatId);

            if (count($args) > 0) {
                // If first arg is something like D9 or D10
                if (preg_match('/^D\d+$/i', $args[0])) {
                    $vargaType = strtoupper(array_shift($args));
                }

                $profile = $this->requestedBirthProfile($chatId, $args);
                if ($profile === false) {
                    $this->sendMessage($chatId, 'Saved birth profile unavailable here. Save and use it in a private chat with /setbirth.');
                    return;
                }

                if ($profile !== null) {
                    $timezone = $profile->timezone;
                    $timeStr = $profile->birth_date->format('Y-m-d') . ' ' . $profile->birth_time;
                    [$lat, $lon] = [$profile->latitude, $profile->longitude];
                } elseif (count($args) > 0) {
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
            $date = \Carbon\Carbon::parse($timeStr, $timezone)->timezone($timezone);
            $location = $this->makeLocation($lat, $lon);
            $datetime = new DateTimeImmutable($date->format('Y-m-d H:i:s'), new DateTimeZone($timezone));
            $locationName = $this->getLocationName((float)$lat, (float)$lon);

            // Validate Varga
            $vargaEnum = Varga::tryFrom($vargaType);
            if (!$vargaEnum) {
                $this->sendMessage($chatId, "❌ Invalid Varga type. Try D9, D10, etc.");
                return;
            }

            $panchangEngine = app(Panchang::class);
            $kundali = $panchangEngine->varga($datetime, $location, $vargaEnum);

            $language = $this->getChatLanguage($chatId);
            $ascendant = PanchangTranslator::translateWith($kundali->ascendant->rashi, $language) ?? 'Unknown';
            
            $placements = [];
            foreach ($kundali->placements as $pl) {
                $placements[] = "• " . str_pad($pl->planet->name, 8) . ": " . (PanchangTranslator::translateWith($pl->rashi, $language) ?? '-');
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
            $timezone = $this->getChatTimezone($chatId);
            $dateStr = 'today';
            
            if (count($args) > 0) {
                if (!str_contains($args[0], ',')) {
                    $dateStr = $args[0];
                }
            }

            $date = $this->parseDate($dateStr, $timezone);
            $datetime = new DateTimeImmutable($date->format('Y-m-d H:i:s'), new DateTimeZone($timezone));

            $panchangEngine = app(Panchang::class);
            $cal = $panchangEngine->calendar($datetime);

            $language = $this->getChatLanguage($chatId);
            $amanta = PanchangTranslator::translateWith($cal->amantaMonth, $language);
            if ($cal->isAmantaAdhika) $amanta = "Adhika $amanta";
            
            $purnimanta = PanchangTranslator::translateWith($cal->purnimantaMonth, $language);
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
                      . "🌗 *Paksha (Phase):* " . PanchangTranslator::translateWith($cal->paksha, $language) . "
"
                      . "🍃 *Ritu (Season):* " . PanchangTranslator::translateWith($cal->ritu, $language);

            $this->sendMessage($chatId, $response, 'Markdown');
        } catch (\Throwable $e) {
            Log::error("Telegram /calendar error: " . $e->getMessage());
            $this->sendMessage($chatId, "❌ Error getting calendar. Format: `/calendar YYYY-MM-DD`");
        }
    }

    private function handleElectional(int|string $chatId, array $args): void
    {
        try {
            $timezone = $this->getChatTimezone($chatId);
            $timeStr = 'now';
            [$lat, $lon] = $this->getChatLocation($chatId);

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
            $date = \Carbon\Carbon::parse($timeStr, $timezone)->timezone($timezone);
            $location = $this->makeLocation($lat, $lon);
            $datetime = new DateTimeImmutable($date->format('Y-m-d H:i:s'), new DateTimeZone($timezone));
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

    private function handleSetLocation(int|string $chatId, array $args): void
    {
        if (count($args) < 1) {
            $this->sendMessage($chatId, "Usage: `/setlocation latitude,longitude` or `/setlocation city, country`\nExamples: `/setlocation 19.0760,72.8777`, `/setlocation Mumbai, India`", 'Markdown');
            return;
        }

        $query = trim(implode(' ', $args));
        $coordinates = $this->parseCoordinates($query) ?? $this->findLocationCoordinates($query);
        if ($coordinates === null) {
            $this->sendMessage($chatId, 'Could not find that place. Try a city and country, or use latitude,longitude coordinates.');
            return;
        }

        [$lat, $lon] = $this->saveChatLocation($chatId, $coordinates[0], $coordinates[1]);
        $this->syncSubscriptionPreferences($chatId, ['latitude' => $lat, 'longitude' => $lon]);
        $name = $this->getLocationName($lat, $lon);
        $this->sendMessage($chatId, "📍 Saved this chat's default location: {$name}\nLocation based commands will use it until you change it or send /clear-location. Location search by OpenStreetMap.");
    }

    private function handleShowLocation(int|string $chatId): void
    {
        [$lat, $lon] = $this->getChatLocation($chatId);
        $name = $this->getLocationName($lat, $lon);
        $saved = Cache::has($this->locationCacheKey($chatId));
        $description = $saved ? 'Saved chat location' : 'Default location (New Delhi)';

        $this->sendMessage($chatId, "📍 {$description}: {$name}\nCoordinates: " . round($lat, 4) . ',' . round($lon, 4));
    }

    private function handleClearLocation(int|string $chatId): void
    {
        Cache::forget($this->locationCacheKey($chatId));
        $this->syncSubscriptionPreferences($chatId, ['latitude' => 28.6139, 'longitude' => 77.2090]);
        $this->sendMessage($chatId, "Cleared the saved location. Location based commands will use New Delhi by default.");
    }

    private function handleSubscribe(int|string $chatId, array $args): void
    {
        if (!$this->requirePrivateChat($chatId)) {
            return;
        }

        if (count($args) > 1 || (isset($args[0]) && !$this->isValidSendTime($args[0]))) {
            $this->sendMessage($chatId, "Usage: `/subscribe [HH:MM]`\nExample: `/subscribe 07:30`", 'Markdown');
            return;
        }

        if (!Schema::hasTable('telegram_subscriptions')) {
            $this->sendMessage($chatId, 'Daily delivery is not enabled yet. The site administrator needs to run the database migrations first.');
            return;
        }

        $timezone = $this->getChatTimezone($chatId);
        $sendTime = $args[0] ?? '07:00';
        $lastSentOn = now($timezone)->format('H:i') >= $sendTime ? now($timezone)->toDateString() : null;
        [$lat, $lon] = $this->getChatLocation($chatId);

        TelegramSubscription::updateOrCreate(
            ['chat_id' => (string) $chatId],
            [
                'send_time' => $sendTime,
                'timezone' => $timezone,
                'latitude' => $lat,
                'longitude' => $lon,
                'language' => $this->getChatLanguage($chatId),
                'last_sent_on' => $lastSentOn,
            ],
        );

        $this->sendMessage($chatId, "Daily Panchang enabled for {$sendTime} ({$timezone}). Use /subscription to review settings or /unsubscribe to stop.");
    }

    private function handleSetSubscriptionTime(int|string $chatId, array $args): void
    {
        if (!$this->requirePrivateChat($chatId)) {
            return;
        }

        if (count($args) !== 1 || !$this->isValidSendTime($args[0])) {
            $this->sendMessage($chatId, "Usage: `/settime HH:MM`\nExample: `/settime 07:30`", 'Markdown');
            return;
        }

        if (!Schema::hasTable('telegram_subscriptions')) {
            $this->sendMessage($chatId, 'Daily delivery is not enabled yet. The site administrator needs to run the database migrations first.');
            return;
        }

        $subscription = TelegramSubscription::where('chat_id', (string) $chatId)->first();
        if (!$subscription) {
            $this->sendMessage($chatId, 'You are not subscribed yet. Send /subscribe first.');
            return;
        }

        $timezone = $this->getChatTimezone($chatId);
        $sendTime = $args[0];
        $lastSentOn = now($timezone)->format('H:i') >= $sendTime ? now($timezone)->toDateString() : null;
        $subscription->update([
            'send_time' => $sendTime,
            'timezone' => $timezone,
            'last_sent_on' => $lastSentOn,
        ]);

        $this->sendMessage($chatId, "Daily Panchang time changed to {$sendTime} ({$timezone}).");
    }

    private function handleSubscriptionStatus(int|string $chatId): void
    {
        if (!$this->requirePrivateChat($chatId)) {
            return;
        }

        if (!Schema::hasTable('telegram_subscriptions')) {
            $this->sendMessage($chatId, 'Daily delivery is not enabled yet.');
            return;
        }

        $subscription = TelegramSubscription::where('chat_id', (string) $chatId)->first();
        if (!$subscription) {
            $this->sendMessage($chatId, 'Daily Panchang is off. Send /subscribe to turn it on.');
            return;
        }

        $this->sendMessage($chatId, "Daily Panchang: on\nDelivery: {$subscription->send_time} ({$subscription->timezone})\nLanguage: {$subscription->language}\nLocation: " . round($subscription->latitude, 4) . ',' . round($subscription->longitude, 4));
    }

    private function handleUnsubscribe(int|string $chatId): void
    {
        if (!$this->requirePrivateChat($chatId)) {
            return;
        }

        if (Schema::hasTable('telegram_subscriptions')) {
            TelegramSubscription::where('chat_id', (string) $chatId)->delete();
        }

        $this->sendMessage($chatId, 'Daily Panchang subscription stopped. Send /subscribe any time to start again.');
    }

    private function handleAddReminder(int|string $chatId, array $args): void
    {
        if (!$this->requirePrivateChat($chatId)) {
            return;
        }

        $type = strtolower($args[0] ?? '');
        $sendTime = $args[1] ?? '08:00';
        if (count($args) < 1 || count($args) > 2 || !in_array($type, self::REMINDER_TYPES, true) || !$this->isValidSendTime($sendTime)) {
            $this->sendMessage($chatId, "Usage: `/remind ekadashi|purnima|amavasya|festival [HH:MM]`\nExample: `/remind ekadashi 08:00`", 'Markdown');
            return;
        }

        if (!Schema::hasTable('telegram_reminders')) {
            $this->sendMessage($chatId, 'Reminders are not enabled yet. The site administrator needs to run the database migrations first.');
            return;
        }

        $timezone = $this->getChatTimezone($chatId);
        $lastSentOn = now($timezone)->format('H:i') >= $sendTime ? now($timezone)->toDateString() : null;
        [$lat, $lon] = $this->getChatLocation($chatId);
        TelegramReminder::updateOrCreate(
            ['chat_id' => (string) $chatId, 'type' => $type],
            [
                'send_time' => $sendTime,
                'timezone' => $timezone,
                'latitude' => $lat,
                'longitude' => $lon,
                'language' => $this->getChatLanguage($chatId),
                'last_sent_on' => $lastSentOn,
            ],
        );

        $this->sendMessage($chatId, "{$type} reminder saved for {$sendTime} ({$timezone}). Use /reminders to see all reminders.");
    }

    private function handleRemoveReminder(int|string $chatId, array $args): void
    {
        if (!$this->requirePrivateChat($chatId)) {
            return;
        }

        $type = strtolower($args[0] ?? '');
        if (count($args) !== 1 || !in_array($type, self::REMINDER_TYPES, true)) {
            $this->sendMessage($chatId, 'Usage: /unremind ekadashi|purnima|amavasya|festival');
            return;
        }

        if (Schema::hasTable('telegram_reminders')) {
            TelegramReminder::where('chat_id', (string) $chatId)->where('type', $type)->delete();
        }

        $this->sendMessage($chatId, "Removed the {$type} reminder.");
    }

    private function handleListReminders(int|string $chatId): void
    {
        if (!$this->requirePrivateChat($chatId)) {
            return;
        }

        if (!Schema::hasTable('telegram_reminders')) {
            $this->sendMessage($chatId, 'Reminders are not enabled yet.');
            return;
        }

        $reminders = TelegramReminder::where('chat_id', (string) $chatId)->orderBy('type')->get();
        if ($reminders->isEmpty()) {
            $this->sendMessage($chatId, 'You have no reminders. Add one with /remind ekadashi, /remind purnima, /remind amavasya, or /remind festival.');
            return;
        }

        $lines = $reminders->map(fn (TelegramReminder $reminder) => "• {$reminder->type}: {$reminder->send_time} ({$reminder->timezone})")->all();
        $this->sendMessage($chatId, "Your reminders:\n" . implode("\n", $lines));
    }

    private function handleSetBirthProfile(int|string $chatId, array $args): void
    {
        if (!$this->requirePrivateChat($chatId)) {
            return;
        }

        if (count($args) < 2 || count($args) > 3) {
            $this->sendMessage($chatId, "Usage: `/setbirth YYYY-MM-DD HH:MM [lat,lon]`\nExample: `/setbirth 1990-05-15 14:30 19.0760,72.8777`", 'Markdown');
            return;
        }

        if (!Schema::hasTable('telegram_birth_profiles')) {
            $this->sendMessage($chatId, 'Birth profiles are not enabled yet. The site administrator needs to run the database migrations first.');
            return;
        }

        $timezone = $this->getChatTimezone($chatId);
        try {
            $birth = \Carbon\Carbon::createFromFormat('!Y-m-d H:i', $args[0] . ' ' . $args[1], $timezone);
        } catch (\Throwable) {
            $birth = false;
        }

        if (!$birth || $birth->format('Y-m-d H:i') !== $args[0] . ' ' . $args[1] || $birth->isFuture()) {
            $this->sendMessage($chatId, 'Please provide a past birth date and 24-hour time in YYYY-MM-DD HH:MM format.');
            return;
        }

        if (isset($args[2])) {
            $coordinates = $this->parseCoordinates($args[2]);
            if ($coordinates === null) {
                $this->sendMessage($chatId, 'Please provide valid coordinates in latitude,longitude format.');
                return;
            }
            [$lat, $lon] = $coordinates;
        } else {
            [$lat, $lon] = $this->getChatLocation($chatId);
        }

        TelegramBirthProfile::updateOrCreate(
            ['chat_id' => (string) $chatId, 'user_id' => (string) ($this->activeUserId ?? $chatId)],
            [
                'birth_date' => $birth->format('Y-m-d'),
                'birth_time' => $birth->format('H:i'),
                'timezone' => $timezone,
                'latitude' => $lat,
                'longitude' => $lon,
            ],
        );

        $this->sendMessage($chatId, "Birth details saved for {$birth->format('d M Y H:i')} ({$timezone}) at " . round($lat, 4) . ',' . round($lon, 4) . '. Use /kundli saved to generate your chart.');
    }

    private function handleShowBirthProfile(int|string $chatId): void
    {
        if (!$this->requirePrivateChat($chatId)) {
            return;
        }

        $profile = $this->getBirthProfile($chatId);
        if (!$profile) {
            $this->sendMessage($chatId, 'No birth profile is saved. Use /setbirth YYYY-MM-DD HH:MM [lat,lon].');
            return;
        }

        $this->sendMessage($chatId, "Saved birth details:\nDate and time: {$profile->birth_date->format('d M Y')} {$profile->birth_time} ({$profile->timezone})\nCoordinates: " . round($profile->latitude, 4) . ',' . round($profile->longitude, 4));
    }

    private function handleClearBirthProfile(int|string $chatId): void
    {
        if (!$this->requirePrivateChat($chatId)) {
            return;
        }

        if (Schema::hasTable('telegram_birth_profiles')) {
            TelegramBirthProfile::where('chat_id', (string) $chatId)
                ->where('user_id', (string) ($this->activeUserId ?? $chatId))
                ->delete();
        }

        $this->sendMessage($chatId, 'Saved birth details deleted.');
    }

    private function getBirthProfile(int|string $chatId): ?TelegramBirthProfile
    {
        if (!Schema::hasTable('telegram_birth_profiles')) {
            return null;
        }

        return TelegramBirthProfile::where('chat_id', (string) $chatId)
            ->where('user_id', (string) ($this->activeUserId ?? $chatId))
            ->first();
    }

    private function requestedBirthProfile(int|string $chatId, array $args): TelegramBirthProfile|false|null
    {
        if (strtolower($args[0] ?? '') !== 'saved') {
            return null;
        }

        if (!$this->isPrivateChat) {
            return false;
        }

        return $this->getBirthProfile($chatId) ?? false;
    }

    private function isValidSendTime(string $value): bool
    {
        return preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/D', $value) === 1;
    }

    private function handleSetTimezone(int|string $chatId, array $args): void
    {
        if (count($args) !== 1) {
            $this->sendMessage($chatId, "Usage: `/settimezone Area/City`\nExample: `/settimezone America/New_York`", 'Markdown');
            return;
        }

        $timezone = trim($args[0]);
        if (!in_array($timezone, DateTimeZone::listIdentifiers(DateTimeZone::ALL_WITH_BC), true)) {
            $this->sendMessage($chatId, "Unknown timezone. Use an IANA name such as `Asia/Kolkata`, `Europe/London`, or `America/New_York`.", 'Markdown');
            return;
        }

        Cache::forever($this->timezoneCacheKey($chatId), $timezone);
        $this->syncSubscriptionPreferences($chatId, ['timezone' => $timezone]);
        $now = now($timezone)->format('D, d M Y H:i T');
        $this->sendMessage($chatId, "Timezone saved: {$timezone}\nLocal time: {$now}");
    }

    private function handleShowTimezone(int|string $chatId): void
    {
        $timezone = $this->getChatTimezone($chatId);
        $now = now($timezone)->format('D, d M Y H:i T');
        $this->sendMessage($chatId, "Current timezone: {$timezone}\nLocal time: {$now}");
    }

    private function handleClearTimezone(int|string $chatId): void
    {
        Cache::forget($this->timezoneCacheKey($chatId));
        $this->syncSubscriptionPreferences($chatId, ['timezone' => self::DEFAULT_TIMEZONE]);
        $this->sendMessage($chatId, 'Timezone reset to ' . self::DEFAULT_TIMEZONE . '.');
    }

    private function getChatTimezone(int|string $chatId): string
    {
        $timezone = Cache::get($this->timezoneCacheKey($chatId));
        if (is_string($timezone) && in_array($timezone, DateTimeZone::listIdentifiers(DateTimeZone::ALL_WITH_BC), true)) {
            return $timezone;
        }

        return self::DEFAULT_TIMEZONE;
    }

    private function timezoneCacheKey(int|string $chatId): string
    {
        return 'telegram_chat_timezone_' . $this->preferenceScopeId($chatId);
    }

    private function handleSetLanguage(int|string $chatId, array $args): void
    {
        if (count($args) !== 1) {
            $this->sendMessage($chatId, "Usage: `/setlanguage code`\nSupported: en, hi, gu, mr, bn, ta, te, kn, ml, sa", 'Markdown');
            return;
        }

        $language = strtolower(trim($args[0]));
        if (!array_key_exists($language, PanchangTranslator::SUPPORTED)) {
            $this->sendMessage($chatId, "Unsupported language. Choose one of: en, hi, gu, mr, bn, ta, te, kn, ml, sa.");
            return;
        }

        Cache::forever($this->languageCacheKey($chatId), $language);
        $this->syncSubscriptionPreferences($chatId, ['language' => $language]);
        $this->sendMessage($chatId, "Language saved: {$language}. Panchang values will use this language where translations are available.");
    }

    private function handleShowLanguage(int|string $chatId): void
    {
        $this->sendMessage($chatId, 'Current language: ' . $this->getChatLanguage($chatId));
    }

    private function handleClearLanguage(int|string $chatId): void
    {
        Cache::forget($this->languageCacheKey($chatId));
        $this->syncSubscriptionPreferences($chatId, ['language' => 'en']);
        $this->sendMessage($chatId, 'Language reset to English.');
    }

    private function getChatLanguage(int|string $chatId): string
    {
        $language = Cache::get($this->languageCacheKey($chatId), 'en');
        return is_string($language) && array_key_exists($language, PanchangTranslator::SUPPORTED) ? $language : 'en';
    }

    private function languageCacheKey(int|string $chatId): string
    {
        return 'telegram_chat_language_' . $this->preferenceScopeId($chatId);
    }

    private function syncSubscriptionPreferences(int|string $chatId, array $attributes): void
    {
        if (!$this->isPrivateChat) {
            return;
        }

        if (Schema::hasTable('telegram_subscriptions')) {
            TelegramSubscription::where('chat_id', (string) $chatId)->update($attributes);
        }
        if (Schema::hasTable('telegram_reminders')) {
            TelegramReminder::where('chat_id', (string) $chatId)->update($attributes);
        }
    }

    /** @return array{float, float}|null */
    private function parseCoordinates(string $value): ?array
    {
        $parts = explode(',', $value);
        if (count($parts) !== 2 || !is_numeric(trim($parts[0])) || !is_numeric(trim($parts[1]))) {
            return null;
        }

        $lat = (float) trim($parts[0]);
        $lon = (float) trim($parts[1]);
        if ($lat < -90 || $lat > 90 || $lon < -180 || $lon > 180) {
            return null;
        }

        return [$lat, $lon];
    }

    /** @return array{float, float} */
    private function saveChatLocation(int|string $chatId, float $lat, float $lon): array
    {
        if ($lat < -90 || $lat > 90 || $lon < -180 || $lon > 180) {
            throw new \InvalidArgumentException('Coordinates are outside the valid latitude/longitude ranges.');
        }

        Cache::forever($this->locationCacheKey($chatId), [$lat, $lon]);
        return [$lat, $lon];
    }

    /** @return array{float, float} */
    private function getChatLocation(int|string $chatId): array
    {
        $coordinates = Cache::get($this->locationCacheKey($chatId));
        if (is_array($coordinates) && count($coordinates) === 2) {
            return [(float) $coordinates[0], (float) $coordinates[1]];
        }

        return [28.6139, 77.2090];
    }

    private function makeLocation(mixed $lat, mixed $lon): GeoLocation
    {
        if (!is_numeric($lat) || !is_numeric($lon)) {
            throw new \InvalidArgumentException('Coordinates must be numeric latitude and longitude values.');
        }

        $coordinates = $this->parseCoordinates((string) $lat . ',' . (string) $lon);
        if ($coordinates === null) {
            throw new \InvalidArgumentException('Coordinates are outside the valid latitude/longitude ranges.');
        }

        return new GeoLocation($coordinates[0], $coordinates[1], 0);
    }

    /** @return array{float, float}|null */
    private function findLocationCoordinates(string $query): ?array
    {
        if ($query === '') {
            return null;
        }

        $cacheKey = 'telegram_location_search_' . hash('sha256', mb_strtolower($query));
        $coordinates = Cache::remember($cacheKey, now()->addDays(30), function () use ($query) {
            $response = $this->nominatimGet('search', [
                'format' => 'jsonv2',
                'limit' => 1,
                'q' => $query,
            ]);

            if (!$response?->successful()) {
                return false;
            }

            $result = $response->json()[0] ?? null;
            if (!is_array($result) || !isset($result['lat'], $result['lon'])) {
                return false;
            }

            return $this->parseCoordinates($result['lat'] . ',' . $result['lon']) ?? false;
        });

        return is_array($coordinates) ? $coordinates : null;
    }

    private function nominatimGet(string $endpoint, array $query): ?\Illuminate\Http\Client\Response
    {
        try {
            return Cache::lock('telegram_nominatim_rate_limit', 10)->block(5, function () use ($endpoint, $query) {
                $lastRequestAt = (float) Cache::get('telegram_nominatim_last_request_at', 0);
                $delayMicros = max(0, (int) ((1 - (microtime(true) - $lastRequestAt)) * 1_000_000));
                if ($delayMicros > 0) {
                    usleep($delayMicros);
                }
                Cache::put('telegram_nominatim_last_request_at', microtime(true), now()->addMinute());

                return Http::connectTimeout(3)->timeout(5)->withHeaders([
                    'User-Agent' => 'TithiDarshan-Telegram-Bot/1.0 (location lookup)',
                    'Accept-Language' => 'en',
                ])->get("https://nominatim.openstreetmap.org/{$endpoint}", $query);
            });
        } catch (\Throwable $e) {
            Log::warning('Telegram location lookup failed: ' . $e->getMessage());
            return null;
        }
    }

    private function locationCacheKey(int|string $chatId): string
    {
        return 'telegram_chat_location_' . $this->preferenceScopeId($chatId);
    }

    private function preferenceScopeId(int|string $chatId): string
    {
        if (!$this->isPrivateChat && $this->activeUserId !== null) {
            return $chatId . '_user_' . $this->activeUserId;
        }

        return (string) $chatId;
    }

    private function requirePrivateChat(int|string $chatId): bool
    {
        if ($this->isPrivateChat) {
            return true;
        }

        $this->sendMessage($chatId, 'For privacy and to avoid duplicate group messages, manage subscriptions and reminders in a private chat with the bot.');
        return false;
    }

    private function sendMessage(int|string $chatId, string $text, string $parseMode = '', ?array $replyMarkup = null): bool
    {
        if (empty($this->token)) {
            Log::warning("Telegram bot token not configured. Message not sent: $text");
            return false;
        }

        $payload = [
            'chat_id' => $chatId,
            'text' => $text,
        ];

        if ($parseMode) {
            $payload['parse_mode'] = $parseMode;
        }
        if ($replyMarkup !== null) {
            $payload['reply_markup'] = $replyMarkup;
        }

        try {
            $response = Http::connectTimeout(5)->timeout(20)->post("{$this->apiUrl}/sendMessage", $payload);
        } catch (\Throwable $e) {
            Log::error('Telegram message request failed: ' . $e->getMessage(), ['chat_id' => $chatId]);
            return false;
        }

        if (!$response->successful() || $response->json('ok') === false) {
            Log::error("Telegram API Error: " . $response->body());
            return false;
        }

        return true;
    }

    /**
     * Reverse geocode coordinates to a human-readable location name (e.g. "Mumbai, Maharashtra").
     */
    private function getLocationName(float $lat, float $lon): string
    {
        $cacheKey = "geocode_v1_{$lat}_{$lon}";

        return Cache::remember($cacheKey, now()->addDays(30), function () use ($lat, $lon) {
            try {
                $response = $this->nominatimGet('reverse', [
                    'format' => 'jsonv2',
                    'lat' => $lat,
                    'lon' => $lon,
                    'zoom' => 10, // City level
                ]);

                if ($response?->successful()) {
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
    private function parseDate(string $dateStr, string $timezone): \Carbon\Carbon
    {
        if (strtolower($dateStr) === 'today') {
            return \Carbon\Carbon::today($timezone);
        }

        // Standardize slashes to dashes so PHP correctly treats DD-MM-YYYY (European) 
        // rather than trying to parse MM/DD/YYYY (American) and failing.
        $standardized = str_replace('/', '-', $dateStr);

        return \Carbon\Carbon::parse($standardized, $timezone)->timezone($timezone);
    }
}
