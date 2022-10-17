<?php

Route::group(['prefix' => 'analytics', 'middleware' => []], function () {
    Route::get('/export/{user}/{dateStart}/{dateEnd}', 'AnalyticsController@export')->name('analytics.export');
});
