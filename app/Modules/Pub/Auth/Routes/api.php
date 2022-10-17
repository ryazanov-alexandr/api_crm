<?php

Route::group(['prefix' => 'auths', 'middleware' => []], function () {
    Route::post('/login', 'Api\LoginController@login')->name('api.auths.login');

    Route::get('/logout', 'Api\LoginController@logout')->name('api.auths.logout');
});
