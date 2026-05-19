<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('acquisition_channel'); // organic, google_ads, facebook_ads, referral
            $table->dateTime('signup_at');
            $table->decimal('first_order_amount', 10, 2)->default(0);
            $table->integer('total_orders_count')->default(0);
            $table->decimal('total_spend', 10, 2)->default(0);
            $table->integer('days_since_last_order')->default(0);
            $table->decimal('ltv_12mo', 10, 2)->default(0); // Target regression label column
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
