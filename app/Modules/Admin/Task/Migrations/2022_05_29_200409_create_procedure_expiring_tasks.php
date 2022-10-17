<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProcedureExpiringTasks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $str = <<<EOD
CREATE PROCEDURE countUserTasksExpiring(IN user_id INT)
BEGIN
SELECT COUNT(id) as CountTasks from tasks where (now() BETWEEN DATE_SUB(due_date, INTERVAL 2 HOUR) and due_date) and is_complete = 0 and responsible_id = 'user_id';

END

EOD;

        \Illuminate\Support\Facades\DB::unprepared($str);//вызываем на исполнение неподготовленный sql запрос
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('countUserTasksExpiring(IN user_id INT)');
    }
}
