<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArticlePhotos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('article_photos', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('article_id')->nullable()->default(null);
            $table->foreign('article_id')->references('id')->on('articles')
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
        Schema::dropIfExists('article_photos');
    }
}
