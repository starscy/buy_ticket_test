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
        Schema::table('orders', function (Blueprint $table) {
            $table->integer('ticket_benefits_price')->after('ticket_kid_quantity')->nullable();
            $table->integer('ticket_benefits_quantity')->after('ticket_kid_quantity')->nullable();
            $table->integer('ticket_command_price')->after('ticket_kid_quantity')->nullable();
            $table->integer('ticket_command_quantity')->after('ticket_kid_quantity')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('ticket_benefits_price');
            $table->dropColumn('ticket_benefits_quantity');
            $table->dropColumn('ticket_command_price');
            $table->dropColumn('ticket_command_quantity');
        });
    }
};
