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
use Lasallesoftware\Library\Database\DatabaseSeeds\BaseSeeder;

// Laravel Framework
use Illuminate\Support\Facades\DB;

class DomainbydomaintypeTableSeeder extends BaseSeeder
{
    /**
     * Populate the domain_domaintype pivot table with the app's name (well, id) with "blog" (well, 2).
     *
     * @return void
     */
    public function run()
    {
        DB::table('installeddomain_domaintype')->insert([
            'id'                    => $this->getNewInstalleddomain_domaintypeId(),
            'installed_domain_id'   => $this->getInstalledDomainId(),
            'lookup_domain_type_id' => 3,                             // type  = blog
        ]);
    }
}
