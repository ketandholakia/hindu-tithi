<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class SetTelegramWebhook extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:set-webhook {url : The full public URL including the path (e.g. https://yourdomain.com/api/telegram/webhook)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set the Telegram bot webhook URL';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $token = config('telegram.bot_token');
        $secret = config('telegram.webhook_secret');
        $url = $this->argument('url');

        if (empty($token)) {
            $this->error('TELEGRAM_BOT_TOKEN is not set in .env');
            return Command::FAILURE;
        }

        $this->info("Setting webhook to: $url");

        $payload = [
            'url' => $url,
        ];

        if (!empty($secret)) {
            $payload['secret_token'] = $secret;
        }

        $response = Http::post("https://api.telegram.org/bot{$token}/setWebhook", $payload);

        if ($response->successful()) {
            $this->info('Webhook set successfully!');
            $this->line($response->body());
            return Command::SUCCESS;
        } else {
            $this->error('Failed to set webhook.');
            $this->error($response->body());
            return Command::FAILURE;
        }
    }
}
