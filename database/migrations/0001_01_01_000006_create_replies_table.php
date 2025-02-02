<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('replies', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->text('content');
            $table->foreignUlid('user_id')->constrained();
            $table->foreignUlid('column_id')->constrained()->cascadeOnDelete();
            $table->integer('order_by')->default(0);
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('replies');
    }
};
