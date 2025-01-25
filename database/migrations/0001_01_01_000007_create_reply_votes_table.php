<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('reply_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('reply_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            // Prevent duplicate votes
            $table->unique(['user_id', 'reply_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('reply_votes');
    }
};
