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

namespace Lasallesoftware\Blogbackend;

// Laravel class
use Illuminate\Support\Facades\Gate;

/**
 * Trait BlogbackendPoliciesServiceProvider
 *
 * Adapted from https://github.com/laravel/framework/blob/5.8/src/Illuminate/Foundation/Support/Providers/AuthServiceProvider.php
 *
 * @package Lasallesoftware\Blogbackend
 */
trait BlogbackendPoliciesServiceProvider
{
    /**
     * Register the application's policies.
     *
     * @return void
     */
    public function registerPolicies()
    {
        foreach ($this->policies() as $key => $value) {
            Gate::policy($key, $value);
        }
    }
    /**
     * Get the policies defined on the provider.
     *
     * @return array
     */
    public function policies()
    {
        return [
            'Lasallesoftware\Blogbackend\Models\Category'   => 'Lasallesoftware\Blogbackend\Policies\CategoryPolicy',
            'Lasallesoftware\Blogbackend\Models\Tag'        => 'Lasallesoftware\Blogbackend\Policies\TagPolicy',
            'Lasallesoftware\Blogbackend\Models\Post'       => 'Lasallesoftware\Blogbackend\Policies\PostPolicy',
            'Lasallesoftware\Blogbackend\Models\Postupdate' => 'Lasallesoftware\Blogbackend\Policies\PostupdatePolicy',
        ];
    }
}
