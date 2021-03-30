<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Customer extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
		Schema::create('Customers', function (Blueprint $table) {
		    $table->id('IdCustomer');
		    $table->string('Phone')->nullable();
		    $table->string('Email')->nullable();
		    $table->string('Password');
		    $table->string('Name')->nullable();
		    $table->string('Lastname')->nullable();
		    $table->uuid('Guid');
			$table->boolean('Active');
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
		Schema::dropIfExists('Customers');
    }
}
