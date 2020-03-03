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
 * @copyright  (c) 2019-2020 The South LaSalle Trading Corporation
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

// This route will populate the database with sample data
//Route::get('factorydata', 'Lasallesoftware\Blogbackend\Http\Controllers\FactoryController@Bob');

Route::middleware(['jwt_auth'], 'throttle:60,1')
    ->group(function () {
        Route::get('/api/v1/blogrssfeed',          'Lasallesoftware\Blogbackend\Http\Controllers\BlogRSSFeedController@BlogRSSFeed');
        Route::get('/api/v1/allblogposts',         'Lasallesoftware\Blogbackend\Http\Controllers\AllBlogPostsController@AllBlogPosts');
        Route::get('/api/v1/homepageblogposts',    'Lasallesoftware\Blogbackend\Http\Controllers\HomepageBlogPostsController@HomepageBlogPosts');
        Route::get('/api/v1/allcategoryblogposts', 'Lasallesoftware\Blogbackend\Http\Controllers\AllCategoryBlogPostsController@AllCategoryBlogPosts');
        Route::get('/api/v1/alltagblogposts',      'Lasallesoftware\Blogbackend\Http\Controllers\AllTagBlogPostsController@AllTagBlogPosts');
        Route::get('/api/v1/allauthorblogposts',   'Lasallesoftware\Blogbackend\Http\Controllers\AllAuthorBlogPostsController@AllAuthorBlogPosts');
        Route::get('/api/v1/singleblogpost',       'Lasallesoftware\Blogbackend\Http\Controllers\SingleBlogPostController@SingleBlogPost');
    }
);


