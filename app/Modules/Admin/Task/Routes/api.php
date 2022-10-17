<?php

Route::group(['prefix' => 'tasks', 'middleware' => ['auth:api']], function () {
    Route::get('/', 'Api\TasksController@index')->name('api.tasks.index');
    Route::post('/', 'Api\TasksController@store')->name('api.tasks.store');
    Route::get('/{task}', 'Api\TasksController@show')->name('api.tasks.read');

    Route::get('/userTasks/{user}', 'Api\TasksController@tasksByUser')->name('api.tasksByUser');
    Route::get('/userTasks/count/index', 'Api\TasksController@countUserTasks')->name('api.count.user.tasks');
    Route::get('/userTasks/count/expiring', 'Api\TasksController@countUserTasksExpiring')->name('api.count.user.tasks.expiring');

    Route::get('/recTasks/index/{user}', 'Api\TasksController@recomendedTasks')->name('api.recTasks.index');
    Route::get('/todayTasks/index', 'Api\TasksController@todayTasks')->name('api.todayTasks.index');
    Route::get('/tomorrowTasks/index', 'Api\TasksController@tomorrowTasks')->name('api.tomorrowTasks.index');
    Route::get('/upcomingTasks/index', 'Api\TasksController@upcomingTasks')->name('api.upcomingTasks.index');
    Route::get('/expiredTasks/index', 'Api\TasksController@expiredTasks')->name('api.expiredTasks.index');
    Route::get('/completeTasks/index', 'Api\TasksController@completeTasks')->name('api.completeTasks.index');

    Route::get('/priorityId/{priority_id}', 'Api\TasksController@tasksByPriorityId')->name('api.tasksByPriorityId');

    Route::get('/archive/index', 'Api\TasksController@archive')->name('tasks.archive.index');

    Route::get('/history/{task}', 'Api\TasksController@comments')->name('api.tasks.comments');

});
