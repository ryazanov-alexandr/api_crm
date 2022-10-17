<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeadCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lead_comments', function (Blueprint $table) {
            $table->id();

            $table->text('text');//текст комментария

            $table->bigInteger('user_id')->unsigned()->nullable();//кто добавил
            $table->foreign('user_id')->references('id')->on('users');

            $table->bigInteger('lead_id')->unsigned()->nullable();
            $table->foreign('lead_id')->references('id')->on('leads');

            $table->bigInteger('status_id')->unsigned()->nullable();//статус лида
            $table->foreign('status_id')->references('id')->on('statuses');

            $table->text('comment_value')->nullable();//комментарий от конкретного юзера

            $table->boolean('is_event')->default(0);//комментарий системный или добавлен юзером

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
        Schema::dropIfExists('lead_comments');
    }
}
