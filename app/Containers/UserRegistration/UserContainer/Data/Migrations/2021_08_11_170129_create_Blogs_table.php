<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBlogsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('blogs_table', function (Blueprint $table) {
            
            $table->id();
            $table->string('name');
            $table->string('price');
            $table->string('country');
            $table->longText('description');
            $table->integer('rating');
            $table->longText('image');
            $table->unsignedInteger('admin_id');
            $table->foreign('admin_id')->references('id')->on('admin_table');
            $table->timestamps();
            //$table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blogs_table');
    }
}
