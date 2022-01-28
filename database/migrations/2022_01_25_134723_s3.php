<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class S3 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
  /*  public function up()
    {
        Schema::create('tags_activities', function (Blueprint $table) {
            $table->id('id');
            $table->string('activity_id');
            $table->foreign('activity_id')
                ->references('id')
                ->on('activities');
            $table->string('tag_id');
            $table->foreign('tag_id')
                ->references('id')
                ->on('tags');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    /*public function down()
    {
        Schema::dropIfExists('tags_activities');
    }*/
}
