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
 * @copyright  (c) 2019-2021 The South LaSalle Trading Corporation
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

namespace Lasallesoftware\Blogbackend\Database\Factories;

// LaSalle Software
use Lasallesoftware\Librarybackend\Common\Factories\CommonFactory;

/**
 * Class PostupdateFactory
 *
 * A class to make it easier to run this factory from different classes, including tests.
 *
 * @package Lasallesoftware\Blogbackend\Database\Factories
 */
class PostupdateFactory extends CommonFactory
{
    /**
     * Create posts records via the factory.
     *
     * @param  int  $numberOfRecordsToCreate
     * @param  int  $installedDomainId
     * @param  int  $personbydomainId
     * @param  int  $postId
     */
    public function createPostupdateRecordsFromThePostupdateFactory($numberOfRecordsToCreate = 1,
                                                                    $installedDomainId = 1,
                                                                    $personbydomainId = 1,
                                                                    $postId = 1)
    {
        \Lasallesoftware\Blogbackend\Models\Postupdate::unsetEventDispatcher();

        factory(\Lasallesoftware\Blogbackend\Models\Postupdate::class, $numberOfRecordsToCreate)->create([
            'installed_domain_id' => $installedDomainId,
            'personbydomain_id'   => $personbydomainId,
            'post_id'             => $postId
        ]);

        return;
    }
}
