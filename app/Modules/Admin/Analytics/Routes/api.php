<?php

Route::group(['prefix' => 'analytics', 'middleware' => []], function () {
    Route::post('/', 'Api\AnalyticsController@index')->name('api.analytics.store');
    Route::post('/sources', 'Api\AnalyticsController@sourcesAnalytic')->name('api.sources.analytics');
    Route::post('/leads', 'Api\AnalyticsController@leadsAnalytic')->name('api.leads.analytics');
    Route::post('/responsibles', 'Api\AnalyticsController@responsiblesAnalytic')->name('api.responsibles.analytics');
    Route::post('/count/quality/leads', 'Api\AnalyticsController@countQualityLeads')->name('api.count.quality.leads');
});
