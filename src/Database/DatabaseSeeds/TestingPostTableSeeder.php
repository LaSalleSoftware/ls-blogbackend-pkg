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

namespace Lasallesoftware\Blogbackend\Database\DatabaseSeeds;

// LaSalle Software
use Lasallesoftware\Blogbackend\Models\Post;
use Lasallesoftware\Librarybackend\Database\DatabaseSeeds\BaseSeeder;

// Laravel Framework
use Illuminate\Support\Facades\DB;
//use Illuminate\Support\Carbon;

// Third party class
use Carbon\Carbonimmutable;
use Faker\Generator as Faker;

class TestingPostTableSeeder extends BaseSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Faker $faker)
    {
        $now = CarbonImmutable::now();

        if ($this->doPopulateWithTestData()) {

            // note that we are, indeed, populating the "updated" fields, because we added a model event to do so
            // on creation, for the purpose of sorting the post drop-down in the postupdate create/update forms.

            // installed_domain_id = 1
            Post::firstOrCreate(
                ['slug' => 'biography-of-blues-boy-king-on-domain-1'],
                [
                    'installed_domain_id' => 1,
                    'personbydomain_id'   => 1,
                    'category_id'         => 1,
                    'title'               => 'Biography of Blues Boy King on Domain 1',
                    'content'             => 'the author is Bob Bloom, personbydomain_id = 1',
                    'excerpt'             => $faker->words(12, true),
                    'meta_description'    => $faker->words(12, true),
                    'featured_image_upload' => null,
                    'featured_image_code' => "data:image/jpeg;base64,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",
                    'featured_image_external_file' => null,
                    'enabled'             => 1,
                    'publish_on'          => $now->subDays(9),
                    'uuid'                => 'created_during_initial_seeding',
                    'created_at'          => $now->subDays(9),
                    'created_by'          => 1,
                    'updated_at'          => $now->subDays(9),
                    'updated_by'          => 1,
                    'locked_at'           => null,
                    'locked_by'           => null,
                ]
            );

            Post::firstOrCreate(
                ['slug' => 'biography-of-robert-johnson-on-domain-1',],
                [
                    'installed_domain_id' => 1,
                    'personbydomain_id'   => 4,
                    'category_id'         => 2,
                    'title'               => 'Biography of Robert Johnson on Domain 1',
                    'content'             => 'the author is Sidney Bechet, personbydomain_id = 4',
                    'excerpt'             => $faker->words(12, true),
                    'meta_description'    => $faker->words(12, true),
                    'featured_image_upload' => null,
                    'featured_image_code' => null,
                    'featured_image_external_file' => null,
                    'enabled'             => 1,
                    'publish_on'          => $now->subDays(8),
                    'uuid'                => 'created_during_initial_seeding',
                    'created_at'          => $now->subDays(8),
                    'created_by'          => 1,
                    'updated_at'          => $now->subDays(8),
                    'updated_by'          => 1,
                    'locked_at'           => null,
                    'locked_by'           => null,
                ]
            );

            Post::firstOrCreate(
                ['slug' => 'biography-of-stevie-ray-vaughan-on-domain-1',],
                [
                    'installed_domain_id' => 1,
                    'personbydomain_id'   => 5,
                    'category_id'         => 2,
                    'title'               => 'Biography of Stevie Ray Vaughan on Domain 1',
                    'content'             => 'the author is Robert Johnson, personbydomain_id = 5',
                    'excerpt'             => $faker->words(12, true),
                    'meta_description'    => $faker->words(12, true),
                    'featured_image_upload' => null,
                    'featured_image_code' => null,
                    'featured_image_external_file' => null,
                    'enabled'             => 1,
                    'publish_on'          => $now->subDays(7),
                    'uuid'                => 'created_during_initial_seeding',
                    'created_at'          => $now->subDays(7),
                    'created_by'          => 1,
                    'updated_at'          => $now->subDays(7),
                    'updated_by'          => 1,
                    'locked_at'           => null,
                    'locked_by'           => null,
                ]
            );


            // installed_domain_id = 2
            Post::firstOrCreate(
                ['slug' => 'biography-of-blues-boy-king-on-domain-2',],
                [
                    'installed_domain_id' => 2,
                    'personbydomain_id'   => 1,
                    'category_id'         => 1,
                    'title'               => 'Biography of Blues Boy King on Domain 2',
                    'content'             => 'the author is Bob Bloom, personbydomain_id = 1',
                    'excerpt'             => $faker->words(12, true),
                    'meta_description'    => $faker->words(12, true),
                    'featured_image_upload' => null,
                    'featured_image_code' => "data:image/jpeg;base64,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",
                    'featured_image_external_file' => null,
                    'enabled'             => 1,
                    'publish_on'          => $now->subDays(6),
                    'uuid'                => 'created_during_initial_seeding',
                    'created_at'          => $now->subDays(6),
                    'created_by'          => 1,
                    'updated_at'          => $now->subDays(6),
                    'updated_by'          => 1,
                    'locked_at'           => null,
                    'locked_by'           => null,
                ]
            );

            Post::firstOrCreate(
                ['slug' => 'biography-of-robert-johnson-on-domain-2',],
                [
                    'installed_domain_id' => 2,
                    'personbydomain_id'   => 1,
                    'category_id'         => 2,
                    'title'               => 'Biography of Robert Johnson on Domain 2',
                    'content'             => 'the author is Bob Bloom, personbydomain_id = 1',
                    'excerpt'             => $faker->words(12, true),
                    'meta_description'    => $faker->words(12, true),
                    'featured_image_upload' => null,
                    'featured_image_code' => null,
                    'featured_image_external_file' => null,
                    'enabled'             => 1,
                    'publish_on'          => $now->subDays(5),
                    'uuid'                => 'created_during_initial_seeding',
                    'created_at'          => $now->subDays(5),
                    'created_by'          => 1,
                    'updated_at'          => $now->subDays(5),
                    'updated_by'          => 1,
                    'locked_at'           => null,
                    'locked_by'           => null,
                ]
            );

            Post::firstOrCreate(
                ['slug' => 'biography-of-stevie-ray-vaughan-on-domain-2',],
                [
                    'installed_domain_id' => 2,
                    'personbydomain_id'   => 1,
                    'category_id'         => 2,
                    'title'               => 'Biography of Stevie Ray Vaughan on Domain 2',
                    'content'             => 'the author is Bob Bloom, personbydomain_id = 1',
                    'excerpt'             => $faker->words(12, true),
                    'meta_description'    => $faker->words(12, true),
                    'featured_image_upload' => null,
                    'featured_image_code' => null,
                    'featured_image_external_file' => null,
                    'enabled'             => 1,
                    'publish_on'          => $now->subDays(4),
                    'uuid'                => 'created_during_initial_seeding',
                    'created_at'          => $now->subDays(4),
                    'created_by'          => 1,
                    'updated_at'          => $now->subDays(4),
                    'updated_by'          => 1,
                    'locked_at'           => null,
                    'locked_by'           => null,
                ]
            );

            // installed_domain_id = 3
            Post::firstOrCreate(
                ['slug' => 'biography-of-blues-boy-king-on-domain-3',],
                [
                    'installed_domain_id' => 3,
                    'personbydomain_id'   => 1,
                    'category_id'         => 1,
                    'title'               => 'Biography of Blues Boy King on Domain 3',
                    'content'             => 'the author is Bob Bloom, personbydomain_id = 1',
                    'excerpt'             => $faker->words(12, true),
                    'meta_description'    => $faker->words(12, true),
                    'featured_image_upload' => null,
                    'featured_image_code' => "data:image/jpeg;base64,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",
                    'featured_image_external_file' => null,
                    'enabled'             => 1,
                    'publish_on'          => $now->subDays(3),
                    'uuid'                => 'created_during_initial_seeding',
                    'created_at'          => $now->subDays(3),
                    'created_by'          => 1,
                    'updated_at'          => $now->subDays(3),
                    'updated_by'          => 1,
                    'locked_at'           => null,
                    'locked_by'           => null,
                ]
            );

            Post::firstOrCreate(
                ['slug' => 'biography-of-robert-johnson-on-domain-3',],
                [
                    'installed_domain_id' => 3,
                    'personbydomain_id'   => 1,
                    'category_id'         => 2,
                    'title'               => 'Biography of Robert Johnson on Domain 3',
                    'content'             => 'the author is Bob Bloom, personbydomain_id = 1',
                    'excerpt'             => $faker->words(12, true),
                    'meta_description'    => $faker->words(12, true),
                    'featured_image_upload' => null,
                    'featured_image_code' => null,
                    'featured_image_external_file' => null,
                    'enabled'             => 1,
                    'publish_on'          => $now->subDays(2),
                    'uuid'                => 'created_during_initial_seeding',
                    'created_at'          => $now->subDays(2),
                    'created_by'          => 1,
                    'updated_at'          => $now->subDays(2),
                    'updated_by'          => 1,
                    'locked_at'           => null,
                    'locked_by'           => null,
                ]
            );

            Post::firstOrCreate(
                ['slug' => 'biography-of-stevie-ray-vaughan-on-domain-3',],
                [
                    'installed_domain_id' => 3,
                    'personbydomain_id'   => 1,
                    'category_id'         => 2,
                    'title'               => 'Biography of Stevie Ray Vaughan on Domain 3',
                    'content'             => 'the author is Bob Bloom, personbydomain_id = 1',
                    'excerpt'             => $faker->words(12, true),
                    'meta_description'    => $faker->words(12, true),
                    'featured_image_upload' => null,
                    'featured_image_code' => null,
                    'featured_image_external_file' => null,
                    'enabled'             => 1,
                    'publish_on'          => $now,
                    'uuid'                => 'created_during_initial_seeding',
                    'created_at'          => $now,
                    'created_by'          => 1,
                    'updated_at'          => $now,
                    'updated_by'          => 1,
                    'locked_at'           => null,
                    'locked_by'           => null,
                ]
            );
        }
    }
}
