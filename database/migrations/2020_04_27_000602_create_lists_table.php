<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lists', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('status') ->default(1);

            $table->unsignedBigInteger('user_id')->default(1);
            $table->foreign('user_id')->references('id')->on('users');

            $table->string('C1')->default(0);
            $table->string('C2')->default(0);
            $table->string('C3')->default(0);
            $table->string('C4')->default(0);
            $table->string('C5')->default(0);
            $table->string('C6')->default(0);
            $table->string('C7')->default(0);
            $table->string('C8')->default(0);
            $table->string('C9')->default(0);
            $table->string('C10')->default(0);
            $table->string('C11')->default(0);
            $table->string('C12')->default(0);
            $table->string('C13')->default(0);
            $table->string('C14')->default(0); 
            $table->string('C15')->default(0); 
            $table->string('C16')->default(0); 
            $table->string('C17')->default(0); 
            $table->string('C18')->default(0); 
            $table->string('C19')->default(0); 
            $table->string('C20')->default(0); 
            $table->string('C21')->default(0); 
            $table->string('C22')->default(0); 
            $table->string('C23')->default(0); 
            $table->string('C24')->default(0); 
            $table->string('C25')->default(0); 
            $table->string('C26')->default(0); 
            $table->string('C27')->default(0); 
            $table->string('C28')->default(0); 
            $table->string('C29')->default(0); 
            $table->string('C30')->default(0); 
            $table->string('C31')->default(0); 
            $table->string('C32')->default(0); 
            $table->string('C33')->default(0); 
            $table->string('C34')->default(0); 
            $table->string('C35')->default(0); 
            $table->string('C36')->default(0); 
            $table->string('C37')->default(0); 
            $table->string('C38')->default(0); 
            $table->string('C39')->default(0); 
            $table->string('C40')->default(0); 
            
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
        Schema::dropIfExists('lists');
    }
}