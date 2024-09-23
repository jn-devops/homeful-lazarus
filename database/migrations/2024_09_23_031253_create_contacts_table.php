<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('contacts', function (Blueprint $table) {
             $table->id();
             $table->string('reference_code')->unique();
             $table->string('first_name');
             $table->string('middle_name');
             $table->string('last_name');
             $table->string('civil_status');
             $table->string('sex');
             $table->string('nationality');
             $table->date('date_of_birth');
             $table->string('email');
             $table->string('mobile');
             $table->json('spouse');
             $table->json('addresses')->nullable();
             $table->json('employment')->nullable();
             $table->json('co_borrowers')->nullable();
             $table->json('order')->nullable();
             $table->timestamps();
        });
    }
};
