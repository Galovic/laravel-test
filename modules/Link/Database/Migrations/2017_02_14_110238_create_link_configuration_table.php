<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLinkConfigurationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('module_link_configurations', function (Blueprint $table) {
            $table->increments('id');

            $table->string('text');

            $table->string('model')->nullable()->default(null);
            $table->unsignedInteger('model_id')->nullable()->default(null);

            $table->string('url')->nullable()->default(null);

            $table->string('view')->nullable()->default(null);

            $table->text('tags')->nullable()->default(null);

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
        Schema::dropIfExists('module_link_configurations');
    }
}
