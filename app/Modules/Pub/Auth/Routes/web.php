<?php

Route::group(['prefix' => 'auths', 'middleware' => []], function () {
    Route::get('login', 'LoginController@showLoginForm')->name('login');
    Route::post('login', 'LoginController@login')->name('login.post');

    Route::get('logout', 'LoginController@logout')->name('logout');

});
