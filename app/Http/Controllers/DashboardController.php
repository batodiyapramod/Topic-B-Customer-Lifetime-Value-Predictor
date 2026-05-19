<?php
namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use App\Services\SynapCores\SynapCoresClient;

class DashboardController extends Controller
{
    public function index(SynapCoresClient $client)
    {
        // View 1: Fetch Top 100 high-value targets based on standard actual data
        $topCustomers = Customer::orderBy('ltv_12mo', 'desc')->limit(100)->get();

        // View 2: Aggregate values clustered by acquisition pipeline channel
        $channelAnalytics = Customer::select('acquisition_channel')
            ->selectRaw('count(*) as customer_volume')
            ->selectRaw('avg(first_order_amount) as avg_initial_spend')
            ->selectRaw('avg(ltv_12mo) as avg_actual_ltv')
            ->groupBy('acquisition_channel')
            ->orderBy('avg_actual_ltv', 'desc')
            ->get();

        return view('dashboard', compact('topCustomers', 'channelAnalytics'));
    }
}
