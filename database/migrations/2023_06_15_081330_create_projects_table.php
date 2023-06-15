<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('movie_projects', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255);
            $table->string('description', 500)->nullable();
            $table->string('duration', 255)->nullable();
            $table->string('artists', 255)->nullable();
            $table->string('genres', 255)->nullable();
            $table->string('watch_url', 500)->nullable();
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
        Schema::dropIfExists('movie_projects');
    }
}
