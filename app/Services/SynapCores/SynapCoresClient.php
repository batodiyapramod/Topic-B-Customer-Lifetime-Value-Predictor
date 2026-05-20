<?php
namespace App\Services\SynapCores;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class SynapCoresClient
{
    protected string $baseUrl;
    protected string $apiKey;
    protected int $timeout;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.synapcores.base_url', env('SYNAPCORES_BASE_URL')), '/');
        $this->apiKey = config('services.synapcores.api_key', env('SYNAPCORES_API_KEY'));
       $this->timeout = (float) config('services.synapcores.timeout', 60.0);
    }
    // app/Services/SynapCores/SynapCoresClient.php

    public static function query(string $sql, array $bindings = [])
    {
        $instance = app(self::class);

        return Http::withHeaders([
            'Authorization' => "Bearer " . $instance->apiKey,
            'Content-Type' => 'application/json',
        ])
        ->timeout(300)
        ->post("{$instance->baseUrl}/v1/query/execute", [
            'sql' => $sql,
            'params' => $bindings
        ]);
    }
}
