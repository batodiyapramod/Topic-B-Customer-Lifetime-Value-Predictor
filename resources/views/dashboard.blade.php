<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>AllCalls - SynapCores LTV Engine</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-900 font-sans p-8">
    <div class="max-w-7xl mx-auto">
        <header class="flex justify-between items-center mb-8 pb-4 border-b">
            <div>
                <h1 class="text-3xl font-bold tracking-tight">LTV Predictive Workspace</h1>
                <p class="text-sm text-gray-500">Powered by SynapCores AIDB Regression Engine</p>
            </div>
            <a href="{{ route('dashboard.export') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-4 py-2 rounded shadow transition">
                Export Predictions CSV
            </a>
        </header>

        <!-- Channel Summary Layout Section -->
        <section class="mb-12">
            <h2 class="text-xl font-semibold mb-4">Acquisition Channel Analytics Summary</h2>
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider">Channel Source</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider">Total Volume</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider">Avg Initial Basket</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider">Aggregated Mean LTV</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($channelAnalytics as $channel)
                        <tr>
                            <td class="px-6 py-4 font-medium">{{ str($channel->acquisition_channel)->upper() }}</td>
                            <td class="px-6 py-4">{{ number_format($channel->customer_volume) }}</td>
                            <td class="px-6 py-4">${{ number_format($channel->avg_initial_spend, 2) }}</td>
                            <td class="px-6 py-4 text-green-600 font-medium">${{ number_format($channel->avg_actual_ltv, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Top 100 Profiles Layout Section -->
        <section>
            <h2 class="text-xl font-semibold mb-4">Top 100 Profiles Ranked by Predicted LTV Target Value</h2>
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider">Customer ID</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider">Channel Path</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider">Order Density</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider">Days Since Last Touch</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider">12mo Projected LTV</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($topCustomers as $customer)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-mono text-sm">#{{ $customer->id }}</td>
                            <td class="px-6 py-4 text-sm">{{ $customer->acquisition_channel }}</td>
                            <td class="px-6 py-4 text-sm">{{ $customer->total_orders_count }} orders</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $customer->days_since_last_order }} days ago</td>
                            <td class="px-6 py-4 text-sm font-semibold text-blue-600">${{ number_format($customer->ltv_12mo, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</body>
</html>
