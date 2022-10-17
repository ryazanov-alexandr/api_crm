<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class createProcedureLeadsAnalytics extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $str = <<<EOD
CREATE PROCEDURE leadsAnalytic(IN p1 DATE, IN p2 DATE)
BEGIN
SELECT
    COUNT(*) AS CountLeads,
    COUNT(IF(leads.isQualityLead='1', 1, null)) as CountQualityLeads,
    COUNT(IF(leads.isQualityLead='1' AND leads.is_add_sale='1', 1, null)) as CountQualitySaleLeads,
    COUNT(IF(leads.isQualityLead='0', 1, null)) as CountNotQualityLeads,
    COUNT(IF(leads.isQualityLead='0' AND leads.is_add_sale='1', 1, null)) as CountNotQualitySaleLeads,
    COUNT(IF(leads.is_express_delivery='1', 1, null)) as CountExpressDelivery,
    COUNT(IF(leads.count_create > '1', 1, null)) as CountRepeatLeads
FROM
	leads
     WHERE leads.created_at >= p1 AND leads.created_at <= p2 AND leads.status_id ='3';

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
        Schema::dropIfExists('leadsAnalytic(IN p1 DATE, IN p2 DATE)');
    }
}
