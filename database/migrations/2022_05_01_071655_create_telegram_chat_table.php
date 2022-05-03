<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTelegramChatTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('telegram_chat', function (Blueprint $table) {
            $table->id();
            $table->integer('update_id');
            $table->integer('message_id')->nullable();
            $table->integer('chat_id')->nullable();
            $table->dateTime('date')->nullable();
            $table->dateTime('message_text')->nullable();
            $table->boolean('replied')->nullable();
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
        Schema::dropIfExists('telegram_chat');
    }
}
