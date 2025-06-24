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
        Schema::table('key_opinion_leaders', function (Blueprint $table) {
            $table->dropColumn([
                'skin_type',
                'skin_concern',
                'bank_name',
                'bank_account',
                'bank_account_name',
                'npwp',
                'npwp_number',
                'nik',
                'notes',
                'product_delivery',
                'product'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('key_opinion_leaders', function (Blueprint $table) {
            $table->string('skin_type')->nullable();
            $table->string('skin_concern')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_account')->nullable();
            $table->string('bank_account_name')->nullable();
            $table->boolean('npwp')->nullable();
            $table->string('npwp_number')->nullable();
            $table->string('nik')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('product_delivery')->nullable();
            $table->text('product')->nullable();
        });
    }
};
