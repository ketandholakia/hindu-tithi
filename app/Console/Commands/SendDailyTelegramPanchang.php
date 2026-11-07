<?php

namespace App\Console\Commands;

use App\Models\TelegramSubscription;
use App\Services\TelegramBotService;
use Illuminate\Console\Command;

class SendDailyTelegramPanchang extends Command
{
    protected $signature = 'telegram:send-daily-panchang';

    protected $description = 'Send scheduled daily Panchang messages to Telegram subscribers';

    public function handle(TelegramBotService $bot): int
    {
        $sent = 0;

        foreach (TelegramSubscription::query()->orderBy('id')->get() as $subscription) {
            $localNow = now($subscription->timezone);
            if ($localNow->format('H:i') < $subscription->send_time
                || $subscription->last_sent_on?->toDateString() === $localNow->toDateString()) {
                continue;
            }

            if ($bot->sendDailyPanchang(
                $subscription->chat_id,
                $subscription->latitude,
                $subscription->longitude,
                $subscription->timezone,
                $subscription->language,
            )) {
                $subscription->last_sent_on = $localNow->toDateString();
                $subscription->save();
                $sent++;
            }
        }

        $this->info("Sent {$sent} daily Panchang message(s).");

        return self::SUCCESS;
    }
}
