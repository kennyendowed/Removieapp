<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->bigInteger('pin')->default(0);
            $table->string('phone');
            $table->string('status');
            $table->string('avater')->nullable();
            $table->string('address')->nullable();
            $table->string('email_code')->nullable();
            $table->string('email_time')->nullable();
            $table->bigInteger('email_verify')->default(0);
            $table->tinyInteger('is_permission');
            $table->ipAddress('ip_address');
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
