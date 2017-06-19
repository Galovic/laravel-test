<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMenuItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menu_items', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('language_id');
            $table->foreign('language_id')->references('id')->on('languages')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->unsignedInteger('menu_id');
            $table->foreign('menu_id')->references('id')->on('menu')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->string('name');

            $table->string('url')->nullable()->default(null);

            $table->string('class')->nullable()->default(null);

            $table->boolean('open_new_window')->default(false);

            $table->unsignedInteger('order')->default(0);

            $table->unsignedInteger('page_id')->nullable()->default(null);
            $table->foreign('page_id')->references('id')->on('pages')
                ->onDelete('cascade');

            $table->unsignedInteger('category_id')->nullable()->default(null);
            $table->foreign('category_id')->references('id')->on('categories')
                ->onDelete('cascade');

            $table->unsignedInteger('parent_id')->nullable()->default(null);
            $table->foreign('parent_id')->references('id')->on('menu_items')
                ->onUpdate('cascade')->onDelete('cascade');

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
        Schema::dropIfExists('menu_items');
    }
}
