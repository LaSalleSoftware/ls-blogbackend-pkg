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
 * @copyright  (c) 2019-2024 The South LaSalle Trading Corporation
 * @license    http://opensource.org/licenses/MIT
 * @author     Bob Bloom
 * @email      bob.bloom@lasallesoftware.ca
 * @link       https://lasallesoftware.ca
 * @link       https://packagist.org/packages/lasallesoftware/ls-blogbackend-pkg
 * @link       https://github.com/LaSalleSoftware/ls-blogbackend-pkg
 *
 */

namespace Lasallesoftware\Blogbackend\Policies;

// LaSalle Software class
use Lasallesoftware\Librarybackend\Common\Policies\CommonPolicy;
use Lasallesoftware\Librarybackend\Authentication\Models\Personbydomain as User;
use Lasallesoftware\Blogbackend\Models\Post as Model;

// Laravel class
use Illuminate\Auth\Access\HandlesAuthorization;

// Laravel facade
use Illuminate\Support\Facades\DB;


/**
 * Class PostPolicy
 *
 * @package Lasallesoftware\Blogbackend\Policies
 */
class PostPolicy extends CommonPolicy
{
    use HandlesAuthorization;


    /**
     * Records that are not deletable.
     *
     * @var array
     */
    protected $recordsDoNotDelete = [];


    /**
     * Determine whether the user can view the post details.
     *
     * @param  \Lasallesoftware\Librarybackend\Authentication\Models\Personbydomain  $user
     * @param  \Lasallesoftware\Blogbackend\Models\Post                              $model
     * @return bool
     */
    public function view(User $user, Model $model)
    {
        // owner sees all!
        if ($user->hasRole('owner')) return true;

        // super admins only view posts that belong to their domain
        if (($user->hasRole('superadministrator')) && ($model->installed_domain_id == $user->installed_domain_id)) {
            return true;
        }

        // admins view posts that they authored
        if (($user->hasRole('administrator')) && ($model->personbydomain_id == $user->id)) {
            return true;
        }

        // still here? better return false!
        return false;
    }

    /**
     * Determine whether the user can create posts.
     *
     * @param  \Lasallesoftware\Librarybackend\Authentication\Models\Personbydomain  $user
     * @return bool
     */
    public function create(User $user)
    {
        // owner can create a post
        if ($user->hasRole('owner')) return true;

        // super admin can create a post
        if ($user->hasRole('superadministrator')) return true;

        // admin can create a post
        if ($user->hasRole('administrator')) return true;

        // still here?
        return false;
    }

    /**
     * Determine whether the user can update the posts.
     *
     * @param  \Lasallesoftware\Librarybackend\Authentication\Models\Personbydomain  $user
     * @param  \Lasallesoftware\Blogbackend\Models\Post                              $model
     * @return bool
     */
    public function update(User $user, Model $model)
    {
        // do not update posts that are on the "do not delete" list
        if ($this->isRecordDoNotDelete($model)) {
            return false;
        }

        // owner can update all post
        if ($user->hasRole('owner')) return true;

        // super admin can update posts that belong to their domain
        if (($user->hasRole('superadministrator')) && ($model->installed_domain_id == $user->installed_domain_id)) {
            return true;
        }

        // admin can update posts that they authored
        if (($user->hasRole('administrator')) && ($model->personbydomain_id == $user->id)) {
            return true;
        }

        // still here?
        return false;
    }

    /**
     * Determine whether the user can delete the posts.
     *
     * @param  \Lasallesoftware\Librarybackend\Authentication\Models\Personbydomain  $user
     * @param  \Lasallesoftware\Blogbackend\Models\Post                              $model
     * @return bool
     */
    public function delete(User $user, Model $model)
    {
        // do not delete posts that are on the "do not delete" lis
        if ($this->isRecordDoNotDelete($model)) {
            return false;
        }

        // owner can delete any post
        if ($user->hasRole('owner')) return true;

        // super admins can delete posts that belong to their domain
        if (($user->hasRole('superadministrator')) && ($model->installed_domain_id == $user->installed_domain_id)) {
            return true;
        }

        // admins can delete posts that they authored
        if (($user->hasRole('administrator')) && ($model->personbydomain_id == $user->id)) {
            return true;
        }

        // if still here, then this post is not deletable
        return false;
    }

    /**
     * Determine whether the user can restore the posts.
     *
     * DO NOT USE THIS FEATURE!
     *
     * @param  \Lasallesoftware\Librarybackend\Authentication\Models\Personbydomain  $user
     * @param  \Lasallesoftware\Blogbackend\Models\Post                              $model
     * @return bool
     */
    public function restore(User $user, Model $model)
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the posts.
     *
     * DO NOT USE THIS FEATURE!
     *
     * @param  \Lasallesoftware\Librarybackend\Authentication\Models\Personbydomain  $user
     * @param  \Lasallesoftware\Blogbackend\Models\Post                              $model
     * @return bool
     */
    public function forceDelete(User $user, Model $model)
    {
        return false;
    }
}
