<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateModuleFormLocalisationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('module_form_localisation', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('form_id');
            $table->foreign('form_id')->references('id')->on('module_forms')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->unsignedInteger('language_id');
            $table->foreign('language_id')->references('id')->on('languages')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->string('submit_text')->nullable()->default(null);
            $table->string('reset_text')->nullable()->default(null);
            $table->string('success_message')->nullable()->default(null);

            $table->unsignedInteger('success_page_id')->nullable()->default(null);
            $table->foreign('success_page_id')->references('id')->on('pages')
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
        Schema::dropIfExists('module_form_localisation');
    }
}
