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

namespace Lasallesoftware\Blogbackend\Database\Factories;

// LaSalle Software
use Lasallesoftware\Library\Common\Factories\CommonFactory;

/**
 * Class PostFactory
 *
 * A class to make it easier to run this factory from different classes, including tests.
 *
 * @package Lasallesoftware\Blogbackend\Database\Factories
 */
class PostFactory extends CommonFactory
{
    /**
     * Create posts records via the factory.
     *
     * @param  int  $numberOfRecordsToCreate
     * @param  int  $installedDomainId
     * @param  int  $personbydomainId
     * @param  int  $categoryId
     */
    public function createPostRecordsFromThePostFactory($numberOfRecordsToCreate = 1,
                                                        $installedDomainId = 1,
                                                        $personbydomainId = 1,
                                                        $categoryId = 1)
    {
        \Lasallesoftware\Blogbackend\Models\Post::unsetEventDispatcher();

        factory(\Lasallesoftware\Blogbackend\Models\Post::class, $numberOfRecordsToCreate)
            ->create([
                'installed_domain_id' => $installedDomainId,
                'personbydomain_id'   => $personbydomainId,
                'category_id'         => $categoryId,
            ])
        ;

        return;
    }
}
