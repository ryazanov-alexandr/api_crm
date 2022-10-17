<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();

            $table->string('title')->nullable();//ссылка, телефон, чтобы юзер что-то с ними сделал, прозвонил и тд
            $table->string('description')->nullable();

            $table->bigInteger('author_id')->unsigned()->nullable();//кто создал задачу
            $table->bigInteger('responsible_id')->unsigned()->nullable();//кто должен выполнить
            $table->bigInteger('priority_id')->unsigned()->nullable();//приоритет задачи
            $table->bigInteger('lead_id')->unsigned()->nullable();

            $table->foreign('author_id')->references('id')->on('users');
            $table->foreign('responsible_id')->references('id')->on('users');
            $table->foreign('priority_id')->references('id')->on('priorities');
            $table->foreign('lead_id')->references('id')->on('leads');

            $table->string('time_to_complete');
            $table->timestamp('due_date');
            $table->boolean('is_complete')->default(false);

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
        Schema::dropIfExists('tasks');
    }
}
