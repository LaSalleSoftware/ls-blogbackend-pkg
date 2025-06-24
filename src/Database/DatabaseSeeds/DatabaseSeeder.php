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
 * @copyright  (c) 2019-2025 The South LaSalle Trading Corporation
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
use Lasallesoftware\Librarybackend\Database\DatabaseSeeds\BaseSeeder;

class DatabaseSeeder extends BaseSeeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            DomainbydomaintypeTableSeeder::class,
            CategoryTableSeeder::class,
        ]);

        if ($this->doPopulateWithTestData()) {

            // we want to let the post and postupdate model events know to use the test data,
            // especially since there is no one logged in (so cannot use Auth::id())
            if (! defined('BLOGBACKENDDBSEEDINGTESTDATA')) {
                define("BLOGBACKENDDBSEEDINGTESTDATA", true);
            }

            $this->call([
                TestingInstalledDomainsTableSeeder::class,
                TestingAdminsForBlogPackageTableSeeder::class,
                TestingCategoryTableSeeder::class,
                TestingTagTableSeeder::class,
                TestingPostTableSeeder::class,
                TestingPostupdateTableSeeder::class,
            ]);
        }
    }
}
