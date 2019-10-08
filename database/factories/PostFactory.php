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



/* =========================================================================
   My custom model events will interfere with the personbydomain_id field.
   Model::unsetEventDispatcher(); "turns off" model events.
   ========================================================================= */


// LaSalle Software
use Lasallesoftware\Library\Common\Models\CommonModel;

// Laravel class
use Illuminate\Support\Str;

// Third party class
use Faker\Generator as Faker;
use Carbon\CarbonImmutable;


$factory->define(Lasallesoftware\Blogbackend\Models\Post::class, function (Faker $faker) {

    $title   = CommonModel::deepWashText(ucwords($faker->realText(100)));
    $title   = CommonModel::stripCharactersFromText1($title);
    $content = CommonModel::washContent($faker->realText(5555));
    $now     = CarbonImmutable::now();

    return [
        'installed_domain_id' => $faker->numberBetween($min = 1, $max = 5),
        'personbydomain_id'   => $faker->numberBetween($min = 1, $max = 5),
        'category_id'         => '1',
        'title'               => $title,
        'slug'                => CommonModel::makeSlug(null, $title, 'posts', 0),
        'content'             => $content,
        'excerpt'             => CommonModel::makeExcerpt(null, $content),
        'meta_description'    => CommonModel::makeMetadescription(null, $content),
        'featured_image_upload' => null,
        'featured_image_code' => null,
        'featured_image_external_file' => 'https://unsplash.com/photos/V5Z4xV7WnEE',
        'enabled'             => 1,
        'publish_on'          => $now,
        'uuid'                => (string)Str::uuid(),
        'created_at'          => $now,
        'created_by'          => 1,
        'updated_at'          => $now,
        'updated_by'          => 1,
        'locked_at'           => null,
        'locked_by'           => null,
    ];
});

