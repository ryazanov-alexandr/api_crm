<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();

            $table->string('title')->nullable();
            $table->string('phone')->nullable();
            $table->string('link')->nullable();
            //чтобы посмотреть историю лида
            $table->integer('count_create')->default(1);//счётчик (сколько раз обратился в компанию)

            //$table->boolean('is_processed')->default(false);//обработан лид или нет
            $table->boolean('isQualityLead')->default(false);//был ли лид успешным, завершена ли сделка
            $table->boolean('is_express_delivery')->default(false);//экспресс доставка или нет
            $table->boolean('is_add_sale')->default(false);//была ли доп продажа

            $table->bigInteger('source_id')->nullable()->unsigned();//ссылка на источник лида
            $table->foreign('source_id')->references('id')->on('sources')->onDelete('cascade');
            $table->bigInteger('unit_id')->nullable()->unsigned();//ссылка на подразделение
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('cascade');

            $table->bigInteger('user_id')->nullable()->unsigned();//ссылка на пользователя, который создал лид
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->bigInteger('status_id')->nullable()->unsigned();//id статуса лида
            $table->foreign('status_id')->references('id')->on('statuses')->onDelete('cascade');

            $table->bigInteger('responsible_id')->unsigned()->nullable();//кто должен выполнить
            $table->foreign('responsible_id')->references('id')->on('users');

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
        Schema::dropIfExists('leads');
    }
}
