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

namespace Lasallesoftware\Blogbackend\Database\DatabaseSeeds;

// LaSalle Software
use Lasallesoftware\Library\Database\DatabaseSeeds\BaseSeeder;

// Laravel Framework
use Illuminate\Support\Facades\DB;

// Third party class
use Faker\Generator as Faker;
use Illuminate\Support\Carbon;


class TestingCategoryTableSeeder extends BaseSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Faker $faker)
    {
        if ($this->doPopulateWithTestData()) {

            // installed_domain_id = 1
            factory(\Lasallesoftware\Blogbackend\Models\Category::class)->create([
                'installed_domain_id' => 1,
                'title'               => 'Music',
                'content'             => 'Music',
                'description'         => 'Music',
                'featured_image'      => 'https://unsplash.com/photos/s9XDWLJ_LyE/info',
                'uuid'                => 'created_during_initial_seeding',
            ]);

            factory(\Lasallesoftware\Blogbackend\Models\Category::class)->create([
                'installed_domain_id' => 1,
                'title'               => 'Sports',
                'content'             => 'Sports',
                'description'         => 'Sports',
                'featured_image'      => 'https://unsplash.com/photos/s9XDWLJ_LyE/info',
                'uuid'                => 'created_during_initial_seeding',
            ]);


            // installed_domain_id = 2
            factory(\Lasallesoftware\Blogbackend\Models\Category::class)->create([
                'title'               => 'Music For Domain 2',
                'uuid'                => 'created_during_initial_seeding',
            ]);
            $this->updateInstalledDomainId(2);

            factory(\Lasallesoftware\Blogbackend\Models\Category::class)->create([
                'title'               => 'Sports For Domain 2',
                'uuid'                => 'created_during_initial_seeding',
            ]);
            $this->updateInstalledDomainId(2);


            // installed_domain_id = 3
            factory(\Lasallesoftware\Blogbackend\Models\Category::class)->create([
                'title'               => 'Music For Domain 3',
                'uuid'                => 'created_during_initial_seeding',
            ]);
            $this->updateInstalledDomainId(3);

            factory(\Lasallesoftware\Blogbackend\Models\Category::class)->create([
                'title'               => 'Sports For Domain 3',
                'uuid'                => 'created_during_initial_seeding',
            ]);
            $this->updateInstalledDomainId(3);
        }
    }

    /**
     * Update the categories db table's installed_domain_id field with the desired valued.
     *
     * Incredibly, inexplicably, frustratingly, the factory zaps in "1" for installed_domain_id and cannot, will
     * not, absolutely refuses, to change the value. Not without spending quality time trying to figure this out.
     *
     * So, let's just update it after the fact and be done with it.
     *
     * @param  int  $desired_Installed_Domain_id
     */
    private function updateInstalledDomainId($desired_Installed_Domain_id = 1)
    {
        $lastId = \Lasallesoftware\Blogbackend\Models\Category::orderBy('id', 'desc')->first();

        DB::table('categories')
            ->where('id', $lastId->id)
            ->update(['installed_domain_id' => $desired_Installed_Domain_id,])
        ;
    }
}
