<?php

Route::group(['prefix' => 'priorities', 'middleware' => []], function () {
    Route::get('/', 'Api\PriorityController@index')->name('api.priorities.index');
    Route::get('/{priority}', 'Api\PriorityController@show')->name('api.priorities.read');
});
