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
 * @copyright  (c) 2019-2024 The South LaSalle Trading Corporation
 * @license    http://opensource.org/licenses/MIT
 * @author     Bob Bloom
 * @email      bob.bloom@lasallesoftware.ca
 * @link       https://lasallesoftware.ca
 * @link       https://packagist.org/packages/lasallesoftware/ls-blogbackend-pkg
 * @link       https://github.com/LaSalleSoftware/ls-blogbackend-pkg
 *
 */


/* =========================================================================
   My custom model events will interfere with the personbydomain_id field.
   Model::unsetEventDispatcher(); "turns off" model events.
   ========================================================================= */



// LaSalle Software
use Lasallesoftware\Librarybackend\Common\Models\CommonModel;

// Laravel class
use Illuminate\Support\Str;

// Third party class
use Faker\Generator as Faker;
use Carbon\CarbonImmutable;


$factory->define(Lasallesoftware\Blogbackend\Models\Postupdate::class, function (Faker $faker) {

    $title   = CommonModel::deepWashText(ucwords($faker->realText(25)));
    $title   = CommonModel::stripCharactersFromText1($title);
    $content = CommonModel::washContent($faker->realText(5555));
    $now     = CarbonImmutable::now();

    return [
        'installed_domain_id' => $faker->numberBetween($min = 1, $max = 5),
        'personbydomain_id'   => $faker->numberBetween($min = 1, $max = 5),
        'post_id'             => 1,
        'title'               => $title,
        'content'             => $content,
        'excerpt'             => CommonModel::makeExcerpt(null, $content),
        'enabled'             => 1,
        'publish_on'          => $now,
        'uuid'                => (string)Str::uuid(),
        'created_at'          => $now,
        'created_by'          => 1,
        'updated_at'          => null,
        'updated_by'          => null,
        'locked_at'           => null,
        'locked_by'           => null,
    ];
});

