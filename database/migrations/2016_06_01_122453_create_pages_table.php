<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('language_id');
            $table->foreign('language_id')->references('id')->on('languages')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->string('name');

            $table->integer('type');

            $table->string('view')->nullable()->default(null);

            $table->string('image')->nullable()->default(null);

            $table->boolean('published')->default(false);

            $table->boolean('listed')->default(true);

            $table->string('url')->nullable()->default(null);

            $table->string('seo_title')->nullable()->default(null);
            $table->text('seo_keywords')->nullable()->default(null);
            $table->text('seo_description')->nullable()->default(null);

            $table->dateTime('published_at')->nullable()->default(null);
            $table->dateTime('unpublished_at')->nullable()->default(null);

            $table->boolean('seo_index')->default(true);
            $table->boolean('seo_follow')->default(true);
            $table->boolean('seo_sitemap')->default(true);

            $table->boolean('is_homepage')->default(false);

            $table->string('og_title')->nullable()->default(null);
            $table->string('og_type')->nullable()->default(null);
            $table->string('og_image')->nullable()->default(null);
            $table->string('og_url')->nullable()->default(null);
            $table->string('og_description')->nullable()->default(null);

            $table->text('content')->nullable()->default(null);

            $table->unsignedInteger('parent_id')->nullable()->default(null);
            $table->foreign('parent_id')->references('id')->on('pages')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->integer('lft')->nullable()->default(null);
            $table->integer('rgt')->nullable()->default(null);
            $table->integer('depth')->nullable()->default(null);

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
        Schema::dropIfExists('pages');
    }
}
