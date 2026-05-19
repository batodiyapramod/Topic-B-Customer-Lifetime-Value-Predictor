<?php
namespace App\Services\SynapCores;

class SynapCoresMLService
{
    protected SynapCoresClient $client;

    public function __construct(SynapCoresClient $client)
    {
        $this->client = $client;
    }

    public function initializeAndTrainModel(): array
    {
        $this->client->query("
            CREATE EXPERIMENT IF NOT EXISTS ltv_v1
            WITH (
                target = 'ltv_12mo',
                model_type = 'regression',
                features = ['acquisition_channel', 'first_order_amount', 'total_orders_count', 'days_since_last_order']
            )
        ");

        return $this->client->query("TRAIN ltv_v1");
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
