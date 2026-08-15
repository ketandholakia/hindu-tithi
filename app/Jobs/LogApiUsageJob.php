<?php

namespace App\Jobs;

use App\Models\ApiUsageLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class LogApiUsageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private readonly int $apiKeyId,
        private readonly string $endpoint,
        private readonly string $method,
        private readonly int $statusCode,
        private readonly int $responseTimeMs,
        private readonly string $ipAddress,
    ) {}

    public function handle(): void
    {
        ApiUsageLog::create([
            'api_key_id' => $this->apiKeyId,
            'endpoint' => $this->endpoint,
            'method' => $this->method,
            'status_code' => $this->statusCode,
            'response_time_ms' => $this->responseTimeMs,
            'ip_address' => $this->ipAddress,
        ]);
    }
}
