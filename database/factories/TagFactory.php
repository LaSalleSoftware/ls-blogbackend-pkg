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
 * @copyright  (c) 2019-2022 The South LaSalle Trading Corporation
 * @license    http://opensource.org/licenses/MIT
 * @author     Bob Bloom
 * @email      bob.bloom@lasallesoftware.ca
 * @link       https://lasallesoftware.ca
 * @link       https://packagist.org/packages/lasallesoftware/ls-blogbackend-pkg
 * @link       https://github.com/LaSalleSoftware/ls-blogbackend-pkg
 *
 */

// LaSalle Software
use Lasallesoftware\Librarybackend\Common\Models\CommonModel;

// Laravel class
use Illuminate\Support\Str;

// Third party class
use Faker\Generator as Faker;
use Carbon\CarbonImmutable;

$factory->define(Lasallesoftware\Blogbackend\Models\Tag::class, function (Faker $faker) {

    $title = CommonModel::deepWashText(ucwords($faker->realText(25)));
    $title = CommonModel::stripCharactersFromText1($title);

    return [
        'installed_domain_id' => $faker->numberBetween($min = 1, $max = 5),
        'title'               => $title,
        'description'         => CommonModel::washContent($faker->realText(255)),
        'enabled'             => 1,
        'uuid'                => (string)Str::uuid(),
        'created_at'          => CarbonImmutable::now(),
        'created_by'          => 1,
        'updated_at'          => null,
        'updated_by'          => null,
        'locked_at'           => null,
        'locked_by'           => null,
    ];
});

