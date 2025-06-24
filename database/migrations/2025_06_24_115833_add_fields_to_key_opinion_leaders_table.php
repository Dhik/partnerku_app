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
            $table->text('link')->nullable();
            $table->integer('price_per_slot')->nullable();
            $table->string('category')->nullable();
            $table->string('tier')->nullable();
            $table->integer('gmv')->nullable();
            $table->string('pic_listing')->nullable();
            $table->string('pic_content')->nullable();
            $table->string('status_recommendation')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('key_opinion_leaders', function (Blueprint $table) {
            $table->dropColumn([
                'link',
                'price_per_slot',
                'category',
                'tier',
                'gmv',
                'pic_listing',
                'pic_content',
                'status_recommendation'
            ]);
        });
    }
};
