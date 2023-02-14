<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('responsible_user_id');
            $table->unsignedBigInteger('group_id');
            $table->unsignedBigInteger('status_id');
            $table->unsignedBigInteger('pipeline_id');
            $table->unsignedBigInteger('loss_reason_id')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by');
            $table->unsignedBigInteger('account_id');
            $table->unsignedBigInteger('company_id')->nullable();

            $table->string('name');
            $table->BigInteger('price');

            $table->integer('closed_at')->nullable();
            $table->integer('closest_task_at')->nullable();
            $table->boolean('is_deleted')->default(false);
            $table->string('custom_fields_values')->nullable();
            $table->integer('score')->nullable();
            $table->float('labor_cost')->nullable();
            $table->boolean('is_price_computed')->default(false);

            $table->timestamps();
            $table->softDeletes();
            $table->foreign('company_id')->references('id')->on('companies');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('leads');
    }
}
