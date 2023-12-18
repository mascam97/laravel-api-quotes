<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('gifts', function (Blueprint $table) {
            $table->id();
            $table->string('note', 255)->nullable();
            $table->bigInteger('amount');
            $table->char('currency', 3);
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('sender_user_id')->constrained('users');
            // TODO: Evaluate if add or not pocket_id field, it can be gotten from the user now
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gifts');
    }
};
