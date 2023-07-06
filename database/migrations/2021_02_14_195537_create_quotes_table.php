<?php

use Domain\Quotes\States\Banned;
use Domain\Quotes\States\Drafted;
use Domain\Quotes\States\Published;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quotes', function (Blueprint $table) {
            $table->id();
            $table->string('title', 80)->unique();
            $table->text('content');
            $table->float('average_score')->nullable();
            $table->enum('state', [Drafted::$name, Published::$name, Banned::$name]);
            $table->foreignId('user_id')->constrained();
            $table->timestamps();

            $table->index('title');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('quotes');
    }
}
