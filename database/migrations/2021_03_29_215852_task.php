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
		    $table->string('Name')->nullable();
		    $table->string('Description')->nullable();
		    $table->string('Priority');
		    $table->string('Color');
			$table->boolean('Pinned');
		    $table->boolean('Active');
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
