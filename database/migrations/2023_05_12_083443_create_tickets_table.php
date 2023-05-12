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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number')->nullable();
            $table->unsignedBigInteger('location_id');
            $table->string('reference')->nullable();
            $table->string('container_qty')->nullable();
            $table->string('ticket_date')->nullable();
            $table->double('amount')->nullable();
            $table->unsignedBigInteger('customer_id');
            $table->boolean('status')->default(1); //active
            $table->unsignedBigInteger('created_by')->nullable();

            $table->foreign('created_by')
            ->references('id')
            ->on('users');

            $table->foreign('location_id')
            ->references('id')
            ->on('locations');

            $table->foreign('customer_id')
            ->references('id')
            ->on('customers');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tickets');
    }
};
