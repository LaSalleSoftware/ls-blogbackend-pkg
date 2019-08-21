<?php

/**
 * This file is part of the Lasalle Software blog back-end package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright  (c) 2019 The South LaSalle Trading Corporation
 * @license    http://opensource.org/licenses/MIT
 * @author     Bob Bloom
 * @email      bob.bloom@lasallesoftware.ca
 * @link       https://lasallesoftware.ca
 * @link       https://packagist.org/packages/lasallesoftware/lsv2-blogbackend-pkg
 * @link       https://github.com/LaSalleSoftware/lsv2-blogbackend-pkg
 *
 */

/*
|--------------------------------------------------------------------------
| Blog Routes
|--------------------------------------------------------------------------
|
| From php artisan make:en
|
| https://github.com/laravel/framework/blob/f769989694cdcb77e53fbe36d7a47cd06371998c/src/Illuminate/Routing/Router.php#L1178
|
*/

//Route::get('/api/v1/singlearticleblog', 'Lasallesoftware\Blogbackend\Http\Controllers\SinglePostController@ShowSinglePost');

/*
Route::group(['middleware' => ['v1/api']], function () {
    Route::get('/singlearticleblog', 'Lasallesoftware\Blogbackend\Http\Controllers\SinglePostController@ShowSinglePost');
});
*/

/*
Route::group(['middleware' => ['jwt_auth']], function () {
    Route::get('/api/v1/testapi', 'Lasallesoftware\Blogbackend\Http\Controllers\TestAPIController@Index');
});
*/


Route::middleware(['jwt_auth'])
    ->group(function () {
        Route::get('/api/v1/testapi',           'Lasallesoftware\Blogbackend\Http\Controllers\TestAPIController@Index');
        Route::get('/api/v1/singlearticleblog', 'Lasallesoftware\Blogbackend\Http\Controllers\SinglePostController@ShowSinglePost');
});


//Route::get('/api/v1/testapi',           'Lasallesoftware\Blogbackend\Http\Controllers\TestAPIController@Index');

