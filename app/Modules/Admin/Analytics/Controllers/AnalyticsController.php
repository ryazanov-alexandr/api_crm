<?php

namespace App\Modules\Admin\Analytics\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Admin\Analytics\Export\LeadsExport;
use App\Modules\Admin\User\Models\User;
use Excel;

class AnalyticsController extends Controller
{
    public function export(User $user, $dateStart = null, $dateEnd = null) {
        $export = new LeadsExport($user, $dateStart, $dateEnd);
        return Excel::download($export, 'leads.xlsx');
    }
}
