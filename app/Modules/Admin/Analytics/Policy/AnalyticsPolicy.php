<?php

namespace App\Modules\Admin\Analytics\Policy;

use App\Modules\Admin\User\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

trait AnalyticsPolicy
{
    public function viewAnalytic(User $user) {
        return $user->canDo(['SUPER_ADMINISTRATOR', 'ANALYTICS_ACCESS']);
    }
}
