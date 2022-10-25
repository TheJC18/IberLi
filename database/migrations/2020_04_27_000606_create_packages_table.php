<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->Bigincrements('id');
            $table->string('code');
            $table->integer('column')->default(0);
            $table->string('box', 10)->default(0);
            $table->integer('location')->default(0);
            $table->string('type', 3)->default(0);
            $table->float('kg')->default(0);
            $table->string('route')->default(0);
            $table->string('list_id', 10)->default(0);
            $table->integer('status')->default(1);
            
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
        Schema::dropIfExists('packages');
    }
}