<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('services', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('language_id');
            $table->foreign('language_id')->references('id')->on('languages')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->string('title');

            $table->tinyInteger('status')->default(1)->unsigned();

            $table->string('url');

            $table->text('perex');
            $table->text('text')->nullable()->default(null);

            $table->string('seo_title')->nullable()->default(null);
            $table->text('seo_description')->nullable()->default(null);
            $table->text('seo_keywords')->nullable()->default(null);

            $table->string('image')->nullable()->default(null);
            $table->string('thumbnail')->nullable()->default(null);

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
        Schema::dropIfExists('services');
    }
}
