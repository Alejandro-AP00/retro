<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('board_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->json('columns')->default('[]'); // Add JSON column for storing column configurations
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('board_templates');
    }
};
