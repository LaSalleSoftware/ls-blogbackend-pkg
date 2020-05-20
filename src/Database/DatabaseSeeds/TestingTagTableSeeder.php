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
 * @link       https://packagist.org/packages/lasallesoftware/ls-blogbackend-pkg
 * @link       https://github.com/LaSalleSoftware/ls-blogbackend-pkg
 *
 */

namespace Lasallesoftware\Blogbackend\Database\DatabaseSeeds;

// LaSalle Software
use Lasallesoftware\Blogbackend\Models\Tag;
use Lasallesoftware\Library\Database\DatabaseSeeds\BaseSeeder;

// Laravel Framework
use Illuminate\Support\Facades\DB;

// Third party class
use Illuminate\Support\Carbon;;

class TestingTagTableSeeder extends BaseSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $domain_title        = app('config')->get('lasallesoftware-library.lasalle_app_domain_name');
        $installed_domain_id = DB::table('installed_domains')->where('title', $domain_title)->value('id');

        if ($this->doPopulateWithTestData()) {

            // installed_domain_id = 1
            Tag::firstOrCreate(
                ['title' => 'Classical Domain 1',],
                [
                    'installed_domain_id' => 1,
                    'description'         => 'Classical Domain 1',
                    'enabled'             => 1,
                    'uuid'                => 'created_during_initial_seeding',
                    'created_at'          => Carbon::now(),
                    'created_by'          => 1,
                    'updated_at'          => null,
                    'updated_by'          => null,
                    'locked_at'           => null,
                    'locked_by'           => null,
                ]
            );

            Tag::firstOrCreate(
                ['title' => 'Blues Domain 1',],
                [
                    'installed_domain_id' => 1,
                    'description'         => 'Blues Domain 1',
                    'enabled'             => 1,
                    'uuid'                => 'created_during_initial_seeding',
                    'created_at'          => Carbon::now(),
                    'created_by'          => 1,
                    'updated_at'          => null,
                    'updated_by'          => null,
                    'locked_at'           => null,
                    'locked_by'           => null,
                ]
            );

            Tag::firstOrCreate(
                ['title' => 'Jazz Domain 1',],
                [
                    'installed_domain_id' => 1,
                    'description'         => 'Jazz Domain 1',
                    'enabled'             => 1,
                    'uuid'                => 'created_during_initial_seeding',
                    'created_at'          => Carbon::now(),
                    'created_by'          => 1,
                    'updated_at'          => null,
                    'updated_by'          => null,
                    'locked_at'           => null,
                    'locked_by'           => null,
                ]
            );

            // installed_domain_id = 2
            Tag::firstOrCreate(
                ['title' => 'Classical Domain 2',],
                [
                    'installed_domain_id' => 2,
                    'description'         => 'Classical Domain 2',
                    'enabled'             => 1,
                    'uuid'                => 'created_during_initial_seeding',
                    'created_at'          => Carbon::now(),
                    'created_by'          => 1,
                    'updated_at'          => null,
                    'updated_by'          => null,
                    'locked_at'           => null,
                    'locked_by'           => null,
                ]
            );

            Tag::firstOrCreate(
                ['title' => 'Blues Domain 2',],
                [
                    'installed_domain_id' => 2,
                    'description'         => 'Blues Domain 2',
                    'enabled'             => 1,
                    'uuid'                => 'created_during_initial_seeding',
                    'created_at'          => Carbon::now(),
                    'created_by'          => 1,
                    'updated_at'          => null,
                    'updated_by'          => null,
                    'locked_at'           => null,
                    'locked_by'           => null,
                ]
            );

            // installed_domain_id = 3
            Tag::firstOrCreate(
                ['title' => 'Classical Domain 3',],
                [
                    'installed_domain_id' => 3,
                    'description'         => 'Classical Domain 3',
                    'enabled'             => 1,
                    'uuid'                => 'created_during_initial_seeding',
                    'created_at'          => Carbon::now(),
                    'created_by'          => 1,
                    'updated_at'          => null,
                    'updated_by'          => null,
                    'locked_at'           => null,
                    'locked_by'           => null,
                ]
            );

            Tag::firstOrCreate(
                ['title' => 'Blues Domain 3',],
                [
                    'installed_domain_id' => 3,
                    'description'         => 'Blues Domain 3',
                    'enabled'             => 1,
                    'uuid'                => 'created_during_initial_seeding',
                    'created_at'          => Carbon::now(),
                    'created_by'          => 1,
                    'updated_at'          => null,
                    'updated_by'          => null,
                    'locked_at'           => null,
                    'locked_by'           => null,
                ]
            );
        }
    }
}
