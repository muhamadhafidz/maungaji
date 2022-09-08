<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid', 36)->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamp('device_timestamp');
            $table->integer('total_amount');
            $table->integer('paid_amount');
            $table->integer('change_amount');
            $table->enum('payment_method', ['cash', 'card']);
            $table->timestamps();
            $table->softDeletes($column = 'deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactions');
    }
};
