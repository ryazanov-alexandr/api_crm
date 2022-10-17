<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class createResponsiblesAnalytic extends Migration
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
CREATE PROCEDURE responsiblesAnalytic(IN p1 DATE, IN p2 DATE)
BEGIN
SELECT
	users.id,
    users.firstname,
    users.lastname,
    COUNT(*) AS CountLeads
FROM
    leads
LEFT JOIN users ON(users.id = leads.responsible_id)
WHERE leads.created_at >= p1 AND leads.created_at <= p2 AND leads.status_id BETWEEN 1 AND 2
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
        Schema::dropIfExists('responsiblesAnalytic(IN p1 DATE, IN p2 DATE)');
    }
}
