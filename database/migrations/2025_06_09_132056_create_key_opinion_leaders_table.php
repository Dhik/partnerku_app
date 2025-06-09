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
        Schema::create('key_opinion_leaders', function (Blueprint $table) {
            $table->id();
            $table->string('channel')->nullable();
            $table->string('username')->nullable();
            $table->string('niche')->nullable();
            $table->integer('average_view')->nullable();
            $table->string('skin_type')->nullable();
            $table->string('skin_concern')->nullable();
            $table->string('content_type')->nullable();
            $table->integer('rate')->nullable();
            $table->unsignedBigInteger('pic_contact')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('pic_contact')->references('id')->on('users')->nullOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->integer('cpm')->nullable();
            $table->string('name')->nullable();
            $table->text('address')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_account')->nullable();
            $table->string('bank_account_name')->nullable();
            $table->boolean('npwp')->nullable();
            $table->string('npwp_number')->nullable();
            $table->string('nik')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('product_delivery')->nullable();
            $table->text('product')->nullable();
            $table->unsignedBigInteger('tenant_id');
            $table->timestamps();

            $table->index('tenant_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('key_opinion_leaders');
    }
};
