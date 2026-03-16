<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('goal_installments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('goal_id')->constrained('goals')->onDelete('cascade');
            $table->decimal('amount', 15, 2);
            $table->date('date');
            $table->string('note')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'goal_id']);
            $table->index(['goal_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('goal_installments');
    }
};
