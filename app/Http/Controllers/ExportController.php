<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    public function download()
    {
        $response = new StreamedResponse(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Customer ID', 'Acquisition Channel', 'First Order Amount', 'Total Orders', 'Actual 12mo LTV']);

            Customer::chunk(500, function ($customers) use ($handle) {
                foreach ($customers as $customer) {
                    fputcsv($handle, [
                        $customer->id,
                        $customer->acquisition_channel,
                        $customer->first_order_amount,
                        $customer->total_orders_count,
                        $customer->ltv_12mo,
                    ]);
                }
            });

            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="synapcores_ltv_predictions.csv"');

        return $response;
    }
}
