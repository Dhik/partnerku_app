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
            $table->integer('following')->nullable();
            $table->integer('followers')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('key_opinion_leaders', function (Blueprint $table) {
            $table->dropColumn(['following', 'followers']);
        });
    }
};
