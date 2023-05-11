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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->enum('business_model_type', ['business', 'individual'])->default('business');
            $table->string('business_model')->nullable();
            $table->string('abn_number')->nullable();
            $table->boolean('abn_later')->default(0);
            $table->string('business_name');
            $table->string('phone');
            $table->string('name');
            $table->string('contact_number')->nullable();
            $table->string('suburb')->nullable();
            $table->string('state')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('email')->nullable();
            $table->string('transaction_summary_perference')->nullable();

            $table->unsignedBigInteger('created_by');
            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

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
        Schema::dropIfExists('customers');
    }
};
