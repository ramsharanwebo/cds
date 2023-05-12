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
        Schema::create('duckets', function (Blueprint $table) {
            $table->id();
            $table->string('identity')->nullable();
            // $table->unsignedBigInteger('ticket_id')->nullable();
            $table->date('ducket_date')->nullable();
            $table->text('goods')->nullable();
            $table->text('notes')->nullable();
            $table->string('gst')->nullable();
            $table->string('levy')->nullable();
            $table->string('total_amount')->nullable();
            $table->string('count')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();

            // $table->foreign('ticket_id')
            //     ->references('id')
            //     ->on('tickets');

            $table->foreign('created_by')
                ->references('id')
                ->on('users');
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
        Schema::dropIfExists('duckets');
    }
};
