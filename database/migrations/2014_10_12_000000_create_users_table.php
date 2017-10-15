<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('telephone');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('address_1');
            $table->string('address_2')->nullable();
            $table->string('address_3')->nullable();;
            $table->string('city');
            $table->string('postcode');
            $table->boolean('is_admin')->default(false);
            $table->boolean('approved')->default(false);
            $table->boolean('blocked')->default(false);
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
