<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFormspluginTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('module_forms', function (Blueprint $table) {
            $table->increments('id');

            $table->string('name');
            $table->string('send_to_email')->nullable()->default(null);
            $table->string('email_view')->nullable()->default(null);

            $table->boolean('is_ajax')->default(0);

            $table->unsignedInteger('user_id')->nullable()->default(null);
            $table->foreign('user_id')->references('id')->on('users')
                ->onDelete('set null');

            $table->softDeletes();
            $table->timestamps();
        });


        Schema::create('module_form_fields', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('form_id');
            $table->foreign('form_id')->references('id')->on('module_forms')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->unsignedInteger('language_id');
            $table->foreign('language_id')->references('id')->on('languages')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->string('name');
            $table->string('type');

            $table->text('options');

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
        Schema::dropIfExists('module_form_fields');
        Schema::dropIfExists('module_forms');
    }
}
