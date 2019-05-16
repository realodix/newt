<?php

Auth::routes();

Route::view('/', 'frontend.welcome');
Route::post('/create', 'UrlController@create')->name('createshortlink');
Route::post('/custom-link-avail-check', 'UrlController@checkExistingCustomUrl');

Route::namespace('Frontend')->group(function () {
    Route::get('/+{url_key}', 'UrlController@view')->name('short_url.stats');
    Route::get('/duplicate/{url_key}', 'UrlController@duplicate')->middleware('auth')->name('duplicate');
});

Route::namespace('Backend')->group(function () {
    Route::middleware('auth')->prefix('admin')->group(function () {
        // Dashboard (My URLs)
        Route::get('/', 'DashboardController@view')->name('admin');
        Route::get('/myurl/getdata', 'DashboardController@getData');
        Route::get('/delete/{url_hashId}', 'DashboardController@delete')->name('admin.delete');
        Route::get('/duplicate/{url_key}', 'DashboardController@duplicate')->name('admin.duplicate');

        // All URLs
        Route::get('/allurl', 'AllUrlController@index')->name('admin.allurl');
        Route::get('/allurl/getdata', 'AllUrlController@getData');
        Route::get('/allurl/delete/{url_hashId}', 'AllUrlController@delete')->name('admin.allurl.delete');

        // User
        Route::namespace('User')->prefix('user')->group(function () {
            Route::get('/', 'UserController@index')->name('user.index');
            Route::get('/user/getdata', 'UserController@getData');

            Route::get('{user}/edit', 'UserController@edit')->name('user.edit');
            Route::post('{user_hashId}/edit', 'UserController@update')->name('user.update');

            Route::get('{user}/changepassword', 'ChangePasswordController@view')->name('user.change-password');
            Route::post('{user_hashId}/changepassword', 'ChangePasswordController@update')->name('user.change-password.post');
        });
    });
});

Route::get('/{url_key}', 'UrlController@urlRedirection');
