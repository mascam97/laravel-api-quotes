<?php

use Domain\Users\Enums\SexEnum;
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
            $table->id();
            $table->string('name', 80);
            $table->enum('sex', [
                SexEnum::MASCULINE->value,
                SexEnum::FEMININE->value,
                SexEnum::NOT_APPLICABLE->value,
            ])->nullable();
            $table->date('birthday')->nullable();
            $table->string('email', 80)->unique();
            $table->string('locale', 5);
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
