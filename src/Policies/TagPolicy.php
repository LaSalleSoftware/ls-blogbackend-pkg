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
 * @copyright  (c) 2019-2022 The South LaSalle Trading Corporation
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
use Lasallesoftware\Blogbackend\Models\Tag as Model;

// Laravel class
use Illuminate\Auth\Access\HandlesAuthorization;

// Laravel facade
use Illuminate\Support\Facades\DB;


/**
 * Class TagPolicy
 *
 * @package Lasallesoftware\Blogbackend\Policies
 */
class TagPolicy extends CommonPolicy
{
    use HandlesAuthorization;


    /**
     * Records that are not deletable.
     *
     * @var array
     */
    protected $recordsDoNotDelete = [];


    /**
     * Determine whether the user can view the tag details.
     *
     * @param  \Lasallesoftware\Librarybackend\Authentication\Models\Personbydomain  $user
     * @param  \Lasallesoftware\Blogbackend\Models\Tag                               $model
     * @return bool
     */
    public function view(User $user, Model $model)
    {
        // owner sees all!
        if ($user->hasRole('owner')) return true;

        // super admins only view tags that belong to their domain
        if (($user->hasRole('superadministrator')) && ($model->installed_domain_id == $user->installed_domain_id)) {
            return true;
        }

        // admins cannot view any tags
        return false;
    }

    /**
     * Determine whether the user can create tags.
     *
     * @param  \Lasallesoftware\Librarybackend\Authentication\Models\Personbydomain  $user
     * @return bool
     */
    public function create(User $user)
    {
        // owner can create a tag
        if ($user->hasRole('owner')) return true;

        // super admin can create a tag
        if ($user->hasRole('superadministrator')) return true;

        // admins cannot create tags
        return false;
    }

    /**
     * Determine whether the user can update the tags.
     *
     * @param  \Lasallesoftware\Librarybackend\Authentication\Models\Personbydomain  $user
     * @param  \Lasallesoftware\Blogbackend\Models\Tag                               $model
     * @return bool
     */
    public function update(User $user, Model $model)
    {
        // do not modify tags that are on the "do not delete" list
        if ($this->isRecordDoNotDelete($model)) {
            return false;
        }

        // owner can create a tag
        if ($user->hasRole('owner')) return true;

        // super admin can create a tag
        if ($user->hasRole('superadministrator')) return true;

        // admins cannot update tags
        return false;
    }

    /**
     * Determine whether the user can delete the tags.
     *
     * @param  \Lasallesoftware\Librarybackend\Authentication\Models\Personbydomain  $user
     * @param  \Lasallesoftware\Blogbackend\Models\Tag                               $model
     * @return bool
     */
    public function delete(User $user, Model $model)
    {
        // do not delete tags that are on the "do not delete" lis
        if ($this->isRecordDoNotDelete($model)) {
            return false;
        }

        // if this tag is in the post table, then this tag is not deletable
        if ( DB::table('post_tag')->where('tag_id', $model->id)->first() ) {
            return false;
        }

        // owner can delete any tag
        if ($user->hasRole('owner')) return true;

        // super admins can delete tags that belong to their domain
        if (($user->hasRole('superadministrator')) && ($model->installed_domain_id == $user->installed_domain_id)) {
            return true;
        }

        // if still here, then this tag is not deletable
        return false;
    }

    /**
     * Determine whether the user can restore the tags.
     *
     * DO NOT USE THIS FEATURE!
     *
     * @param  \Lasallesoftware\Librarybackend\Authentication\Models\Personbydomain  $user
     * @param  \Lasallesoftware\Blogbackend\Models\Tag                               $model
     * @return bool
     */
    public function restore(User $user, Model $model)
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the tags.
     *
     * DO NOT USE THIS FEATURE!
     *
     * @param  \Lasallesoftware\Librarybackend\Authentication\Models\Personbydomain  $user
     * @param  \Lasallesoftware\Blogbackend\Models\Tag                               $model
     * @return bool
     */
    public function forceDelete(User $user, Model $model)
    {
        return false;
    }
}
