<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TelegramConfigController extends Controller
{
    /**
     * Display the Telegram configuration and current webhook status.
     */
    public function index()
    {
        $token = config('telegram.bot_token');
        $webhookInfo = null;
        $isConfigured = !empty($token);

        if ($isConfigured) {
            $response = Http::get("https://api.telegram.org/bot{$token}/getWebhookInfo");
            if ($response->successful()) {
                $webhookInfo = $response->json('result');
            }
        }

        return view('admin.telegram.index', compact('isConfigured', 'webhookInfo'));
    }

    /**
     * Set the Telegram webhook URL.
     */
    public function setWebhook(Request $request)
    {
        $request->validate([
            'url' => 'required|url'
        ]);

        $token = config('telegram.bot_token');
        $secret = config('telegram.webhook_secret');

        if (empty($token)) {
            return back()->with('error', 'TELEGRAM_BOT_TOKEN is not configured in the environment.');
        }

        $payload = ['url' => $request->url];
        if (!empty($secret)) {
            $payload['secret_token'] = $secret;
        }

        $response = Http::post("https://api.telegram.org/bot{$token}/setWebhook", $payload);

        if ($response->successful() && $response->json('ok')) {
            return back()->with('success', 'Webhook successfully set to: ' . $request->url);
        }

        return back()->with('error', 'Failed to set webhook: ' . $response->json('description', 'Unknown error'));
    }

    /**
     * Remove the Telegram webhook.
     */
    public function deleteWebhook()
    {
        $token = config('telegram.bot_token');

        if (empty($token)) {
            return back()->with('error', 'TELEGRAM_BOT_TOKEN is not configured in the environment.');
        }

        $response = Http::post("https://api.telegram.org/bot{$token}/deleteWebhook");

        if ($response->successful() && $response->json('ok')) {
            return back()->with('success', 'Webhook successfully removed.');
        }

        return back()->with('error', 'Failed to remove webhook: ' . $response->json('description', 'Unknown error'));
    }
}
