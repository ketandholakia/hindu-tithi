<?php

namespace App\Console\Commands;

use App\Models\PanchangReminderSetting;
use App\Services\PanchangTranslator;
use DateTimeImmutable;
use DateTimeZone;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Vittix\Panchang\Enum\Tithi;
use Vittix\Panchang\Panchang;
use Vittix\Panchang\ValueObject\GeoLocation;

class SendWebPanchangReminders extends Command
{
    protected $signature = 'panchang:send-web-reminders';
    protected $description = 'Send opted-in Panchang tithi and festival email reminders';

    public function handle(Panchang $panchang): int
    {
        $sent = 0;

        PanchangReminderSetting::query()->with('user')->where('enabled', true)->orderBy('id')->each(function ($settings) use ($panchang, &$sent) {
            $localNow = now($settings->timezone);
            if ($localNow->format('H:i') < $settings->send_time || $settings->last_sent_on?->toDateString() === $localNow->toDateString()) {
                return;
            }

            $timezone = new DateTimeZone($settings->timezone);
            $date = new DateTimeImmutable($localNow->toDateString() . ' 00:00:00', $timezone);
            $location = new GeoLocation($settings->latitude, $settings->longitude, $settings->elevation_meters);
            $messages = [];

            try {
                $day = $panchang->day($date, $location);
                $tithi = $day->tithiAtSunrise;
                if ($settings->notify_ekadashi && in_array($tithi, [Tithi::ShuklaD11, Tithi::KrishnaD11], true)) {
                    $messages[] = 'Ekadashi: ' . (PanchangTranslator::translateWith($tithi, 'en') ?? $tithi->nameEnglish());
                }
                if ($settings->notify_purnima && $tithi === Tithi::Purnima) $messages[] = 'Purnima';
                if ($settings->notify_amavasya && $tithi === Tithi::Amavasya) $messages[] = 'Amavasya';
                if ($settings->notify_festivals) {
                    $festivals = $panchang->festivals()->getFestivalsForDate($date, $location);
                    if ($festivals !== []) $messages[] = 'Festivals: ' . implode(', ', $festivals);
                }

                if ($messages !== [] && $settings->user) {
                    $subject = 'Panchang reminders for ' . $localNow->format('j M Y');
                    $body = "Your Panchang reminders for {$localNow->format('l, j F Y')}:\n\n"
                        . implode("\n", $messages)
                        . "\n\nLocation: {$settings->latitude}, {$settings->longitude}\nTimezone: {$settings->timezone}\n\nManage preferences: " . route('reminders.edit');
                    Mail::raw($body, fn ($mail) => $mail->to($settings->user->email)->subject($subject));
                    $sent++;
                }

                $settings->last_sent_on = $localNow->toDateString();
                $settings->save();
            } catch (\Throwable $exception) {
                Log::error('Panchang reminder delivery failed.', ['user_id' => $settings->user_id, 'exception' => $exception->getMessage()]);
                $this->error("Reminder failed for user {$settings->user_id}: {$exception->getMessage()}");
            }
        });

        $this->info("Sent {$sent} web reminder email(s).");
        return self::SUCCESS;
    }
}
