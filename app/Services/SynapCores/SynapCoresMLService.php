<?php
namespace App\Services\SynapCores;
use Log;

class SynapCoresMLService
{
    protected SynapCoresClient $client;

    public function __construct(SynapCoresClient $client)
    {
        $this->client = $client;
    }
    public function initializeAndTrainModel(): void
    {
        // 1. Create a tiny dummy table so the experiment definition validation is instant
        SynapCoresClient::query("DROP TABLE IF EXISTS training_sample;");
        SynapCoresClient::query("CREATE TABLE training_sample AS SELECT * FROM customers LIMIT 10;");

        // 2. Define the experiment pointing to the tiny sample table
        SynapCoresClient::query("
            CREATE EXPERIMENT IF NOT EXISTS ltv_v1
            WITH (
                target = 'ltv_12mo',
                model_type = 'regression',
                features = ['acquisition_channel', 'first_order_amount', 'total_orders_count', 'days_since_last_order']
            )
            AS SELECT * FROM training_sample;
        ");

        // 3. Now run the TRAIN command.
        // This part runs in the background and won't time out the HTTP request.
        SynapCoresClient::query("TRAIN ltv_v1;");

        SynapCoresClient::query("DROP TABLE training_sample;");
    }
    public function scoreAdHocVector(string $channel, float $firstAmount, int $totalCount, int $daysSince): float
    {
        $sql = "SELECT AUTOML.PREDICT('ltv_v1', JSON_OBJECT(
            'acquisition_channel', :channel,
            'first_order_amount', :first_amount,
            'total_orders_count', :total_count,
            'days_since_last_order', :days_since
        )) as prediction";

        $results = $this->client->query($sql, [
            'channel' => $channel,
            'first_amount' => $firstAmount,
            'total_count' => $totalCount,
            'days_since' => $daysSince
        ]);

        return (float) ($results[0]['prediction'] ?? 0.00);
    }
}
