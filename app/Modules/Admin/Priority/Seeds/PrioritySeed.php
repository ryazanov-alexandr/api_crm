<?php

namespace App\Modules\Admin\Priority\Seeds;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PrioritySeed extends Seeder
{
    public function run() {
        DB::table('priorities')->insert([
            [
                'title' => 'Срочный',
                'color' => '#A31012',
            ],
            [
                'title' => 'Высокий',
                'color' => '#f55306',
            ],
            [
                'title' => 'Средний',
                'color' => '#050394',
            ],
            [
                'title' => 'Низкий',
                'color' => '#fff',
            ]
        ]);
    }
}
