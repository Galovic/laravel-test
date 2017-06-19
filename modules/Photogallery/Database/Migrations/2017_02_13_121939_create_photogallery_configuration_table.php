<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePhotogalleryConfigurationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('module_photogallery_configurations', function (Blueprint $table) {
            $table->increments('id');

            $table->text('view');

            $table->unsignedInteger('photogallery_id');
            $table->foreign('photogallery_id')->references('id')->on('photogalleries')
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
        Schema::dropIfExists('module_photogallery_configurations');
    }
}
