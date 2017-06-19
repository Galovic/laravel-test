<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCareersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('module_careers', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('language_id');
            $table->foreign('language_id')->references('id')->on('languages')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->string('title');

            $table->tinyInteger('status')->default(1)->unsigned();

            $table->string('url');

            $table->string('salary')->nullable()->default(null);
            $table->string('bound')->nullable()->default(null);

            $table->text('perex');

            $table->text('offerings')->nullable()->default(null);
            $table->text('requirements')->nullable()->default(null);

            $table->string('seo_title')->nullable()->default(null);
            $table->text('seo_description')->nullable()->default(null);
            $table->text('seo_keywords')->nullable()->default(null);

            $table->string('image')->nullable()->default(null);

            $table->unsignedInteger('sort')->default(0);
            $table->unsignedInteger('views')->default(0);

            $table->unsignedInteger('user_id')->nullable()->default(null);
            $table->foreign('user_id')->references('id')->on('users')
                ->onDelete('set null');

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
        Schema::dropIfExists('module_careers');
    }
}
