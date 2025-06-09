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
        Schema::table('campaign_contents', function (Blueprint $table) {
            $table->foreignId('key_opinion_leader_id')->constrained();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('campaign_contents', function (Blueprint $table) {
            $table->dropForeign(['key_opinion_leader_id']);
            $table->dropColumn('key_opinion_leader_id');
        });
    }
};
