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
        $this->timeout = (int) config('services.synapcores.timeout', env('SYNAPCORES_TIMEOUT', 30));
    }

    public function query(string $sql, array $bindings = []): array
    {
        // try {
        //         $response = Http::withHeaders([
        //             'Authorization' => "Bearer {$this->apiKey}",
        //             'Accept' => 'application/json',
        //         ])
        //         ->timeout($this->timeout)
        //         ->post("{$this->baseUrl}/v1/query", [
        //             'sql' => $sql,
        //             'bindings' => $bindings
        //         ]);

        //         if ($response->failed()) {
        //             Log::error('SynapCores Query Engine Failure', [
        //                 'status' => $response->status(),
        //                 'error' => $response->body(),
        //                 'query' => $sql
        //             ]);
        //             throw new Exception("SynapCores Error: " . $response->json('error.message', 'Unknown Query Exception'));
        //         }

        //         return $response->json('data', []);
        //     } catch (Exception $e) {
        //         Log::critical('SynapCores Transport Exception: ' . $e->getMessage());
        //         throw $e;
        //     }
        // }

        //Community Edition "API endpoint not found so this code comment

        try {
            // Route execution explicitly through the secondary connection block
            $results = DB::connection('synapcores')->select($sql, $bindings);

            // Convert array of stdClass objects to associative arrays for compatibility
            return json_decode(json_encode($results), true);
        } catch (Exception $e) {
            Log::critical('SynapCores Database Execution Failure', [
                'sql' => $sql,
                'bindings' => $bindings,
                'error' => $e->getMessage()
            ]);

            throw new Exception("SynapCores Connection Error: " . $e->getMessage());
        }
    }
}
