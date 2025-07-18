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
        Schema::create('other_spent', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->text('detail');
            $table->decimal('amount', 15, 2);
            $table->string('evidence_link');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('other_spent');
    }
};
