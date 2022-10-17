<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProcedureCountQualityLeads extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //создаем процедуру которая будет храниться на сервере бд
        //перекладываем больше логики на сервер бд mysql
        $str = <<<EOD
CREATE PROCEDURE getCountQualityLeads(IN p1 DATE, IN p2 DATE)
BEGIN
SELECT
	users.id,
    users.firstname,
    users.lastname,
    COUNT(IF(leads.isQualityLead='1' and leads.status_id = '3', 1, null)) as CountLeads
FROM
    leads
LEFT JOIN users ON(users.id = leads.responsible_id)
WHERE leads.updated_at >= p1 AND leads.updated_at <= p2
GROUP BY users.id, users.firstname, users.lastname;

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
        Schema::dropIfExists('getCountQualityLeads(IN p1 DATE, IN p2 DATE)');
    }
}
