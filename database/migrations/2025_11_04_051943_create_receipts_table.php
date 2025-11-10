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
        Schema::create('receipts', function (Blueprint $table) {
            $table->id();
            $table->string('s_no')->nullable();
            $table->foreignIdFor(\App\Models\Cell::class)->nullable()->constrained();
            $table->longText('subject')->nullable();
            $table->string('letter_no')->nullable();
            $table->date('letter_date')->nullable();
            $table->string('received_from')->nullable();
            $table->string('name_of_da')->nullable();
            $table->dateTime('received_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receipts');
    }
};
