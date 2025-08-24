<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
         
            $table->timestamp('received_at')->nullable()->after('order_date');
            $table->timestamp('preparing_at')->nullable()->after('received_at');
            $table->timestamp('completed_at')->nullable()->after('preparing_at');

            
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['received_at','preparing_at','completed_at']);
        });
    }
};
