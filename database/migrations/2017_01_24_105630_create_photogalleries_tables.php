<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePhotogalleriesTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('photogalleries', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('language_id');
            $table->foreign('language_id')->references('id')->on('languages')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->string('title');

            $table->string('url');

            $table->text('text')->nullable()->default(null);

            $table->string('seo_title')->nullable()->default(null);
            $table->text('seo_description')->nullable()->default(null);
            $table->text('seo_keywords')->nullable()->default(null);

            $table->unsignedInteger('views')->default(0);
            $table->unsignedInteger('sort')->default(0);

            $table->dateTime('publish_at');
            $table->dateTime('unpublish_at')->nullable()->default(null);

            $table->unsignedInteger('user_id')->nullable()->default(null);
            $table->foreign('user_id')->references('id')->on('users')
                ->onDelete('set null');

            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('photogallery_photos', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('photogallery_id')->nullable()->default(null);
            $table->foreign('photogallery_id')->references('id')->on('photogalleries')
                ->onDelete('cascade');

            $table->string('title')->nullable()->default(null);
            $table->string('author')->nullable()->default(null);

            $table->string('image');

            $table->string('type', 10);

            $table->double('size');

            $table->unsignedInteger('sort')->default(0);

            $table->string('temporary_id')->nullable()->default(null);

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
        Schema::dropIfExists('photogallery_photos');
        Schema::dropIfExists('photogalleries');
    }
}
