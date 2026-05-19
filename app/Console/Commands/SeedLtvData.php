<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Customer;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SeedLtvData extends Command
{
    protected $signature = 'synapcores:seed';
    protected $description = 'Generates 5,000+ customers and 50,000 orders with predictable LTV vectors.';

    public function handle()
    {
        DB::connection()->disableQueryLog();
        $this->info('Generating data vectors...');

        $channels = ['organic', 'google_ads', 'facebook_ads', 'referral'];
        $batchSize = 500;
        $customersBatch = [];

        for ($i = 1; $i <= 5000; $i++) {
            $channel = $channels[array_rand($channels)];
            $signupAt = Carbon::now()->subDays(rand(365, 730));
            $firstOrderAmount = rand(20, 150);
            $totalOrdersCount = rand(1, 25);
            $daysSinceLastOrder = rand(2, 180);

            $channelMultiplier = match($channel) {
                'referral' => 1.4,
                'google_ads' => 1.1,
                'facebook_ads' => 0.85,
                default => 1.0, // organic
            };

            $baseLtv = ($firstOrderAmount * 1.6) + ($totalOrdersCount * 42.5) - ($daysSinceLastOrder * 0.4);
            $ltv12Mo = max($firstOrderAmount, ($baseLtv * $channelMultiplier) + rand(-15, 15));

            $customersBatch[] = [
                'acquisition_channel' => $channel,
                'signup_at' => $signupAt,
                'first_order_amount' => $firstOrderAmount,
                'total_orders_count' => $totalOrdersCount,
                'total_spend' => $firstOrderAmount + ($totalOrdersCount > 1 ? ($totalOrdersCount - 1) * rand(15, 60) : 0),
                'days_since_last_order' => $daysSinceLastOrder,
                'ltv_12mo' => round($ltv12Mo, 2),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if (count($customersBatch) === $batchSize) {
                Customer::insert($customersBatch);
                $customersBatch = [];
            }
        }

        $this->info('Populating child order logs...');
        $this->backfillOrders();

        $this->info('Data generation phase complete.');
    }

    private function backfillOrders()
    {
        Customer::chunk(500, function($customers) {
            $ordersBatch = [];
            foreach ($customers as $customer) {
                for ($j = 0; $j < $customer->total_orders_count; $j++) {
                    $ordersBatch[] = [
                        'customer_id' => $customer->id,
                        'amount' => $j === 0 ? $customer->first_order_amount : rand(15, 70),
                        'placed_at' => Carbon::parse($customer->signup_at)->addDays(rand(0, 365)),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
            Order::insert($ordersBatch);
        });
    }
}
