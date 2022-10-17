<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnalyticsTable extends Migration
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
CREATE PROCEDURE countLeads(IN p1 DATE, IN p2 DATE)
BEGIN
SELECT
	users.id,
    users.firstname,
    users.lastname,
    COUNT(*) AS CountLeads,
    COUNT(IF(leads.isQualityLead='1', 1, null)) as CountQualityLeads, /*кол-во качественных лидов*/
    COUNT(IF(leads.isQualityLead='1' AND leads.is_add_sale='1', 1, null)) as CountQualitySaleLeads, /*кол-во качественных лидов, которые являются доп продажей*/
    COUNT(IF(leads.isQualityLead='0', 1, null)) as CountNotQualityLeads, /*кол-во не качественных лидов*/
    COUNT(IF(leads.isQualityLead='0' AND leads.is_add_sale='1', 1, null)) as CountNotQualitySaleLeads /*кол-во не качественных лидов, которые являются доп продажей*/
FROM
    leads
LEFT JOIN users ON(users.id = leads.user_id)
WHERE leads.created_at >= p1 AND leads.created_at <= p2  AND leads.status_id = '3'
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
        Schema::dropIfExists('countLeads(IN p1 DATE, IN p2 DATE)');
    }
}
