<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('folder_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('name');
            $table->string('type')->nullable();
            $table->string('size')->nullable();
            $table->string('link')->nullable();
            $table->boolean('is_folder')->default(0);
            $table->boolean('is_archived')->default(0);
            $table->boolean('is_trashed')->default(0);
            $table->timestamp('consulted_at');
            $table->foreign('folder_id')->references('id')->on('files');
            $table->foreign('user_id')->references('id')->on('users');
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
        Schema::dropIfExists('files');
    }
}
