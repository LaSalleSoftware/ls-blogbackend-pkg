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

namespace Lasallesoftware\Blogbackend\Policies;

// LaSalle Software class
use Lasallesoftware\Librarybackend\Common\Policies\CommonPolicy;
use Lasallesoftware\Librarybackend\Authentication\Models\Personbydomain as User;
use Lasallesoftware\Blogbackend\Models\Postupdate as Model;

// Laravel class
use Illuminate\Auth\Access\HandlesAuthorization;

// Laravel facade
use Illuminate\Support\Facades\DB;


/**
 * Class PostupdatePolicy
 *
 * @package Lasallesoftware\Blogbackend\Policies
 */
class PostupdatePolicy extends CommonPolicy
{
    use HandlesAuthorization;


    /**
     * Records that are not deletable.
     *
     * @var array
     */
    protected $recordsDoNotDelete = [];


    /**
     * Determine whether the user can view the postupdate details.
     *
     * @param  \Lasallesoftware\Librarybackend\Authentication\Models\Personbydomain  $user
     * @param  \Lasallesoftware\Blogbackend\Models\Postupdate                       $model
     * @return bool
     */
    public function view(User $user, Model $model)
    {
        // owner sees all!
        if ($user->hasRole('owner')) return true;

        // super admins only view postupdates that belong to their domain
        if (($user->hasRole('superadministrator')) && ($model->installed_domain_id == $user->installed_domain_id)) {
            return true;
        }

        // admins view postupdates that they authored
        if (($user->hasRole('administrator')) && ($model->personbydomain_id == $user->id)) {
            return true;
        }

        // still here? better return false!
        return false;
    }

    /**
     * Determine whether the user can create post updates.
     *
     * Suppress the create button when there are no posts available.
     *
     * @param  \Lasallesoftware\Librarybackend\Authentication\Models\Personbydomain  $user
     * @return bool
     */
    public function create(User $user)
    {
        // owner can create post updates for all posts
        if (($user->hasRole('owner')) && ($this->isPostsForOwner())) return true;

        // super admin can create post updates for posts associated with their domain
        if (($user->hasRole('superadministrator')) && ($this->isPostsForSuperadministrator($user))) return true;

        // admin can create post updates for posts that they authored
        if (($user->hasRole('administrator')) && ($this->isPostsForAdministrator($user))) return true;

        // still here may mean that there are no posts available
        return false;
    }

    /**
     * Determine whether the user can update the postupdates.
     *
     * @param  \Lasallesoftware\Librarybackend\Authentication\Models\Personbydomain  $user
     * @param  \Lasallesoftware\Blogbackend\Models\Postupdate                        $model
     * @return bool
     */
    public function update(User $user, Model $model)
    {
        // do not update postupdates that are on the "do not delete" list
        if ($this->isRecordDoNotDelete($model)) {
            return false;
        }

        // owner can update all postupdate
        if ($user->hasRole('owner')) return true;

        // super admin can update postupdates that belong to their domain
        if (($user->hasRole('superadministrator')) && ($model->installed_domain_id == $user->installed_domain_id)) {
            return true;
        }

        // admin can update postupdates that they authored
        if (($user->hasRole('administrator')) && ($model->personbydomain_id == $user->id)) {
            return true;
        }

        // still here?
        return false;
    }

    /**
     * Determine whether the user can delete the postupdates.
     *
     * @param  \Lasallesoftware\Librarybackend\Authentication\Models\Personbydomain  $user
     * @param  \Lasallesoftware\Blogbackend\Models\Postupdate                        $model
     * @return bool
     */
    public function delete(User $user, Model $model)
    {
        // do not delete postupdates that are on the "do not delete" lis
        if ($this->isRecordDoNotDelete($model)) {
            return false;
        }

        // owner can delete any postupdate
        if ($user->hasRole('owner')) return true;

        // super admins can delete postupdates that belong to their domain
        if (($user->hasRole('superadministrator')) && ($model->installed_domain_id == $user->installed_domain_id)) {
            return true;
        }

        // admins can delete postupdates that they authored
        if (($user->hasRole('administrator')) && ($model->personbydomain_id == $user->id)) {
            return true;
        }

        // if still here, then this postupdate is not deletable
        return false;
    }

    /**
     * Determine whether the user can restore the postupdates.
     *
     * DO NOT USE THIS FEATURE!
     *
     * @param  \Lasallesoftware\Librarybackend\Authentication\Models\Personbydomain  $user
     * @param  \Lasallesoftware\Blogbackend\Models\Postupdate                        $model
     * @return bool
     */
    public function restore(User $user, Model $model)
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the postupdates.
     *
     * DO NOT USE THIS FEATURE!
     *
     * @param  \Lasallesoftware\Librarybackend\Authentication\Models\Personbydomain  $user
     * @param  \Lasallesoftware\Blogbackend\Models\Postupdate                        $model
     * @return bool
     */
    public function forceDelete(User $user, Model $model)
    {
        return false;
    }


    // I ADAPT THE FOLLOWING THREE METHODS AT Lasallesoftware\Blogbackend\Nova\Resources\Postupdate for availableForNavigation()

    /**
     * Are there posts available to an owner?
     *
     * @return bool
     */
    private function isPostsForOwner()
    {
        return (DB::table('posts')->count() == 0) ? false : true;
    }

    /**
     * Are there posts available to a super administrator?
     *
     * @param  \Lasallesoftware\Librarybackend\Authentication\Models\Personbydomain  $user
     * @return bool
     */
    private function isPostsForSuperadministrator($user)
    {
        return (DB::table('posts')->where('installed_domain_id', $user->installed_domain_id)->count() == 0) ? false : true;
    }

    /**
     * Are there posts available to an administrator?
     *
     * @param  \Lasallesoftware\Librarybackend\Authentication\Models\Personbydomain  $user
     * @return bool
     */
    private function isPostsForAdministrator($user)
    {
        return (DB::table('posts')->where('personbydomain_id', $user->id)->count() == 0) ? false : true;
    }
}
