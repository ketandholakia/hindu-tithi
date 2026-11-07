<?php

namespace App\Console\Commands;

use App\Models\TelegramReminder;
use App\Services\PanchangTranslator;
use App\Services\TelegramBotService;
use DateTimeImmutable;
use DateTimeZone;
use Illuminate\Console\Command;
use Vittix\Panchang\Enum\Tithi;
use Vittix\Panchang\Panchang;
use Vittix\Panchang\ValueObject\GeoLocation;

class SendTelegramReminders extends Command
{
    protected $signature = 'telegram:send-reminders';

    protected $description = 'Send Ekadashi, Purnima, Amavasya, and festival reminders';

    public function handle(Panchang $panchang, TelegramBotService $bot): int
    {
        $sent = 0;

        foreach (TelegramReminder::query()->orderBy('id')->get() as $reminder) {
            $localNow = now($reminder->timezone);
            if ($localNow->format('H:i') < $reminder->send_time
                || $reminder->last_sent_on?->toDateString() === $localNow->toDateString()) {
                continue;
            }

            $timezone = new DateTimeZone($reminder->timezone);
            $date = new DateTimeImmutable($localNow->toDateString() . ' 00:00:00', $timezone);
            $location = new GeoLocation($reminder->latitude, $reminder->longitude, 0);

            try {
                $detail = $this->getReminderDetail($reminder->type, $panchang, $date, $location, $reminder->language);
            } catch (\Throwable $e) {
                $this->error("Could not calculate {$reminder->type} reminder for chat {$reminder->chat_id}: {$e->getMessage()}");
                continue;
            }

            if ($detail === null) {
                $reminder->last_sent_on = $localNow->toDateString();
                $reminder->save();
                continue;
            }

            if ($bot->sendReminderMessage($reminder->chat_id, $reminder->type, $detail, $date)) {
                $reminder->last_sent_on = $localNow->toDateString();
                $reminder->save();
                $sent++;
            }
        }

        $this->info("Sent {$sent} reminder(s).");

        return self::SUCCESS;
    }

    private function getReminderDetail(
        string $type,
        Panchang $panchang,
        DateTimeImmutable $date,
        GeoLocation $location,
        string $language,
    ): ?string {
        if ($type === 'festival') {
            $festivals = $panchang->festivals()->getFestivalsForDate($date, $location);
            return $festivals === [] ? null : implode(', ', $festivals);
        }

        $day = $panchang->day($date, $location);
        $tithi = $day->tithiAtSunrise;

        $matches = match ($type) {
            'ekadashi' => in_array($tithi, [Tithi::ShuklaD11, Tithi::KrishnaD11], true),
            'purnima' => $tithi === Tithi::Purnima,
            'amavasya' => $tithi === Tithi::Amavasya,
            default => false,
        };

        if (!$matches) {
            return null;
        }

        return PanchangTranslator::translateWith($tithi, $language) ?? $tithi->nameEnglish();
    }
}
