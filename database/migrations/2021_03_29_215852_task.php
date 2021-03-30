<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Task extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
		Schema::create('Task', function (Blueprint $table) {
		    $table->id('IdTask');
			$table->unsignedBigInteger('IdCustomer');
			$table->foreign('IdCustomer')
				  ->references('IdCustomer')
				  ->on('Customer')
				  ->onDelete('cascade');
		    $table->string('Name')->nullable();
		    $table->string('Description')->nullable();
		    $table->string('Priority')->default(1);
		    $table->string('Color')->default('#bfbfbf');
			$table->boolean('Pinned')->default(0);
		    $table->boolean('Active')->default(1);
			$table->dateTime('Date', $precision = 0)->nullable();
		    $table->uuid('Guid');
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
        //
		Schema::dropIfExists('Task');
    }
}
