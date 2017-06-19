<?php

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

            $table->string('firstname');
            $table->string('lastname');

            $table->string('username')->unique();
            $table->string('email')->unique();

            $table->string('password');

            $table->ipAddress('last_login_ip')->nullable()->default(null);
            $table->dateTime('last_login_at')->nullable()->default(null);

            $table->boolean('enabled')->default(false);
            $table->boolean('protected')->default(false);

            $table->rememberToken();
            $table->softDeletes();
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
