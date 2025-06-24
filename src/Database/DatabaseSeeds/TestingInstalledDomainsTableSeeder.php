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
use Illuminate\Auth\EloquentUserProvider;
use Lasallesoftware\Librarybackend\Database\DatabaseSeeds\BaseSeeder;
use Lasallesoftware\Librarybackend\Profiles\Models\Installed_domain;

// Laravel Framework
use Illuminate\Support\Facades\DB;

// Third party classes
use Illuminate\Support\Carbon;

class TestingInstalledDomainsTableSeeder extends BaseSeeder
{
    protected $now;

    public function __construct()
    {
        $this->now  = Carbon::now();
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if ($this->doPopulateWithTestData()) {

            $this->setUpSimulatedFrontendDomain1();
            $this->setUpSimulatedFrontendDomain2();
            $this->setUpSimulatedFrontendDomain3();
        }
    }

    /**
     * Create a pretend front-end domain for testing
     *
     * @return void
     */
    private function setUpSimulatedFrontendDomain1()
    {
        Installed_domain::firstOrCreate(
            ['title' => 'pretendfrontend.com'],
            [
                'description' => 'PretendFrontEnd.com for testing',
                'enabled'     => '1',
                'created_at'  => $this->now,
                'created_by'  => 1,
                'updated_at'  => null,
                'updated_by'  => null,
                'locked_at'   => null,
                'locked_by'   => null,
            ]
        );

        $installedDomain = $this->getLastInstalledDomain();

        DB::table('installeddomain_domaintype')->insert([
            'installed_domain_id'   => $installedDomain->id,
            'lookup_domain_type_id' => '2',
        ]);
    }

    /**
     * Create another pretend front-end domain for testing
     *
     * @return void
     */
    private function setUpSimulatedFrontendDomain2()
    {
        Installed_domain::firstOrCreate(
            ['title' => 'anotherpretendfrontend.com'],
            [
                'description' => 'AnotherPretendFrontEnd.com for testing',
                'enabled'     => '1',
                'created_at'  => $this->now,
                'created_by'  => 1,
                'updated_at'  => null,
                'updated_by'  => null,
                'locked_at'   => null,
                'locked_by'   => null,
            ]
        );

        $installedDomain = $this->getLastInstalledDomain();

        DB::table('installeddomain_domaintype')->insert([
            'installed_domain_id'   => $installedDomain->id,
            'lookup_domain_type_id' => '2',
        ]);
    }

    /**
     * Create hackintosh.ls-basicfrontend-app.com for testing
     *
     * @return void
     */
    private function setUpSimulatedFrontendDomain3()
    {
        Installed_domain::firstOrCreate(
            ['title' => 'hackintosh.lsv2-basicfrontend-app.com'],
            [
                'description' => 'hackintosh.lsv2-basicfrontend-app.com',
                'enabled'     => '1',
                'created_at'  => $this->now,
                'created_by'  => 1,
                'updated_at'  => null,
                'updated_by'  => null,
                'locked_at'   => null,
                'locked_by'   => null,
            ]
        );

        $installedDomain = $this->getLastInstalledDomain();

        DB::table('installeddomain_domaintype')->insert([
            'installed_domain_id' => $installedDomain->id,
            'lookup_domain_type_id' => '2',
        ]);
    }

    /**
     * Get the last record in the installed_domains table.
     *
     * @return Eloquent
     */
    private function getLastInstalledDomain()
    {
        return \Lasallesoftware\Librarybackend\Profiles\Models\Installed_domain::orderBy('id', 'desc')->first();
    }
}
