<?php

namespace App\Modules\Pub\Analytics\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Admin\Analytics\Export\LeadsExport;
use App\Modules\Admin\User\Models\User;
use Excel;

class AnalyticsController extends Controller
{
    public function export(User $user, $dateStart, $dateEnd) {
        $export = new LeadsExport($user, $dateStart, $dateEnd);
        return Excel::download($export,'leads.xlsx');
    }
}
