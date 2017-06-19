<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('language_id');
            $table->foreign('language_id')->references('id')->on('languages')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->string('name');
            $table->string('url');

            $table->string('flag', 10);

            $table->string('seo_title')->nullable()->default(null);

            $table->text('seo_description')->nullable()->default(null);
            $table->text('seo_keywords')->nullable()->default(null);

            $table->unsignedInteger('parent_id')->nullable()->default(null);
            $table->foreign('parent_id')->references('id')->on('categories')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->integer('lft')->nullable()->default(null);
            $table->integer('rgt')->nullable()->default(null);
            $table->integer('depth')->nullable()->default(null);

            $table->tinyInteger('show')->default(0)->unsigned();

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
        Schema::dropIfExists('categories');
    }
}