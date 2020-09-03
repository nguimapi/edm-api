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
            $table->string('code')->nullable();
            $table->string('type')->nullable();
            $table->string('original_type')->nullable();
            $table->string('relative_path')->nullable();
            $table->string('size')->nullable();
            $table->string('path')->nullable();
            $table->string('batch')->nullable();
            $table->boolean('is_folder')->default(0);
            $table->boolean('is_confirmed')->default(0);
            $table->boolean('is_archived')->default(0);
            $table->boolean('is_trashed')->default(0);
            $table->timestamp('consulted_at')->nullable();
            $table->timestamp('archived_at')->nullable();
            $table->timestamp('trashed_at')->nullable();
            $table->foreign('folder_id')->references('id')->on('files');
            $table->foreign('user_id')->references('id')->on('users');
            $table->timestamps();
			$table->softDeletes();
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
