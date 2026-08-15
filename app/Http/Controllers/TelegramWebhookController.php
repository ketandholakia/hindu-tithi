<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\TelegramBotService;

class TelegramWebhookController extends Controller
{
    private TelegramBotService $botService;

    public function __construct(TelegramBotService $botService)
    {
        $this->botService = $botService;
    }

    /**
     * Handle incoming Telegram webhook updates.
     */
    public function handle(Request $request)
    {
        $secretToken = config('telegram.webhook_secret');
        
        // Optional security check if secret token is configured
        if (!empty($secretToken)) {
            $headerToken = $request->header('X-Telegram-Bot-Api-Secret-Token');
            if ($headerToken !== $secretToken) {
                Log::warning("Unauthorized Telegram webhook attempt.", ['ip' => $request->ip()]);
                return response()->json(['error' => 'Unauthorized'], 401);
            }
        }

        try {
            $update = $request->all();
            $this->botService->handleUpdate($update);
        } catch (\Exception $e) {
            Log::error("Error processing Telegram update: " . $e->getMessage());
        }

        // Always return 200 OK to Telegram so it doesn't retry
        return response()->json(['status' => 'ok']);
    }
}
